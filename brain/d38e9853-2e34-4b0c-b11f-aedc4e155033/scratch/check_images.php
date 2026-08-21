<?php
require 'e:/Files/codes/Web Store/vendor/autoload.php';
$app = require_once 'e:/Files/codes/Web Store/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::with('variants')->get();
foreach ($products as $p) {
    echo "ID: {$p->id} | Name: {$p->name}\n";
    echo "  image_url: " . var_export($p->image_url, true) . "\n";
    echo "  images: " . var_export($p->images, true) . "\n";
    foreach ($p->variants as $v) {
        echo "  Variant '{$v->variant_name}': image_url=" . var_export($v->image_url, true) . "\n";
    }
    echo "----------------------------------------\n";
}
