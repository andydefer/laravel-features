<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Totp;

use AndyDefer\LaravelFeatures\Modules\Totp\Services\TOTPService;
use Illuminate\Support\ServiceProvider;

final class TotpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TOTPService::class);
    }

    public function boot(): void {}
}
