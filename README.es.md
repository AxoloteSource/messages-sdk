# Messages SDK

SDK para la integración con el servicio de mensajería AxMessages de Axolote Source. Esta librería facilita el envío de mensajes de WhatsApp, correos electrónicos y notificaciones push desde aplicaciones Laravel.

## Instalación

Puedes instalar el paquete a través de Composer:

```bash
composer require axolote-source/messages-sdk
```

## Configuración

Para utilizar este SDK en Laravel, puedes publicar el archivo de configuración:

```bash
php artisan vendor:publish --provider="AxoloteSource\MessagesSdk\AxMessagesServiceProvider" --tag="config"
```

Esto creará un archivo `config/axMessages.php`. También puedes configurar las variables de entorno en tu archivo `.env`:

```env
AXMESSAGES_URL=https://api.ejemplo.com
AXMESSAGES_TOKEN=tu-token-aqui
AXMESSAGES_DEBUG=false
AXMESSAGES_DISABLED=false
```

### Variables de Configuración

- `AXMESSAGES_URL`: La URL base del servicio de mensajería.
- `AXMESSAGES_TOKEN`: Tu token de autenticación.
- `AXMESSAGES_DEBUG`: (Opcional) Habilita el modo de depuración (logs detallados).
- `AXMESSAGES_DISABLED`: (Opcional) Desactiva el servicio globalmente.

## Uso

La clase principal `AxoloteSource\MessagesSdk\AxMessages` proporciona métodos estáticos para interactuar con los servicios.

### Enviar WhatsApp con Plantilla

Puedes enviar un mensaje de WhatsApp utilizando una plantilla predefinida:

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
    // El mensaje se envió correctamente
    echo $response->id;
}
```

También puedes usar la instancia fluida:

```php
$response = AxMessages::sendWhatsapp()
    ->template($to, $templateName, $variables);
```

### Enviar Correo Electrónico con Plantilla

Para enviar correos electrónicos, utiliza el método `sendEmailTemplate`:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$data = [
    'to' => 'user@example.com',
    'subject' => 'Bienvenida',
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

O mediante la instancia:

```php
$response = AxMessages::sendEmail()->template($data);
```

### Enviar Notificación Push

Para enviar una notificación push, utiliza el método `pushNotification()` que retorna una instancia de `SendPushNotification`:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$userListId = 'user-list-456';
$pushNotificationId = 'push-notification-123';

$response = AxMessages::pushNotification()->send($userListId, $pushNotificationId);

if ($response) {
    // La notificación push se envió correctamente
    echo $response->id;
}
```

### Mostrar Notificación Push

También puedes consultar los detalles de una notificación push previamente enviada:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$pushNotification = AxMessages::pushNotification()->show('1');

if ($pushNotification) {
    echo $pushNotification->title;
    echo $pushNotification->body;
    echo $pushNotification->statusName;
    echo $pushNotification->totalDevices;
    echo $pushNotification->successCount;
    echo $pushNotification->failureCount;
}
```

### Crear Notificación Push

Para crear una nueva notificación push, utiliza el método `create`:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$pushNotification = AxMessages::pushNotification()->create(
    title: 'Título de la Notificación',
    body: 'Contenido del cuerpo de la notificación',
    data: ['key' => 'value'] // opcional
);

if ($pushNotification) {
    echo $pushNotification->id;
    echo $pushNotification->title;
    echo $pushNotification->body;
    echo $pushNotification->statusName;
}
```

### Actualizar Notificación Push

Para actualizar una notificación push existente, utiliza el método `update`. El `id` es requerido, mientras que `title`, `body` y `data` son opcionales:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$pushNotification = AxMessages::pushNotification()->update(
    id: '1',
    title: 'Título Actualizado', // opcional
    body: 'Contenido actualizado', // opcional
    data: ['key' => 'value'] // opcional
);

if ($pushNotification) {
    echo $pushNotification->id;
    echo $pushNotification->title;
    echo $pushNotification->body;
}
```

### Eliminar Notificación Push

Para eliminar una notificación push, utiliza el método `destroy`:

```php
use AxoloteSource\MessagesSdk\AxMessages;

$success = AxMessages::pushNotification()->destroy('1');

if ($success) {
    // La notificación push se eliminó correctamente
}
```

## Pruebas (Mocking)

El SDK incluye una funcionalidad para simular el envío de mensajes en entornos de prueba, evitando realizar peticiones reales.

```php
use AxoloteSource\MessagesSdk\AxMessages;

// Activar el modo fake
AxMessages::fake();

// Estas llamadas no realizarán peticiones reales
AxMessages::sendWhatsappTemplate('...', '...');

// Verificar si se está usando el modo fake
if (AxMessages::isFake()) {
    // ...
}
```

## Depuración

Puedes activar manualmente el modo de depuración para obtener más información en los logs en caso de error:

```php
AxMessages::sendWhatsapp()->activeDebugMode()->template(...);
```
