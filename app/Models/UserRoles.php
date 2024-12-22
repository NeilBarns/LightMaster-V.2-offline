<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoles extends Model
{
    use HasFactory;

    protected $table = 'UserRoles';
    protected $primaryKey = 'UserRoleId';

    protected $fillable = [
        'UserRoleId',
        'UserId',
        'RoleId'
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'UserId');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'RoleId');
    }
}
