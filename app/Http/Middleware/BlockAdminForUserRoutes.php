<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockAdminForUserRoutes
{
    public function handle(Request $request, Closure $next)
    {
        // Check if admin is logged in
        if (Auth::guard('admin')->check()) {
            return response()->json(['error' => 'Admins cannot access user routes.'], 403);
        }

        return $next($request);
    }
}
