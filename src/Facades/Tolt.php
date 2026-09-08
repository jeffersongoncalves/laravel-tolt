<?php

namespace JeffersonGoncalves\Tolt\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\Tolt\Tolt as ToltClient;

/**
 * @see ToltClient
 */
class Tolt extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ToltClient::class;
    }
}
