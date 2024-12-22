<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissions extends Model
{
    use HasFactory;

    protected $fillable = [
        'PermissionId',
        'PermissionName',
        'Description'
    ];

    public $timestamps = false;

    protected $primaryKey = 'PermissionId';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'RolePermissions', 'PermissionId', 'RoleID');
    }
}
