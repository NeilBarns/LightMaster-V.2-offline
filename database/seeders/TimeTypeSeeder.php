<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('TimeType')->insert([
            ['TimeTypeID' => 1, 'Name' => 'BASE'],
            ['TimeTypeID' => 2, 'Name' => 'INCREMENT'],
            ['TimeTypeID' => 3, 'Name' => 'OPEN'],
        ]);
    }
}
