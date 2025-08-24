<?php

namespace App\Http\Controllers\API;

use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    // Admin Login
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $admin = AdminUser::where('email', $request->email)->first();

    if (!$admin || !Hash::check($request->password, $admin->password)) {
        throw ValidationException::withMessages([
            'email' => ['Invalid credentials.']
        ]);
    }

    // Create token with simple scope
    $token = $admin->createToken('admin_token', ['admin'])->plainTextToken;

    return response()->json([
        'message' => 'Admin login successful',
        'access_token' => $token,
        'token_type' => 'Bearer'
    ]);
}

public function updatePassword(Request $request)
{
    $admin = $request->user();
    
    // Manual ability check
    if (!$admin->tokenCan('admin')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'current_password' => 'required|string',
        'new_password' => [
            'required',
            'string',
            Password::min(8)->mixedCase()->numbers()->symbols(),
            'confirmed'
        ],
    ]);

    if (!Hash::check($request->current_password, $admin->password)) {
        throw ValidationException::withMessages([
            'current_password' => ['Invalid current password'],
        ]);
    }

    $admin->update(['password' => Hash::make($request->new_password)]);
    
    return response()->json(['message' => 'Password updated successfully']);
}

    // Admin Logout
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    // Send Admin Reset Link
    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email']);
        
        $status = Password::broker('admin_users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent.'])
            : response()->json(['error' => 'Failed to send reset link.'], 422);
    }

    // Get current admin
    public function admin(Request $request) {
        return response()->json($request->user());
    }
}