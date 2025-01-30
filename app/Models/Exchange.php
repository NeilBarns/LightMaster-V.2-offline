<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    protected $table = 'Exchange';

    protected $primaryKey = 'ExchangeID';

    public $incrementing = true;

    protected $fillable = [
        'DeviceID',
        'Thread',
        'Active',
        'SerialNumber',
        'ExchangeSerialNumber',
        'Reason',
        'ExchangeStatusID'
    ];
}
