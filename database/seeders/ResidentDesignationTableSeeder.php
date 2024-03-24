<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResidentDesignationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('resident_designation')->truncate();
        $designations = [
            [
                'society_id' => 0,
                'designation_name' => 'Society Admin',
                'can_update_authority_claims' => 2,
                'estatus' => 1,
                'created_by' => 1, 
                'updated_by' => 1, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'society_id' => 0,
                'designation_name' => 'Committee Member',
                'can_update_authority_claims' => 2,
                'estatus' => 1,
                'created_by' => 1, 
                'updated_by' => 1, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'society_id' => 0,
                'designation_name' => 'Society Member',
                'can_update_authority_claims' => 2,
                'estatus' => 1,
                'created_by' => 1, 
                'updated_by' => 1, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('resident_designation')->insert($designations);
    }
}
