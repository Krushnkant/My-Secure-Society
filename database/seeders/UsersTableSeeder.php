<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed a single user
        DB::table('User')->truncate();
        DB::table('User')->insert([
            'UserType' => 1,
            'FullName' => 'Super Admin',
            'MobileNo' => '9909909909',
            'Email' => 'admin@gmail.com',
            'UserCode' => '123456',
            //'EmailVerifiedAt' => Carbon::now(),
            'Password' => bcrypt('admin@123'),
            'Gender' => 2,
            'CreatedAt' => Carbon::now(),
            'CreatedBy' => 1,
            'UpdatedAt' => Carbon::now(),
            'UpdatedBy' => 1,
        ]);
    }
}
