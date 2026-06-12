<?php

// src/Configs/MfaConfig.php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otps\Configs;

use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\LaravelFeatures\Modules\Otps\Collections\SupportedLocaleCollection;
use AndyDefer\LaravelFeatures\Modules\Otps\Contracts\Configs\OtpConfigInterface;
use AndyDefer\LaravelFeatures\Modules\Otps\Enums\SupportedLocale;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\CleanupConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\OtpConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\OtpLocalizationConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\OtpSecurityConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\RecoveryCodesConfigRecord;

class OtpConfig implements OtpConfigInterface
{
    private HydrationService $hydration;

    public function __construct()
    {
        $this->hydration = new HydrationService;
    }

    public function otpConfig(): OtpConfigRecord
    {
        return $this->hydration->hydrate(OtpConfigRecord::class, [
            'default_expiry_minutes' => (int) config('otp.default_expiry_minutes', 10),
            'default_max_attempts' => (int) config('otp.default_max_attempts', 3),
        ]);
    }

    public function otpLocalizationConfig(): OtpLocalizationConfigRecord
    {
        $supportedLocales = SupportedLocaleCollection::fromStrings(
            config('otp.localization.supported_locales', ['fr', 'en'])
        );

        $fallbackLocale = SupportedLocale::fromString(
            config('otp.localization.fallback_locale', 'en')
        ) ?? SupportedLocale::ENGLISH;

        return $this->hydration->hydrate(OtpLocalizationConfigRecord::class, [
            'locale' => config('otp.localization.locale', 'en'),
            'supported_locales' => $supportedLocales,
            'fallback_locale' => $fallbackLocale,
        ]);
    }

    public function otpSecurityConfig(): OtpSecurityConfigRecord
    {
        return $this->hydration->hydrate(OtpSecurityConfigRecord::class, [
            'rate_limit_requests' => (int) config('otp.security.rate_limit_requests', 3),
            'rate_limit_verifications' => (int) config('otp.security.rate_limit_verifications', 5),
            'rate_limit_decay_minutes' => (int) config('otp.security.rate_limit_decay_minutes', 60),
            'failed_verification_decay_seconds' => (int) config('otp.security.failed_verification_decay_seconds', 300),
            'rate_limit_hit_decay_seconds' => (int) config('otp.security.rate_limit_hit_decay_seconds', 60),
        ]);
    }

    public function recoveryCodesConfig(): RecoveryCodesConfigRecord
    {
        return $this->hydration->hydrate(RecoveryCodesConfigRecord::class, [
            'characters' => config('recovery_codes.characters', 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'),
            'default_count' => (int) config('recovery_codes.default_count', 8),
            'default_length' => (int) config('recovery_codes.default_length', 10),
            'hash_algorithm' => config('recovery_codes.hash_algorithm', 'sha256'),
        ]);
    }

    public function cleanupConfig(): CleanupConfigRecord
    {
        return $this->hydration->hydrate(CleanupConfigRecord::class, [
            'auto_cleanup' => (bool) config('cleanup.auto_cleanup', true),
            'frequency' => (int) config('cleanup.frequency', 60),
            'retention_days' => (int) config('cleanup.retention_days', 30),
        ]);
    }

    public function shouldCleanup(): bool
    {
        $config = $this->cleanupConfig();

        return $config->auto_cleanup && $config->frequency > 0;
    }
}
