<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/cities_lite.sql');
        \DB::unprepared(file_get_contents($path));
        $this->command->info('Cities table seeded!');
    }
}
