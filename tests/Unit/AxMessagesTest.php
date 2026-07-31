<?php

namespace AxoloteSource\MessagesSdk\Tests\Unit;

use AxoloteSource\MessagesSdk\AxMessages;
use AxoloteSource\MessagesSdk\Clases\SendPushNotification;
use AxoloteSource\MessagesSdk\Tests\TestCase;
use AxoloteSource\MessagesSdk\DTO\Message;
use AxoloteSource\MessagesSdk\DTO\PushNotification;

class AxMessagesTest extends TestCase
{
    public function test_can_set_fake_mode()
    {
        AxMessages::fake(true);
        $this->assertTrue(AxMessages::isFake());

        AxMessages::fake(false);
        $this->assertFalse(AxMessages::isFake());
    }

    public function test_send_whatsapp_template_returns_message_in_fake_mode()
    {
        AxMessages::fake(true);
        
        $message = AxMessages::sendWhatsappTemplate('1234567890', 'welcome_template', ['name' => 'John']);
        
        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals(1, $message->id);
    }

    public function test_send_email_template_returns_message_in_fake_mode()
    {
        AxMessages::fake(true);
        
        $message = AxMessages::sendEmailTemplate([
            'to' => 'test@example.com',
            'subject' => 'Test Email',
            'body' => 'Hello World'
        ]);
        
        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals(1, $message->id);
    }

    public function test_send_push_notification_returns_message_in_fake_mode()
    {
        AxMessages::fake(true);

        $message = AxMessages::pushNotification()->send('user-list-456', 'push-notification-123');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals(1, $message->id);
    }

    public function test_send_push_notification_with_user_ids_returns_message_in_fake_mode()
    {
        AxMessages::fake(true);

        $message = AxMessages::pushNotification()->send(['1', '2', '3'], 'push-notification-123');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals(1, $message->id);
    }

    public function test_show_push_notification_returns_push_notification_in_fake_mode()
    {
        AxMessages::fake(true);

        $pushNotification = AxMessages::pushNotification()->show('1');

        $this->assertInstanceOf(PushNotification::class, $pushNotification);
        $this->assertEquals('1', $pushNotification->id);
        $this->assertEquals('Test Push Notification', $pushNotification->title);
    }

    public function test_push_notification_returns_send_push_notification_instance()
    {
        $pushNotification = AxMessages::pushNotification();

        $this->assertInstanceOf(SendPushNotification::class, $pushNotification);
    }

    public function test_show_push_notification_returns_all_properties()
    {
        AxMessages::fake(true);

        $pushNotification = AxMessages::pushNotification()->show('1');

        $this->assertInstanceOf(PushNotification::class, $pushNotification);
        $this->assertEquals('1', $pushNotification->id);
        $this->assertEquals('Test Push Notification', $pushNotification->title);
        $this->assertEquals('This is a test push notification', $pushNotification->body);
        $this->assertEquals(['key' => 'value'], $pushNotification->data);
        $this->assertEquals(['1', '2', '3'], $pushNotification->userIds);
        $this->assertEquals('Pending', $pushNotification->statusName);
        $this->assertEquals('1', $pushNotification->pushNotificationStatusId);
        $this->assertEquals(100, $pushNotification->totalDevices);
        $this->assertEquals(0, $pushNotification->successCount);
        $this->assertEquals(0, $pushNotification->failureCount);
        $this->assertCount(1, $pushNotification->histories);
    }

    public function test_send_push_notification_returns_all_properties()
    {
        AxMessages::fake(true);

        $message = AxMessages::pushNotification()->send('user-list-456', 'push-notification-123');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals(1, $message->id);
        $this->assertEquals(1, $message->channel_provider_id);
        $this->assertEquals(1, $message->message_status_id);
        $this->assertEquals('1', $message->created_user_id);
        $this->assertEquals(1, $message->attempts);
        $this->assertCount(1, $message->message_histories);
    }

    public function test_create_push_notification_returns_push_notification_in_fake_mode()
    {
        AxMessages::fake(true);

        $pushNotification = AxMessages::pushNotification()->create('Test Title', 'Test Body', ['key' => 'value']);

        $this->assertInstanceOf(PushNotification::class, $pushNotification);
        $this->assertEquals('1', $pushNotification->id);
        $this->assertEquals('Test Push Notification', $pushNotification->title);
    }

    public function test_create_push_notification_returns_all_properties()
    {
        AxMessages::fake(true);

        $pushNotification = AxMessages::pushNotification()->create('Test Title', 'Test Body', ['key' => 'value']);

        $this->assertInstanceOf(PushNotification::class, $pushNotification);
        $this->assertEquals('1', $pushNotification->id);
        $this->assertEquals('Test Push Notification', $pushNotification->title);
        $this->assertEquals('This is a test push notification', $pushNotification->body);
        $this->assertEquals(['key' => 'value'], $pushNotification->data);
        $this->assertEquals(['1', '2', '3'], $pushNotification->userIds);
        $this->assertEquals('Pending', $pushNotification->statusName);
        $this->assertEquals('1', $pushNotification->pushNotificationStatusId);
        $this->assertEquals(100, $pushNotification->totalDevices);
        $this->assertEquals(0, $pushNotification->successCount);
        $this->assertEquals(0, $pushNotification->failureCount);
        $this->assertCount(1, $pushNotification->histories);
    }

    public function test_update_push_notification_returns_push_notification_in_fake_mode()
    {
        AxMessages::fake(true);

        $pushNotification = AxMessages::pushNotification()->update('1', 'Updated Title', 'Updated Body');

        $this->assertInstanceOf(PushNotification::class, $pushNotification);
        $this->assertEquals('1', $pushNotification->id);
    }

    public function test_destroy_push_notification_returns_true_in_fake_mode()
    {
        AxMessages::fake(true);

        $result = AxMessages::pushNotification()->destroy('1');

        $this->assertTrue($result);
    }
}
