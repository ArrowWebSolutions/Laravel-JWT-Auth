<?php

namespace Arrow\JwtAuth\Tests\Tokens;

use DateTimeImmutable;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Arrow\JwtAuth\Tests\TestCase;
use Arrow\JwtAuth\Contracts\JwtConfiguration;

class ClaimsTest extends TestCase
{
    protected Configuration $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = $this->app->make(JwtConfiguration::class);
    }

    protected function token(DateTimeImmutable $issuedAt, DateTimeImmutable $canBeUsedAfter, DateTimeImmutable $expiresAt, array $claims)
    {
        $builer = $this->config->builder()
            ->issuedBy('https://arrow-web.dev')
            ->permittedFor('https://example.com')
            ->identifiedBy(Str::random(12))
            ->issuedAt($issuedAt)
            ->canOnlyBeUsedAfter($canBeUsedAfter)
            ->expiresAt($expiresAt);

        foreach ($claims as $name => $value) {
            $builer->withClaim($name, $value);
        }

        return $builer
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    /** @test */
    public function we_get_an_unauthorised_without_token()
    {
        $this->getJson('/user')
            ->assertUnauthorized();
    }

    /**
     * @test
     * @define-env useRsaSignature
    */
    public function we_can_supply_a_simple_claim()
    {
        $now = new DateTimeImmutable();

        $this
            ->withToken($this->token($now, $now, $now->modify('+1 hour'), ['name' => 'Example'])->toString())
            ->getJson('/user')
            ->assertSuccessful()
            ->assertJson(['name' => 'Example']);
    }

    /**
     * @test
     * @define-env useRsaSignature
    */
    public function we_can_supply_a_more_complex_claim()
    {
        $now = new DateTimeImmutable();

        $this
            ->withToken($this->token($now, $now, $now->modify('+1 hour'), ['roles' => ['test1', 'test2'], 'dogs' => ['spaniel' => 'hyper']])->toString())
            ->getJson('/user')
            ->assertSuccessful()
            ->assertJson(['roles' => ['test1', 'test2']])
            ->assertJson(['dogs' => ['spaniel' => 'hyper']]);
    }
}
