<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->hasVerifiedEmail()) {
            if ($request->user()) {
                // User is authenticated but not verified
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            
            return redirect()->route('login')
                ->withErrors(['email' => 'Please verify your email address.']);
        }
        
        return $next($request);
    }
}