<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;

        if (Tax::where('country', 'Global')->doesntExist()) {
            Tax::create(['country' => 'Global']);
            ++$i;
        }

        if ($i > 0)
            $this->command->info('Seeded and updated the taxes table successfully!');
        else
            $this->command->line('Records already exist in the taxes table. Skipped seeding!');
    }
}
