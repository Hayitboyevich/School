<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BookAuthorSeeder::class,
            GenreSeeder::class,
            AcademicYearSeeder::class,
            DirectionSeeder::class,
            CountrySeeder::class,
            RegionSeeder::class,
            CitySeeder::class,
            BookSeeder::class,
        ]);
    }
}
