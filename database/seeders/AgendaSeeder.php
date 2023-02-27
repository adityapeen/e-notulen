<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Agenda::create([
            "name" => "Rapat Internal Timstra"
        ]);
        Agenda::create([
            "name" => "Rapat Inti BPSDM"
        ]);
        Agenda::create([
            "name" => "Rapat PPK"
        ]);
        Agenda::create([
            "name" => "Rapat Piutang"
        ]);
        Agenda::create([
            "name" => "Rapat Cluster 1"
        ]);
        Agenda::create([
            "name" => "Rapat Cluster 2"
        ]);
        Agenda::create([
            "name" => "Rapat Cluster 3"
        ]);
        Agenda::create([
            "name" => "Rapat Cluster 4"
        ]);
        Agenda::create([
            "name" => "Rapat Bussiness Development"
        ]);
        Agenda::create([
            "name" => "Rapat Marketing"
        ]);
        Agenda::create([
            "name" => "Rapat Progress IP ASN"
        ]);
        Agenda::create([
            "name" => "Rapat Evaluasi"
        ]);
        Agenda::create([
            "name" => "Rapat Progress IT"
        ]);
        Agenda::create([
            "name" => "Rapat Zona Integritas"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran SBP"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran BDT"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran BPA"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran BPB"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran BPE"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran BPG"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran BPM"
        ]);
        Agenda::create([
            "name" => "Rapat 1-on-1 Anggaran BPP"
        ]);
        

    }
}
