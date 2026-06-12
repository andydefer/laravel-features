<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Ratings;

use AndyDefer\LaravelFeatures\Modules\Ratings\Repositories\RatingRepository;
use AndyDefer\LaravelFeatures\Modules\Ratings\Services\RatingService;
use Illuminate\Support\ServiceProvider;

final class RatingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RatingRepository::class);
        $this->app->singleton(RatingService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/migrations');
        }

        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations'),
        ], 'Ratings-migrations');
    }
}
