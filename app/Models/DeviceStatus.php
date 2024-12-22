<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceStatus extends Model
{
    use HasFactory;

    protected $table = 'DeviceStatus';

    public function devices()
    {
        return $this->hasMany(Device::class, 'DeviceStatusID', 'DeviceStatusID');
    }
}
