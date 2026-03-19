<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSalaryType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $type
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $type)
    {
        $user = Auth::user();

        // Check if the user's salary type matches the required type
        if ($user && $user->salary_type === $type) {
            return $next($request);
        }

        // If not authorized, return a 403 response
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}