<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissions extends Model
{
    use HasFactory;

    protected $table = 'Permissions';

    public $timestamps = false;

    protected $primaryKey = 'PermissionId';

    protected $fillable = [
        'PermissionId',
        'PermissionName',
        'Description'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'RolePermissions', 'PermissionId', 'RoleID');
    }
}
