<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceTimeTransactionsResponse extends Model
{
    public $timestamps = false;

    protected $table = null;

    protected $fillable = [
        'DeviceID',
        'TransactionType',
        'IsOpenTime',
        'StartTime',
        'PauseTime',
        'ResumeTime',
        'EndTime',
        'TotalTime',
        'TotalRate',
        'DoHeartbeatCheck',
        'Test',
        'TotalUsedTime'
    ];
}
