<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceTimeTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LongPollingController extends Controller
{
    public function GetActiveTransactions()
    {
        try {
            // Group transactions by DeviceID, sum durations, and select the earliest StartTime
            $activeTransactions = DeviceTimeTransactions::select('DeviceID', DB::raw('SUM(Duration) as totalDuration'), DB::raw('MIN(StartTime) as StartTime'))
                ->where('Active', true)
                ->groupBy('DeviceID')
                ->get();

            // Return the result as JSON
            return response()->json($activeTransactions);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
