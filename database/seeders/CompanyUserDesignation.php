<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyUserDesignation extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('CompanyUserDesignation')->insert([
            'UserId' => 1,
            'CompanyDesignationId' => 1,
            'UpdatedAt' => Carbon::now(),
            'UpdatedBy' => 1,
        ]);
    }
}
