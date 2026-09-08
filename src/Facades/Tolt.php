<?php

namespace Jeffersongoncalves\Tolt\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffersongoncalves\Tolt\Tolt
 */
class Tolt extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-tolt';
    }
}
