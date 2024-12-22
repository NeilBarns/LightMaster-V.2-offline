<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Roles extends Model
{
    use HasFactory;

    protected $table = 'Roles';

    protected $primaryKey = 'RoleID';

    public $incrementing = true;

    protected $fillable = [
        'RoleName',
        'Description',
        'CreatedByUserID',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permissions::class, 'RolePermissions', 'RoleID', 'PermissionID');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermissions::class, 'RoleID', 'RoleID');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Users::class, 'UserRole', 'RoleId', 'UserId');
    }

    public function hasPermission($permissionName): bool
    {
        return $this->permissions()->where('PermissionName', $permissionName)->exists();
    }
}
