<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationsController extends Controller
{
    public function GetNotifications()
    {
        try {
            return Notifications::orderBy('created_at', 'desc')->limit(100)->get();
        } catch (\Exception $e) {
            Log::error('Error fetching notifications', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}
