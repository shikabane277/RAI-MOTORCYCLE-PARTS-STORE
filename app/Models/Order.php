<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'guest_name', 'guest_email', 'guest_phone',
        'ship_recipient', 'ship_phone', 'ship_line1', 'ship_barangay', 'ship_city',
        'ship_province', 'ship_region', 'ship_zip',
        'subtotal', 'shipping_fee', 'discount_total', 'grand_total', 'coupon_code',
        'payment_method', 'gcash_number', 'payment_status', 'status',
        'courier', 'tracking_number', 'notes', 'admin_notes', 'placed_at',
    ];

    protected $casts = [
        'placed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public static function generateOrderNumber(): string
    {
        return 'MB-' . date('Y') . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->latest('id');
    }

    public function logStatusUpdate(string $title, ?string $description = null, ?string $status = null): OrderStatusLog
    {
        if ($status && $status !== $this->status) {
            $this->update(['status' => $status]);
        }

        return $this->statusLogs()->create([
            'status'      => $status ?? $this->status,
            'title'       => $title,
            'description' => $description,
        ]);
    }

    /**
     * Get 1-indexed Shopee progress stage (1: Order Placed, 2: To Ship, 3: To Receive, 4: Received)
     */
    public function getShopeeStepAttribute(): int
    {
        return match($this->status) {
            'pending_payment', 'confirmed', 'order_placed' => 1,
            'processing', 'preparing', 'to_ship'           => 2,
            'shipped', 'out_for_delivery', 'to_receive'     => 3,
            'delivered', 'completed', 'received'           => 4,
            default                                        => 1,
        };
    }

    public function getShopeeStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'Order Placed (Awaiting Payment)',
            'confirmed'       => 'Order Placed (Confirmed)',
            'processing'      => 'To Ship (Preparing Items)',
            'shipped'         => 'To Receive (In Transit / Picked Up)',
            'delivered'       => 'Received (Delivered)',
            'completed'       => 'Completed',
            'cancelled'       => 'Cancelled',
            'refunded'        => 'Refunded',
            default           => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Record a confirmed online payment against this order.
     *
     * Idempotent by design: the customer returning from the gateway and the
     * gateway's own webhook race each other, and either one may arrive twice.
     * Returns true only for the call that actually settled the order.
     */
    public function settleOnlinePayment(?string $gatewayPaymentId = null, array $gatewayResponse = []): bool
    {
        return DB::transaction(function () use ($gatewayPaymentId, $gatewayResponse) {
            // Re-read under a row lock so two concurrent confirmations cannot
            // both decide they are the first.
            $fresh = static::whereKey($this->getKey())->lockForUpdate()->first();

            if (! $fresh || $fresh->payment_status === 'paid') {
                return false;
            }

            $attributes = ['payment_status' => 'paid'];

            // Only an order still waiting on payment advances to confirmed. If a
            // late event reports payment on an order that has since been
            // cancelled or refunded, record the money but leave the status alone
            // rather than pulling it back into fulfilment behind staff's backs.
            if ($fresh->status === 'pending_payment') {
                $attributes['status'] = 'confirmed';
            }

            $fresh->update($attributes);

            $fresh->statusLogs()->create([
                'status'      => 'confirmed',
                'title'       => 'Payment Received & Verified',
                'description' => 'Payment has been successfully verified. Order is now confirmed and sent to warehouse to be prepared.',
            ]);

            $payment = $fresh->payments()->latest('id')->first();

            if ($payment) {
                // gateway_ref keeps the cs_... session id — both the return trip
                // and the webhook look the order up by it, so the PayMongo
                // payment id goes into gateway_response instead.
                $payment->update([
                    'status'           => 'paid',
                    'paid_at'          => now(),
                    'gateway_response' => array_filter([
                        'paymongo_payment_id' => $gatewayPaymentId,
                        'session'             => $gatewayResponse ?: null,
                    ]) ?: $payment->gateway_response,
                ]);
            }

            $this->refresh();

            return true;
        });
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Guest';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending_payment'  => 'warning',
            'confirmed'        => 'info',
            'processing'       => 'primary',
            'shipped'          => 'info',
            'delivered'        => 'success',
            'completed'        => 'success',
            'cancelled'        => 'danger',
            'return_requested' => 'warning',
            'refunded'         => 'secondary',
            default            => 'secondary',
        };
    }
}
