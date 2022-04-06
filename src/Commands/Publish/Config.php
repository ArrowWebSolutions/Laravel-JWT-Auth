<?php
namespace Arrow\JwtAuth\Commands\Publish;

use Illuminate\Console\Command;

class Config extends Command
{
    public $signature = 'jwt-auth:publish:config';
    public $description = 'Publish the config file for the JWT Auth package';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--provider' => 'Arrow\JwtAuth\JwtAuthenticationServiceProvider',
            '--tag' => 'jwt-auth-config',
        ]);

        $this->info("The config file has been published to " . config_path('jwt-auth.php'));

        return self::SUCCESS;
    }
}
