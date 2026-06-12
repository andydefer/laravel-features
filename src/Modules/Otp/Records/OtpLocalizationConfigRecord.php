<?php

// src/Records/OtpLocalizationConfigRecord.php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otps\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\LaravelFeatures\Modules\Otps\Collections\SupportedLocaleCollection;
use AndyDefer\LaravelFeatures\Modules\Otps\Enums\SupportedLocale;

final class OtpLocalizationConfigRecord extends AbstractRecord
{
    public function __construct(
        public readonly string $locale,
        public readonly SupportedLocaleCollection $supported_locales,
        public readonly SupportedLocale $fallback_locale,
    ) {}
}
