<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::with('variants')->get();
echo "TOTAL PRODUCTS: " . $products->count() . "\n";
foreach ($products as $p) {
    echo "ID: {$p->id} | Name: {$p->name}\n";
    echo "  Option Config: " . json_encode($p->option_config) . "\n";
    echo "  Parsed Option Groups: " . json_encode($p->parsed_option_groups) . "\n";
    foreach ($p->variants as $v) {
        echo "    Variant ID: {$v->id} | Label: {$v->label} | Price: {$v->price} | Stock: {$v->stock_qty}\n";
    }
    echo "--------------------------------------------------\n";
}
