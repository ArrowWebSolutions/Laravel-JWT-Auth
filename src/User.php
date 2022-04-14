<?php

namespace Arrow\JwtAuth;

use Lcobucci\JWT\UnencryptedToken;
use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    protected array $attributes = [];

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
        return 'sub';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
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

    public function __get($name)
    {
        $name = ($name === 'id' && ! isset($this->attributes[$name])) ? 'sub' : $name;

        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}
