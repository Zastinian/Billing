<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AffiliateProgram;

class AffiliateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;

        if (is_null(AffiliateProgram::where('key', 'enabled')->first())) {
            AffiliateProgram::create([
                'key' => 'enabled',
                'value' => 'true',
            ]);
            ++$i;
        }

        if (is_null(AffiliateProgram::where('key', 'conversion')->first())) {
            AffiliateProgram::create([
                'key' => 'conversion',
                'value' => '50',
            ]);
            ++$i;
        }
        
        if ($i > 0)
            $this->command->info('Seeded and updated the affiliate_programs table successfully!');
        else
            $this->command->line('Records already exist in the affiliate_programs table. Skipped seeding!');
    }
}
