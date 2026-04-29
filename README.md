# Messages SDK

SDK for integration with the AxMessages messaging service from Axolote Source. This library facilitates sending WhatsApp messages and emails from Laravel applications.

## Installation

You can install the package via Composer:

```bash
composer require axolote-source/messages-sdk
```

## Configuration

To use this SDK in Laravel, you can publish the configuration file:

```bash
php artisan vendor:publish --provider="AxoloteSource\MessagesSdk\AxMessagesServiceProvider" --tag="config"
```

This will create a `config/axMessages.php` file. You can also configure the environment variables in your `.env` file:

```env
AXMESSAGES_URL=https://api.ejemplo.com
AXMESSAGES_TOKEN=your-token-here
AXMESSAGES_DEBUG=false
AXMESSAGES_DISABLED=false
```

### Configuration Variables

- `AXMESSAGES_URL`: The base URL of the messaging service.
- `AXMESSAGES_TOKEN`: Your authentication token.
- `AXMESSAGES_DEBUG`: (Optional) Enables debug mode (detailed logs).
- `AXMESSAGES_DISABLED`: (Optional) Disables the service globally.

## Usage

The main class `AxoloteSource\MessagesSdk\AxMessages` provides static methods to interact with the services.

### Send WhatsApp with Template

You can send a WhatsApp message using a predefined template:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$to = '521234567890';
$templateName = 'welcome_message';
$variables = [
    [
        'key' => 'user',
        'type' => 'text',
        'value' => 'John Doe',
    ],
]

$response = AxMessages::sendWhatsappTemplate($to, $templateName, $variables);

if ($response) {
    // Message sent successfully
    echo $response->id;
}
```

You can also use the fluid instance:

```php
$response = AxMessages::sendWhatsapp()
    ->template($to, $templateName, $variables);
```

### Send Email with Template

To send emails, use the `sendEmailTemplate` method:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$data = [
    'to' => 'user@example.com',
    'subject' => 'Welcome',
    'template' => 'welcome_email',
    'params' => [
        [
            'key' => 'name',
            'value' => 'John Doe'
        ]
    ]
];

$response = AxMessages::sendEmailTemplate($data);
```

Or through the instance:

```php
$response = AxMessages::sendEmail()->template($data);
```

## Testing (Mocking)

The SDK includes functionality to simulate message sending in testing environments, avoiding real requests.

```php
use AxoloteSource\MessagesSdk\AxMessages;

// Activate fake mode
AxMessages::fake();

// These calls will not make real requests
AxMessages::sendWhatsappTemplate('...', '...');

// Verify if fake mode is being used
if (AxMessages::isFake()) {
    // ...
}
```

## Debugging

You can manually activate debug mode to get more information in the logs in case of an error:

```php
AxMessages::sendWhatsapp()->activeDebugMode()->template(...);
```
