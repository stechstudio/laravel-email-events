<?php

namespace STS\EmailEvents;

use Route;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class EmailEvents
{
    /**
     * @var array
     */
    protected $config;

    /**
     * EmailEvents constructor.
     *
     * @param $config
     */
    public function __construct( $config )
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     *
     * @return Provider
     */
    public function provider( $name )
    {
        return new Provider(
            $name,
            Arr::get($this->config, "providers.$name.adapter"),
            $this->getAuthorizer(Arr::get($this->config, "providers.$name.auth"))
        );
    }

    /**
     * @param $name
     * @param $adapter
     * @param $authorizer
     *
     * @return $this
     */
    public function extend( $name, $adapter, $authorizer )
    {
        $this->config['providers'][$name] = [
            'adapter' => $adapter,
            'auth'    => $authorizer
        ];

        return $this;
    }

    /**
     * @param $auth
     *
     * @return callable
     */
    protected function getAuthorizer( $auth )
    {
        return is_callable($auth)
            ? $auth
            : resolve(Arr::get($this->config, "authorizers.$auth"));
    }

    /**
     *
     */
    public function routes()
    {
        Route::post($this->config['url'] . '/{provider}', function ( Request $request, $provider ) {
            $this->provider($provider)
                ->authorize($request)
                ->adapt($request->all())
                ->dispatch();
        })->name('webhook.email-events');
    }
}