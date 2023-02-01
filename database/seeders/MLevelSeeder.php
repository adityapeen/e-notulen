<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MLevel;

class MLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MLevel::create([
            "name" => "Ka. BPSDM"
        ]);
        MLevel::create([
            "name" => "Timstra"
        ]);
        MLevel::create([
            "name" => "Ses. BPSDM"
        ]);
        MLevel::create([
            "name" => "Kepala Pusat"
        ]);
        MLevel::create([
            "name" => "Kepala Bagian Umum"
        ]);
        MLevel::create([
            "name" => "Koordinator"
        ]);
        MLevel::create([
            "name" => "Subkoordinator"
        ]);

    }
}
