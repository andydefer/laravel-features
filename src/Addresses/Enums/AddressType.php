<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Addresses\Enums;

/**
 * Enum representing address types.
 */
enum AddressType: string
{
    case PRIMARY = 'primary';
    case BILLING = 'billing';
    case SHIPPING = 'shipping';
    case WORK = 'work';
    case OTHER = 'other';
}
