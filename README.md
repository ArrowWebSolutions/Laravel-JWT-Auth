# Laravel JWT Auth
A simple method to authenticate users in Laravel via a JWT - [http://jwt.io](http://jwt.io)

A bearer token is taken from the request, this token is then checked using the configured JWT algorithm. If the token is deemed valid, the the request is "authenticated". This package will check to ensure the token was signed using an appropriate key and is valid at the time of the request (iat, nbf and exp claim checks).

A common use case is in authentication in Laravel micro-services. Your auth service (Passport for example) can issue a JWT on successful login. Your other services can then use this package to authenticate requests without touching your auth service. Because a JWT is cryptographicaly signed you can verify the token originated from your auth service and hasn't been tampered with.

## Installation
	composer require arrow-web-sol/laravel-jwt-auth

Then run

	php artisan jwt-auth:publish:config

That creates a jwt-config.php config file, in here you can set the signing method used, hash used, key (hmac only) and the public key (rsa and ecdsa). The key or public key is used to verify the JWT.

NOTE: the `jwt-config.php` is merged into `auth.php` as part of the service provider boot process. It adds a guard and provider for 'jwt', which in most cases will never be set nor used. But if you do have a 'jwt' guard and or provider in your `auth.php` file then be aware this package will override that key.

## Middleware
To protect routes, you can now use `'auth:jwt'`:
```php
Route::middleware('auth:jwt')->get('/user', [UserController::class, 'index']);
```
Or, to set default auth method, edit `config/auth.php`
```php
	    'defaults' => [
        	'guard' => 'jwt',
```

The package needs the token to be sent as a bearer token in the authorization header.


## Testing
You can use the normal Laravel test methods:
```php
//if jwt isn't your default guard
$this->actingAs($user, 'jwt');

//if jwt is your default guard
$this->actingAs($user);
```

Although we do this as part of our test suite, you can test the full token flow:
```php
//NOTE: You need the private key set in config to do this for asymetric signatures
$jwtConfig = $this->app()->make(\Arrow\JwtAuth\Contracts\JwtConfiguration::class);

$token = $jwtConfig
    ->builder()
    ->issuedBy('https://arrow-web.dev')
    ->permittedFor('https://example.com')
    ->identifiedBy(Str::random(12))
    ->issuedAt(now()->toDateTimeImmutable())
    ->canOnlyBeUsedAfter(now()->toDateTimeImmutable())
    ->expiresAt(now()->addHour()->toDateTimeImmutable())
    ->withClaim('claim-name', 'claim-value')
    ->getToken($jwtConfig->signer(), $jwtConfig()->signingKey());

$this->withToken($token->toString())
    ->getJson('/user')
    ->assertSuccessful();
```
