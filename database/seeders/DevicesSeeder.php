<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DevicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('Devices')->insert([
            [
                'DeviceName' => 'Device 1',
                'Description' => 'First device description',
                'DeviceStatusID' => 1,
                'OperationDate' => null,
                'IPAddress' => '192.168.1.101',
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ],
            [
                'DeviceName' => 'Device 2',
                'Description' => 'Second device description',
                'DeviceStatusID' => 3,
                'OperationDate' => Carbon::now('Asia/Manila'),
                'IPAddress' => '192.168.1.102',
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ],
            [
                'DeviceName' => 'Device 3',
                'Description' => 'Third device description',
                'DeviceStatusID' => 3,
                'OperationDate' => Carbon::now('Asia/Manila'),
                'IPAddress' => '192.168.1.103',
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ],
            [
                'DeviceName' => 'Device 4',
                'Description' => 'Third device description',
                'DeviceStatusID' => 4,
                'OperationDate' => Carbon::now('Asia/Manila'),
                'IPAddress' => '192.168.1.104',
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ],
        ]);
    }
}
