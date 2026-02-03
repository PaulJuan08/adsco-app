<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Check if user has a role attribute
        if (!$user->role) {
            // If no role column, try to get it from relationships or default
            $user->role = $user->roles->first()->name ?? 'user';
        }
        
        // Map roles to view paths
        $viewMap = [
            'admin' => 'admin.profile.edit',
            'registrar' => 'registrar.profile.edit', 
            'teacher' => 'teacher.profile.edit',
            'student' => 'student.profile.edit',
        ];
        
        $role = strtolower($user->role);
        
        // Check if the view exists
        if (isset($viewMap[$role]) && view()->exists($viewMap[$role])) {
            return view($viewMap[$role], compact('user'));
        }
        
        // Fallback to common profile view
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'f_name' => 'required|string|max:50',
            'l_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update basic info
        $user->update([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('current_password') && $request->filled('password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Current password is incorrect.');
            }
        }

        // Determine redirect route based on role
        $redirectRoute = match($user->role) {
            'admin' => 'admin.profile.edit',
            'registrar' => 'registrar.profile.edit',
            'teacher' => 'teacher.profile.edit',
            'student' => 'student.profile.edit',
            default => 'profile.edit',
        };

        return redirect()->route($redirectRoute)
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Your account has been permanently deleted.');
    }
}