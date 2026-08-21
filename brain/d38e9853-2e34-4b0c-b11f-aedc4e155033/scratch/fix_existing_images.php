<?php
require 'e:/Files/codes/Web Store/vendor/autoload.php';
$app = require_once 'e:/Files/codes/Web Store/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductVariant;

$sampleImages = [
    '/uploads/products/cover_sample_1.jpg',
    '/images/logo.png',
];

$products = Product::all();
foreach ($products as $p) {
    if (empty($p->image_url)) {
        $p->image_url = '/images/logo.png';
        $p->save();
        echo "Updated Product ID {$p->id} image_url to /images/logo.png\n";
    }
}
