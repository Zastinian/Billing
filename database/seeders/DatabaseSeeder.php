<?php

namespace Database\Seeders;

use Extensions\ExtensionManager;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(array_merge([
            AffiliateSeeder::class,
            CurrencySeeder::class,
            TaxSeeder::class,
            SettingSeeder::class,
            PageSeeder::class,
        ], ExtensionManager::getAllSeeders()));
    }
}
