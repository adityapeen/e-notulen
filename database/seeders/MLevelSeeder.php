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
        // #1
        MLevel::create([
            "name" => "Ka. BPSDM"
        ]);
        // #2
        MLevel::create([
            "name" => "Tim Strategis / Superadmin"
        ]);
        // #3
        MLevel::create([
            "name" => "Ses. BPSDM"
        ]);
        // #4
        MLevel::create([
            "name" => "Kepala Pusat"
        ]);
        // #5
        MLevel::create([
            "name" => "Koordinator Program"
        ]);
        // #6
        MLevel::create([
            "name" => "Sekretaris Kapus"
        ]);
        // #7
        MLevel::create([
            "name" => "Admin Satker"
        ]);
        // #8
        MLevel::create([
            "name" => "Admin Bidang / Pokja"
        ]);
        // #9
        MLevel::create([
            "name" => "Pegawai"
        ]);

    }
}
