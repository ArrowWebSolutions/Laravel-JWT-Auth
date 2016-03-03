<?php

namespace Arrow\JwtAuth;

use Guard;
use UserProvider;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Parser as JwtParser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;

class ServiceProvider extends IlluminateServiceProvider
{

    public function register()
    {
        if (isset($this->app->config['jwt-auth']))
        {
            //merge our config into auth
            $this->app->config['auth'] = array_replace_recursive(
                $this->app->config['auth'],
                $this->app->config['jwt-auth']
            );

            $this->app->bind(Signer::class, $this->getSigner($this->app->config['auth']['providers']['jwt']));
        }
        else
        {
            //temporarily bind to this - it allows us to call vendor:publish
            $this->app->bind(Signer::class, \Lcobucci\JWT\Signer\Hmac\Sha512::class);
        }
    }

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(Request $request, JwtParser $jwtParser, Signer $signer)
    {
        Auth::extend('jwt', function($app, $name, array $config) use ($request) {
            return new JwtGuard(Auth::createUserProvider($config['provider']), $request);
        });

        Auth::provider('jwt', function($app, array $config) use ($jwtParser, $signer) {
            $key = $this->getKey($signer, $config);
            return new JwtUserProvider($jwtParser, $signer, $key);
        });

        $this->publishes([
            __DIR__ . '/config/jwt-auth.php' => config_path('jwt-auth.php'),
        ]);
    }

    /**
     * This is baddd - redo this
     * @return [type] [description]
     */
    protected function getSigner($config)
    {
        $className = "\Lcobucci\JWT\Signer\\" . ucwords($config['signature']) . "\\" . ucwords($config['hash']);
        $func = new \ReflectionClass($className);
        return $func->getName();
    }

    protected function getKey($signer, $config)
    {
        switch (strtoupper(substr($signer->getAlgorithmId(), 0, 2)))
        {
            case "HS":
                return $config['key'];
                break;
            case "RS":
            case "ES":
                return new Key($config['public-key']);
                break;
        }
    }
}
