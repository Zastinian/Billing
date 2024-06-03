<?php

namespace App\Console\Commands;

use App\Jobs\CreatePanelUser;
use App\Models\Client;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:make:client {email} {--admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a client or admin account';

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
        $validator = Validator::make([
            'email' => $email = $this->argument('email'),
            'password' => $password = $this->secret('Password'),
            'password_confirmation' => $this->secret('Confirm Password'),
        ], [
            'email' => 'required|string|email|unique:clients',
            'password' => 'required|string|confirmed|min:8|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) $this->error($error);
            return;
        }

        $client = Client::create([
            'email' => $email,
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'password' => Hash::make($password),
            'currency' => Currency::where('default', true)->value('name'),
            'country' => 'Global',
            'is_admin' => $this->option('admin'),
        ]);

        CreatePanelUser::dispatch($client)->onQueue('high');

        return $this->info('Account created successfully!');
    }
}
