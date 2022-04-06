<?php

namespace Arrow\JwtAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\UnencryptedToken;

class User implements Authenticatable
{
    protected $sub;

    public function fromToken(UnencryptedToken $token)
    {
        foreach ($token->claims()->all() as $name => $value) {
            $this->$name = $value;
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
        return '';
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return '';
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
        return '';
    }
}
