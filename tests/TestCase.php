<?php

namespace Jeffersongoncalves\Tolt\Tests;

use Jeffersongoncalves\Tolt\ToltServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ToltServiceProvider::class,
        ];
    }
}
