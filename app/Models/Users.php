<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Users extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $table = 'Users';

    protected $primaryKey = 'UserID';

    public $incrementing = true;

    protected $fillable = [
        'FirstName',
        'LastName',
        'UserName',
        'Password',
        'Active'
    ];

    protected $casts = [
        'LastLoggedDate' => 'datetime'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'UserRoles', 'UserId', 'RoleId');
    }

    public function hasPermission($permissionName): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('PermissionName', $permissionName);
        })->exists();
    }
}
