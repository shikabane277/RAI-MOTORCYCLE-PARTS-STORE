<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            ['code' => 'WELCOME10',  'type' => 'percentage', 'value' => 10,  'min_spend' => 0,    'usage_limit' => 100],
            ['code' => 'SAVE100',    'type' => 'fixed',       'value' => 100, 'min_spend' => 500,  'usage_limit' => 50],
            ['code' => 'TITANIUM15', 'type' => 'percentage', 'value' => 15,  'min_spend' => 1000, 'usage_limit' => 30],
            ['code' => 'FREERIDE',   'type' => 'fixed',       'value' => 150, 'min_spend' => 1500, 'usage_limit' => null],
            ['code' => 'MACHBOLT20', 'type' => 'percentage', 'value' => 20,  'min_spend' => 2000, 'usage_limit' => 20],
        ];

        foreach ($coupons as $c) {
            Coupon::create(array_merge($c, ['is_active' => true]));
        }
    }
}
