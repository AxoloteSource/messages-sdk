<?php

return [
    /*
    |--------------------------------------------------------------------------
    | URL Configuration
    |--------------------------------------------------------------------------
    |
    | This option defines the URL for the AxMessages API.
    | You can set it in the environment file using the variable AXMESSAGES_URL.
    | If not set, it defaults to 'https://api.ejemplo.com'.
    |
    */
    'url' => env('AXMESSAGES_URL', 'https://api.ejemplo.com'),

    /*
     |--------------------------------------------------------------------------
     | Authentication Token
     |--------------------------------------------------------------------------
     |
     | This option defines the token used to authenticate requests to the
     | AxMessages API. Set it in the environment file using the variable
     | AXMESSAGES_TOKEN. Leave it blank to disable token-based authentication.
     |
     */
    'token' => env('AXMESSAGES_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | This option controls whether debug mode is enabled.
    | When enabled, logs will be printed for debugging purposes.
    | Set to true to enable debug logging or false to disable it.
    |
    */
    'debug' => env('AXMESSAGES_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Disable AxMessages
    |--------------------------------------------------------------------------
    |
    | This option controls whether the AxMessages service is disabled.
    | When set to true, all AxMessages functionality will be disabled.
    | Set it in the environment file using AXMESSAGES_DISABLED variable.
    |
    */
    'disabled' => env('AXMESSAGES_DISABLED', false),
];
