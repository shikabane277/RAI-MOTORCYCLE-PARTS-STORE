<?php
require 'e:/Files/codes/Web Store/vendor/autoload.php';
$app = require_once 'e:/Files/codes/Web Store/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

try {
    $user = User::first();
    auth()->login($user);

    // Create a dummy cart item for test
    $cart = Cart::firstOrCreate(['user_id' => $user->id]);
    $variant = ProductVariant::first();
    $cart->items()->delete();
    $cart->items()->create(['product_variant_id' => $variant->id, 'qty' => 1]);

    $controller = new App\Http\Controllers\CheckoutController();
    $reqData = [
        'recipient_name'  => 'Test Customer',
        'phone'           => '09171234567',
        'line1'           => '123 Test Street',
        'barangay'        => 'Barangay Central',
        'city'            => 'Quezon City',
        'province'        => 'NCR (Metro Manila)',
        'region'          => 'NCR (Metro Manila)',
        'zip_code'        => '1100',
        'shipping_method' => 'standard',
        'payment_method'  => 'cod',
    ];
    $request = Request::create('/checkout', 'POST', $reqData);
    $request->setLaravelSession($app['session']->driver());

    $response = $controller->store($request);
    echo "SUCCESS: Checkout returned status " . $response->getStatusCode() . "!\n";
    if (method_exists($response, 'getTargetUrl')) {
        echo "REDIRECT TARGET: " . $response->getTargetUrl() . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
