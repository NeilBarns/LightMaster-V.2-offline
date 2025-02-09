<?php

use App\Events\DeviceTransactionUpdates;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceManagementController;
use App\Http\Controllers\DeviceTimeController;
use App\Http\Controllers\LoggingController;
use App\Http\Controllers\LongPollingController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\DeviceTimeTransactionsResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes that do not require authentication
Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('/login', function () {
    return view('login');
})->name('login');

// PUBLIC APIs - These routes are accessible without authentication
Route::post('/api/device/insert', [DeviceManagementController::class, 'InsertDeviceDetails'])->name('device.insert');
Route::post('/api/device/update', [DeviceManagementController::class, 'UpdateDeviceDetails'])->name('device.update');
Route::post('/api/device/{id}/request/delete', [DeviceManagementController::class, 'RequestDeleteDevice'])->name('device.request.delete');
Route::post('/api/device/response/delete', [DeviceManagementController::class, 'ResponseDeleteDevice'])->name('device.response.delete');
Route::post('/api/device/test/{id}', [DeviceManagementController::class, 'TestDevice'])->name('device.test');
Route::post('/api/device-time/end', [DeviceTimeController::class, 'EndDeviceTimeAPI'])->name('device-time.api.end');
Route::post('/api/device/heartbeat', [DeviceManagementController::class, 'UpdateHeartbeatDeviceStatusToOnline'])->name('device-heartbeat');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/device', function () {
        return view('device');
    })->name('device');

    Route::get('/reports', function () {
        return view('reports');
    })->name('reports');

    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    // DEVICE CONTROLLERS
    Route::get('/device', [DeviceManagementController::class, 'GetDevices'])->name('devicemanagement');
    Route::get('/device/{id}', [DeviceManagementController::class, 'GetDeviceDetails'])->name('device.detail');
    Route::post('/device/deploy/{id}', [DeviceManagementController::class, 'DeployDevice'])->name('device.deploy');
    Route::post('/device/{id}/disable', [DeviceManagementController::class, 'DisableDevice'])->name('device.disable');
    Route::post('/device/{id}/enable', [DeviceManagementController::class, 'EnableDevice'])->name('device.enable');
    Route::post('/device/update/name', [DeviceManagementController::class, 'UpdateDeviceName'])->name('device.update.devicename');
    Route::post('/device/update/watchdog', [DeviceManagementController::class, 'UpdateWatchdogInterval'])->name('device.update.watchdog');
    Route::post('/device/update/remainingtime', [DeviceManagementController::class, 'UpdateRemainingTimeNotification'])->name('device.update.remainingtime');
    Route::post('/device/exchange', [DeviceManagementController::class, 'UpdateNodeExchange'])->name('device.exchange');
    Route::post('/device/cancel/exchange/{id}', [DeviceManagementController::class, 'UpdateCancelNodeExchange'])->name('device.cancel.exchange');

    // TIME CONTROLLERS
    Route::get('/device-time/running', [DeviceTimeController::class, 'GetCurrentlyRunningDevices'])->name('device-time.running');
    Route::post('/device-time/increment', [DeviceTimeController::class, 'InsertDeviceIncrement'])->name('device-time.increment.insert');
    Route::post('/device-time/increment/update/{id}', [DeviceTimeController::class, 'UpdateDeviceIncrement'])->name('device-time.increment.update');
    Route::delete('/device-time/increment/delete/{id}', [DeviceTimeController::class, 'DeleteDeviceIncrement'])->name('device-time.increment.delete');
    Route::post('/device-time/increment/status/{id}', [DeviceTimeController::class, 'UpdateDeviceIncrementStatus'])->name('device-time.increment.status');

    // HEARTBEATS
    Route::get('/device/update/heartbeats', [DeviceManagementController::class, 'GetDeviceHeartbeatStatuses']);
    Route::get('/device/update/heartbeat/{id}', [DeviceManagementController::class, 'GetDeviceHeartbeatStatus']);

    Route::post('/device-time/base', [DeviceTimeController::class, 'InsertDeviceBase'])->name('device-time.base');
    Route::post('/device-time/open', [DeviceTimeController::class, 'InsertDeviceOpen'])->name('device-time.open');
    Route::post('/device-time/freetimelimit', [DeviceTimeController::class, 'InsertDeviceFreeTimeLimit'])->name('device-time.freetime');
    Route::post('/device-time/start/rated/{id}', [DeviceTimeController::class, 'StartDeviceRatedTime'])->name('device-time.start.rated');
    Route::post('/device-time/start/open/{id}', [DeviceTimeController::class, 'StartDeviceOpenTime'])->name('device-time.start.open');
    Route::post('/device-time/end/{id}', [DeviceTimeController::class, 'EndDeviceTimeManual'])->name('device-time.end');
    Route::post('/device-time/pause/{id}', [DeviceTimeController::class, 'PauseDeviceTime'])->name('device-time.pause');
    Route::post('/device-time/resume/{id}', [DeviceTimeController::class, 'ResumeDeviceTime'])->name('device-time.resume');
    Route::post('/device-time/extend/{id}', [DeviceTimeController::class, 'ExtendDeviceTime'])->name('device-time.extend');
    Route::post('/device/startfree/{id}', [DeviceTimeController::class, 'StartFreeLight'])->name('device.free');
    Route::post('/device/stopfree/{id}', [DeviceTimeController::class, 'StopFreeLight'])->name('device.stop.free');

    // ROLES
    Route::get('/manage-roles', [RoleController::class, 'GetRoles'])->name('manage-roles');
    Route::get('/role/{roleId}', [RoleController::class, 'GetRole'])->name('role');
    Route::post('/role/insert', [RoleController::class, 'InsertRole'])->name('roles.insert');
    Route::delete('/role/delete/{roleId}', [RoleController::class, 'DeleteRole'])->name('roles.delete');
    Route::post('/roles/{roleId}/update', [RoleController::class, 'UpdateRole'])->name('roles.update');

    // USERS
    Route::get('/manage-users', [UserController::class, 'GetUsers'])->name('manage-users');
    Route::get('/user/{userId}', [UserController::class, 'GetUser'])->name('user');
    Route::get('/profile/{userId}', [UserController::class, 'GetUserProfile'])->name('profile');
    Route::post('/user/insert', [UserController::class, 'InsertUser'])->name('user.insert');
    Route::post('/user/{userId}/update', [UserController::class, 'UpdateUser'])->name('user.update');
    Route::post('/profile/{userId}/update', [UserController::class, 'UpdateUserProfile'])->name('profile.update');
    Route::delete('/user/delete/{userId}', [UserController::class, 'DeleteUser'])->name('user.delete');
    Route::post('/user/status/{userId}/{status}', [UserController::class, 'UserStatus'])->name('user.status');

    // REPORTS
    Route::get('/reports/finance', [ReportsController::class, 'GetFinanceReports'])->name('reports.finance');
    Route::get('/reports/usage/daily/device/{id}', [ReportsController::class, 'GetDailyUsageByDevice'])->name('reports.device.daily.usage');
    Route::get('/reports/usage/monthly', [ReportsController::class, 'GetMonthlyUsage'])->name('reports.monthly.usage');
    Route::get('/reports/usage/monthly/device/{id}', [ReportsController::class, 'GetMonthlyUsageByDevice'])->name('reports.device.monthly.usage');
    Route::post('/reports/transactions/overview/device', [ReportsController::class, 'GetOverviewTimeTransactions'])->name('reports.overview.transactions.device.');
    Route::post('/reports/transactions/detailed/device', [ReportsController::class, 'GetDetailedTimeTransactions'])->name('reports.detailed.transactions.device.');
    Route::get('/export-overview', [ReportsController::class, 'exportOverviewTimeTransactions']);

    
    Route::get('/reports/transactions/filter', [ReportsController::class, 'GetFilteredDetailedTransactions'])->name('reports.transactions.filter');
    Route::get('/reports/transactions/filter/overview', [ReportsController::class, 'GetFilteredOverviewTransactions'])->name('reports.transactions.filter.overview');
    Route::get('/activity-logs', [LoggingController::class, 'GetActivityLogs'])->name('activity.logs');




    // LONG POLLING
    Route::get('/active-transactions', [LongPollingController::class, 'GetActiveTransactions']);
    Route::get('/check-session', function () {
        $sessionLifetime = Config::get('session.lifetime'); // Lifetime in minutes
        $sessionActive = auth()->check(); // Check if the user is logged in

        return response()->json([
            'session_active' => $sessionActive,
            'session_lifetime' => $sessionLifetime
        ]);
    });


    // Route::get('/check-ssid', function () {
    //     $output = shell_exec('netsh wlan show interfaces');
    
    //     preg_match('/\s*SSID\s*:\s*([^\r\n]*)/', $output, $matches);
    
    //     $currentSSID = isset($matches[1]) ? trim($matches[1]) : null;
    
    //     $expectedSSID = config('app.expected_ssid');
    
    //     if ($currentSSID === $expectedSSID) {
    //         return response()->json(['connected' => true, 'ssid' => $currentSSID]);
    //     }
    
    //     return response()->json(['connected' => false, 'ssid' => $currentSSID]);
    // });
    
});

// AUTH
Route::post('/login', [AuthController::class, 'UserLogin'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'UserLogout'])->name('auth.logout');
