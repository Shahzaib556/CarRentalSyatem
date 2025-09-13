<?php

namespace App\Http\Controllers\API;

use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserPasswordRequest;

class UserController extends Controller
{
    // Add constructor to ensure only regular users can access
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\BlockAdminForUserRoutes::class);
    }

    // Return current user profile
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // Update user profile (name, phone, address, city, country, profile image)
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'          => 'required|string|max:25',
            'phone'         => 'required|string|max:11',
            'address'       => 'required|string|max:255',
            'city'          => 'required|string|max:20',
            'country'       => 'required|string|max:20',
            'cnic'          => ['required','string','max:15','regex:/^\d{5}-\d{7}-\d{1}$/','unique:users,cnic,' . $user->id,],

            'guarantor_a_name'      => 'required|string|max:20',
            'guarantor_a_cnic'      => ['required','string','max:15','regex:/^\d{5}-\d{7}-\d{1}$/',],
            'guarantor_a_phone'     => 'required|string|max:11',

            'guarantor_b_name'      => 'required|string|max:20',
            'guarantor_b_cnic'      => ['required','string','max:15','regex:/^\d{5}-\d{7}-\d{1}$/',],
            'guarantor_b_phone'     => 'required|string|max:11',
            
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:7148',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

            // Update other fields
            $user->fill($request->only(
            'name',
            'phone',
            'address',
            'city',
            'country',
            'cnic',
            'guarantor_a_name',
            'guarantor_a_cnic',
            'guarantor_a_phone',
            'guarantor_b_name',
            'guarantor_b_cnic',
            'guarantor_b_phone'
        ));

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user,
        ]);
    }

    // Get User Password Requirements
    public function getUserPassword(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'password_requirements' => [
                'min_length'           => 8,
                'requires_mixed_case'  => true,
                'requires_numbers'     => true,
                'requires_symbols'     => true,
                'last_changed'         => $user->password_changed_at?->format('Y-m-d H:i:s'),
                'hint'                 => $user->password_hint ?? null,
            ],
            'message' => 'User password requirements retrieved'
        ]);
    }

    // Update user password
    public function updateUserPassword(UpdateUserPasswordRequest $request)
    {
        $user = $request->user();
        
        $user->update([
            'password' => Hash::make($request->new_user_password)
        ]);

        // Optional: Revoke all other tokens
        $user->tokens()->delete();

        return response()->json([
            'message'             => 'User password updated successfully',
            'password_changed_at' => now()->toDateTimeString()
        ]);
    }

    // Get all users with all columns
    public function listAllUsers()
    {
        $users = User::all(); // fetch everything from the users table

        return response()->json([
            'status' => 'success',
            'users'  => $users
        ]);
    }
}
