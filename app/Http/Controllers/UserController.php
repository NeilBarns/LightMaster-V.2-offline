<?php

namespace App\Http\Controllers;

use App\Enums\LogEntityEnum;
use App\Enums\LogTypeEnum;
use App\Models\Roles;
use App\Models\UserRoles;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function GetUsers()
    {
        try {
            $users = Users::with('roles')->get();
            return view('manage-users', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error fetching users', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching users']);
        }
    }

    public function GetUser($userId)
    {
        try {
            $user = $userId == 0 ? new Users() : Users::with('roles')->findOrFail($userId);
            $roles = Roles::all();
            return view('user', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error fetching user details for UserID: ' . $userId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching user details']);
        }
    }

    public function GetUserProfile($userId)
    {
        try {
            $user = $userId == 0 ? new Users() : Users::with('roles')->findOrFail($userId);
            $roles = Roles::all();
            return view('profile', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error fetching user profile for UserID: ' . $userId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching user profile']);
        }
    }

    public function InsertUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'user_name' => 'required|string',
            'password' => 'required|string',
            'roles' => 'required'
        ]);

        try {
            $user = Users::create([
                'FirstName' => $request->input('first_name'),
                'LastName' => $request->input('last_name'),
                'UserName' => $request->input('user_name'),
                'Password' => Hash::make($request->input('password')),
                'Active' => 1
            ]);

            $roles = json_decode($request->input('roles'), true);

            foreach ($roles as $roleId) {
                UserRoles::create([
                    'UserId' => $user->UserID,
                    'RoleId' => $roleId
                ]);
            }

            LoggingController::InsertLog(
                LogEntityEnum::USER,
                $user->UserID,
                'Created User ' . $user->UserName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return response()->json(['success' => true, 'message' => 'User created successfully.']);
        } catch (\Exception $e) {
            Log::error('Error inserting user: ' . $request->input('user_name'), ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving user: ' . $e->getMessage()
            ]);
        }
    }

    public function UpdateUser(Request $request, $userId)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'user_name' => 'required|string',
            'password' => 'required|string',
            'roles' => 'required'
        ]);

        try {
            $user = Users::findOrFail($userId);
            $user->FirstName = $request->input('first_name');
            $user->LastName = $request->input('last_name');
            $user->UserName = $request->input('user_name');
            $newPassword = $request->input('password');

            if ($newPassword != $user->Password) {
                $user->Password = Hash::make($newPassword);
            }

            $user->save();

            $roles = json_decode($request->input('roles'), true);

            UserRoles::where('UserID', $userId)->delete();
            foreach ($roles as $roleId) {
                UserRoles::create([
                    'UserId' => $user->UserID,
                    'RoleId' => $roleId
                ]);
            }

            LoggingController::InsertLog(
                LogEntityEnum::USER,
                $user->UserID,
                'Updated User ' . $user->UserName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating user with UserID: ' . $userId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()]);
        }
    }

    public function UpdateUserProfile(Request $request, $userId)
    {
        $request->validate([
            'user_name_profile' => 'required|string',
            'password_profile' => 'required|string'
        ]);

        try {
            $user = Users::findOrFail($userId);
            $user->UserName = $request->input('user_name_profile');

            $newPassword = $request->input('password_profile');

            if ($newPassword != $user->Password) {
                $user->Password = Hash::make($newPassword);
            }
            $user->save();

            LoggingController::InsertLog(
                LogEntityEnum::USER,
                $user->UserID,
                'Updated User ' . $user->UserName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating user profile for UserID: ' . $userId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error updating user profile: ' . $e->getMessage()]);
        }
    }

    public function DeleteUser($userId)
    {
        try {
            $user = Users::findOrFail($userId);
            $userName = $user->UserName;
            // $user->roles()->delete();
            $user->roles()->detach();
            $user->delete();

            LoggingController::InsertLog(
                LogEntityEnum::USER,
                $user->UserID,
                'Deleted User ' . $userName,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error deleting user with UserID: ' . $userId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }

    public function UserStatus($userId, $status)
    {
        try {
            $user = Users::findOrFail($userId);

            $oldStatus = $user->Active;

            $user->Active = $status;
            $user->save(); // Save the changes to the database

            LoggingController::InsertLog(
                LogEntityEnum::USER,
                $user->UserID,
                'Updated User ' . $user->UserName . ' status from ' . $oldStatus . ' to ' . $status,
                LogTypeEnum::INFO,
                auth()->id()
            );

            return response()->json(['success' => true, 'message' => 'User status changed successfully.']);
        } catch (\Exception $e) {
            Log::error('Error changing user status for UserID: ' . $userId, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error on user status change: ' . $e->getMessage()]);
        }
    }
}
