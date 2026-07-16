<?php

namespace AxoloteSource\MessagesSdk\Traits;

use AxoloteSource\MessagesSdk\Clases\AxMessagesBase;
use Illuminate\Http\Client\Factory as HttpFactory;

trait UseFake
{
    private static bool $isFake = false;

    public static function isFake(): bool
    {
        return self::$isFake;
    }

    public static function fake(bool $isFake = true): void
    {
        self::$isFake = $isFake;

        if ($isFake) {
            if (property_exists(AxMessagesBase::class, 'httpClient')) {
                $reflection = new \ReflectionClass(AxMessagesBase::class);
                $property = $reflection->getProperty('httpClient');
                $property->setValue(null, new HttpFactory);
            }
        }
    }
}
