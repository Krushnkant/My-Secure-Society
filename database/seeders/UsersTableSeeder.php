<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed a single user
        User::truncate();
        User::insert([
            'user_type' => 1,
            'full_name' => 'Super Admin',
            'mobile_no' => '9909909909',
            'email' => 'admin@gmail.com',
            'user_code' => '123456',
            //'EmailVerifiedAt' => Carbon::now(),
            'password' => Hash::make('admin@123'),
            'gender' => 2,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
