<?php

use Illuminate\Support\Facades\Auth;

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

it('sets up the guard correctly', function () {
    expect(Auth::guard('jwt'))
        ->toBeInstanceOf(\Arrow\JwtAuth\Guard::class);
});

it('doesnt break the default gaurd', function () {
    expect(Auth::guard())
        ->toBeInstanceOf(\Illuminate\Auth\SessionGuard::class);
});

it('can change the default gaurd when configured', function () {
    $this->app->config->set('auth.defaults.guard', 'jwt');
    expect(Auth::guard())
        ->toBeInstanceOf(\Arrow\JwtAuth\Guard::class);
});
