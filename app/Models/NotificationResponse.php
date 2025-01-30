<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationResponse extends Model
{
    public $timestamps = false;

    protected $table = null;

    protected $fillable = [
        'NotificationID',
        'Notification',
        'NotificationLevelID',
        'NotificationSourceID',
        'DeviceID',
        'CreatedDate'
    ];
}
