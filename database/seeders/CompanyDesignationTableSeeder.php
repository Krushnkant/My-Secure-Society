<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyDesignationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('CompanyDesignation')->truncate();
        $Designation = array(
            array(
                'DesignationName' => "System Admin",
                'eStatus' => 1,
                'CreatedAt' => Carbon::now(),
                'CreatedBy' => 1,
                'UpdatedAt' => Carbon::now(),
                'UpdatedBy' => 1
            ),
            // array(
            //     'DesignationName' => "Sub Admin",
            //     'eStatus' => 1,
            //     'CreatedAt' => Carbon::now(),
            //     'CreatedBy' => 1,
            //     'UpdatedAt' => Carbon::now(),
            //     'UpdatedBy' => 1
            // )
        );
        DB::table('CompanyDesignation')->insert($Designation);
    }
}
