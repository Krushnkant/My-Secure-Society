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
        DB::table('company_user_designation')->truncate();
        DB::table('company_user_designation')->insert([
            'user_id' => 1,
            'company_designation_id' => 1,
            'updated_at' => Carbon::now(),
            'updated_by' => 1,
        ]);
    }
}
