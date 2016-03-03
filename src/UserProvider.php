<?php

namespace Arrow\JwtAuth;

use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\Parser as JwtParser;
use Lcobucci\JWT\Signer;

class UserProvider implements UserProviderContract
{
    protected $jwtParser, $signer, $key;

    public function __construct(JwtParser $jwtParser, Signer $signer, $key)
    {
        $this->key = $key;
        $this->jwtParser = $jwtParser;
        $this->signer = $signer;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        throw new \Exception("Not implemented");
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new \Exception("Not implemented");
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new \Exception("Not implemented");
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $jwt = $this->jwtParser->parse($credentials['jwt']);

        if ($jwt->verify($this->signer, $this->key))
        {
            //maybe need to do some validation on the fields?
            //
            //valid jwt, so everything is good
            return ((new User)->fromToken($jwt));
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new \Exception("Not implemented");
    }
}