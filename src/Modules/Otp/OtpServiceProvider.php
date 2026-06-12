<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otp;

use AndyDefer\LaravelFeatures\Modules\Otp\Repositories\OneTimePasswordRepository;
use AndyDefer\LaravelFeatures\Modules\Otp\Services\OtpService;
use Illuminate\Support\ServiceProvider;

final class OtpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OneTimePasswordRepository::class);
        $this->app->singleton(OtpService::class);
    }

    public function boot(): void {}
}
