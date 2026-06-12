<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Addresses;

use AndyDefer\LaravelFeatures\Modules\Addresses\Repositories\AddressRepository;
use AndyDefer\LaravelFeatures\Modules\Addresses\Services\AddressService;
use Illuminate\Support\ServiceProvider;

final class AddressesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AddressRepository::class);
        $this->app->singleton(AddressService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/migrations');
        }

        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations'),
        ], 'Addresses-migrations');
    }
}
