<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RptDeviceTimeTransactions extends Model
{
    use HasFactory;

    protected $table = 'RptDeviceTimeTransactions';

    protected $primaryKey = 'TransactionID';

    protected $fillable = [
        'DeviceTimeTransactionsID',
        'DeviceID',
        'TransactionType',
        'IsOpenTime',
        'Time',
        'StoppageType',
        'Duration',
        'Rate',
        'Reason',
        'CreatedByUserId'
    ];

    public $timestamps = false;

    protected $casts = [
        'Time' => 'datetime'
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
