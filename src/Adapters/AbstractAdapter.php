<?php

namespace STS\EmailEvents\Adapters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class AbstractAdapter
{
    /**
     * @var string
     */
    protected static $userAgent;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var array
     */
    protected $eventMap = [];

    /**
     * @var string
     */
    protected $provider;

    /**
     * AbstractAdapter constructor.
     *
     * @param $payload
     */
    public function __construct( $payload )
    {
        $this->payload = $payload;
    }

    /**
     * If known, provide the user-agent pattern we expect to be used by the email provider
     */
    public static function getUserAgent()
    {
        return static::$userAgent;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return is_string($this->getAction()) && is_string($this->getRecipient());
    }

    /**
     * @return string
     */
    abstract public function getAction();

    /**
     * @return string
     */
    abstract public function getMessageId();

    /**
     * @return string
     */
    abstract public function getRecipient();

    /**
     * @return int
     */
    abstract public function getTimestamp();

    /**
     * @return mixed
     */
    abstract public function getResponse();

    /**
     * @return mixed
     */
    abstract public function getReason();

    /**
     * @return mixed
     */
    abstract public function getCode();

    /**
     * @return Collection
     */
    abstract public function getTags();

    /**
     * @return Collection
     */
    abstract public function getData();

    /**
     * @param array $payload
     *
     * @return bool
     */
    abstract public static function supports( array $payload );

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param $attribute
     *
     * @return mixed
     */
    public function get($attribute)
    {
        return Arr::get($this->payload, $attribute);
    }
}