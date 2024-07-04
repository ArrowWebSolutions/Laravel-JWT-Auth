<?php

namespace Arrow\JwtAuth;

use DateInterval;
use Lcobucci\JWT\Signer;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint;
use Spatie\LaravelPackageTools\Package;
use Arrow\JwtAuth\Commands\Publish\Config;
use Arrow\JwtAuth\Contracts\JwtConfiguration;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Support\Str;

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
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //merge our config into auth
        $this->app['config']['auth'] = array_replace_recursive(
            $this->app['config']['auth'],
            $this->app['config']['jwt-auth']
        );

        $this->app->bind(JwtConfiguration::class, function ($app) {
            $config = $app['config']->get('auth.providers.jwt');
            $signer = $this->getSigner($config);
            $jwtConfig = null;
            if ($config['signature'] === 'hmac') {
                $jwtConfig = Configuration::forSymmetricSigner(
                    $signer,
                    InMemory::plainText($config['key'])
                );
            } else {
                $jwtConfig = Configuration::forAsymmetricSigner(
                    $signer,
                    file_exists($config['private-key']) ? InMemory::file($config['private-key']) : InMemory::plainText(Str::random()),
                    file_exists($config['public-key']) ? InMemory::file($config['public-key']) : InMemory::plainText(Str::random()),
                );
            }

            $jwtConfig->setValidationConstraints(
                new Constraint\SignedWith($jwtConfig->signer(), $jwtConfig->verificationKey()),
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
     * @return Signer gets a signer based on the config
     */
    protected function getSigner($config): Signer
    {
        $className = "\Lcobucci\JWT\Signer\\" . ucwords($config['signature']) . "\\" . ucwords($config['hash']);
        $func = new \ReflectionClass($className);

        return app()->make($func->getName());
    }
}
