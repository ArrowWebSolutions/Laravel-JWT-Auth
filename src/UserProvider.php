<?php

namespace Arrow\JwtAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

class UserProvider implements UserProviderContract
{
    public function __construct(protected Configuration $jwtConfig)
    {
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
        $jwt = $this->jwtConfig->parser()->parse($credentials['jwt']);

        try {
            $this->jwtConfig->validator()->assert($jwt, ...$this->jwtConfig->validationConstraints());
        } catch (RequiredConstraintsViolated $e) {
            return null;
        }

        return (new User())->fromToken($jwt);
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
