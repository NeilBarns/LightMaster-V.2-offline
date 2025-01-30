<?php

namespace App\Http\Controllers;

use App\Enums\DeviceStatusEnum;
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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportsController extends Controller
{
    public function GetDailyUsageByDevice($deviceID)
    {
        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentMonthName = $today->format('F'); // Get the full name of the current month

        $dailyUsage = DeviceTimeTransactions::where('DeviceID', $deviceID)
            ->whereNotNull('TransactionDateTime')
            ->whereNotNull('Duration')
            ->whereNotNull('Rate')
            ->whereMonth('TransactionDateTime', $currentMonth)
            ->selectRaw('DAY(TransactionDateTime) as day, SUM(Duration) / 60 as totalDuration, SUM(Rate) as totalRate')
            ->groupBy('day')
            ->get();

        // Check if data is empty
        if ($dailyUsage->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data found for the current month.']);
        }

        $data = $dailyUsage->mapWithKeys(function ($item) use ($currentMonthName) {
            return [
                "{$currentMonthName}-{$item->day}" => [
                    'totalDuration' => $item->totalDuration,
                    'totalRate' => $item->totalRate,
                ],
            ];
        });

        // Check if mapping resulted in empty data
        if ($data->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data after mapping.']);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function GetMonthlyUsage()
    {
        $monthlyUsage = DeviceTimeTransactions::whereNotNull('TransactionDateTime')
            ->whereNotNull('Duration')
            ->whereNotNull('Rate')
            ->selectRaw('MONTH(TransactionDateTime) as month, DeviceID, SUM(Duration) / 60 as totalDuration, SUM(Rate) as totalRate')
            ->groupBy('month', 'DeviceID') // Group by both month and DeviceID
            ->whereHas('device', function ($query) {
                $query->whereNotIn('DeviceStatusID', [9, 10]); // Exclude devices with DeviceStatusID of 9 and 10
            })
            ->with('device') // Ensure device relationship is loaded
            ->get();

        if ($monthlyUsage->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data found.']);
        }

        $data = $monthlyUsage->groupBy('month')->map(function ($monthlyGroup, $month) {
            $monthName = Carbon::createFromFormat('m', intval($month))->format('F'); // Converts month number to name
            return [
                'month' => $monthName,
                'devices' => $monthlyGroup->map(function ($item) {
                    return [
                        'deviceId' => $item->DeviceID,
                        'deviceName' => $item->device->DeviceName ?? 'Unknown Device', // Fetch device name if exists
                        'totalDuration' => $item->totalDuration,
                        'totalRate' => $item->totalRate,
                    ];
                })->values(),
            ];
        })->values(); // Reset keys for clean JSON output

        return response()->json(['success' => true, 'data' => $data]);
    }



    public function GetMonthlyUsageByDevice($deviceID)
    {
        $monthlyUsage = DeviceTimeTransactions::where('DeviceID', $deviceID)
        ->whereNotNull('TransactionDateTime')
        ->whereNotNull('Duration')
        ->whereNotNull('Rate')
        ->selectRaw('MONTH(TransactionDateTime) as month, SUM(Duration) / 60 as totalDuration, SUM(Rate) as totalRate')
        ->groupBy('month')
        ->get();
    
        if ($monthlyUsage->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data found.']);
        }
        
        $data = $monthlyUsage->mapWithKeys(function ($item) {
            $monthName = Carbon::createFromFormat('m', intval($item->month))->format('F'); // Converts month number to name
            return [$monthName => ['totalDuration' => $item->totalDuration, 'totalRate' => $item->totalRate]];
        });
        
        // Debug the mapped result
        if ($data->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data  after mapping.']);
        }
        
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function GetOverviewTimeTransactions(Request $request)
    {
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        $deviceIds = $request->input('deviceIds', []);
        $timeTransactions = null;

        if (!is_array($deviceIds)) {
            $deviceIds = explode(',', $deviceIds);
        }

        $timeTransactions = DeviceTimeTransactions::
        when($dateFrom, function ($query, $dateFrom) {
            return $query->whereDate('TransactionDateTime', '>=', Carbon::parse($dateFrom));
        })
        ->when($dateTo, function ($query, $dateTo) {
            return $query->whereDate('TransactionDateTime', '<=', Carbon::parse($dateTo));
        })
        ->when(!empty($deviceIds), function ($query) use ($deviceIds) {
            return $query->whereHas('device', function ($query) use ($deviceIds) {
                $query->whereIn('DeviceID', $deviceIds);
            });
        })
        ->with(['creator', 'device'])
        ->get();

        if ($timeTransactions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data found. ' . $dateFrom . $dateTo]);
        }

        $groupedTransactions = $timeTransactions->groupBy('Thread');

        $formattedData = $groupedTransactions->map(function ($group, $thread) {
            return [
                'thread' => $thread,
                'totalDuration' => $group->sum('Duration'), 
                'totalRate' => $group->sum('Rate'), 
                'transactions' => $group->map(function ($transaction) {

                    $suffix = '';

                    if ($transaction->device->DeviceStatusID == DeviceStatusEnum::DELETED_ID)
                    {
                        $suffix = '(Deleted)';
                    }

                    return [
                        'transactionId' => $transaction->TransactionID,
                        'deviceId' => $transaction->DeviceID,
                        'deviceStatusID' => $transaction->device->DeviceStatusID,
                        'deviceName' => $transaction->device->DeviceName . ' ' . $suffix ?? 'Unknown Device',
                        'transactionType' => $transaction->TransactionType,
                        'isOpenTime' => (bool) $transaction->IsOpenTime,
                        'transactionDateTime' => $transaction->TransactionDateTime,
                        'stoppageType' => $transaction->StoppageType,
                        'duration' => $transaction->Duration,
                        'rate' => $transaction->Rate,
                        'active' => $transaction->Active,
                        'reason' => $transaction->Reason,
                        'triggeredBy' => $transaction->creator->UserName ?? 'System',
                        'createdAt' => $transaction->created_at,
                        'updatedAt' => $transaction->updated_at
                    ];
                })
            ];
        })->values(); 

        return response()->json(['success' => true, 'data' => $formattedData]);
    }

    public function GetDetailedTimeTransactions(Request $request)
    {
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        $deviceIds = $request->input('deviceIds', []);
        $transactions = null;

        if (!is_array($deviceIds)) {
            $deviceIds = explode(',', $deviceIds);
        }

        $transactions = DeviceTimeTransactions::
        when($dateFrom, function ($query, $dateFrom) {
            return $query->whereDate('TransactionDateTime', '>=', Carbon::parse($dateFrom));
        })
        ->when($dateTo, function ($query, $dateTo) {
            return $query->whereDate('TransactionDateTime', '<=', Carbon::parse($dateTo));
        })
        ->when(!empty($deviceIds), function ($query) use ($deviceIds) {
            return $query->whereHas('device', function ($query) use ($deviceIds) {
                $query->whereIn('DeviceID', $deviceIds);
            });
        })
        ->with(['creator', 'device'])
        ->get();

        // Return error if no transactions found
        if ($transactions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data found.']);
        }

        // Map the transactions to the required format
        $formattedData = $transactions->map(function ($transaction) {
            $suffix = '';

            if ($transaction->device->DeviceStatusID == DeviceStatusEnum::DELETED_ID)
            {
                $suffix = '(Deleted)';
            }

            return [
                'deviceName' => $transaction->device->DeviceName . ' ' . $suffix ?? 'Unknown Device',
                'transactionType' => $transaction->TransactionType,
                'isOpenTime' => (bool) $transaction->IsOpenTime,
                'transactionDateTime' => $transaction->TransactionDateTime,
                'duration' => $transaction->Duration,
                'rate' => $transaction->Rate,
                'reason' => $transaction->Reason,
                'triggeredBy' => $transaction->creator->UserName ?? 'System'
            ];
        });

        // Return JSON response with success and formatted data
        return response()->json(['success' => true, 'data' => $formattedData]);
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
                'name' => $device->DeviceName,
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
            $startDate = $request->input('dateFrom');
            $endDate = $request->input('dateTo');
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
                        $query->whereIn('DeviceName', $deviceNames);
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
                        'deviceName' => $transaction->device->DeviceName ?? 'N/A',
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
                        $query->whereIn('DeviceName', $deviceNames);
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

    public function exportOverviewTimeTransactions(Request $request)
    {
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        $deviceIds = $request->input('deviceIds', []);
        $timeTransactions = null;

        if (!is_array($deviceIds)) {
            $deviceIds = explode(',', $deviceIds);
        }

        $timeTransactions = DeviceTimeTransactions::
        when($dateFrom, function ($query, $dateFrom) {
            return $query->whereDate('TransactionDateTime', '>=', Carbon::parse($dateFrom));
        })
        ->when($dateTo, function ($query, $dateTo) {
            return $query->whereDate('TransactionDateTime', '<=', Carbon::parse($dateTo));
        })
        ->when(!empty($deviceIds), function ($query) use ($deviceIds) {
            return $query->whereHas('device', function ($query) use ($deviceIds) {
                $query->whereIn('DeviceID', $deviceIds);
            });
        })
        ->with(['creator', 'device'])
        ->get();

        if ($timeTransactions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data found. ' . $dateFrom . $dateTo]);
        }

        $groupedTransactions = $timeTransactions->groupBy('Thread');
        
        // $formattedData = $groupedTransactions->map(function ($group, $thread) {
        //     return [
        //         'thread' => $thread,
        //         'totalDuration' => $group->sum('Duration'),
        //         'totalRate' => $group->sum('Rate'),
        //         'transactions' => $group->map(function ($transaction) {
        //             return [
        //                 'Device' => $transaction->device->DeviceName ?? 'Unknown Device',
        //                 'Start Time' => Carbon::parse($transaction->TransactionDateTime)->format('n/j/Y g:i:s A'),
        //                 'End Time' => $transaction->EndTime ? Carbon::parse($transaction->EndTime)->format('n/j/Y g:i:s A') : '',
        //                 'Open Time?' => $transaction->IsOpenTime ? 'Yes' : 'No',
        //                 'Total Duration' => gmdate('H:i:s', $transaction->Duration),
        //                 'Total Rate' => $transaction->Rate,
        //             ];
        //         })->toArray(),
        //     ];
        // });

        $formattedData = $groupedTransactions->map(function ($group, $thread) {
            // Find the start and end transactions
            $startTransaction = $group->firstWhere('TransactionType', 'Start');
            $endTransaction = $group->firstWhere('TransactionType', 'End');
        
            return [
                'thread' => $thread,
                'deviceName' => $startTransaction ? ($startTransaction->device->DeviceName ?? 'Unknown Device') : 'Unknown Device',
                'startTime' => $startTransaction ? $startTransaction->TransactionDateTime : null,
                'endTime' => $endTransaction ? $endTransaction->TransactionDateTime : null,
                'isOpenTime' => $startTransaction ? (bool)$startTransaction->IsOpenTime : false,
                'totalDuration' => $group->sum('Duration'),
                'totalRate' => $group->sum('Rate'),
                'transactions' => $group->map(function ($transaction) {
                    return [
                        'deviceName' => $transaction->device->DeviceName ?? 'Unknown Device',
                        'transactionType' => $transaction->TransactionType,
                        'isOpenTime' => (bool)$transaction->IsOpenTime,
                        'transactionDateTime' => $transaction->TransactionDateTime,
                        'stoppageType' => $transaction->StoppageType,
                        'duration' => $transaction->Duration,
                        'rate' => $transaction->Rate,
                        'active' => $transaction->Active,
                        'reason' => $transaction->Reason,
                        'createdByUserId' => $transaction->CreatedByUserId,
                        'createdAt' => $transaction->created_at,
                        'updatedAt' => $transaction->updated_at
                    ];
                })->values(), // Reset keys for clean JSON
            ];
        })->values(); // Reset keys for clean JSON output
        

        // Prepare data for Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Device', 'Start Time', 'End Time', 'Open Time?', 'Total Duration', 'Total Rate'];
        $sheet->fromArray($headers, null, 'A1');

        // Populate rows
        $row = 2;
        foreach ($formattedData as $threadData) {
            foreach ($threadData['transactions'] as $transaction) {
                $sheet->fromArray(array_values($transaction), null, "A{$row}");
                $row++;
            }
        }

        // Set proper formatting for Total Duration and Total Rate columns
        $sheet->getStyle('E2:E' . ($row - 1))->getNumberFormat()->setFormatCode('[h]:mm:ss');
        $sheet->getStyle('F2:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        // Output the file
        $writer = new Xlsx($spreadsheet);
        $filename = "overview_time_transactions.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $writer->save('php://output');
        exit;
    }

    
}
