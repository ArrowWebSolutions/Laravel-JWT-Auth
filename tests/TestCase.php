<?php

namespace Arrow\JwtAuth\Tests;

use Arrow\JwtAuth\JwtAuthenticationServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;
use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\RSA;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            JwtAuthenticationServiceProvider::class,
        ];
    }

    /**
     *
     * @param Router $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->middleware('auth:jwt')->group(function () use ($router) {
            $router->get('/', function () {
                return ['authenticated' => true];
            });

            $router->get('/user', function (Request $request) {
                return $request->user();
            });
        });
    }

    protected function useRsaSignature($app)
    {
        //we need to set this in the jwt-auth as it'll get merged into auth as part of the boot
        $app['config']->set('jwt-auth.providers.jwt.signature', 'rsa');
        $key = RSA::createKey();

        file_put_contents($app->config->get('jwt-auth.providers.jwt.public-key'), (string) $key->getPublicKey());
        file_put_contents($app->config->get('jwt-auth.providers.jwt.private-key'), (string) $key);
    }

    protected function useHmacSignature($app)
    {
        $app['config']->set('jwt-auth.providers.jwt.signature', 'hmac');
        $app['config']->set('jwt-auth.providers.jwt.key', Str::random());
    }

    protected function useEcdsaSignature($app)
    {
        $app['config']->set('jwt-auth.providers.jwt.signature', 'ecdsa');
        $key = EC::createKey('nistp521');

        file_put_contents($app->config->get('jwt-auth.providers.jwt.public-key'), (string) $key->getPublicKey());
        file_put_contents($app->config->get('jwt-auth.providers.jwt.private-key'), (string) $key);
    }
}
