<?php

namespace AxoloteSource\MessagesSdk;

use AxoloteSource\MessagesSdk\Clases\AxMessagesBase;
use AxoloteSource\MessagesSdk\Clases\SendEmail;
use AxoloteSource\MessagesSdk\Clases\SendWhatsapp;
use AxoloteSource\MessagesSdk\DTO\Message;
use Illuminate\Http\Client\Factory as HttpFactory;

class AxMessages
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

    public static function sendWhatsapp(): SendWhatsapp
    {
        return new SendWhatsapp;
    }

    public static function sendWhatsappTemplate(string $to, string $templateName, array $variables = []): ?Message
    {
        return self::sendWhatsapp()->template($to, $templateName, $variables);
    }

    public static function sendEmail(): SendEmail
    {
        return new SendEmail;
    }

    public static function sendEmailTemplate(array $values): ?Message
    {
        return self::sendEmail()->template($values);
    }
}
