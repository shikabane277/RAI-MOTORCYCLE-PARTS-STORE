<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key', env('PAYMONGO_SECRET_KEY', 'sk_test_sample_key'));
        $this->publicKey = config('services.paymongo.public_key', env('PAYMONGO_PUBLIC_KEY', 'pk_test_sample_key'));
    }

    /**
     * Create a PayMongo Checkout Session for an Order
     */
    public function createCheckoutSession(Order $order, string $paymentType = 'qrph'): array
    {
        // Convert items into PayMongo line_items format (amounts in centavos e.g. 100 PHP = 10000)
        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'name'        => $item->product_name . ' (' . ($item->variant_name ?? 'Standard') . ')',
                'amount'      => (int) round($item->unit_price * 100),
                'currency'    => 'PHP',
                'quantity'    => $item->qty,
            ];
        }

        // Add Shipping fee line item if applicable
        if ($order->shipping_fee > 0) {
            $lineItems[] = [
                'name'     => 'Lalamove Delivery / Shipping Fee',
                'amount'   => (int) round($order->shipping_fee * 100),
                'currency' => 'PHP',
                'quantity' => 1,
            ];
        }

        // Add Discount line item if applicable
        if ($order->discount_total > 0) {
            $lineItems[] = [
                'name'     => 'Coupon Discount (' . ($order->coupon_code ?? 'PROMO') . ')',
                'amount'   => -((int) round($order->discount_total * 100)),
                'currency' => 'PHP',
                'quantity' => 1,
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
                    'send_email_receipt' => true,
                    'show_description'   => true,
                    'show_line_items'    => true,
                    'payment_method_types' => match($paymentType) {
                        'card' => ['card'],
                        'qrph' => ['qrph', 'gcash', 'paymaya'],
                        default => ['card', 'qrph', 'gcash', 'paymaya'],
                    },
                    'line_items'  => $lineItems,
                    'description' => 'Payment for Order #' . $order->order_number . ' at RAI MOTORCYCLE PARTS',
                    'success_url' => route('checkout.success', ['order' => $order->id]) . '?paymongo=success',
                    'cancel_url'  => route('checkout.index') . '?paymongo=cancelled',
                    'reference_number' => $order->order_number,
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($this->baseUrl . '/checkout_sessions', $payload);

            if ($response->successful()) {
                $data = $response->json()['data'];
                return [
                    'success'      => true,
                    'checkout_url' => $data['attributes']['checkout_url'],
                    'session_id'   => $data['id'],
                ];
            }

            Log::error('PayMongo API Error: ' . $response->body());
            return [
                'success' => false,
                'message' => $response->json()['errors'][0]['detail'] ?? 'PayMongo session creation failed.',
            ];

        } catch (\Exception $e) {
            Log::error('PayMongo Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
