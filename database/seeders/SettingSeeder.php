<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;

        if (is_null(Setting::where('key', 'company_name')->first())) {
            Setting::create([
                'key' => 'company_name',
                'value' => 'Company Name',
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'store_url')->first())) {
            Setting::create([
                'key' => 'store_url',
                'value' => 'https://example.com',
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'logo_path')->first())) {
            Setting::create([
                'key' => 'logo_path',
                'value' => '/img/icon.webp',
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'favicon_path')->first())) {
            Setting::create([
                'key' => 'favicon_path',
                'value' => '/img/favicon.webp',
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'dark_mode')->first())) {
            Setting::create([
                'key' => 'dark_mode',
                'value' => 'true',
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'open_registration')->first())) {
            Setting::create([
                'key' => 'open_registration',
                'value' => 'true',
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'panel_url')->first())) {
            Setting::create([
                'key' => 'panel_url',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'panel_client_api_key')->first())) {
            Setting::create([
                'key' => 'panel_client_api_key',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'panel_app_api_key')->first())) {
            Setting::create([
                'key' => 'panel_app_api_key',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'phpmyadmin_url')->first())) {
            Setting::create([
                'key' => 'phpmyadmin_url',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'hcaptcha_site_key')->first())) {
            Setting::create([
                'key' => 'hcaptcha_site_key',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'hcaptcha_secret_key')->first())) {
            Setting::create([
                'key' => 'hcaptcha_secret_key',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Setting::where('key', 'google_analytics_id')->first())) {
            Setting::create([
                'key' => 'google_analytics_id',
                'value' => null,
            ]);
            ++$i;
        }

        if ($i > 0)
            $this->command->info('Seeded and updated the settings table successfully!');
        else
            $this->command->line('Records already exist in the settings table. Skipped seeding!');
    }
}
