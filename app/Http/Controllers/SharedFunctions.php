<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SharedFunctions extends Controller
{
    public static function GetNextAvailableIP()
    {
        $startIP = ip2long(env('START_IP'));
        $endIP = ip2long(env('END_IP'));

        $assignedIPs = DB::table('devices')->pluck('IPAddress')->map(function ($ip) {
            return ip2long($ip);
        })->toArray();

        for ($ip = $startIP; $ip <= $endIP; $ip++) {
            if (!in_array($ip, $assignedIPs)) {
                return long2ip($ip);
            }
        }

        return null;
    }
}
