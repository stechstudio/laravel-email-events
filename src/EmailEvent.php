<?php

namespace STS\EmailEvents;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use STS\EmailEvents\Adapters\AbstractAdapter;

/**
 * Class EmailEvent
 * @package STS\EmailEventParser
 */
class EmailEvent
{
    use Dispatchable;

    const EMAIL_ACCEPTED = "accepted";
    const EVENT_SENT = "sent";
    const EVENT_DEFERRED = "deferred";
    const EVENT_DELIVERED = "delivered";
    const EVENT_BOUNCED = "bounced";
    const EVENT_DROPPED = "dropped";
    const EVENT_COMPLAINED = "complained";
    const EVENT_OPENED = "opened";
    const EVENT_CLICKED = "clicked";

    const BOUNCE_HARD = "hard";   // permanent — safe to suppress
    const BOUNCE_SOFT = "soft";   // transient — retry later
    const BOUNCE_BLOCK = "block"; // blocked by reputation/policy

    /**
     * @var AbstractAdapter
     */
    protected $adapter;

    /**
     * EmailEvent constructor.
     *
     * @param AbstractAdapter $adapter
     */
    public function __construct( AbstractAdapter $adapter )
    {
        $this->adapter = $adapter;
    }

    /**
     * @param AbstractAdapter $adapter
     *
     * @return EmailEvent|null
     */
    public static function create( AbstractAdapter $adapter )
    {
        return $adapter->isValid()
            ? new static($adapter)
            : null;
    }

    /**
     * @return AbstractAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->adapter->getProvider();
    }

    /**
     * @return string|null
     */
    public function getAction()
    {
        return $this->adapter->getAction();
    }

    /**
     * @return string|null
     */
    public function getMessageId()
    {
        return $this->adapter->getMessageId();
    }

    /**
     * @return string|null
     */
    public function getRecipient()
    {
        return $this->adapter->getRecipient();
    }

    /**
     * @return int|null
     */
    public function getTimestamp()
    {
        return $this->adapter->getTimestamp();
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->adapter->getResponse();
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->adapter->getReason();
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->adapter->getCode();
    }

    /**
     * @return Collection
     */
    public function getTags()
    {
        return $this->adapter->getTags();
    }

    /**
     * @return Collection
     */
    public function getData()
    {
        return $this->adapter->getData();
    }

    /**
     * @return string|null
     */
    public function getBounceType()
    {
        return $this->adapter->getBounceType();
    }

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return $this->adapter->isPermanent();
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->adapter->getPayload();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'provider'  => $this->adapter->getProvider(),
            'event'     => $this->adapter->getAction(),
            'timestamp' => $this->adapter->getTimestamp(),
            'recipient' => $this->adapter->getRecipient(),
            'messageId' => $this->adapter->getMessageId(),
            'tags'      => $this->adapter->getTags()->toArray(),
            'data'      => $this->adapter->getData()->toArray(),
            'response'  => $this->adapter->getResponse(),
            'reason'    => $this->adapter->getReason(),
            'code'      => $this->adapter->getCode(),
            'bounceType' => $this->adapter->getBounceType(),
        ];
    }
}