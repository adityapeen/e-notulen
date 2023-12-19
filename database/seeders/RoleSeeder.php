<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'Ka. BPSDM']);
        Role::create(['name' => 'Superadmin']);
        Role::create(['name' => 'Ses. BPSDM']);
        Role::create(['name' => 'Kepala Pusat']);
        Role::create(['name' => 'Koordinator Program']);
        Role::create(['name' => 'Sekretaris Kapus']);
        Role::create(['name' => 'Admin Satker']);
        Role::create(['name' => 'Admin Bidang']);
        Role::create(['name' => 'Pegawai']);

        User::where('level_id', 1)->update(['current_role_id' => 1]);
        User::where('level_id', 2)->update(['current_role_id' => 2]);
        User::where('level_id', 3)->update(['current_role_id' => 3]);
        User::where('level_id', 4)->update(['current_role_id' => 4]);
        User::where('level_id', 5)->update(['current_role_id' => 5]);
        User::where('level_id', 6)->update(['current_role_id' => 6]);
        User::where('level_id', 7)->update(['current_role_id' => 7]);
        User::where('level_id', 8)->update(['current_role_id' => 8]);
        User::where('level_id', 9)->update(['current_role_id' => 9]);
    }
}
