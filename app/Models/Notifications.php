<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'Notifications';

    protected $primaryKey = 'NotificationID';

    public $incrementing = true;

    protected $fillable = [
        'Notification',
        'NotificationLevelID',
        'NotificationSourceID',
        'DeviceID'
    ];
}
