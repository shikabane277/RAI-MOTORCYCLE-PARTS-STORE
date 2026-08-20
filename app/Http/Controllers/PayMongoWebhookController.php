<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PayMongoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives PayMongo webhook events. This — not the customer's browser — is the
 * authoritative signal that an order has been paid: it still arrives when the
 * customer closes the tab on the gateway's page before being redirected back.
 */
class PayMongoWebhookController extends Controller
{
    /** Events that mean "money has settled for a checkout session". */
    private const PAID_EVENTS = [
        'checkout_session.payment.paid',
        'payment.paid',
        'link.payment.paid',
    ];

    /** How stale a signed timestamp may be before we note it in the log. */
    private const TIMESTAMP_TOLERANCE = 300; // seconds

    /** Only these orders are ever settled by a gateway event. */
    private const ONLINE_METHODS = ['google_pay', 'qrph', 'gcash'];

    public function handle(Request $request, PayMongoService $payMongo): JsonResponse
    {
        $secret = (string) config('services.paymongo.webhook_secret');

        if ($secret === '') {
            Log::error('PayMongo webhook: no webhook secret configured, event rejected');

            // 503 so PayMongo keeps retrying once the secret is in place.
            return response()->json(['error' => 'webhook not configured'], 503);
        }

        $rawBody = $request->getContent();

        if (! $this->signatureIsValid($request->header('Paymongo-Signature'), $rawBody, $secret)) {
            Log::warning('PayMongo webhook: signature verification failed', [
                'ip'         => $request->ip(),
                'signature'  => $request->header('Paymongo-Signature'),
                'body_bytes' => strlen($rawBody),
            ]);

            return response()->json(['error' => 'invalid signature'], 400);
        }

        $event     = $request->json('data.attributes') ?? [];
        $eventType = $event['type'] ?? null;
        $resource  = $event['data'] ?? [];

        if (! in_array($eventType, self::PAID_EVENTS, true)) {
            // Acknowledge anything else so PayMongo stops retrying it.
            Log::info('PayMongo webhook: ignoring event', ['type' => $eventType]);

            return response()->json(['received' => true]);
        }

        $order = $this->resolveOrder($resource);

        if (! $order) {
            Log::error('PayMongo webhook: could not match event to an order', [
                'type'             => $eventType,
                'resource_id'      => $resource['id'] ?? null,
                'reference_number' => $resource['attributes']['reference_number'] ?? null,
            ]);

            // 200 — retrying will not conjure up an order that is not there.
            return response()->json(['received' => true]);
        }

        // An order is only ever settled from PayMongo's own records, never from
        // the payload. That means the event must resolve to an online order that
        // carries the checkout session id we stored when we created it; anything
        // else (a COD order matched by a free-text description, an order whose
        // session id never got written) is acknowledged and left alone.
        if (! in_array($order->payment_method, self::ONLINE_METHODS, true)) {
            Log::warning('PayMongo webhook: event resolved to a non-online order, ignoring', [
                'order'  => $order->order_number,
                'method' => $order->payment_method,
                'type'   => $eventType,
            ]);

            return response()->json(['received' => true]);
        }

        $sessionId = $order->payments()->whereNotNull('gateway_ref')->latest('id')->value('gateway_ref');

        if (! is_string($sessionId) || ! str_starts_with($sessionId, 'cs_')) {
            Log::error('PayMongo webhook: no checkout session on file, refusing to settle unverified', [
                'order' => $order->order_number,
                'type'  => $eventType,
            ]);

            return response()->json(['received' => true]);
        }

        // Trust the signature for authenticity, but re-read the session so the
        // paid status comes from PayMongo's own records rather than the payload.
        $verified = $payMongo->verifyCheckoutSessionPaid($sessionId);

        if (! $verified['success']) {
            Log::error('PayMongo webhook: could not verify session, will retry', [
                'order'      => $order->order_number,
                'session_id' => $sessionId,
            ]);

            // 503 so PayMongo retries — do not settle on an unverified event.
            return response()->json(['error' => 'verification unavailable'], 503);
        }

        if (empty($verified['paid'])) {
            Log::warning('PayMongo webhook: paid event but session is not paid', [
                'order'      => $order->order_number,
                'session_id' => $sessionId,
                'status'     => $verified['status'] ?? null,
            ]);

            return response()->json(['received' => true]);
        }

        $expected = (int) round(((float) $order->grand_total) * 100);
        $paid     = $verified['amount'] ?? null;

        if ($paid !== null && $paid < $expected) {
            Log::error('PayMongo webhook: captured amount is short of the order total, not settling', [
                'order'      => $order->order_number,
                'session_id' => $sessionId,
                'paid'       => $paid,
                'expected'   => $expected,
            ]);

            return response()->json(['received' => true]);
        }

        $paymentId = $verified['payment_id'] ?? $this->extractPaymentId($resource);
        $session   = $verified['session'] ?? ($resource['attributes'] ?? []);

        if ($order->settleOnlinePayment($paymentId, $session)) {
            Log::info('PayMongo webhook: order settled', [
                'order'      => $order->order_number,
                'type'       => $eventType,
                'payment_id' => $paymentId,
            ]);
        } else {
            Log::info('PayMongo webhook: order already settled', ['order' => $order->order_number]);
        }

        return response()->json(['received' => true]);
    }

