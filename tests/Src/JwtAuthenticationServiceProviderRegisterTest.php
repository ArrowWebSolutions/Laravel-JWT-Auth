<?php
namespace Arrow\JwtAuth\Tests\Src;

use Lcobucci\JWT\Signer;
use Arrow\JwtAuth\Tests\TestCase;

class JwtAuthenticationServiceProviderRegisterTest extends TestCase
{
    /**
     * @test
     * @define-env useRsaSignature
     */
    public function it_binds_to_the_rsa_signer_correctly()
    {
        expect(config('auth.providers.jwt.signature'))->toBe('rsa');
        expect($this->app->make(Signer::class))->toBeInstanceOf(Signer\Rsa::class);
    }

    /**
     * @test
     * @define-env useHmacSignature
     */
    public function it_binds_to_the_hmac_signer_correctly()
    {
        expect(config('auth.providers.jwt.signature'))->toBe('hmac');
        expect($this->app->make(Signer::class))->toBeInstanceOf(Signer\Hmac::class);
    }

    /**
     * @test
     * @define-env useEcdsaSignature
     */
    public function it_binds_to_the_ecdsa_signer_correctly()
    {
        expect(config('auth.providers.jwt.signature'))->toBe('ecdsa');
        expect($this->app->make(Signer::class))->toBeInstanceOf(Signer\Ecdsa::class);
    }
}
