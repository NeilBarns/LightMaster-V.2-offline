<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeType extends Model
{
    use HasFactory;

    protected $table = 'TimeType';
    protected $primaryKey = 'TimeTypeID';
    public $incrementing = false; // Since the primary key is not auto-incrementing

    protected $fillable = [
        'Name',
    ];

    public function deviceTimes()
    {
        return $this->hasMany(DeviceTime::class, 'TimeTypeID');
    }
}
