<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures;

use AndyDefer\LaravelFeatures\Addresses\Repositories\AddressRepository;
use AndyDefer\LaravelFeatures\Addresses\Services\AddressService;
use AndyDefer\LaravelFeatures\Comments\Repositories\CommentRepository;
use AndyDefer\LaravelFeatures\Comments\Services\CommentService;
use AndyDefer\LaravelFeatures\Likes\Repositories\LikeRepository;
use AndyDefer\LaravelFeatures\Likes\Services\LikeService;
use AndyDefer\LaravelFeatures\Ratings\Repositories\RatingRepository;
use AndyDefer\LaravelFeatures\Ratings\Services\RatingService;
use Illuminate\Support\ServiceProvider;

final class LaravelFeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Addresses
        $this->app->singleton(AddressRepository::class);
        $this->app->singleton(AddressService::class);

        // Likes
        $this->app->singleton(LikeRepository::class);
        $this->app->singleton(LikeService::class);

        // Ratings
        $this->app->singleton(RatingRepository::class);
        $this->app->singleton(RatingService::class);

        // Comments
        $this->app->singleton(CommentRepository::class);
        $this->app->singleton(CommentService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Addresses/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Likes/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Ratings/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Comments/migrations');
    }
}
