<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications;

use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\LaravelFeatures\Modules\Notifications\Channels\DatabaseChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Channels\MailChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\DestinationValidatorInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Repositories\NotificationRepository;
use AndyDefer\LaravelFeatures\Modules\Notifications\Services\DestinationValidatorService;
use AndyDefer\LaravelFeatures\Modules\Notifications\Services\NotificationService;
use AndyDefer\Logger\Contracts\LoggerInterface;
use AndyDefer\Task\Services\TaskRegistryService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Mail\Mailer;
use Illuminate\Support\ServiceProvider;

final class NotificationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Destination Validator
        $this->app->singleton(DestinationValidatorInterface::class, DestinationValidatorService::class);

        // Notification Repository
        $this->app->singleton(NotificationRepository::class, function ($app) {
            return new NotificationRepository(
                $app->make(HydrationService::class),
            );
        });

        // Notification Service
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService(
                taskRegistry: $app->make(TaskRegistryService::class),
                repository: $app->make(NotificationRepository::class),
                hydration: $app->make(HydrationService::class),
                logger: $app->make(LoggerInterface::class),
                container: $app->make(Container::class),
            );
        });

        // Mail Channel
        $this->app->when(MailChannel::class)
            ->needs(Mailer::class)
            ->give(fn ($app) => $app->make(Mailer::class));

        $this->app->when(MailChannel::class)
            ->needs(LoggerInterface::class)
            ->give(fn ($app) => $app->make(LoggerInterface::class));

        $this->app->when(MailChannel::class)
            ->needs(DestinationValidatorInterface::class)
            ->give(fn ($app) => $app->make(DestinationValidatorInterface::class));

        // Database Channel
        $this->app->when(DatabaseChannel::class)
            ->needs(NotificationRepository::class)
            ->give(fn ($app) => $app->make(NotificationRepository::class));

        $this->app->when(DatabaseChannel::class)
            ->needs(LoggerInterface::class)
            ->give(fn ($app) => $app->make(LoggerInterface::class));

        $this->app->when(DatabaseChannel::class)
            ->needs(DestinationValidatorInterface::class)
            ->give(fn ($app) => $app->make(DestinationValidatorInterface::class));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/migrations');
        }

        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations'),
        ], 'Notifications-migrations');
    }
}
