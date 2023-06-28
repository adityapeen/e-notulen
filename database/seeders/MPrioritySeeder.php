<?php

namespace Database\Seeders;

use App\Models\MPriority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MPriority::create([
            "id" => 1,
            "name" => "Utama"
        ]);
        MPriority::create([
            "id" => 2,
            "name" => "Tinggi"
        ]);
        MPriority::create([
            "id" => 3,
            "name" => "Normal"
        ]);
        MPriority::create([
            "id" => 4,
            "name" => "Rendah"
        ]);
    }
}
