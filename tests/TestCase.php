<?php

namespace Arrow\JwtAuth\Tests;

use Arrow\JwtAuth\JwtAuthenticationServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;

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
                return response()->json($request->user());
            });
        });
    }

    protected function useRsaSignature($app)
    {
        //we need to set this in the jwt-auth as it'll get merged into auth as part of the boot
        $app['config']->set('jwt-auth.providers.jwt.signature', 'rsa');
        $key = RSA::createKey();
        $this->putKey((string) $key->getPublicKey(), (string)$key, $app);
    }

    protected function useHmacSignature($app)
    {
        $app['config']->set('jwt-auth.providers.jwt.signature', 'hmac');
        $app['config']->set('jwt-auth.providers.jwt.key', Str::random());
    }

    protected function useEcdsaSignature($app)
    {
        $app['config']->set('jwt-auth.providers.jwt.signature', 'ecdsa');
        $this->putKey(<<<EOK
-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAE7it/EKmcv9bfpcV1fBreLMRXxWpn
d0wxa2iFruiI2tsEdGFTLTsyU+GeRqC7zN0aTnTQajarUylKJ3UWr/r1kg==
-----END PUBLIC KEY-----
EOK
        , <<<EOK
-----BEGIN EC PRIVATE KEY-----
MHcCAQEEIBGpMoZJ64MMSzuo5JbmXpf9V4qSWdLIl/8RmJLcfn/qoAoGCCqGSM49
AwEHoUQDQgAE7it/EKmcv9bfpcV1fBreLMRXxWpnd0wxa2iFruiI2tsEdGFTLTsy
U+GeRqC7zN0aTnTQajarUylKJ3UWr/r1kg==
-----END EC PRIVATE KEY-----
EOK
        , $app);
    }

    protected function putKey($publicKey, $privateKey, $app)
    {
        if (! file_exists(dirname($app->config->get('jwt-auth.providers.jwt.public-key')))) {
            mkdir(dirname($app->config->get('jwt-auth.providers.jwt.public-key')));
        }
        file_put_contents($app->config->get('jwt-auth.providers.jwt.public-key'), $publicKey);
        file_put_contents($app->config->get('jwt-auth.providers.jwt.private-key'), $privateKey);
    }
}
