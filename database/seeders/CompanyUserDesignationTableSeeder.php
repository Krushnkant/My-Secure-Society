<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompanyUserDesignationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('CompanyUserDesignation')->truncate();
        DB::table('CompanyUserDesignation')->insert([
            'UserId' => 1,
            'CompanyDesignationId' => 1,
            'UpdatedAt' => Carbon::now(),
            'UpdatedBy' => 1,
        ]);
    }
}
