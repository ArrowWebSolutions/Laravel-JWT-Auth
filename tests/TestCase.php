<?php

namespace VendorName\Skeleton\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Arrow\JwtAuth\JwtAuthenticationServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            JwtAuthenticationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
    }
}
