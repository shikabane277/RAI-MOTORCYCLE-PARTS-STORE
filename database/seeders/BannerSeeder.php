<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'       => 'Precision CNC Parts for Filipino Riders',
                'subtitle'    => 'Titanium & anodized aluminum hardware — built for the streets and the track',
                'image_url'   => '/images/banners/banner1.jpg',
                'link_url'    => '/shop/bolts-fasteners',
                'button_text' => 'Shop Now',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'title'       => 'New Arrival: Titanium Fairing Kits',
                'subtitle'    => 'Grade 5 titanium — lighter than steel, stronger than you think',
                'image_url'   => '/images/banners/banner2.jpg',
                'link_url'    => '/shop/fairing-bolt-kits',
                'button_text' => 'View Collection',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'title'       => 'Free Shipping on Orders ₱1,500+',
                'subtitle'    => 'Metro Manila same-day dispatch on orders before 12NN. Nationwide J&T & Ninja Van.',
                'image_url'   => '/images/banners/banner3.jpg',
                'link_url'    => '/shop',
                'button_text' => 'Shop the Sale',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
        ];

        foreach ($banners as $b) {
            Banner::create($b);
        }
    }
}
