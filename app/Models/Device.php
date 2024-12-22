<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'Devices';

    protected $primaryKey = 'DeviceID';

    public $incrementing = true;

    protected $fillable = [
        'DeviceName',
        'ExternalDeviceName',
        'Description',
        'DeviceStatusID',
        'WatchdogInterval',
        'RemainingTimeNotification',
        'IPAddress',
    ];

    protected $casts = [
        'OperationDate' => 'datetime'
    ];


    public function deviceStatus()
    {
        return $this->belongsTo(DeviceStatus::class, 'DeviceStatusID', 'DeviceStatusID');
    }

    public function increments()
    {
        return $this->hasMany(DeviceTime::class, 'DeviceID')->where('TimeTypeID', 2);
    }

    public function deviceTimeTransactions()
    {
        return $this->hasMany(DeviceTimeTransactions::class, 'DeviceID');
    }
}
