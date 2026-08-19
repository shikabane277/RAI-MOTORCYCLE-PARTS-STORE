<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            MotorcycleModelSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            BannerSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
