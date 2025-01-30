<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QueueStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('QueueStatus')->insert([
            ['QueueStatusID' => 1, 'QueueStatusName' => 'Active'],
            ['QueueStatusID' => 2, 'QueueStatusName' => 'Completed'],
            ['QueueStatusID' => 3, 'QueueStatusName' => 'Error'],
            ['QueueStatusID' => 4, 'QueueStatusName' => 'Pending'],
        ]);
    }
}
