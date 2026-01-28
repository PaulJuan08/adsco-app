<?php
// app/Http/Middleware/LogUserAttendance.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Attendance;

class LogUserAttendance
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only log for authenticated users on non-login/register routes
        if (auth()->check() && !$request->routeIs('login*', 'register*', 'logout')) {
            $user = auth()->user();
            
            // Update last login time
            $user->last_login_at = now();
            $user->save();
            
            // Check if attendance already logged today
            $today = now()->format('Y-m-d');
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
            
            if (!$existingAttendance) {
                Attendance::create([
                    'user_id' => $user->id,
                    'date' => now(),
                    'login_time' => now(),
                    'ip_address' => $request->ip(),
                    'device_info' => $request->userAgent(),
                    'session_duration' => 0
                ]);
            }
        }
        
        return $response;
    }
}