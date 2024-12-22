<?php

namespace App\Http\Controllers;

use App\Enums\LogEntityEnum;
use App\Enums\LogTypeEnum;
use App\Models\Permissions;
use App\Models\RolePermissions;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function GetRoles()
    {
        try {
            $roles = Roles::all();
            return view('manage-roles', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Error fetching roles', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching roles']);
        }
    }

    public function GetRole($roleId)
    {
        try {
            $role = $roleId == 0 ? new Roles() : Roles::with('rolePermissions')->findOrFail($roleId);
            $permissions = Permissions::all();
            return view('role', compact('role', 'permissions'));
        } catch (\Exception $e) {
            Log::error('Error fetching role details for RoleID: ' . $roleId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching role details']);
        }
    }

    public function InsertRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string',
            'permissions' => 'required'
        ]);

        try {
            $role = Roles::create([
                'RoleName' => $request->input('role_name'),
                'Description' => $request->input('role_description')
                // 'CreatedByUserID' => auth()->id() // Assuming you have authentication set up
            ]);

            $permissions = json_decode($request->input('permissions'), true);

            foreach ($permissions as $permissionId) {
                RolePermissions::create([
                    'RoleID' => $role->RoleID,
                    'PermissionID' => $permissionId,
                    'CreatedByUserID' => auth()->id()
                ]);
            }

            LoggingController::InsertLog(
                LogEntityEnum::ROLE,
                $role->RoleID,
                'Inserted new Role ' . $role->RoleName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return response()->json(['success' => true, 'message' => 'Role created successfully.']);
        } catch (\Exception $e) {
            Log::error('Error inserting role: ' . $request->input('role_name'), ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving role: ' . $e->getMessage()
            ]);
        }
    }

    public function DeleteRole($roleId)
    {
        try {
            $role = Roles::findOrFail($roleId);
            $role->rolePermissions()->delete();

            LoggingController::InsertLog(
                LogEntityEnum::ROLE,
                $roleId,
                'Role ' . $role->RoleName . ' deleted',
                LogTypeEnum::INFO,
                auth()->id()
            );

            $role->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error deleting role with RoleID: ' . $roleId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error deleting role: ' . $e->getMessage()]);
        }
    }

    public function UpdateRole(Request $request, $roleId)
    {
        $request->validate([
            'role_name' => 'required|string',
            'permissions' => 'required'
        ]);

        try {
            $role = Roles::findOrFail($roleId);
            $role->RoleName = $request->input('role_name');
            $role->Description = $request->input('role_description');
            $role->save();

            $permissions = json_decode($request->input('permissions'), true);

            RolePermissions::where('RoleID', $roleId)->delete();
            foreach ($permissions as $permissionId) {
                RolePermissions::create([
                    'RoleID' => $role->RoleID,
                    'PermissionID' => $permissionId,
                    'CreatedByUserID' => auth()->id() // or another user ID reference
                ]);
            }

            LoggingController::InsertLog(
                LogEntityEnum::ROLE,
                $roleId,
                'Updated role  ' . $role->RoleName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating role with RoleID: ' . $roleId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error updating role: ' . $e->getMessage()]);
        }
    }
}
