<?php
// app/Http/Controllers/Teacher/ProfileController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;

class ProfileController extends Controller
{
    /**
     * Display the teacher's profile.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get additional stats for teacher profile
        $stats = [
            'total_courses' => Course::where('teacher_id', $user->id)->count(),
            'total_students' => Course::where('teacher_id', $user->id)
                ->withCount('enrollments')
                ->get()
                ->sum('enrollments_count'),
        ];
        
        return view('teacher.profile.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the teacher's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('teacher.profile.edit', compact('user'));
    }

    /**
     * Update the teacher's profile information (including password if provided).
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Base validation rules
        $rules = [
            'f_name' => 'required|string|max:50',
            'l_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|max:20',
            'sex' => 'nullable|in:male,female',
        ];

        // Add password validation only if password field is filled
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        // Update basic info
        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->email = $request->email;
        
        if ($request->filled('contact')) {
            $user->contact = $request->contact;
        }

        if ($request->filled('sex')) {
            $user->sex = $request->sex;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}