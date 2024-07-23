<?php

namespace Extensions\Gateways\MercadoPago;

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

        if (is_null(Extension::where(['extension' => 'MercadoPago', 'key' => 'access_token'])->first())) {
            Extension::create([
                'extension' => 'MercadoPago',
                'key' => 'access_token',
                'value' => null,
            ]);
            ++$i;
        }

        if (is_null(Extension::where(['extension' => 'MercadoPago', 'key' => 'enabled'])->first())) {
            Extension::create([
                'extension' => 'MercadoPago',
                'key' => 'enabled',
                'value' => '0',
            ]);
            ++$i;
        }

        if ($i > 0)
            $this->command->info('Seeded and updated the MercadoPago extension successfully!');
        else
            $this->command->line('Records of MercadoPago extension already exist in the extensions table. Skipped seeding!');
    }
}