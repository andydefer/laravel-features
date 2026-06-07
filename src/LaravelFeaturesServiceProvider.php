<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures;

use AndyDefer\LaravelFeatures\Addresses\Repositories\AddressRepository;
use AndyDefer\LaravelFeatures\Addresses\Services\AddressService;
use Illuminate\Support\ServiceProvider;

final class LaravelFeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Addresses
        $this->app->singleton(AddressRepository::class);
        $this->app->singleton(AddressService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Addresses/migrations');
    }
}
