<?php

namespace Shakurov\Coinbase;

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
        ]);
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