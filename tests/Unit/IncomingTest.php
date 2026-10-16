<?php

namespace AxoloteSource\MessagesSdk\Tests\Unit;

use AxoloteSource\MessagesSdk\AxMessages;
use AxoloteSource\MessagesSdk\Clases\AxMessagesBase;
use AxoloteSource\MessagesSdk\Clases\Incoming;
use AxoloteSource\MessagesSdk\DTO\Contact;
use AxoloteSource\MessagesSdk\DTO\IncomingMessage;
use AxoloteSource\MessagesSdk\Tests\TestCase;
use Illuminate\Http\Client\Factory as HttpFactory;

class IncomingTest extends TestCase
{
    public function test_incoming_returns_incoming_instance(): void
    {
        $this->assertInstanceOf(Incoming::class, AxMessages::incoming());
    }

    public function test_list_contacts_returns_contacts_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $contacts = AxMessages::incoming()->listContacts();

        $this->assertIsArray($contacts);
        $this->assertCount(1, $contacts);
        $this->assertInstanceOf(Contact::class, $contacts[0]);
        $this->assertEquals(1, $contacts[0]->id);
        $this->assertSame('+521234567890', $contacts[0]->phone);
        $this->assertSame('Test User', $contacts[0]->name);
        $this->assertSame('Hello', $contacts[0]->lastMessageText);
        $this->assertSame(2, $contacts[0]->unreadCount);
    }

    public function test_show_contact_returns_contact_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $contact = AxMessages::incoming()->showContact('1');

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals(1, $contact->id);
        $this->assertSame('Test User', $contact->name);
        $this->assertSame(2, $contact->messagesCount);
    }

    public function test_list_messages_returns_messages_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $messages = AxMessages::incoming()->listMessages('1');

        $this->assertIsArray($messages);
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(IncomingMessage::class, $messages[0]);
        $this->assertSame('wamid.test123', $messages[0]->waMessageId);
        $this->assertSame('text', $messages[0]->type);
        $this->assertSame('Hello', $messages[0]->text);
        $this->assertEquals(1, $messages[0]->contactId);
    }

    public function test_show_message_returns_message_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $message = AxMessages::incoming()->showMessage('1');

        $this->assertInstanceOf(IncomingMessage::class, $message);
        $this->assertSame('Hello', $message->text);
        $this->assertSame(1717000000, $message->waTimestamp);
    }

    public function test_has_error_is_false_on_success(): void
    {
        AxMessages::fake(true);

        $incoming = AxMessages::incoming();
        $incoming->listContacts();

        $this->assertFalse($incoming->hasError());
        $this->assertEmpty($incoming->getError());
    }

    public function test_has_error_is_true_when_response_fails(): void
    {
        AxMessages::fake(false);
        $this->fakeHttpResponse(
            'https://api.ejemplo.com/api/v1/whatsapp/incoming/contacts',
            ['message' => 'Server error'],
            500
        );

        $incoming = AxMessages::incoming();
        $contacts = $incoming->listContacts();

        $this->assertSame([], $contacts);
        $this->assertTrue($incoming->hasError());
        $this->assertSame('error', $incoming->getError()['status']);
        $this->assertSame('Server error', $incoming->getError()['message']);
    }

    private function fakeHttpResponse(string $url, array $body, int $status): void
    {
        $factory = new HttpFactory;
        $factory->fake([
            $url => HttpFactory::response($body, $status, ['Accept' => 'application/json']),
        ]);

        $reflection = new \ReflectionClass(AxMessagesBase::class);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue(null, $factory);
    }
}
