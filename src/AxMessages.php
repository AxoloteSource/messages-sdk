<?php

namespace AxoloteSource\MessagesSdk;

use AxoloteSource\MessagesSdk\Traits\UseFake;
use AxoloteSource\MessagesSdk\Clases\Drivers;
use AxoloteSource\MessagesSdk\Clases\Incoming;
use AxoloteSource\MessagesSdk\Clases\Providers;
use AxoloteSource\MessagesSdk\Clases\ReplicateUrls;
use AxoloteSource\MessagesSdk\Clases\SendEmail;
use AxoloteSource\MessagesSdk\Clases\SendPushNotification;
use AxoloteSource\MessagesSdk\Clases\SendWhatsapp;
use AxoloteSource\MessagesSdk\DTO\Message;

class AxMessages
{
    use UseFake;

    public static function Whatsapp(): SendWhatsapp
    {
        return new SendWhatsapp;
    }

    public static function email(): SendEmail
    {
        return new SendEmail;
    }

    public static function pushNotification(): SendPushNotification
    {
        return new SendPushNotification;
    }

    /**
     * Read-only access to inbound WhatsApp messages.
     */
    public static function incoming(): Incoming
    {
        return new Incoming;
    }

    /**
     * CRUD access to the WhatsApp replicate hook URLs.
     */
    public static function replicateUrls(): ReplicateUrls
    {
        return new ReplicateUrls;
    }

    /**
     * CRUD access to the messaging drivers.
     */
    public static function drivers(): Drivers
    {
        return new Drivers;
    }

    /**
     * CRUD access to the messaging providers.
     */
    public static function providers(): Providers
    {
        return new Providers;
    }

    /*****************************
     *     DEPRECATED METHODS    *
     *****************************/

    /**
     * @deprecated use whatsapp()
     */
    public static function sendWhatsapp(): SendWhatsapp
    {
        return new SendWhatsapp;
    }

    /**
     * @deprecated use whatsapp()->template($to, $templateName, $variables)
     */
    public static function sendWhatsappTemplate(string $to, string $templateName, array $variables = []): ?Message
    {
        return self::sendWhatsapp()->template($to, $templateName, $variables);
    }

    /**
     * @deprecated use email()
     */
    public static function sendEmail(): SendEmail
    {
        return new SendEmail;
    }

    /**
     * @deprecated use email()->template()
     */
    public static function sendEmailTemplate(array $values): ?Message
    {
        return self::sendEmail()->template($values);
    }
}
