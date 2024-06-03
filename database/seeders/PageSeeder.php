<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;

        if (is_null(Page::where('name', 'home')->first())) {
            Page::create([
                'name' => 'home',
                'content' => "<h1>Welcome to your new store.</h1>\n<p>This is the home page. You may edit this page in the admin area.</p>",
            ]);
            ++$i;
        }

        if (is_null(Page::where('name', 'contact')->first())) {
            Page::create([
                'name' => 'contact',
                'content' => null,
            ]);
            ++$i;
        }

        if (is_null(Page::where('name', 'status')->first())) {
            Page::create([
                'name' => 'status',
                'content' => null,
            ]);
            ++$i;
        }

        if (is_null(Page::where('name', 'terms')->first())) {
            Page::create([
                'name' => 'terms',
                'content' => "<h1>Welcome to your Terms of Service page.</h1>\n<p>You may edit this page in the admin area.</p>",
            ]);
            ++$i;
        }

        if (is_null(Page::where('name', 'privacy')->first())) {
            Page::create([
                'name' => 'privacy',
                'content' => "<h1>Welcome to your Privacy Policy page.</h1>\n<p>You may edit this page in the admin area.</p>",
            ]);
            ++$i;
        }

        if ($i > 0)
            $this->command->info('Seeded and updated the pages table successfully!');
        else
            $this->command->line('Records already exist in the pages table. Skipped seeding!');
    }
}
