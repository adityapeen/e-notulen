<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_pass = Hash::make("12345");
        User::create([
            "name" => "Superadmin" ,
            "email" => "superadmin@mail.com",
            "password" => $default_pass,
            "satker_id" => 1,
            "level_id" => 2,
            "phone" => "08123456789",
        ]);        
    }
}
