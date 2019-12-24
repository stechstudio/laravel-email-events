<?php

namespace STS\EmailEvents\Tests;

use Illuminate\Http\Request;
use STS\EmailEvents\Auth\BasicHttpAuth;

class BasicHttpAuthTest extends TestCase
{
    public function testBasicHttpAuth()
    {
        config([
            'email-events.basic_username' => 'secretusername',
            'email-events.basic_password' => 'secretpassword',
        ]);

        $auth = resolve(BasicHttpAuth::class);

        $request = Request::capture();
        $this->assertFalse($auth($request));

        $request->headers->set('PHP_AUTH_USER', 'secretusername');
        $request->headers->set('PHP_AUTH_PW', 'secretpassword');

        $this->assertTrue($auth($request));
    }
}