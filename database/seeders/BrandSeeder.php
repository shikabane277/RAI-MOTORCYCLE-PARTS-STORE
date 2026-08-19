<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'ProBolt',     'slug' => 'probolt',     'description' => 'Premium titanium and stainless fasteners for performance motorcycles.'],
            ['name' => 'CNC Racing',  'slug' => 'cnc-racing',  'description' => 'Italian-designed CNC machined motorcycle accessories and hardware.'],
            ['name' => 'BikeMaster',  'slug' => 'bikemaster',  'description' => 'Trusted OEM-quality replacement hardware for all motorcycle types.'],
            ['name' => 'LightSpeed',  'slug' => 'lightspeed',  'description' => 'Ultralight titanium fasteners for weight-conscious riders.'],
            ['name' => 'RAI',         'slug' => 'rai',         'description' => 'Our in-house brand — precision motorcycle parts made for Filipino riders.'],
        ];

        foreach ($brands as $brand) {
            Brand::create(array_merge($brand, ['is_active' => true]));
        }
    }
}
