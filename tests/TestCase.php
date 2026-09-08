<?php

namespace JeffersonGoncalves\Tolt\Tests;

use JeffersonGoncalves\Tolt\ToltServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ToltServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('tolt.api_key', 'fake-api-key');
        $app['config']->set('tolt.program_id', 'prg_1');
        $app['config']->set('tolt.base_url', 'https://api.tolt.com/v1');
    }
}
