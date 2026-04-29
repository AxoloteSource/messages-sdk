<?php

namespace AxoloteSource\MessagesSdk\Tests\Unit;

use AxoloteSource\MessagesSdk\AxMessages;
use AxoloteSource\MessagesSdk\Tests\TestCase;
use AxoloteSource\MessagesSdk\DTO\Message;

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
}
