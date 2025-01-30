<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceDisplay extends Model
{
    use HasFactory;

    protected $table = 'DeviceDisplay';

    protected $primaryKey = 'DeviceID';

    protected $fillable = [
        'DeviceID',
        'TransactionType',
        'IsOpenTime',
        'StartTime',
        'EndTime',
        'PauseTime',
        'ResumeTime',
        'TotalTime',
        'TotalRate'
    ];

    protected $casts = [
        'StartTime' => 'datetime',
        'EndTime' => 'datetime',
        'PauseTime' => 'datetime',
        'ResumeTime' => 'datetime'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'DeviceID', 'DeviceID');
    }
}
