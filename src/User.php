<?php

namespace Arrow\JwtAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\UnencryptedToken;

class User implements Authenticatable
{
    public function fromToken(UnencryptedToken $token)
    {
        foreach ($token->claims() as $claim) {
            $name = $claim->getName();
            $this->$name = $claim->getValue();
        }

        return $this;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return (string)$this->sub;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getAuthIdentifierName();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return null;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return null;
    }
}
