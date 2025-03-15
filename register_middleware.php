<?php
// Create a file called register_middleware.php in your project root

// Include the autoloader
require_once __DIR__.'/vendor/autoload.php';

// Create the application
$app = require_once __DIR__.'/bootstrap/app.php';

// Get the kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Manually register middleware
$router = $app->make('router');
$router->aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);

echo "Middleware registered successfully!\n";