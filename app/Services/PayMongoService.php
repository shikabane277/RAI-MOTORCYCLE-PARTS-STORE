<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    private const CAPABILITIES_CACHE_PREFIX = 'paymongo:enabled_payment_methods:';
    private const CAPABILITIES_CACHE_TTL = 86400; // 24 hours

    /**
     * Every identifier PayMongo currently accepts in payment_method_types.
     * Anything outside this list is rejected at session creation with
     * {"code":"parameter_invalid","detail":"<x> is an invalid payment_method."}
     */
    private const VALID_METHODS = [
        'billease', 'brankas', 'card', 'dob', 'gcash',
        'grab_pay', 'paymaya', 'qrph', 'shopee_pay',
    ];

    /** Customer-facing names, used when we have to explain what is unavailable. */
    private const METHOD_LABELS = [
        'card'       => 'Card / Google Pay',
        'qrph'       => 'QR Ph',
        'gcash'      => 'GCash',
        'paymaya'    => 'Maya',
        'grab_pay'   => 'GrabPay',
        'shopee_pay' => 'ShopeePay',
        'dob'        => 'Online Banking',
        'brankas'    => 'Online Banking',
        'billease'   => 'BillEase',
    ];

    /**
     * Which methods each checkout option asks for. Only the ones the merchant
     * account has actually onboarded will survive into the session.
     */
    private const DESIRED_METHODS = [
        'card'  => ['card', 'qrph'],
        'qrph'  => ['qrph', 'gcash', 'paymaya'],
        'gcash' => ['gcash', 'qrph'],
    ];

    /** PayMongo rejects any session totalling under PHP 1.00. */
    private const MINIMUM_CENTAVOS = 100;

    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = (string) config('services.paymongo.secret_key');
        $this->publicKey = (string) config('services.paymongo.public_key');
    }

    public function isConfigured(): bool
    {
        return $this->secretKey !== '';
    }

    /**
     * Create a PayMongo Checkout Session for an Order.
     *
     * @param  string  $paymentType  One of the keys of self::DESIRED_METHODS.
     * @return array{success: bool, checkout_url?: string, session_id?: string, message?: string, error_code?: string}
     */
    public function createCheckoutSession(Order $order, string $paymentType = 'qrph'): array
    {
        if (! $this->isConfigured()) {
            Log::error('PayMongo: no secret key configured', ['order' => $order->order_number]);

            return [
                'success'    => false,
                'error_code' => 'not_configured',
                'message'    => 'Online payment is not configured on this store.',
            ];
        }

        $resolved = $this->resolvePaymentMethods($paymentType);

        if (! $resolved['success']) {
            Log::warning('PayMongo: requested payment method is not enabled', [
                'order'   => $order->order_number,
                'type'    => $paymentType,
                'desired' => $resolved['desired'],
                'enabled' => $resolved['enabled'],
            ]);

            return $resolved;
        }

        $lineItems = $this->buildLineItems($order);
        $total     = $this->sumLineItems($lineItems);

        if ($total < self::MINIMUM_CENTAVOS) {
            Log::error('PayMongo: order total below gateway minimum', [
                'order'    => $order->order_number,
                'centavos' => $total,
            ]);

            return [
                'success'    => false,
                'error_code' => 'below_minimum',
                'message'    => 'Online payment requires a minimum order total of PHP 1.00. Please choose Cash on Delivery.',
            ];
        }

        $payload = [
            'data' => [
                'attributes' => [
                    'billing' => [
                        'name'  => $order->ship_recipient,
                        'email' => $order->user?->email ?? $order->guest_email ?? 'customer@raimotorcycleparts.ph',
                        'phone' => $order->ship_phone,
                    ],
                    'send_email_receipt'   => true,
                    'show_description'     => true,
                    'show_line_items'      => true,
                    'payment_method_types' => $resolved['methods'],
                    'line_items'           => $lineItems,
                    'description'          => 'Payment for Order #' . $order->order_number . ' at RAI MOTORCYCLE PARTS',
                    'success_url'          => route('checkout.success', ['order' => $order->order_number]) . '?paymongo=success',
                    'cancel_url'           => route('checkout.index') . '?paymongo=cancelled',
                    'reference_number'     => $order->order_number,
                ],
            ],
        ];

        try {
            $response = $this->request()->post($this->baseUrl . '/checkout_sessions', $payload);

            if ($response->successful()) {
                $data = $response->json('data');

                return [
                    'success'      => true,
                    'checkout_url' => $data['attributes']['checkout_url'],
                    'session_id'   => $data['id'],
                    'methods'      => $data['attributes']['payment_method_types'] ?? $resolved['methods'],
                ];
            }

            Log::error('PayMongo: checkout session creation failed', [
                'order'       => $order->order_number,
                'http_status' => $response->status(),
                'type'        => $paymentType,
                'methods'     => $resolved['methods'],
                'centavos'    => $total,
                'errors'      => $response->json('errors') ?? $response->body(),
                'payload'     => $this->redactPayload($payload),
            ]);

            return [
                'success'    => false,
                'error_code' => $response->json('errors.0.code') ?? 'gateway_error',
                'message'    => $response->json('errors.0.detail') ?: 'PayMongo session creation failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('PayMongo: checkout session exception', [
                'order' => $order->order_number,
                'type'  => $paymentType,
                'error' => $e->getMessage(),
            ]);

            return [
                'success'    => false,
                'error_code' => 'exception',
                'message'    => $e->getMessage(),
            ];
        }
    }

    /**
     * Re-fetch a checkout session and report whether PayMongo considers it paid.
     * This is the authoritative check — never trust the ?paymongo=success flag
     * on the return URL, which any visitor can type by hand.
     *
     * @return array{success: bool, paid: bool, message?: string, payment_id?: ?string, amount?: ?int, status?: ?string, session?: array}
     */
    public function verifyCheckoutSessionPaid(string $sessionId): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'paid' => false, 'message' => 'Online payment is not configured on this store.'];
        }

        try {
            $response = $this->request()->get($this->baseUrl . '/checkout_sessions/' . $sessionId);

            if (! $response->successful()) {
                Log::error('PayMongo: checkout session retrieval failed', [
                    'session_id'  => $sessionId,
                    'http_status' => $response->status(),
                    'errors'      => $response->json('errors') ?? $response->body(),
                ]);

                return [
                    'success' => false,
                    'paid'    => false,
                    'message' => $response->json('errors.0.detail') ?: 'Could not confirm your payment with PayMongo.',
                ];
            }

            $session = $response->json('data.attributes') ?? [];

            foreach ($session['payments'] ?? [] as $payment) {
                if (($payment['attributes']['status'] ?? null) === 'paid') {
                    return [
                        'success'    => true,
                        'paid'       => true,
                        'payment_id' => $payment['id'] ?? null,
                        'amount'     => $payment['attributes']['amount'] ?? null,
                        'status'     => 'paid',
                        'session'    => $session,
                    ];
                }
            }

            return [
                'success' => true,
                'paid'    => false,
                'status'  => $session['payment_intent']['attributes']['status'] ?? ($session['status'] ?? null),
                'session' => $session,
            ];
        } catch (\Throwable $e) {
            Log::error('PayMongo: checkout session retrieval exception', [
                'session_id' => $sessionId,
                'error'      => $e->getMessage(),
            ]);

            return ['success' => false, 'paid' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Payment methods actually onboarded on this merchant account.
     *
     * PayMongo accepts any *valid* identifier when a session is created and
     * echoes it straight back, but its hosted page filters out methods the
     * account has not been approved for. A session whose methods are all
     * disabled renders as "No payment methods are available", so we have to
     * intersect against this list before sending the customer anywhere.
     *
     * Returns [] when the capability lookup itself is unavailable; callers
     * treat that as "unknown" and fall back to the requested set.
     *
     * @return list<string>
     */
    public function enabledPaymentMethods(bool $fresh = false): array
    {
        if (! $fresh) {
            $cached = Cache::get($this->capabilitiesCacheKey());

            if (is_array($cached)) {
                return $cached;
            }
        }

        try {
            $response = $this->request()->get($this->baseUrl . '/merchants/capabilities/payment_methods');

            if ($response->successful()) {
                $methods = array_values(array_intersect((array) $response->json(), self::VALID_METHODS));
                Cache::put($this->capabilitiesCacheKey(), $methods, self::CAPABILITIES_CACHE_TTL);

                return $methods;
            }

            Log::warning('PayMongo: merchant capabilities lookup failed', [
                'http_status' => $response->status(),
                'errors'      => $response->json('errors') ?? $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('PayMongo: merchant capabilities exception', ['error' => $e->getMessage()]);
        }

        // Deliberately not cached — retry on the next checkout attempt.
        return [];
    }

    /**
     * Cache key for the capability list, scoped to the credential that fetched
     * it. Swapping between live and test keys would otherwise inherit up to a
     * day of the other account's capabilities — which is precisely how a
     * session gets created for a method the account cannot present.
     */
    private function capabilitiesCacheKey(): string
    {
        return self::CAPABILITIES_CACHE_PREFIX . substr(hash('sha256', $this->secretKey), 0, 12);
    }

    /**
     * Narrow the methods wanted for $paymentType down to the ones this account
     * can actually present, and explain the gap when nothing is left.
     *
     * @return array{success: bool, methods?: list<string>, desired: list<string>, enabled: list<string>, error_code?: string, message?: string}
     */
    private function resolvePaymentMethods(string $paymentType): array
    {
        $desired = self::DESIRED_METHODS[$paymentType] ?? self::DESIRED_METHODS['qrph'];
        $enabled = $this->enabledPaymentMethods();

        // Capability lookup unavailable — do not block checkout on a transient
        // outage; send the requested set and let PayMongo have the last word.
        if ($enabled === []) {
            return ['success' => true, 'methods' => $desired, 'desired' => $desired, 'enabled' => []];
        }

        $methods = array_values(array_intersect($desired, $enabled));

        if ($methods !== []) {
            return ['success' => true, 'methods' => $methods, 'desired' => $desired, 'enabled' => $enabled];
        }

        return [
            'success'    => false,
            'desired'    => $desired,
            'enabled'    => $enabled,
            'error_code' => 'payment_method_not_enabled',
            'message'    => $this->unavailableMessage($desired, $enabled),
        ];
    }

    /**
     * @param  list<string>  $desired
     * @param  list<string>  $enabled
     */
    private function unavailableMessage(array $desired, array $enabled): string
    {
        $wanted = $this->labels($desired);
        $spare  = array_values(array_diff($this->labels($enabled), $wanted));

        $message = implode(' / ', $wanted) . ' payments are not currently enabled on our payment gateway.';

        if ($spare !== []) {
            return $message . ' Please pay with ' . implode(' or ', $spare) . ', or choose Cash on Delivery.';
        }

        return $message . ' Please choose Cash on Delivery instead.';
    }

    /**
     * @param  list<string>  $methods
     * @return list<string>
     */
    private function labels(array $methods): array
    {
        return array_values(array_unique(array_map(
            fn (string $method): string => self::METHOD_LABELS[$method] ?? $method,
            $methods
        )));
    }

    /**
     * PayMongo has no discount field and rejects negative amounts outright
     * ("Parameter line_item.amount must not be negative"), so a coupon cannot
     * be expressed as its own line. Itemise only when the lines add up to
     * grand_total exactly; otherwise collapse to a single reconciled line so
     * the customer is always charged the order total to the centavo.
     *
     * @return list<array{name: string, amount: int, currency: string, quantity: int}>
     */
    private function buildLineItems(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $variant = trim((string) ($item->variant_label ?? '')) ?: 'Standard';

            $items[] = [
                'name'     => $item->product_name . ' (' . $variant . ')',
                'amount'   => $this->centavos($item->unit_price),
                'currency' => 'PHP',
                'quantity' => max(1, (int) $item->qty),
            ];
        }

        if ($order->shipping_fee > 0) {
            $items[] = [
                'name'     => 'Lalamove Delivery / Shipping Fee',
                'amount'   => $this->centavos($order->shipping_fee),
                'currency' => 'PHP',
                'quantity' => 1,
            ];
        }

        $payable = $this->centavos($order->grand_total);

        if ($items !== [] && $this->sumLineItems($items) === $payable) {
            return $items;
        }

        // A discount applies, or per-unit rounding drifted off the stored
        // total. Either way, charge one line that matches grand_total exactly.
        return [[
            'name'     => 'Order #' . $order->order_number . ($order->discount_total > 0 ? ' (discount applied)' : ''),
            'amount'   => $payable,
            'currency' => 'PHP',
            'quantity' => 1,
        ]];
    }

    /** @param  list<array{amount: int, quantity: int}>  $items */
    private function sumLineItems(array $items): int
    {
        return array_sum(array_map(fn (array $item): int => $item['amount'] * $item['quantity'], $items));
    }

    private function centavos(mixed $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function request(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->timeout(30);
    }

    /** Strip customer contact details before a payload reaches the log file. */
    private function redactPayload(array $payload): array
    {
        foreach (['email', 'phone'] as $field) {
            if (isset($payload['data']['attributes']['billing'][$field])) {
                $payload['data']['attributes']['billing'][$field] = '[redacted]';
            }
        }

        return $payload;
    }
}
