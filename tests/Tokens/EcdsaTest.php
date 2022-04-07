<?php

namespace Arrow\JwtAuth\Tests\Tokens;

use DateTimeImmutable;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Arrow\JwtAuth\Tests\TestCase;
use Arrow\JwtAuth\Contracts\JwtConfiguration;

class EcdsaTest extends TestCase
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

    /** @test */
    public function we_get_an_unauthorised_without_token()
    {
        $this->getJson('/')
            ->assertUnauthorized();
    }

    /**
     * @test
     * @define-env useEcdsaSignature
    */
    public function we_get_a_successful_response_with_a_token()
    {
        $now = new DateTimeImmutable();

        $this
            ->withToken($this->token($now, $now, $now->modify('+1 hour'))->toString())
            ->getJson('/')
            ->assertSuccessful();
    }
}
