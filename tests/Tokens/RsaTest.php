<?php

namespace Arrow\JwtAuth\Tests\Tokens;

use Arrow\JwtAuth\Contracts\JwtConfiguration;
use Arrow\JwtAuth\Tests\TestCase;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;

class RsaTest extends TestCase
{
    protected Configuration $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = $this->app->make(JwtConfiguration::class);
    }

    protected function token(DateTimeImmutable $issuedAt, DateTimeImmutable $canBeUsedAfter, DateTimeImmutable $expiresAt)
    {
        return $this->config->builder()
            ->issuedBy('https://arrow-web.dev')
            ->permittedFor('https://example.com')
            ->identifiedBy(Str::random(12))
            ->issuedAt($issuedAt)
            ->canOnlyBeUsedAfter($canBeUsedAfter)
            ->expiresAt($expiresAt)
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    /**
     * @test
     * @define-env useRsaSignature
     */
    public function we_get_an_unauthorised_without_token()
    {
        $this->getJson('/')
            ->assertUnauthorized();
    }

    /**
     * @test
     * @define-env useRsaSignature
    */
    public function we_get_a_successful_response_with_a_token()
    {
        $now = new DateTimeImmutable();

        $this
            ->withToken($this->token($now, $now, $now->modify('+1 hour'))->toString())
            ->getJson('/')
            ->assertSuccessful();
    }

    /**
     * @test
     * @define-env useRsaSignature
     */
    public function our_token_expires_correctly()
    {
        $now = new DateTimeImmutable();

        $this
            ->withToken($this->token($now->modify('-2 hours'), $now->modify('-2 hours'), $now->modify('-1 hour'))->toString())
            ->getJson('/')
            ->assertUnauthorized();
    }

    /**
     * @test
     * @define-env useRsaSignature
     */
    public function we_cant_use_a_token_before_its_valid()
    {
        $now = new DateTimeImmutable();

        $this
            ->withToken($this->token($now, $now->modify('+1 hour'), $now->modify('+2 hours'))->toString())
            ->getJson('/')
            ->assertUnauthorized();
    }

    /**
     * @test
     * @define-env useRsaSignature
     */
    public function a_valid_jwt_but_wrong_signature_doesnt_work()
    {
        $jwtConfig = Configuration::forUnsecuredSigner();
        $token = $jwtConfig
            ->builder()
            ->issuedBy('https://arrow-web.dev')
            ->permittedFor('https://example.com')
            ->identifiedBy(Str::random(12))
            ->issuedAt(now()->toDateTimeImmutable())
            ->canOnlyBeUsedAfter(now()->toDateTimeImmutable())
            ->expiresAt(now()->addHours(1)->toDateTimeImmutable())
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey());

        $this
            ->withToken($token->toString())
            ->getJson('/')
            ->assertUnauthorized();
    }
}
