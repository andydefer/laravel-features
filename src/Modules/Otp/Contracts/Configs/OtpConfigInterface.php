<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otps\Contracts\Configs;

use AndyDefer\LaravelFeatures\Modules\Otps\Records\CleanupConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\OtpConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\OtpLocalizationConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\OtpSecurityConfigRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Records\RecoveryCodesConfigRecord;

interface OtpConfigInterface
{
    /**
     * Get OTP configuration.
     */
    public function otpConfig(): OtpConfigRecord;

    /**
     * Get OTP localization configuration.
     */
    public function otpLocalizationConfig(): OtpLocalizationConfigRecord;

    /**
     * Get OTP security configuration.
     */
    public function otpSecurityConfig(): OtpSecurityConfigRecord;

    /**
     * Get recovery codes configuration.
     */
    public function recoveryCodesConfig(): RecoveryCodesConfigRecord;

    /**
     * Get cleanup configuration.
     */
    public function cleanupConfig(): CleanupConfigRecord;

    // ============================================================================
    // Helper Methods
    // ============================================================================

    /**
     * Check if cleanup is enabled and has valid configuration.
     */
    public function shouldCleanup(): bool;
}
