<?php
require 'e:/Files/codes/Web Store/vendor/autoload.php';
$app = require_once 'e:/Files/codes/Web Store/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;

$banners = Banner::all();
echo "Total Banners in DB: " . $banners->count() . "\n";
foreach ($banners as $b) {
    echo "ID: {$b->id} | Title: {$b->title} | Active: " . ($b->is_active ? 'YES' : 'NO') . " | Image: {$b->image_url}\n";
}