    /**
     * PayMongo signs each event as "t=<timestamp>,te=<test sig>,li=<live sig>",
     * where the signature is an HMAC-SHA256 of "<timestamp>.<raw body>" keyed
     * with the webhook secret. Only one of te/li is populated, depending on the
     * mode the event was generated in, so a match against either is authentic.
     */
    private function signatureIsValid(?string $header, string $rawBody, string $secret): bool
    {
        if (! $header) {
            return false;
        }

        $parts = [];

        foreach (explode(',', $header) as $segment) {
            $pair = explode('=', trim($segment), 2);

            if (count($pair) === 2) {
                $parts[$pair[0]] = $pair[1];
            }
        }

        $timestamp = $parts['t'] ?? null;

        if (! $timestamp) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);
        $matched  = false;

        foreach (['li', 'te'] as $mode) {
            if (! empty($parts[$mode]) && hash_equals($expected, $parts[$mode])) {
                $matched = true;
                break;
            }
        }

        if (! $matched) {
            return false;
        }

        // A valid signature already proves authenticity, and settlement is
        // idempotent, so a replayed event is harmless — worth noting, not worth
        // dropping a real payment over a skewed clock.
        $age = abs(time() - (int) $timestamp);

        if ($age > self::TIMESTAMP_TOLERANCE) {
            Log::warning('PayMongo webhook: signed timestamp is stale', ['age_seconds' => $age]);
        }

        return true;
    }

    /**
     * Match the event's resource back to a local order: first by the checkout
     * session id we stored when the session was created, then by the reference
     * number we sent along with it.
     *
     * @param  array<string, mixed>  $resource
     */
    private function resolveOrder(array $resource): ?Order
    {
        $resourceId = $resource['id'] ?? null;

        if (is_string($resourceId) && $resourceId !== '') {
            $order = Order::whereHas('payments', fn ($query) => $query->where('gateway_ref', $resourceId))->first();

            if ($order) {
                return $order;
            }
        }

        $reference = $resource['attributes']['reference_number']
            ?? $resource['attributes']['description']
            ?? null;

        if (is_string($reference) && $reference !== '') {
            return Order::where('order_number', $reference)->first();
        }

        return null;
    }

    /** @param  array<string, mixed>  $resource */
    private function extractPaymentId(array $resource): ?string
    {
        // payment.paid delivers the payment itself; checkout_session.payment.paid
        // delivers the session with its payments nested inside.
        if (($resource['type'] ?? null) === 'payment') {
            return $resource['id'] ?? null;
        }

        foreach ($resource['attributes']['payments'] ?? [] as $payment) {
            if (($payment['attributes']['status'] ?? null) === 'paid') {
                return $payment['id'] ?? null;
            }
        }

        return null;
    }
}
