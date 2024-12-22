<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermissions extends Model
{
    use HasFactory;

    protected $table = 'RolePermissions';

    protected $primaryKey = 'RolePermissionsID';

    public $incrementing = true;

    protected $fillable = [
        'RoleID',
        'PermissionID',
        'CreatedByUserID',
    ];

    public function roles()
    {
        return $this->belongsTo(Roles::class, 'RoleID', 'RoleID');
    }
}
