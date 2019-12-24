<?php

namespace STS\EmailEvents;

use Illuminate\Http\Request;
use STS\EmailEvents\Exceptions\UnauthorizedException;

/**
 *
 */
class Provider
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $adapterClass;

    /** @var callable */
    protected $authorizer;

    /** @var array */
    protected $events = [];

    /**
     * @param               $name
     * @param               $adapterClass
     * @param callable      $authorizer
     */
    public function __construct( $name, $adapterClass, callable $authorizer )
    {
        $this->name = $name;
        $this->adapterClass = $adapterClass;
        $this->authorizer = $authorizer;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function authorize( Request $request )
    {
        if (!call_user_func($this->authorizer, $request, $this->adapterClass)) {
            throw new UnauthorizedException($request);
        }

        return $this;
    }

    /**
     * @param array $payload
     *
     * @return $this
     */
    public function adapt( array $payload )
    {
        foreach ($this->wrapPayload($payload) AS $data) {
            $this->events[] = EmailEvent::create(
                new $this->adapterClass($data)
            );
        }

        $this->events = array_filter($this->events);

        return $this;
    }

    /**
     * Some providers (like SendGrid) send multiple events at once. If we see that the array is
     * is NOT associative (numerically incrementally indexed) then it's already a multi-event
     * submission. Otherwise we'll wrap it, so we have a consistent array to loop through.
     *
     * @param array $payload
     *
     * @return array
     */
    protected function wrapPayload( array $payload )
    {
        return array_keys($payload) == range(0, count($payload) - 1)
            ? $payload
            : [$payload];
    }

    /**
     * @return $this
     */
    public function dispatch()
    {
        foreach ($this->events AS $event) {
            event($event);
        }

        return $this;
    }
}