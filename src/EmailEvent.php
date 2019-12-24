<?php

namespace STS\EmailEvents;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use STS\EmailEvents\Adapters\AbstractAdapter;

/**
 * Class EmailEvent
 * @package STS\EmailEventParser
 *
 * @method string getProvider()
 * @method string getAction()
 * @method string getMessageId()
 * @method string getRecipient()
 * @method int getTimestamp()
 * @method string getResponse()
 * @method string getReason()
 * @method mixed getCode()
 * @method Collection getTags()
 * @method Collection getData()
 * @method array getPayload()
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
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call( $method, $parameters )
    {
        return call_user_func_array([$this->adapter, $method], $parameters);
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
        ];
    }
}