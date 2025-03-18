<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\MaintenanceModeManager;

class MaintenanceModeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('maintenance.mode', function ($app) {
            return new MaintenanceModeManager($app);
        });
        
        // Bind any classes that might be looking for the old class
        $this->app->singleton('Illuminate\Foundation\MaintenanceMode', function ($app) {
            return $app->make('maintenance.mode')->driver();
        });
    }
}