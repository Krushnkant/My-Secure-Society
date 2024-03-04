<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompanyDesignationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_designation')->truncate();
        $Designation = array(
            array(
                'designation_name' => "System Admin",
                'estatus' => 1,
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'updated_at' => Carbon::now(),
                'updated_by' => 1
            ),
            // array(
            //     'designation_name' => "Sub Admin",
            //     'estatus' => 1,
            //     'CreatedAt' => Carbon::now(),
            //     'CreatedBy' => 1,
            //     'updated_at' => Carbon::now(),
            //     'updated_by' => 1
            // )
        );
        DB::table('company_designation')->insert($Designation);
    }
}
