<?php

namespace Arrow\JwtAuth;

use Arrow\JwtAuth\Commands\Publish\Config;
use Arrow\JwtAuth\Contracts\JwtConfiguration;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Parser as JwtParser;
use Lcobucci\JWT\Validation\Constraint;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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

        //merge our config into auth
        $this->app->config['auth'] = array_replace_recursive(
            $this->app->config['auth'],
            $this->app->config['jwt-auth']
        );

        $this->app->singleton(JwtConfiguration::class, function () {
            $config = $this->app->config->get('auth.providers.jwt');
            $signer = $this->getSigner($config);
            $jwtConfig = null;
            if ($config['signature'] === 'hmac') {
                $jwtConfig = Configuration::forSymmetricSigner(
                    $signer,
                    InMemory::plainText($this->getKey($signer, $config))
                );
            } else {
                $jwtConfig = Configuration::forAsymmetricSigner(
                    $signer,
                    file_exists($config['private-key']) ? InMemory::file($config['private-key']) : InMemory::empty(),
                    file_exists($config['public-key']) ? InMemory::file($config['public-key']) : InMemory::empty()
                );
            }

            $jwtConfig->setValidationConstraints(
                //10 second leeway, this deals with testing and setting up the constraint before the token is generated
                new Constraint\StrictValidAt(new FrozenClock(now()->toDateTimeImmutable()), DateInterval::createFromDateString('10 seconds')),
            );

            return $jwtConfig;
        });

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new Guard(Auth::createUserProvider($config['provider']));
        });

        Auth::provider('jwt', function ($app, array $config) {
            return new UserProvider($app->make(JwtConfiguration::class));
        });
    }

    /**
     * This is baddd - redo this
     * @return [type] [description]
     */
    protected function getSigner($config): Signer
    {
        $className = "\Lcobucci\JWT\Signer\\" . ucwords($config['signature']) . "\\" . ucwords($config['hash']);
        $func = new \ReflectionClass($className);
        if ($config['signature'] === 'ecdsa') {
            return call_user_func($func->getName() . '::create');
        }

        return app()->make($func->getName());
    }

    protected function getKey($signer, $config)
    {
        switch (strtoupper(substr($signer->algorithmId(), 0, 2))) {
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
