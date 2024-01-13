<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Relative;
use App\Models\RelativeResident;
use App\Models\Resident;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CitiesSeeder::class);
        Resident::factory(100)->create();
        Relative::factory(100)->create();
        RelativeResident::factory(200)->create();
        Visit::factory(100)->create();
        $this->call(SuperAdminSeeder::class);

    }
}
