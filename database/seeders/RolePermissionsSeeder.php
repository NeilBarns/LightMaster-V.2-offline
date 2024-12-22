<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('RolePermissions')->insert([
            ['RoleID' => 1, 'PermissionID' => 1],
            ['RoleId' => 1, 'PermissionID' => 2],
            ['RoleId' => 1, 'PermissionID' => 3],
            ['RoleId' => 1, 'PermissionID' => 4]
        ]);
    }
}
