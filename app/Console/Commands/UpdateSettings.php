<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class UpdateSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:update:settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update settings to database';

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
        $this->saveSetting('company_name', $this->ask('Company Name', config('app.company_name')));
        $this->saveSetting('store_url', $this->ask('Store URL', config('app.url')));
        $this->saveSetting('panel_url', $this->ask('Panel URL', config('app.panel_url')));
        $this->saveSetting('panel_client_api_key', $this->ask('Panel Client API Key', config('app.panel_client_api_key')));
        $this->saveSetting('panel_app_api_key', $this->ask('Panel Application API Key', config('app.panel_app_api_key')));
        $this->saveSetting('phpmyadmin_url', $this->ask('phpMyAdmin URL', config('app.phpmyadmin_url')));
        $this->saveSetting('hcaptcha_site_key', $this->ask('hCaptcha Site Key', config('app.hcaptcha_site_key')));
        $this->saveSetting('hcaptcha_secret_key', $this->ask('hCaptcha Secret Key', config('app.hcaptcha_secret_key')));

        $this->info('Settings updated successfully!');
    }

    private function saveSetting($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        $setting->value = $value;
        $setting->save();
    }
}
