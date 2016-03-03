# Laravel JWT Auth
A simple method to authorise users in Laravel via a JWT - [http://jwt.io](http://jwt.io)

*This package is in early development, you are more than welcome to use it. But expect rough edges.*

The package doesn't implement a lot of the stuff provided by core Laravel auth - such as registration, password recovery and remember me tokens. Just because it's not applicable to JWT.

## Installation
	composer require arrow-web-sol/laravel-jwt-auth
	
After updating composer, add the ServiceProvider to the providers array in config/app.php:

	Arrow\JwtAuth\ServiceProvider::class,
	
Then run

	php artisan vendor:publish --provider="Arrow\JwtAuth\ServiceProvider"
	
That creates a jwt-config.php config file, in here you can change the signing method used, hash used, key (hmac only) and the public key (rsa and ecdsa). The key or public key is used to verify the JWT.

Let's then change the default auth method, edit `config/auth.php`

	    'defaults' => [
        	'guard' => 'jwt',
        	
Finally, in your controller / routes, you can now call:

	$this->middleware('auth');
	
This will take a JWT from the request header, if it's signature is verified then the user is 'authenticated'.

## Testing
* Create a JWT on [http://jwt.io](http://jwt.io)
* Ensure your `jwt-config.php` contains the same signature/hash/key as used on [http://jwt.io](http://jwt.io)
* Create a controller, using the auth middleware
* Create a route, calling the controller
* Send a request to the controller ([Postman](http://www.getpostman.com/) is great for this)
