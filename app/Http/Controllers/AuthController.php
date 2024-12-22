<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function UserLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        try {
            $user = Users::where('UserName', $credentials['username'])
                ->with(['Roles' => function ($query) {
                    $query->select('Roles.RoleId', 'Roles.RoleName')
                        ->with(['Permissions' => function ($query) {
                            $query->select('Permissions.PermissionId', 'Permissions.PermissionName');
                        }]);
                }])
                ->first();

            if ($user) {
                if (Hash::check($credentials['password'], $user->Password)) {
                    Auth::login($user);

                    $user->update([
                        'LastLoggedDate' => Carbon::now('Asia/Manila')
                    ]);

                    $user = Auth::user();
                    $permissions = $user->roles->flatMap(function ($role) {
                        return $role->permissions;
                    });

                    $intended = 'device'; // Default redirect

                    if ($permissions->contains('PermissionName', 'view_dashboard')) {
                        $intended = 'dashboard';
                    } elseif ($permissions->contains('PermissionName', 'view_owners') || $permissions->contains('PermissionName', 'manage_owners')) {
                        $intended = 'vehicle-owners';
                    } elseif ($permissions->contains('PermissionName', 'view_vehicles') || $permissions->contains('PermissionName', 'manage_vehicles')) {
                        $intended = 'registered-vehicles';
                    } elseif ($permissions->contains('PermissionName', 'view_users') || $permissions->contains('PermissionName', 'manage_users')) {
                        $intended = 'manage-users';
                    }

                    return redirect()->intended($intended);
                } else {
                    return back()->withErrors([
                        'failed' => 'Invalid password.',
                    ]);
                }
            } else {
                return back()->withErrors([
                    'failed' => 'Unknown credentials.',
                ]);
            }
        } catch (\Exception $e) {
            return back()->withErrors([
                'failed' => 'An error occurred during login. Please try again later.' . $e->getMessage(),
            ]);
        }
    }

    public function UserLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
