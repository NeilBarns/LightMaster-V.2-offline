<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LoggingController extends Controller
{
    public static function InsertLog($entity, $entityId, $log, $type, $userId = null)
    {
        ActivityLog::create([
            'Entity' => $entity,
            'EntityID' => $entityId,
            'Log' => $log,
            'Type' => $type,
            'CreatedByUserId' => $userId,
        ]);
    }

    public function GetLog()
    {
        $logs = ActivityLog::orderBy('created_at', 'desc')->get();
        return view('logs.index', compact('logs'));
    }

    public function GetActivityLogs()
    {
        // Fetch activity logs with related user data
        $logs = ActivityLog::with('user')->get();

        return view('activity-logs', compact('logs'));
    }
}
