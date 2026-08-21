<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\MotorcycleModel;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $rai  = Brand::where('slug', 'rai')->first();
        $probolt   = Brand::where('slug', 'probolt')->first();
        $cncRacing = Brand::where('slug', 'cnc-racing')->first();

        $boltsCat    = Category::where('slug', 'bolts-fasteners')->first();
        $fairingCat  = Category::where('slug', 'fairing-bolt-kits')->first();
        $engineCat   = Category::where('slug', 'engine-bolts')->first();
        $leversCat   = Category::where('slug', 'levers-grips')->first();
        $footpegCat  = Category::where('slug', 'foot-pegs-rearsets')->first();
        $sliderCat   = Category::where('slug', 'frame-sliders')->first();
        $spoolCat    = Category::where('slug', 'swingarm-spools')->first();
        $capCat      = Category::where('slug', 'fluid-caps')->first();
        $discCat     = Category::where('slug', 'disc-rotor-bolts')->first();
        $handlebarCat = Category::where('slug', 'handlebar-lever-bolts')->first();

        // Motorcycle models for fitment
        $sniper155   = MotorcycleModel::where('slug', 'yamaha-sniper-155')->first();
        $aerox155    = MotorcycleModel::where('slug', 'yamaha-mio-aerox-155')->first();
        $click160    = MotorcycleModel::where('slug', 'honda-click-160')->first();
        $nmax155     = MotorcycleModel::where('slug', 'yamaha-nmax-155')->first();
        $r15v3       = MotorcycleModel::where('slug', 'yamaha-r15-v3')->first();
        $cbr150r     = MotorcycleModel::where('slug', 'honda-cbr150r')->first();
        $raiderR150  = MotorcycleModel::where('slug', 'suzuki-raider-r150')->first();
        $gsxR150     = MotorcycleModel::where('slug', 'suzuki-gsx-r150')->first();
        $pcx160      = MotorcycleModel::where('slug', 'honda-pcx-160')->first();
        $mt15        = MotorcycleModel::where('slug', 'yamaha-mt-15')->first();

        $colors  = ['Black', 'Red', 'Blue', 'Gold', 'Silver', 'Rainbow'];
        $sizes   = ['M5', 'M6', 'M8'];
        $materials = ['Titanium Gr5', 'Stainless A4', '7075 Aluminum'];

        $products = [
            // ─── Fairing Bolt Kits ────────────────────────────────────────────────
            [
                'name'              => 'CNC Anodized Fairing Bolt Kit',
                'slug'              => 'cnc-anodized-fairing-bolt-kit',
                'brand_id'          => $rai->id,
                'category_id'       => $fairingCat->id,
                'description'       => 'Upgrade your fairing hardware with our precision CNC-machined anodized bolt kits. Each kit is hand-counted and individually packaged. Available in 6 colors to match your build.',
                'short_description' => 'Full fairing bolt set, 8 pcs, CNC anodized. Available in 6 colors.',
                'is_featured'       => true,
                'is_new_arrival'    => false,
                'status'            => 'active',
                'fitments'          => [$sniper155->id, $aerox155->id, $click160->id, $nmax155->id],
                'variants'          => [
                    ['color' => 'Black',   'material' => 'Titanium Gr5',  'thread_size' => 'M6', 'pack_qty' => 8, 'price' => 850,  'sale_price' => null,  'stock_qty' => 45],
                    ['color' => 'Red',     'material' => 'Titanium Gr5',  'thread_size' => 'M6', 'pack_qty' => 8, 'price' => 850,  'sale_price' => null,  'stock_qty' => 30],
                    ['color' => 'Blue',    'material' => 'Titanium Gr5',  'thread_size' => 'M6', 'pack_qty' => 8, 'price' => 850,  'sale_price' => null,  'stock_qty' => 28],
                    ['color' => 'Gold',    'material' => 'Titanium Gr5',  'thread_size' => 'M6', 'pack_qty' => 8, 'price' => 950,  'sale_price' => null,  'stock_qty' => 15],
                    ['color' => 'Rainbow', 'material' => 'Titanium Gr5',  'thread_size' => 'M6', 'pack_qty' => 8, 'price' => 1100, 'sale_price' => null,  'stock_qty' => 10],
                    ['color' => 'Black',   'material' => 'Stainless A4',  'thread_size' => 'M6', 'pack_qty' => 8, 'price' => 450,  'sale_price' => 380,   'stock_qty' => 60],
                ],
            ],
            [
                'name'              => 'Titanium Fairing Bolt Kit — Full Set 20pc',
                'slug'              => 'titanium-fairing-bolt-kit-20pc',
                'brand_id'          => $probolt->id,
                'category_id'       => $fairingCat->id,
                'description'       => 'Complete full-fairing titanium bolt set for sport bikes. Grade 5 titanium — corrosion-proof, lighter than steel, with a premium raw titanium finish.',
                'short_description' => 'Full titanium fairing set, 20 pcs. Grade 5 titanium.',
                'is_featured'       => false,
                'is_new_arrival'    => true,
                'status'            => 'active',
                'fitments'          => [$r15v3->id, $cbr150r->id, $gsxR150->id, $mt15->id],
                'variants'          => [
                    ['color' => 'Silver',  'material' => 'Titanium Gr5', 'thread_size' => 'M6', 'pack_qty' => 20, 'price' => 2200, 'sale_price' => null, 'stock_qty' => 12],
                    ['color' => 'Gold',    'material' => 'Titanium Gr5', 'thread_size' => 'M6', 'pack_qty' => 20, 'price' => 2400, 'sale_price' => null, 'stock_qty' => 8],
                    ['color' => 'Rainbow', 'material' => 'Titanium Gr5', 'thread_size' => 'M6', 'pack_qty' => 20, 'price' => 2600, 'sale_price' => null, 'stock_qty' => 5],
                ],
            ],

            // ─── Engine Bolts ────────────────────────────────────────────────────
            [
                'name'              => 'Engine Cover Bolt Kit — M6 CNC',
                'slug'              => 'engine-cover-bolt-kit-m6-cnc',
                'brand_id'          => $rai->id,
                'category_id'       => $engineCat->id,
                'description'       => 'CNC-machined engine cover bolts with hex flange heads. Perfect for riders who want a clean, uniform engine bay. Stainless A4 for long-term corrosion resistance.',
                'short_description' => 'Engine cover bolt kit, M6 flange hex, 12 pcs.',
                'is_featured'       => true,
                'is_new_arrival'    => false,
                'status'            => 'active',
                'fitments'          => [$sniper155->id, $raiderR150->id, $click160->id],
                'variants'          => [
                    ['color' => 'Black',  'material' => 'Stainless A4', 'thread_size' => 'M6', 'pack_qty' => 12, 'price' => 520, 'sale_price' => null, 'stock_qty' => 35],
                    ['color' => 'Silver', 'material' => 'Stainless A4', 'thread_size' => 'M6', 'pack_qty' => 12, 'price' => 520, 'sale_price' => 450,  'stock_qty' => 40],
                    ['color' => 'Gold',   'material' => 'Titanium Gr5', 'thread_size' => 'M6', 'pack_qty' => 12, 'price' => 980, 'sale_price' => null, 'stock_qty' => 18],
                ],
            ],

            // ─── Disc/Rotor Bolts ────────────────────────────────────────────────
            [
                'name'              => 'CNC Disc Rotor Bolt Set',
                'slug'              => 'cnc-disc-rotor-bolt-set',
                'brand_id'          => $rai->id,
                'category_id'       => $discCat->id,
                'description'       => 'High-torque disc rotor bolts with locking compound. Precision CNC machined, available in anodized finishes. Sold in sets of 6.',
                'short_description' => 'Disc rotor bolt set, 6 pcs. CNC anodized.',
                'is_featured'       => false,
                'is_new_arrival'    => true,
                'status'            => 'active',
                'fitments'          => [$sniper155->id, $aerox155->id, $click160->id, $pcx160->id],
                'variants'          => [
                    ['color' => 'Black',  'material' => 'Stainless A4', 'thread_size' => 'M8', 'pack_qty' => 6, 'price' => 420, 'sale_price' => null, 'stock_qty' => 25],
                    ['color' => 'Blue',   'material' => 'Titanium Gr5', 'thread_size' => 'M8', 'pack_qty' => 6, 'price' => 680, 'sale_price' => null, 'stock_qty' => 15],
                    ['color' => 'Gold',   'material' => 'Titanium Gr5', 'thread_size' => 'M8', 'pack_qty' => 6, 'price' => 720, 'sale_price' => null, 'stock_qty' => 10],
                    ['color' => 'Red',    'material' => 'Stainless A4', 'thread_size' => 'M8', 'pack_qty' => 6, 'price' => 420, 'sale_price' => 360,  'stock_qty' => 20],
                ],
            ],

            // ─── Levers & Grips ─────────────────────────────────────────────────
            [
                'name'              => 'Adjustable CNC Brake & Clutch Lever Set',
                'slug'              => 'adjustable-cnc-brake-clutch-lever-set',
                'brand_id'          => $cncRacing->id,
                'category_id'       => $leversCat->id,
                'description'       => 'Fully adjustable 6-position CNC aluminum levers. Foldable design prevents snapping in drops. Fits most 22mm handlebars.',
                'short_description' => 'Adjustable 6-pos CNC lever set. Brake + Clutch.',
                'is_featured'       => true,
                'is_new_arrival'    => true,
                'status'            => 'active',
                'fitments'          => [$sniper155->id, $r15v3->id, $cbr150r->id, $raiderR150->id, $gsxR150->id],
                'variants'          => [
                    ['color' => 'Black',  'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1250, 'sale_price' => null, 'stock_qty' => 20],
                    ['color' => 'Red',    'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1250, 'sale_price' => null, 'stock_qty' => 15],
                    ['color' => 'Blue',   'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1250, 'sale_price' => null, 'stock_qty' => 12],
                    ['color' => 'Gold',   'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1350, 'sale_price' => null, 'stock_qty' => 8],
                    ['color' => 'Silver', 'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1250, 'sale_price' => 1050, 'stock_qty' => 25],
                ],
            ],

            // ─── Foot Pegs & Rearsets ────────────────────────────────────────────
            [
                'name'              => 'CNC Billet Foot Pegs — Universal',
                'slug'              => 'cnc-billet-foot-pegs-universal',
                'brand_id'          => $rai->id,
                'category_id'       => $footpegCat->id,
                'description'       => 'Lightweight CNC billet aluminum foot pegs with non-slip knurling. Wide platform for comfortable cornering. Universal 10mm pin mount.',
                'short_description' => 'CNC billet foot pegs, pair, 10mm pin.',
                'is_featured'       => false,
                'is_new_arrival'    => false,
                'status'            => 'active',
                'fitments'          => [$sniper155->id, $r15v3->id, $raiderR150->id],
                'variants'          => [
                    ['color' => 'Black',  'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 980, 'sale_price' => null, 'stock_qty' => 18],
                    ['color' => 'Red',    'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 980, 'sale_price' => null, 'stock_qty' => 10],
                    ['color' => 'Blue',   'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 980, 'sale_price' => null, 'stock_qty' => 8],
                ],
            ],

            // ─── Frame Sliders ──────────────────────────────────────────────────
            [
                'name'              => 'CNC Frame Slider Kit — Short Version',
                'slug'              => 'cnc-frame-slider-kit-short',
                'brand_id'          => $cncRacing->id,
                'category_id'       => $sliderCat->id,
                'description'       => 'Bolt-on crash protection for the track and street. CNC billet mount + Delrin slider puck absorbs impacts and protects your fairings from scratch damage.',
                'short_description' => 'Frame slider kit, short puck, pair.',
                'is_featured'       => true,
                'is_new_arrival'    => false,
                'status'            => 'active',
                'fitments'          => [$r15v3->id, $cbr150r->id, $gsxR150->id],
                'variants'          => [
                    ['color' => 'Black', 'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1800, 'sale_price' => null, 'stock_qty' => 14],
                    ['color' => 'Red',   'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1800, 'sale_price' => null, 'stock_qty' => 9],
                    ['color' => 'Blue',  'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 2, 'price' => 1800, 'sale_price' => 1550, 'stock_qty' => 12],
                ],
            ],

            // ─── Swingarm Spools ────────────────────────────────────────────────
            [
                'name'              => 'CNC Swingarm Spools — M8 x 1.25',
                'slug'              => 'cnc-swingarm-spools-m8',
                'brand_id'          => $rai->id,
                'category_id'       => $spoolCat->id,
                'description'       => 'CNC-machined swingarm spools for paddock stand use. M8 x 1.25 thread. Pair.',
                'short_description' => 'Swingarm spools, M8, pair, CNC anodized.',
                'is_featured'       => false,
                'is_new_arrival'    => false,
                'status'            => 'active',
                'fitments'          => [$r15v3->id, $raiderR150->id, $sniper155->id],
                'variants'          => [
                    ['color' => 'Black',  'material' => '7075 Aluminum', 'thread_size' => 'M8', 'pack_qty' => 2, 'price' => 350, 'sale_price' => null, 'stock_qty' => 40],
                    ['color' => 'Red',    'material' => '7075 Aluminum', 'thread_size' => 'M8', 'pack_qty' => 2, 'price' => 350, 'sale_price' => null, 'stock_qty' => 30],
                    ['color' => 'Blue',   'material' => '7075 Aluminum', 'thread_size' => 'M8', 'pack_qty' => 2, 'price' => 350, 'sale_price' => null, 'stock_qty' => 25],
                    ['color' => 'Gold',   'material' => '7075 Aluminum', 'thread_size' => 'M8', 'pack_qty' => 2, 'price' => 380, 'sale_price' => null, 'stock_qty' => 15],
                    ['color' => 'Silver', 'material' => '7075 Aluminum', 'thread_size' => 'M8', 'pack_qty' => 2, 'price' => 350, 'sale_price' => 290,  'stock_qty' => 20],
                ],
            ],

            // ─── Fluid Caps ─────────────────────────────────────────────────────
            [
                'name'              => 'CNC Oil Filler Cap — Universal Anodized',
                'slug'              => 'cnc-oil-filler-cap-universal',
                'brand_id'          => $rai->id,
                'category_id'       => $capCat->id,
                'description'       => 'CNC-machined anodized oil filler cap. Fits most 30mm and 35mm oil filler ports. O-ring sealed, no leaks.',
                'short_description' => 'CNC oil filler cap, anodized. Universal fit.',
                'is_featured'       => false,
                'is_new_arrival'    => true,
                'status'            => 'active',
                'fitments'          => [$sniper155->id, $raiderR150->id, $click160->id, $aerox155->id],
                'variants'          => [
                    ['color' => 'Black',  'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 1, 'price' => 220, 'sale_price' => null, 'stock_qty' => 50],
                    ['color' => 'Red',    'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 1, 'price' => 220, 'sale_price' => null, 'stock_qty' => 45],
                    ['color' => 'Blue',   'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 1, 'price' => 220, 'sale_price' => null, 'stock_qty' => 40],
                    ['color' => 'Gold',   'material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 1, 'price' => 240, 'sale_price' => null, 'stock_qty' => 20],
                    ['color' => 'Rainbow','material' => '7075 Aluminum', 'thread_size' => null, 'pack_qty' => 1, 'price' => 280, 'sale_price' => null, 'stock_qty' => 12],
                ],
            ],

            // ─── Handlebar & Lever Bolts ────────────────────────────────────────
            [
                'name'              => 'Handlebar Clamp Bolt Set — M5 CNC',
                'slug'              => 'handlebar-clamp-bolt-set-m5-cnc',
                'brand_id'          => $rai->id,
                'category_id'       => $handlebarCat->id,
                'description'       => 'CNC machined M5 handlebar clamp bolts. Button head for sleek look. 4-piece set covers standard clamp.',
                'short_description' => 'Handlebar clamp bolt set, M5 button head, 4 pcs.',
                'is_featured'       => false,
                'is_new_arrival'    => false,
                'status'            => 'active',
                'fitments'          => [$sniper155->id, $r15v3->id, $cbr150r->id, $raiderR150->id],
                'variants'          => [
                    ['color' => 'Black', 'material' => 'Titanium Gr5', 'thread_size' => 'M5', 'pack_qty' => 4, 'price' => 480, 'sale_price' => null, 'stock_qty' => 30],
                    ['color' => 'Gold',  'material' => 'Titanium Gr5', 'thread_size' => 'M5', 'pack_qty' => 4, 'price' => 520, 'sale_price' => null, 'stock_qty' => 20],
                    ['color' => 'Blue',  'material' => 'Stainless A4', 'thread_size' => 'M5', 'pack_qty' => 4, 'price' => 280, 'sale_price' => 240,  'stock_qty' => 35],
                ],
            ],
        ];

        foreach ($products as $pd) {
            $fitments = $pd['fitments'] ?? [];
            $variants = $pd['variants'] ?? [];
            unset($pd['fitments'], $pd['variants']);

            $pd['base_price'] = $pd['base_price'] ?? ($variants[0]['price'] ?? 0);
            $pd['image_url'] = '/images/logo.png';

            $product = Product::create($pd);

            // Attach fitments
            $product->motorcycleModels()->attach($fitments);

            // Create variants
            foreach ($variants as $i => $v) {
                $sku = strtoupper(
                    substr(preg_replace('/[^A-Za-z0-9]/', '', $product->slug), 0, 8) .
                    '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT)
                );
                $vSpec = array_filter([$v['thread_size'] ? $v['thread_size'] . ' Thread' : null, $v['pack_qty'] > 1 ? $v['pack_qty'] . ' Pcs Set' : null, $v['material']]);
                $vName = (!empty($vSpec) ? implode(' / ', $vSpec) : 'Standard') . ' - ' . $v['color'];

                ProductVariant::create([
                    'product_id'   => $product->id,
                    'variant_name' => $vName,
                    'variant_sku'  => $sku,
                    'thread_size'  => $v['thread_size'] ?? null,
                    'thread_pitch' => null,
                    'length_mm'    => null,
                    'head_type'    => 'flange',
                    'material'     => $v['material'],
                    'color'        => $v['color'],
                    'finish'       => 'anodized',
                    'pack_qty'     => $v['pack_qty'],
                    'price'        => $v['price'],
                    'sale_price'   => $v['sale_price'] ?? null,
                    'stock_qty'    => $v['stock_qty'],
                    'low_stock_threshold' => 10,
                    'is_active'    => true,
                ]);
            }
        }
    }
}
