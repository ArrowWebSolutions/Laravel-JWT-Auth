<?php

namespace Arrow\JwtAuth\Tests;

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

    protected function useRsaSignature($app)
    {
        //we need to set this in the jwt-auth as it'll get merged into auth as part of the boot
        $app['config']->set('jwt-auth.providers.jwt.signature', 'rsa');
    }

    protected function useHmacSignature($app)
    {
        $app['config']->set('jwt-auth.providers.jwt.signature', 'hmac');
    }

    protected function useEcdsaSignature($app)
    {
        $app['config']->set('jwt-auth.providers.jwt.signature', 'ecdsa');
    }
}
