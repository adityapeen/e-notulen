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
            "name" => "Vebriany Purnamasari" ,
            "email" => "vebriany.purnamasari@esdm.go.id",
            "password" => $default_pass,
            "satker_id" => 1,
            "level_id" => 2,
            "phone" => "082339970974"
        ]);
        User::create([
            "name" => "Dara Kurnia Sari" ,
            "email" => "dara.kurnia@esdm.go.id",
            "password" => $default_pass,
            "satker_id" => 8,
            "level_id" => 2,
            "phone" => "081363517065"
        ]);
        User::create([
            "name" => "Andhika Sinusaroyo" ,
            "email" => "andhika.sinusaroyo@esdm.go.id",
            "password" => $default_pass,
            "satker_id" => 2,
            "level_id" => 2,
            "phone" => "087862390242"
        ]);
        User::create([
            "name" => "Natasha Ruth Ulida Sitorus" ,
            "email" => "natasha.ulida@esdm.go.id",
            "password" => $default_pass,
            "satker_id" => 4,
            "level_id" => 2,
            "phone" => "081362895797"
        ]);
        User::create([
            "name" => "Meylad Fitriana" ,
            "email" => "meylad.f@mail.com",
            "password" => $default_pass,
            "satker_id" => 1,
            "level_id" => 2,
            "phone" => "081327636327"
        ]);
        User::create([
            "name" => "Aditya Pratama N" ,
            "email" => "aditya.nugraha@esdm.go.id",
            "password" => $default_pass,
            "satker_id" => 5,
            "level_id" => 2,
            "phone" => "087738956722"
        ]);
        User::create([
            "name" => "Nidya " ,
            "email" => "sek.sbp@mail.com",
            "password" => $default_pass,
            "satker_id" => 1,
            "level_id" => 5,
            "phone" => "082299833797"
        ]);
        User::create([
            "name" => "Dhafi" ,
            "email" => "sek.bpa@mail.com",
            "password" => $default_pass,
            "satker_id" => 2,
            "level_id" => 5,
            "phone" => "082126737773"
        ]);
        User::create([
            "name" => "Emma " ,
            "email" => "sek.bpm@mail.com",
            "password" => $default_pass,
            "satker_id" => 3,
            "level_id" => 5,
            "phone" => "081228307566"
        ]);
        User::create([
            "name" => "Devi " ,
            "email" => "sek.bpe@mail.com",
            "password" => $default_pass,
            "satker_id" => 4,
            "level_id" => 5,
            "phone" => "081319463191"
        ]);
        User::create([
            "name" => "Tely H " ,
            "email" => "sek.bpg@mail.com",
            "password" => $default_pass,
            "satker_id" => 5,
            "level_id" => 5,
            "phone" => "082116163719"
        ]);
        User::create([
            "name" => "Dewi" ,
            "email" => "sek.bpp@mail.com",
            "password" => $default_pass,
            "satker_id" => 6,
            "level_id" => 5,
            "phone" => "085640344115"
        ]);
        User::create([
            "name" => "Ayu Ratna Wulansari" ,
            "email" => "sek.bpb@mail.com",
            "password" => $default_pass,
            "satker_id" => 7,
            "level_id" => 5,
            "phone" => "082122195330"
        ]);
        User::create([
            "name" => "Suci Dwi H " ,
            "email" => "sek.bdt@mail.com",
            "password" => $default_pass,
            "satker_id" => 8,
            "level_id" => 5,
            "phone" => "081256014576"
        ]);
        
    }
}
