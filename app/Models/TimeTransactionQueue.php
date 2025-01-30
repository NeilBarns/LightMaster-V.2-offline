<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeTransactionQueue extends Model
{
    use HasFactory;

    protected $table = 'TimeTransactionQueue';

    protected $primaryKey = 'TimeTransactionQueueID';

    protected $fillable = [
        'DeviceID',
        'DeviceStatusID',
        'Thread',
        'EndTime',
        'StoppageType',
        'QueueStatusID',
        'ErrorMessage'
    ];

    protected $casts = [
        'EndTime' => 'datetime'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'DeviceID', 'DeviceID');
    }
}
