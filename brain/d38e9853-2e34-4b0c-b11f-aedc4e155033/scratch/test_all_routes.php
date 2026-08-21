<?php
require 'e:/Files/codes/Web Store/vendor/autoload.php';
$app = require_once 'e:/Files/codes/Web Store/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$routesToTest = [
    '/' => 'Home',
    '/shop' => 'Shop',
    '/cart' => 'Cart',
];

foreach ($routesToTest as $uri => $label) {
    echo "=== Testing $label ($uri) ===\n";
    try {
        $request = Request::create($uri, 'GET');
        $response = $app->handle($request);
        echo "Status: " . $response->getStatusCode() . "\n";
        if (property_exists($response, 'exception') && $response->exception) {
            echo "EXCEPTION: " . get_class($response->exception) . ": " . $response->exception->getMessage() . "\n";
            echo "IN FILE: " . $response->exception->getFile() . ":" . $response->exception->getLine() . "\n";
        }
    } catch (\Throwable $e) {
        echo "CRASH: " . get_class($e) . ": " . $e->getMessage() . "\n";
        echo "IN FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
