<?php

it('publishes the config file', function () {
    $this->artisan('vendor:publish', [
        '--provider' => 'Arrow\JwtAuth\JwtAuthenticationServiceProvider',
        '--tag' => 'jwt-auth-config',
    ])
    ->assertExitCode(0);

    $this->assertFileExists(config_path('jwt-auth.php'));
});

it('can use the command to publish the file', function () {
    expect($this->artisan('jwt-auth:publish:config'))
        ->expectsOutput('The config file has been published to ' . config_path('jwt-auth.php'))
        ->assertExitCode(0);
    $this->assertFileExists(config_path('jwt-auth.php'));
});

it('reads the config file', function () {
    expect(config('jwt-auth.guards.jwt'))
        ->toBe([
            'driver' => 'jwt',
            'provider' => 'jwt',
        ]);
});

it('merges the config file into auth', function () {
    expect(config('auth.guards.jwt'))
        ->toBe([
            'driver' => 'jwt',
            'provider' => 'jwt',
        ]);
});
