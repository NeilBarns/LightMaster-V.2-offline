<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceHeartbeatStatusResponse extends Model
{
    public $timestamps = false;

    protected $table = null;

    protected $fillable = [
        'DeviceID',
        'HeartbeatStatus',
        'DeviceStatus'
    ];
}
