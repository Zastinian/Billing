<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;

        if (count(Currency::all()) == 0) {
            Currency::create([
                'name' => 'USD',
                'symbol' => '&#36;',
                'rate' => 1,
                'precision' => 2,
                'default' => true,
            ]);
            ++$i;
        }

        if ($i > 0)
            $this->command->info('Seeded and updated the currencies table successfully!');
        else
            $this->command->line('Records already exist in the currencies table. Skipped seeding!');
    }
}
