<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Comments;

use AndyDefer\LaravelFeatures\Comments\Services\CommentService;
use AndyDefer\LaravelFeatures\Modules\Comments\Repositories\CommentRepository;
use Illuminate\Support\ServiceProvider;

final class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CommentRepository::class);
        $this->app->singleton(CommentService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/migrations');
        }

        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations'),
        ], 'Comments-migrations');
    }
}
