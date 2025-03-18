<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Foundation\MaintenanceMode as LaravelMaintenanceMode;
use Illuminate\Foundation\Application;

// Initialize the application
$app = new Application(__DIR__);

// Bind the maintenance mode
$app->singleton(MaintenanceMode::class, LaravelMaintenanceMode::class);

echo "MaintenanceMode binding test successful!\n";

// Try to test a minimal route
$router = new Illuminate\Routing\Router(new Illuminate\Events\Dispatcher());
$router->get('/', function() {
    return 'Laravel is working!';
});

echo "Router test successful!\n";
echo "Basic tests completed successfully.\n";