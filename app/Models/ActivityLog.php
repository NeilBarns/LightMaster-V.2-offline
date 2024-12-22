<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'ActivityLog';

    public $incrementing = true;

    protected $primaryKey = 'LogID';

    protected $fillable = [
        'Entity',
        'EntityID',
        'Log',
        'CreatedByUserId',
        'Type'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'CreatedByUserId');
    }
}
