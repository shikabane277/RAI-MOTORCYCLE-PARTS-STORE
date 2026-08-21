<?php
require 'e:/Files/codes/Web Store/vendor/autoload.php';
$app = require_once 'e:/Files/codes/Web Store/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Coupon;

try {
    $code = 'TESTPROMO' . rand(100, 999);
    $c = Coupon::create([
        'code'      => $code,
        'type'      => 'percentage',
        'value'     => 10,
        'is_active' => true,
    ]);
    echo "SUCCESS: Created test coupon {$c->code} (ID: {$c->id})!\n";
    
    // Test free shipping coupon creation
    $fsCode = 'FREESHIP' . rand(100, 999);
    $c2 = Coupon::create([
        'code'      => $fsCode,
        'type'      => 'free_shipping',
        'value'     => 0,
        'is_active' => true,
    ]);
    echo "SUCCESS: Created free shipping coupon {$c2->code} (ID: {$c2->id})!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
