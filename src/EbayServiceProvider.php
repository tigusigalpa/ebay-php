<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay;

use Illuminate\Support\ServiceProvider;

class EbayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ebay.php',
            'ebay'
        );

        $this->app->singleton('ebay', function ($app) {
            return new Ebay();
        });

        $this->app->alias('ebay', Ebay::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ebay.php' => config_path('ebay.php'),
            ], 'ebay-config');
        }
    }

    public function provides(): array
    {
        return ['ebay', Ebay::class];
    }
}
