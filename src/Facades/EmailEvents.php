<?php

namespace STS\EmailEvents\Facades;

use Illuminate\Support\Facades\Facade;

class EmailEvents extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'emailevents';
    }
}
