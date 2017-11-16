<?php

namespace Selfreliance\WithdrawOrders;

use Illuminate\Support\ServiceProvider;

class WithdrawOrdersServiceProvider extends ServiceProvider
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
        $this->app->make('Selfreliance\WithdrawOrders\WithdrawOrdersController');
        $this->loadViewsFrom(__DIR__.'/views', 'withdraw_orders');
        
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