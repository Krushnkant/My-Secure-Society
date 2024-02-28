<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed a single user
        DB::table('users')->insert([
            'UserType' => 1,
            'FirstName' => 'Main',
            'MiddleName' => 'Super',
            'LastName' => 'Admin',
            'MobileNo' => '9909909909',
            'Email' => 'admin@gmail.com',
            'EmailVerifiedAt' => Carbon::now(),
            'Password' => bcrypt('admin@123'),
            'Gender' => 2,
            'CreatedAt' => Carbon::now(),
            'CreatedBy' => 1,
            'UpdatedAt' => Carbon::now(),
            'UpdatedBy' => 1,
        ]);
    }
}
