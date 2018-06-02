<?php

namespace Shakurov\Coinbase;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CoinbaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/coinbase.php' => config_path('coinbase.php'),
        ], 'config');

        $timestamp = date('Y_m_d_His', time());

        $this->publishes([
            __DIR__.'/../database/migrations/create_coinbase_webhook_calls_table.php.stub' => database_path("migrations/{$timestamp}_create_coinbase_webhook_calls_table.php"),
        ], 'migrations');

        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/coinbase.php', 'coinbase'
        );

        $this->app->bind('coinbase', function ($app) {
            return new Coinbase($app);
        });

        $this->app->alias('coinbase', Coinbase::class);
    }
}
