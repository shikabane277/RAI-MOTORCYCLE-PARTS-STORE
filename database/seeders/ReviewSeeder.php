<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products  = Product::where('status', 'active')->get();

        $comments = [
            'Grabe ang ganda! Exactly what I expected — premium quality and the color matches my Sniper 155 perfectly. Worth every peso!',
            'Sobrang solid. Fit perfectly sa aking R15. Hindi ko na need pa ng ibang hardware. Highly recommended!',
            'Fast delivery, J&T next day. The anodized blue color is mas maganda sa personal than on photos. 5/5!',
            'Quality is top notch. I installed these on my Aerox and the engine bay looks super clean now.',
            'Medyo mataas ang price pero worth it. Titanium material feels premium and lightweight compared to stock.',
            'Okay naman pero yung packing pwede pa i-improve. Lahat naman naka-receive in good condition.',
            'Exactly as described. My Sniper 155 looks race-ready now. Will order more colors!',
            'Sulit na sulit! Nakuha ko pa discount sa WELCOME10 code. Maganda ang color, hindi mabilis mag-fade.',
            'I\'ve tried other brands but MachBolt is the best quality for the price in PH. Lagi na ko mag o-order dito.',
            'Fit and finish is excellent. You can tell these were precision machined. The anodized finish is consistent.',
            'Nag-order ako ng black at blue — both look awesome. Pinaka-magandang upgrade sa aking Click 160.',
            'Delivered in 2 days! Ang bilis. Quality is consistent with my previous order. Will definitely reorder.',
            'Great product but one of the bolts had a slight scratch — still usable but expected perfection at this price point.',
            'Ganda nang ganda! My track day photos look 100x better now. Fellow riders kept asking where I got these.',
            'Installation was easy, no issues. Fits my PCX 160 perfectly. The O-ring on the oil cap is a nice touch.',
        ];

        $bikeModels = ['Yamaha Sniper 155', 'Yamaha R15 V3', 'Honda Click 160', 'Honda CBR150R', 'Suzuki Raider R150', 'Yamaha Mio Aerox 155', 'Honda PCX 160'];

        foreach ($products as $i => $product) {
            $numReviews = rand(1, 3);
            for ($j = 0; $j < $numReviews; $j++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id'    => $customers->random()->id,
                    'rating'     => rand(4, 5),
                    'title'      => 'Great product!',
                    'comment'    => $comments[($i * 3 + $j) % count($comments)],
                    'bike_model' => $bikeModels[array_rand($bikeModels)],
                    'status'     => 'approved',
                ]);
            }
        }
    }
}
