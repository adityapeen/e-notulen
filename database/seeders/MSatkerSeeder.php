<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MSatker;

class MSatkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MSatker::create([
            "code" => "SBP",
            "name" => "Sekretariat BPSDM"
        ]);
        MSatker::create([
            "code" => "BPA",
            "name" => "PPSDM Aparatur"
        ]);
        MSatker::create([
            "code" => "BPM",
            "name" => "PPSDM Migas"
        ]);
        MSatker::create([
            "code" => "BPE",
            "name" => "PPSDM KEBTKE"
        ]);
        MSatker::create([
            "code" => "BPG",
            "name" => "PPSDM Geominerba"
        ]);
        MSatker::create([
            "code" => "BPP",
            "name" => "PEM Akamigas"
        ]);
        MSatker::create([
            "code" => "BPB",
            "name" => "PEP Bandung"
        ]);
        MSatker::create([
            "code" => "BDT",
            "name" => "BDTBT Sawahlunto"
        ]);
    }
}
