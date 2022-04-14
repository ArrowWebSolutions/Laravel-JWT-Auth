<?php

use Arrow\JwtAuth\User;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;

beforeEach(function () {
    $jwtConfig = Configuration::forUnsecuredSigner();
    $token = $jwtConfig
        ->builder()
        ->identifiedBy(Str::random())
        ->relatedTo('123')
        ->getToken($jwtConfig->signer(), $jwtConfig->signingKey());

    $user = new User();
    $user->fromToken($token);
    $this->user = $user;
});

it('can create a user object from a token', function () {
    expect($this->user)->toBeInstanceOf(User::class);
});

it('can get the user auth identifier name', function () {
    expect($this->user->getAuthIdentifierName())->toBe('sub');
});

it('can get the user auth identifier', function () {
    expect($this->user->getAuthIdentifier())->toBe('123');
});

it('can access the sub property directly', function () {
    expect($this->user->sub)->toBe('123');
});

it('can use the id property as an alias for sub', function () {
    expect($this->user->id)->toBe('123');
});

it('can still use the id property when set', function () {
    $this->user->id = '456';
    expect($this->user->id)->toBe('456');
    expect($this->user->sub)->toBe('123');
});
