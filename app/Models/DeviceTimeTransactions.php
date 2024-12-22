<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceTimeTransactions extends Model
{
    use HasFactory;

    protected $table = 'DeviceTimeTransactions';

    protected $primaryKey = 'TransactionID';

    protected $fillable = [
        'DeviceID',
        'TransactionType',
        'IsOpenTime',
        'StartTime',
        'EndTime',
        'StoppageType',
        'Duration',
        'Rate',
        'Active',
        'Reason',
        'CreatedByUserId'
    ];

    protected $casts = [
        'StartTime' => 'datetime',
        'EndTime' => 'datetime'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'DeviceID', 'DeviceID');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'CreatedByUserId', 'UserID');
    }
}
