<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'Titanium Gr5',   'sort_order' => 1],
            ['name' => 'Stainless A4',   'sort_order' => 2],
            ['name' => '7075 Aluminum',  'sort_order' => 3],
            ['name' => 'Chromoly',       'sort_order' => 4],
            ['name' => 'Carbon Fiber',   'sort_order' => 5],
            ['name' => 'Billet Aluminum','sort_order' => 6],
        ];

        foreach ($materials as $mat) {
            ProductAttribute::firstOrCreate(
                ['type' => 'material', 'name' => $mat['name']],
                ['sort_order' => $mat['sort_order'], 'is_active' => true]
            );
        }

        $colors = [
            ['name' => 'Black',   'value' => '#111111',                     'sort_order' => 1],
            ['name' => 'Red',     'value' => '#e63946',                     'sort_order' => 2],
            ['name' => 'Blue',    'value' => '#1d3557',                     'sort_order' => 3],
            ['name' => 'Gold',    'value' => '#f5a623',                     'sort_order' => 4],
            ['name' => 'Silver',  'value' => '#c0c0c0',                     'sort_order' => 5],
            ['name' => 'Rainbow', 'value' => 'linear-gradient(135deg,#ff0000,#ff7f00,#ffff00,#00ff00,#0000ff,#8b00ff)', 'sort_order' => 6],
            ['name' => 'Titanium Blue', 'value' => '#0077b6',               'sort_order' => 7],
            ['name' => 'Purple',  'value' => '#7b2cbf',                     'sort_order' => 8],
        ];

        foreach ($colors as $color) {
            ProductAttribute::firstOrCreate(
                ['type' => 'color', 'name' => $color['name']],
                ['value' => $color['value'], 'sort_order' => $color['sort_order'], 'is_active' => true]
            );
        }

        $threadSizes = [
            ['name' => 'M4',  'sort_order' => 1],
            ['name' => 'M5',  'sort_order' => 2],
            ['name' => 'M6',  'sort_order' => 3],
            ['name' => 'M8',  'sort_order' => 4],
            ['name' => 'M10', 'sort_order' => 5],
            ['name' => 'M12', 'sort_order' => 6],
            ['name' => 'M14', 'sort_order' => 7],
        ];

        foreach ($threadSizes as $ts) {
            ProductAttribute::firstOrCreate(
                ['type' => 'thread_size', 'name' => $ts['name']],
                ['sort_order' => $ts['sort_order'], 'is_active' => true]
            );
        }
    }
}
