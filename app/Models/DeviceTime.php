<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceTime extends Model
{
    use HasFactory;

    protected $table = 'DeviceTime';
    protected $primaryKey = 'DeviceTimeID';

    const TIME_TYPE_BASE = 1;
    const TIME_TYPE_INCREMENT = 2;
    const TIME_TYPE_OPEN = 3;

    public $timestamps = false;

    protected $fillable = [
        'DeviceID',
        'Time',
        'Rate',
        'TimeTypeID',
        'Active'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'DeviceID');
    }

    public function timeType()
    {
        return $this->belongsTo(TimeType::class, 'TimeTypeID');
    }
}
