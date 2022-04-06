<?php

return [
    'guards' => [
        'jwt' => [
            'driver' => 'jwt',
            'provider' => 'jwt',
        ],
    ],
    'providers' => [
        'jwt' => [
            'driver' => 'jwt',
            'model' => Arrow\JwtAuth\User::class,
            //can be hmac, rsa or ecdsa
            'signature' => 'rsa',
            //the hash to use, can be sha265, sha384 or sha512
            'hash' => 'sha256',
            //the key used for HMAC signatures
            'key' => 'my-super-secret',
            //path to public key
            'public-key' => storage_path('auth/jwt-public.key'),
            //path to private key, not needed for authentication
            'private-key' => storage_path('auth/jwt-private.key'),
        ],
    ],
];
