<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otps\Collections;

use AndyDefer\DomainStructures\Abstracts\AbstractTypedCollection;
use AndyDefer\LaravelFeatures\Modules\Otps\Enums\OtpChannel;

final class OtpChannelCollection extends AbstractTypedCollection
{
    public function __construct()
    {
        parent::__construct(OtpChannel::class);
    }

    public function hasMail(): bool
    {
        return $this->contains(OtpChannel::MAIL);
    }

    public function hasSms(): bool
    {
        return $this->contains(OtpChannel::SMS);
    }
}
