<?php

namespace BadMushroom\LaravelTourist\Facades;

use Illuminate\Support\Facades\Facade;

class Tour extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tour';
    }
}
