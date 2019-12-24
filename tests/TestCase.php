<?php

namespace STS\EmailEvents\Tests;

use STS\EmailEvents\EmailEventsServiceProvider;
use STS\EmailEvents\Facades\EmailEvents;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [EmailEventsServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'EmailEvents' => EmailEvents::class
        ];
    }
}