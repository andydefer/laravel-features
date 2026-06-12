<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures;

use AndyDefer\LaravelFeatures\Modules\Addresses\AddressesServiceProvider;
use AndyDefer\LaravelFeatures\Modules\Comments\CommentsServiceProvider;
use AndyDefer\LaravelFeatures\Modules\Likes\LikesServiceProvider;
use AndyDefer\LaravelFeatures\Modules\Notifications\NotificationsServiceProvider;
use AndyDefer\LaravelFeatures\Modules\Ratings\RatingsServiceProvider;
use Illuminate\Support\ServiceProvider;

final class LaravelFeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(AddressesServiceProvider::class);
        $this->app->register(CommentsServiceProvider::class);
        $this->app->register(LikesServiceProvider::class);
        $this->app->register(NotificationsServiceProvider::class);
        $this->app->register(RatingsServiceProvider::class);
    }

    public function boot(): void
    {
        // Les migrations sont chargées par chaque module individuellement
    }
}
