<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Check if email is verified
        if (is_null($user->email_verified_at)) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Please verify your email address before accessing the dashboard.');
        }
        
        // Check if user is approved
        if (!$user->is_approved) {
            return view('dashboard.pending-approval', [
                'user' => $user
            ]);
        }
        
        // Redirect to role-specific dashboard controllers
        switch ($user->role) {
            case 1: // Admin
                return app(\App\Http\Controllers\Admin\DashboardController::class)->index();
            case 2: // Registrar
                return app(\App\Http\Controllers\Registrar\DashboardController::class)->index();
            case 3: // Teacher
                return app(\App\Http\Controllers\Teacher\DashboardController::class)->index();
            case 4: // Student
                return app(\App\Http\Controllers\Student\DashboardController::class)->index();
            default:
                abort(403, 'Invalid role');
        }
    }
}