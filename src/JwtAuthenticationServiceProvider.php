<?php

namespace Arrow\JwtAuth;

use Lcobucci\JWT\Signer;
use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Key;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Parser as JwtParser;
use Spatie\LaravelPackageTools\Package;
use Arrow\JwtAuth\Commands\Publish\Config;
use Lcobucci\JWT\Signer\Ecdsa\SignatureConverter;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter;

class JwtAuthenticationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-jwt-auth')
            ->hasCommand(Config::class)
            ->hasConfigFile('jwt-auth');
    }

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot()//Request $request, JwtParser $jwtParser, Signer $signer)
    {
        parent::boot();
        if (isset($this->app->config['jwt-auth'])) {
            //merge our config into auth
            $this->app->config['auth'] = array_replace_recursive(
                $this->app->config['auth'],
                $this->app->config['jwt-auth']
            );

            $this->app->bind(Signer::class, $this->getSigner($this->app->config['auth']['providers']['jwt']));
            if ($this->app['config']->get('auth.providers.jwt.signature') === 'ecdsa') {
                $this->app->bind(SignatureConverter::class, MultibyteStringConverter::class);
            }
        } else {
            //temporarily bind to this - it allows us to call vendor:publish
            $this->app->bind(Signer::class, \Lcobucci\JWT\Signer\Hmac\Sha512::class);
        }

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new Guard(Auth::createUserProvider($config['provider']));
        });

        Auth::provider('jwt', function ($app, array $config) {
            $signer = $app->make(Signer::class);
            $jwtParser = $app->make(JwtParser::class);
            $key = $this->getKey($signer, $config);
            return new UserProvider($jwtParser, $signer, $key);
        });
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
        switch (strtoupper(substr($signer->getAlgorithmId(), 0, 2))) {
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
