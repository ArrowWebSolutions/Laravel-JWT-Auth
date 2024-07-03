<?php

namespace Arrow\JwtAuth;

use Lcobucci\JWT\Configuration;
use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;

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

    /**
     * Rehash the user's password if required and supported.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @param  bool  $force
     * @return void
     */
    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        throw new \Exception("Not implemented");
    }
}
