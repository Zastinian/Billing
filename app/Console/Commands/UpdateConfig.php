<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:update:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update .env file';

    private $result = null;
    private $status = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->bash('if [ ! -f .env ]; then cp .env.example .env; fi');

        $this->editEnv('DB_HOST', $this->ask('MySQL/MariaDB Host', config('database.connections.mysql.host')));
        $this->editEnv('DB_PORT', $this->ask('Database Port', config('database.connections.mysql.port')));
        $this->editEnv('DB_DATABASE', $this->ask('Database Name', config('database.connections.mysql.database')));
        $this->editEnv('DB_USERNAME', $this->ask('Database Username', config('database.connections.mysql.username')));
        $this->editEnv('DB_PASSWORD', $this->secret('Database Password'));

        $this->editEnv('MAIL_HOST', $this->ask('SMTP Host', config('mail.mailers.smtp.host')));
        $this->editEnv('MAIL_PORT', $this->ask('SMTP Port', config('mail.mailers.smtp.port')));
        $this->editEnv('MAIL_USERNAME', $this->ask('SMTP Username', config('mail.mailers.smtp.username')));
        $this->editEnv('MAIL_PASSWORD', $this->secret('SMTP Password'));
        $this->editEnv('MAIL_ENCRYPTION', $this->choice('SMTP Encryption', ['ssl', 'tls'], config('mail.mailers.smtp.encryption')));
        $this->editEnv('MAIL_FROM_ADDRESS', $this->ask('Email From Address', config('mail.from.address')));
        $this->editEnv('MAIL_FROM_NAME', $this->ask('Email From Name', config('mail.from.name')));

        $this->editEnv('REDIS_HOST', $this->ask('Redis Host', config('database.redis.default.host')));
        $this->editEnv('REDIS_PORT', $this->ask('Redis Port', config('database.redis.default.port')));
        $this->editEnv('REDIS_PASSWORD', $this->secret('Redis Password'));

        $this->info('Configurations updated successfully!');
    }

    private function editEnv($key, $value)
    {
        $this->bash("sed -i '/^${key}=/c\\${key}=${value}' .env");
    }

    private function bash($cmd)
    {
        $exec = exec($cmd, $this->result, $this->status);

        if ($this->status !== 0) {
            foreach ($this->result as $line) {
                $this->error($line);
            }
        } elseif ($exec === false) {
            $this->error('Failed to execute bash command!');
        }

        $this->result = $this->status = null;
    }
}
