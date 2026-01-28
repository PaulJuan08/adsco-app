<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApprovalStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }
        
        $user = $request->user();
        
        // Allow access to registration and login pages
        if ($request->routeIs('register', 'login', 'logout')) {
            return $next($request);
        }
        
        // Check if user is approved
        if (!$user->is_approved && !$user->isAdmin()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is pending approval. Please wait for admin confirmation.');
        }
        
        return $next($request);
    }
}