<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $topLevel = [
            ['name' => 'Bolts & Fasteners',   'slug' => 'bolts-fasteners',   'icon' => '🔩', 'sort_order' => 1],
            ['name' => 'Nuts & Washers',       'slug' => 'nuts-washers',       'icon' => '⚙️', 'sort_order' => 2],
            ['name' => 'Spacers & Standoffs',  'slug' => 'spacers-standoffs',  'icon' => '🔧', 'sort_order' => 3],
            ['name' => 'Levers & Grips',       'slug' => 'levers-grips',       'icon' => '🏍️', 'sort_order' => 4],
            ['name' => 'Foot Pegs & Rearsets', 'slug' => 'foot-pegs-rearsets', 'icon' => '🦶', 'sort_order' => 5],
            ['name' => 'Frame Sliders',        'slug' => 'frame-sliders',      'icon' => '🛡️', 'sort_order' => 6],
            ['name' => 'Swingarm Spools',      'slug' => 'swingarm-spools',    'icon' => '🎯', 'sort_order' => 7],
            ['name' => 'Fluid Caps',           'slug' => 'fluid-caps',         'icon' => '💧', 'sort_order' => 8],
        ];

        foreach ($topLevel as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }

        // Subcategories under Bolts & Fasteners (id=1)
        $bolts = Category::where('slug', 'bolts-fasteners')->first();
        $subCategories = [
            ['name' => 'Fairing Bolt Kits',         'slug' => 'fairing-bolt-kits',         'sort_order' => 1],
            ['name' => 'Engine Bolts',               'slug' => 'engine-bolts',               'sort_order' => 2],
            ['name' => 'Sprocket Bolts',             'slug' => 'sprocket-bolts',             'sort_order' => 3],
            ['name' => 'Disc & Rotor Bolts',         'slug' => 'disc-rotor-bolts',           'sort_order' => 4],
            ['name' => 'Handlebar & Lever Bolts',    'slug' => 'handlebar-lever-bolts',      'sort_order' => 5],
            ['name' => 'Windshield Bolts',           'slug' => 'windshield-bolts',           'sort_order' => 6],
            ['name' => 'License Plate Bolts',        'slug' => 'license-plate-bolts',        'sort_order' => 7],
        ];

        foreach ($subCategories as $sub) {
            Category::create(array_merge($sub, ['parent_id' => $bolts->id, 'is_active' => true]));
        }
    }
}
