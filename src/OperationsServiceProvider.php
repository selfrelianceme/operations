<?php

namespace Selfreliance\Operations;

use Illuminate\Support\ServiceProvider;

class OperationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        include __DIR__.'/routes.php';
        $this->app->make('Selfreliance\Operations\OperationsController');
        $this->loadViewsFrom(__DIR__.'/views', 'operations');
        $this->publishes([
            __DIR__ . '/config/operations.php' => config_path('operations.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}