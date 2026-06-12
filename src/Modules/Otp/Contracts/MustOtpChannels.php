<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otps\Contracts;

use AndyDefer\LaravelFeatures\Modules\Otps\Collections\OtpChannelCollection;

/**
 * Interface for entities that require OTP channel configuration.
 *
 * Implementing this interface allows an entity (typically a User model or similar)
 * to define which communication channels should be used for OTP delivery.
 */
interface MustOtpChannels
{
    /**
     * Get the OTP delivery channels configured for this entity.
     */
    public function getOtpChannels(): OtpChannelCollection;
}
