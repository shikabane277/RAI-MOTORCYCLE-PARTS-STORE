<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $statuses  = ['confirmed', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];

        $variants = \App\Models\ProductVariant::with('product')->take(15)->get();

        for ($i = 1; $i <= 10; $i++) {
            $customer = $customers->random();
            $status   = $statuses[array_rand($statuses)];

            $orderVariants = $variants->random(rand(1, 3));
            $subtotal = 0;
            $items = [];

            foreach ($orderVariants as $variant) {
                $qty       = rand(1, 3);
                $unitPrice = $variant->effective_price;
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $items[] = [
                    'product_variant_id' => $variant->id,
                    'product_name'       => $variant->product->name,
                    'variant_sku'        => $variant->variant_sku,
                    'variant_label'      => $variant->label,
                    'qty'                => $qty,
                    'unit_price'         => $unitPrice,
                    'line_total'         => $lineTotal,
                ];
            }

            $shippingFee  = $subtotal >= 1500 ? 0 : 89;
            $grandTotal   = $subtotal + $shippingFee;

            $order = Order::create([
                'order_number'    => 'MB-' . date('Y') . str_pad($i, 5, '0', STR_PAD_LEFT),
                'user_id'         => $customer->id,
                'ship_recipient'  => $customer->name,
                'ship_phone'      => $customer->phone,
                'ship_line1'      => '123 Rizal St',
                'ship_barangay'   => 'Brgy. San Antonio',
                'ship_city'       => 'Manila',
                'ship_province'   => 'Metro Manila',
                'ship_region'     => 'NCR',
                'ship_zip'        => '1000',
                'subtotal'        => $subtotal,
                'shipping_fee'    => $shippingFee,
                'discount_total'  => 0,
                'grand_total'     => $grandTotal,
                'payment_method'  => ['cod', 'gcash', 'maya'][array_rand(['cod', 'gcash', 'maya'])],
                'payment_status'  => in_array($status, ['completed', 'delivered', 'shipped']) ? 'paid' : 'pending',
                'status'          => $status,
                'courier'         => ['J&T Express', 'Ninja Van'][array_rand(['J&T Express', 'Ninja Van'])],
                'tracking_number' => in_array($status, ['shipped', 'delivered', 'completed']) ? 'JT' . rand(100000000, 999999999) : null,
                'placed_at'       => now()->subDays(rand(1, 60)),
            ]);

            foreach ($items as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }
        }
    }
}
