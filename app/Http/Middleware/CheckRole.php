<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        $userRole = $request->user()->role;
        
        // Convert role numbers to match
        $allowedRoles = array_map(function($role) {
            return [
                'admin' => 1,
                'registrar' => 2,
                'teacher' => 3,
                'student' => 4
            ][strtolower($role)] ?? $role;
        }, $roles);
        
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized access.');
        }
        
        return $next($request);
    }
}