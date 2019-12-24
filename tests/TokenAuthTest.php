<?php

namespace STS\EmailEvents\Tests;

use Illuminate\Http\Request;
use STS\EmailEvents\Auth\TokenAuth;

class TokenAuthTest extends TestCase
{
    public function testSimpleToken()
    {
        config([
            'email-events.token' => 'mysupersecrettoken',
        ]);

        $auth = resolve(TokenAuth::class);
        $request = Request::capture();
        $this->assertFalse($auth($request));

        $request->offsetSet('auth', 'invalidtoken');
        $this->assertFalse($auth($request));

        $request->offsetSet('invalidlocation', 'mysupersecrettoken');
        $this->assertFalse($auth($request));

        $request->offsetSet('auth', 'mysupersecrettoken');
        $this->assertTrue($auth($request));
    }

    public function testCustomTokenParam()
    {
        config([
            'email-events.token' => 'mysupersecrettoken',
            'email-events.token_parameter' => 'token',
        ]);

        $auth = resolve(TokenAuth::class);
        $request = Request::capture();
        $this->assertFalse($auth($request));

        $request->offsetSet('auth', 'mysupersecrettoken');
        $this->assertFalse($auth($request));

        $request->offsetSet('token', 'mysupersecrettoken');
        $this->assertTrue($auth($request));
    }
}