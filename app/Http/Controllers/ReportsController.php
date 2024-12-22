<?php

namespace App\Http\Controllers;

use App\Enums\LogEntityEnum;
use App\Enums\LogTypeEnum;
use App\Enums\TimeTransactionTypeEnum;
use App\Models\Device;
use App\Models\DeviceTime;
use App\Models\DeviceTimeTransactions;
use App\Models\RptDeviceTimeTransactions;
use App\Models\Users;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function GetDailyUsage($deviceID)
    {
        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentMonthName = $today->format('M'); //Get the current month name

        $dailyUsage = RptDeviceTimeTransactions::where('DeviceID', $deviceID)
            ->where('TransactionType', TimeTransactionTypeEnum::END)
            ->whereMonth('Time', $currentMonth)
            ->selectRaw('DAY(Time) as day, SUM(Duration) / 60 as totalDuration, SUM(Rate) as totalRate')
            ->groupBy('day')
            ->get();
        $data = $dailyUsage->mapWithKeys(function ($item) use ($currentMonthName) {
            return ["{$currentMonthName}-{$item->day}" => ['totalDuration' => $item->totalDuration, 'totalRate' => $item->totalRate]];
        });

        return response()->json($data);
    }

    public function GetMonthlyUsage($deviceID)
    {
        $monthlyUsage = RptDeviceTimeTransactions::where('DeviceID', $deviceID)
            ->where('TransactionType', TimeTransactionTypeEnum::END)
            ->selectRaw('MONTH(Time) as month, SUM(Duration) / 60 as totalDuration, SUM(Rate) as totalRate')
            ->groupBy('month')
            ->get();

        $data = $monthlyUsage->mapWithKeys(function ($item) {
            $monthName = Carbon::createFromFormat('m', $item->month)->format('F'); // Converts month number to month name
            return [$monthName => ['totalDuration' => $item->totalDuration, 'totalRate' => $item->totalRate]];
        });

        return response()->json($data);
    }

    public function GetFinanceReports()
    {
        $devices = Device::with(['deviceTimeTransactions' => function ($query) {
            $query->selectRaw('DeviceID, MONTH(created_at) as month, SUM(rate) as total_rate, SUM(duration) as total_usage')
                ->groupBy('DeviceID', 'month');
        }])->get();

        $data = $devices->map(function ($device) {
            // Create an array with 12 months initialized to zero
            $monthlyRates = array_fill(0, 12, 0);
            $monthlyUsage = array_fill(0, 12, 0);

            foreach ($device->deviceTimeTransactions as $transaction) {
                $monthIndex = $transaction->month - 1; // Convert month to zero-indexed
                $monthlyRates[$monthIndex] = $transaction->total_rate;
                $monthlyUsage[$monthIndex] = $transaction->total_usage / 60;
            }

            return [
                'name' => $device->ExternalDeviceName,
                'monthlyRates' => $monthlyRates,
                'monthlyUsage' => $monthlyUsage,
            ];
        });

        // Fetch users for the "Triggered By" filter
        $users = Users::all(); // Adjust as necessary to match your user model

        $rptDeviceTimeTransactions = RptDeviceTimeTransactions::whereDate('Time', '>=', Carbon::today()->subDays(1))
            ->whereDate('Time', '<=', Carbon::today())
            ->with('creator', 'device') // Make sure 'device' is loaded
            ->get();

        return view('financial-reports', compact('data', 'rptDeviceTimeTransactions', 'devices', 'users'));
    }



    public function GetRptTimeTransactions($id)
    {
        $device = Device::with('deviceStatus')->findOrFail($id);
        $baseTime = DeviceTime::where('DeviceID', $id)->where('TimeTypeID', DeviceTime::TIME_TYPE_BASE)->first();
        $deviceTimes = DeviceTime::where('DeviceID', $id)->where('TimeTypeID', DeviceTime::TIME_TYPE_INCREMENT)->get();

        $deviceTimeTransactions = DeviceTimeTransactions::where('DeviceID', $id)->where('Active', true)->get();

        $totalTime = $deviceTimeTransactions->sum('Duration');
        $totalRate = $deviceTimeTransactions->sum('Rate');

        $rptDeviceTimeTransactions = RptDeviceTimeTransactions::where('DeviceID', $id)
            ->whereDate('Time', Carbon::now())
            ->with('creator')
            ->get();
    }

    public function GetFilteredOverviewTransactions(Request $request)
    {
        try {
            // Retrieve filters from request
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $deviceNames = $request->input('deviceNames', []);

            // Ensure deviceNames are arrays
            if (!is_array($deviceNames)) {
                $deviceNames = explode(',', $deviceNames);
            }

            // Query to fetch transactions
            $transactions = RptDeviceTimeTransactions::with(['device', 'creator'])
                ->when($startDate, function ($query, $startDate) {
                    return $query->whereDate('Time', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    return $query->whereDate('Time', '<=', $endDate);
                })
                ->when($deviceNames, function ($query, $deviceNames) {
                    return $query->whereHas('device', function ($query) use ($deviceNames) {
                        $query->whereIn('ExternalDeviceName', $deviceNames);
                    });
                })
                ->orderBy('DeviceID', 'asc') // Order by DeviceID first
                ->orderBy('Time', 'asc') // Then order by Time to ensure proper session grouping
                ->get();

            // Group and consolidate transactions into sessions
            $sessions = [];
            $currentSessionId = null;

            foreach ($transactions as $transaction) {
                $deviceId = $transaction->DeviceID;

                // Check if a new session should start
                if ($transaction->TransactionType == 'Start' || !isset($sessions[$currentSessionId])) {
                    $currentSessionId = $transaction->DeviceTimeTransactionsID;
                    $sessions[$currentSessionId] = [
                        'deviceName' => $transaction->device->ExternalDeviceName ?? 'N/A',
                        'startTime' => $transaction->Time,
                        'endTime' => null,
                        'isOpenTime' => $transaction->IsOpenTime,
                        'totalDuration' => $transaction->Duration,
                        'totalRate' => $transaction->Rate,
                        'transactions' => [$transaction],
                    ];
                } elseif ($transaction->TransactionType == 'Extend' && isset($sessions[$currentSessionId])) {
                    $sessions[$currentSessionId]['totalDuration'] += $transaction->Duration;
                    $sessions[$currentSessionId]['totalRate'] += $transaction->Rate;
                    $sessions[$currentSessionId]['transactions'][] = $transaction;
                } elseif ($transaction->TransactionType == 'End' && isset($sessions[$currentSessionId])) {
                    $sessions[$currentSessionId]['endTime'] = $transaction->Time;
                    $sessions[$currentSessionId]['transactions'][] = $transaction;
                    $currentSessionId = null; // End the current session
                }
            }

            // Convert sessions to array format for JSON response
            $sessionsArray = array_values($sessions);
            return response()->json(['sessions' => $sessionsArray], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching transactions:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch transactions'], 500);
        }
    }


    public function GetFilteredDetailedTransactions(Request $request)
    {
        try {
            // Retrieve filters from request
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $deviceNames = $request->input('deviceNames', []);
            $transactionTypes = $request->input('transactionTypes', []);
            $triggeredBy = $request->input('triggeredBy', []);

            // Ensure deviceNames, transactionTypes, and triggeredBy are arrays
            if (!is_array($deviceNames)) {
                $deviceNames = explode(',', $deviceNames);
            }
            if (!is_array($transactionTypes)) {
                $transactionTypes = explode(',', $transactionTypes);
            }
            if (!is_array($triggeredBy)) {
                $triggeredBy = explode(',', $triggeredBy);
            }

            // Check if 'Device' is included in the triggeredBy list
            $includeDevice = in_array('Device', $triggeredBy);

            // Remove 'Device' from the list since it's handled separately
            $triggeredBy = array_diff($triggeredBy, ['Device']);

            // Convert user names to IDs, except for the special 'Device' case
            $userIds = Users::whereIn(DB::raw('CONCAT(FirstName, " ", LastName)'), $triggeredBy)
                ->pluck('UserID')
                ->toArray();

            // Include device user ID if needed
            if ($includeDevice) {
                $userIds[] = 999999;
            }

            // Build query with filters
            $query = RptDeviceTimeTransactions::with(['device', 'creator'])
                ->when($startDate, function ($query, $startDate) {
                    return $query->whereDate('Time', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    return $query->whereDate('Time', '<=', $endDate);
                })
                ->when($deviceNames, function ($query, $deviceNames) {
                    return $query->whereHas('device', function ($query) use ($deviceNames) {
                        $query->whereIn('ExternalDeviceName', $deviceNames);
                    });
                })
                ->when($transactionTypes, function ($query, $transactionTypes) {
                    return $query->whereIn('TransactionType', $transactionTypes);
                })
                ->when($userIds, function ($query, $userIds) {
                    return $query->whereIn('CreatedByUserId', $userIds);
                })
                ->get();

            return response()->json($query, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching transactions:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch transactions'], 500);
        }
    }
}
