<?php

use App\Http\Controllers\DeviceManagementController;
use App\Http\Controllers\DeviceTimeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// PUBLIC APIs - These routes are accessible without authentication
Route::post('/device/insert', [DeviceManagementController::class, 'InsertDeviceDetails'])->name('device.insert');
Route::post('/device/update', [DeviceManagementController::class, 'UpdateDeviceDetails'])->name('device.update');
Route::post('/device/{id}/request/delete', [DeviceManagementController::class, 'RequestDeleteDevice'])->name('device.request.delete');
Route::post('/device/response/delete', [DeviceManagementController::class, 'ResponseDeleteDevice'])->name('device.response.delete');
Route::post('/device/test/{id}', [DeviceManagementController::class, 'TestDevice'])->name('device.test');
Route::post('/device-time/end', [DeviceTimeController::class, 'EndDeviceTimeAPI'])->name('device-time.api.end');
Route::post('/device/heartbeat', [DeviceManagementController::class, 'UpdateHeartbeatDeviceStatusToOnline'])->name('device-heartbeat');
