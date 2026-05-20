<?php

namespace STS\EmailEvents\Tests;

use STS\EmailEvents\Adapters\Postmark;
use STS\EmailEvents\EmailEvent;

class PostmarkAdapterTest extends TestCase
{
    protected function deliveryPayload()
    {
        return [
            'RecordType'  => 'Delivery',
            'MessageID'   => 'postmark-message-1',
            'Recipient'   => 'recipient@example.com',
            'DeliveredAt' => '2021-01-01T00:00:00Z',
            'Details'     => 'smtp;250 OK',
            'Tag'         => 'welcome',
            'Metadata'    => ['order_id' => '1234'],
        ];
    }

    protected function bouncePayload()
    {
        return [
            'RecordType' => 'Bounce',
            'Type'       => 'HardBounce',
            'TypeCode'   => 1,
            'MessageID'  => 'postmark-message-2',
            'Recipient'  => 'recipient@example.com',
            'BouncedAt'  => '2021-01-01T00:00:00Z',
            'Details'    => 'mailbox does not exist',
        ];
    }

    public function testSupports()
    {
        $this->assertTrue(Postmark::supports($this->deliveryPayload()));
        $this->assertFalse(Postmark::supports(['MessageID' => 'x']));
        $this->assertFalse(Postmark::supports(['RecordType' => 'Delivery']));
    }

    public function testParsesDeliveryEvent()
    {
        $adapter = new Postmark($this->deliveryPayload());

        $this->assertTrue($adapter->isValid());
        $this->assertSame('Postmark', $adapter->getProvider());
        $this->assertSame(EmailEvent::EVENT_DELIVERED, $adapter->getAction());
        $this->assertSame('recipient@example.com', $adapter->getRecipient());
        $this->assertSame(strtotime('2021-01-01T00:00:00Z'), $adapter->getTimestamp());
        $this->assertSame('postmark-message-1', $adapter->getMessageId());
        $this->assertSame('smtp;250 OK', $adapter->getResponse());
        $this->assertSame(['welcome'], $adapter->getTags()->all());
        $this->assertSame(['order_id' => '1234'], $adapter->getData()->all());
        $this->assertNull($adapter->getCode());
    }

    public function testParsesBounceEvent()
    {
        $adapter = new Postmark($this->bouncePayload());

        $this->assertSame(EmailEvent::EVENT_BOUNCED, $adapter->getAction());
        $this->assertSame('HardBounce', $adapter->getReason());
        $this->assertSame(1, $adapter->getCode());
    }

    public function testTransientBounceMapsToDeferred()
    {
        $payload = $this->bouncePayload();
        $payload['Type'] = 'Transient';

        $adapter = new Postmark($payload);

        $this->assertSame(EmailEvent::EVENT_DEFERRED, $adapter->getAction());
    }

    public function testUnknownRecordTypeIsInvalid()
    {
        $payload = $this->deliveryPayload();
        $payload['RecordType'] = 'SubscriptionChange';

        $adapter = new Postmark($payload);

        $this->assertNull($adapter->getAction());
        $this->assertFalse($adapter->isValid());
        $this->assertNull(EmailEvent::create($adapter));
    }

    public function testEmailEventToArray()
    {
        $event = EmailEvent::create(new Postmark($this->deliveryPayload()));

        $this->assertInstanceOf(EmailEvent::class, $event);
        $this->assertSame([
            'provider'  => 'Postmark',
            'event'     => EmailEvent::EVENT_DELIVERED,
            'timestamp' => strtotime('2021-01-01T00:00:00Z'),
            'recipient' => 'recipient@example.com',
            'messageId' => 'postmark-message-1',
            'tags'      => ['welcome'],
            'data'      => ['order_id' => '1234'],
            'response'  => 'smtp;250 OK',
            'reason'    => null,
            'code'      => null,
        ], $event->toArray());
    }
}
