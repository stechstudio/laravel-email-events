<?php

namespace STS\EmailEvents\Adapters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use STS\EmailEvents\EmailEvent;

class Mailgun extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $provider = "Mailgun";

    /**
     * @var string
     */
    protected static $userAgent = "mailgun/*";

    protected $signature;

    /**
     * @var array
     */
    protected $eventMap = [
        'delivered'  => EmailEvent::EVENT_DELIVERED,
        'failed'     => EmailEvent::EVENT_BOUNCED,
        'complained' => EmailEvent::EVENT_COMPLAINED,
        'opened'     => EmailEvent::EVENT_OPENED,
        'clicked'    => EmailEvent::EVENT_CLICKED
    ];

    public function __construct( $payload )
    {
        parent::__construct($payload['event-data']);

        $this->signature = $payload['signature'];
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return array_get($this->eventMap, Arr::get($this->payload, 'event'));
    }

    /**
     * @return mixed
     */
    public function getRecipient()
    {
        return Arr::get($this->payload, 'recipient');

    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return Arr::get($this->payload, 'timestamp');
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return Arr::get($this->payload, "id");
    }

    /**
     * @return Collection
     */
    public function getTags()
    {
        return collect((array)Arr::get($this->payload, 'tags'));
    }

    /**
     * @return Collection
     */
    public function getData()
    {
        return collect((array)Arr::get($this->payload, 'user-variables'));
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return Arr::has($this->payload, 'delivery-status.description')
            ? Arr::get($this->payload, 'delivery-status.description')
            : Arr::get($this->payload, 'delivery-status.message');
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return Arr::get($this->payload, 'delivery-status.code');
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return Arr::get($this->payload, 'reason');
    }

    /**
     * @param array $payload
     *
     * @return bool
     */
    public static function supports( array $payload )
    {
        return array_key_exists('signature', $payload) && array_key_exists('event-data', $payload);
    }
}