<?php

namespace Extensions\Gateways\PayPal;

use App\Models\Extension;
use Illuminate\Database\Seeder as DatabaseSeeder;

class Seeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;

        if (is_null(Extension::where(['extension' => 'PayPal', 'key' => 'mode'])->first())) {
            Extension::create([
                'extension' => 'PayPal',
                'key' => 'mode',
                'value' => 'live',
            ]);
            ++$i;
        }

        if (is_null(Extension::where(['extension' => 'PayPal', 'key' => 'username'])->first())) {
            Extension::create([
                'extension' => 'PayPal',
                'key' => 'username',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Extension::where(['extension' => 'PayPal', 'key' => 'password'])->first())) {
            Extension::create([
                'extension' => 'PayPal',
                'key' => 'password',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Extension::where(['extension' => 'PayPal', 'key' => 'secret'])->first())) {
            Extension::create([
                'extension' => 'PayPal',
                'key' => 'secret',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Extension::where(['extension' => 'PayPal', 'key' => 'certificate'])->first())) {
            Extension::create([
                'extension' => 'PayPal',
                'key' => 'certificate',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Extension::where(['extension' => 'PayPal', 'key' => 'app_id'])->first())) {
            Extension::create([
                'extension' => 'PayPal',
                'key' => 'app_id',
                'value' => null,
            ]);
            ++$i;
        }

        if ($i > 0)
            $this->command->info('Seeded and updated the extensions table for PayPal extension successfully!');
        else
            $this->command->line('Records of PayPal extension already exist in the extensions table. Skipped seeding!');
    }
}
