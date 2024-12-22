<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('DeviceStatus')->insert([
            ['DeviceStatusID' => 1, 'Status' => 'Pending Configuration'],
            ['DeviceStatusID' => 2, 'Status' => 'Running'],
            ['DeviceStatusID' => 3, 'Status' => 'Inactive'],
            ['DeviceStatusID' => 4, 'Status' => 'Disabled'],
            ['DeviceStatusID' => 5, 'Status' => 'Pause'],
            ['DeviceStatusID' => 6, 'Status' => 'Resume'],
            ['DeviceStatusID' => 7, 'Status' => 'Start Free'],
            ['DeviceStatusID' => 8, 'Status' => 'End Free'],
        ]);
    }
}
