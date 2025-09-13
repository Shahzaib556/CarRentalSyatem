<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Register User (only for regular users)
    public function register(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:25',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone'    => 'required|string|max:11',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'phone'    => $request->phone,
        ]);

        return response()->json(['message' => 'User registered successfully.'], 201);
    }

    // Login User (only for regular users)
    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.']
            ]);
        }

        // Prevent admin login through user endpoint
        if ($user->role === 'admin') {
            throw ValidationException::withMessages([
                'email' => ['Please use admin login']
            ]);
        }

        $token = $user->createToken('user-token', ['user-access'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    // Logout (for both user and admin tokens)
    public function logout(Request $request) 
    {
    if ($request->user()) 
        {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
        }

    return response()->json(['message' => 'Not authenticated'], 401);
    }


    // Validate email + phone for password reset
    public function forgotPassword(Request $request) {
    $request->validate([
        'email' => 'required|email',
        'phone' => 'required|string',
    ]);

    $user = User::where('email', $request->email)
                ->where('phone', $request->phone)
                ->first();

    if (!$user) {
        return response()->json([
            'message' => 'No user found with this email and phone number.'
        ], 404);
    }

    // User exists → validation successful
    return response()->json([
        'message' => 'Validation successful. You can reset your password.'
    ]);
    }

    // Reset the password
    public function resetPassword(Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed', 
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }

    $user->password = bcrypt($request->password);
    $user->save();

    return response()->json([
        'message' => 'Password has been reset successfully.'
    ]);
}



    // Send Reset Link (for users only)
    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email']);
        
        // Only look in users table
        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent.'])
            : response()->json(['error' => 'Failed to send reset link.'], 422);
    }

    // Get current user
    public function user(Request $request) {
        return response()->json($request->user());
    }
}