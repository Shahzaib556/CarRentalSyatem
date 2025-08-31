<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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

    // Update user profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($request->only('name', 'phone', 'address'));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    // Get User Password
        public function getUserPassword(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'password_requirements' => [
                'min_length' => 8,
                'requires_mixed_case' => true,
                'requires_numbers' => true,
                'requires_symbols' => true,
                'last_changed' => $user->password_changed_at?->format('Y-m-d H:i:s'),
                'hint' => $user->password_hint ?? null
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
        'message' => 'User password updated successfully',
        'password_changed_at' => now()->toDateTimeString()
    ]);
}

    // Upload driver's license or any document
    public function uploadDocument(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Delete old file if exists
        if ($user->license_document) {
            Storage::disk('public')->delete($user->license_document);
        }

        // Store new file in public/documents
        $path = $request->file('document')->store('documents', 'public');

        $user->license_document = $path;
        $user->save();

        return response()->json([
            'message' => 'Document uploaded successfully',
            'path' => $path,
            'url' => Storage::url($path),
        ]);
    }
}
