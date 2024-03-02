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
            CityTableSeeder::class,
            CompanyDesignationTableSeeder::class,
            CompanyDesignationAuthorityTableSeeder::class,
            company_user_designationTableSeeder::class,
            CountryTableSeeder::class,
            StateTableSeeder::class,
            UsersTableSeeder::class,
            // Add other seeder classes here
        ]);
        
    }
}
