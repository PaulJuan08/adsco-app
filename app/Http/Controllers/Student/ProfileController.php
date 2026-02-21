<?php
// app/Http/Controllers/Student/ProfileController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the student's profile.
     */
    public function show()
    {
        $user = Auth::user()->load(['college', 'program']);
        
        // Get additional stats for student profile using only relationships that exist
        $stats = [
            'enrolled_courses' => $user->enrollments()->count(),
            'completed_topics' => $this->getCompletedTopicsCount($user),
            // Remove quiz_attempts if relationship doesn't exist
            // Remove assignments_submitted if relationship doesn't exist
        ];
        
        // Optionally add these if you want to calculate them differently
        // You can add other stats here
        
        return view('student.profile.show', compact('user', 'stats'));
    }

    /**
     * Get completed topics count for a student
     */
    private function getCompletedTopicsCount($user)
    {
        // Check if the relationship exists through a progress table
        // This is a common pattern - adjust based on your actual database structure
        try {
            return $user->completedTopics()->count();
        } catch (\Exception $e) {
            // If the relationship doesn't exist, return 0 or calculate differently
            return 0;
        }
    }

    /**
     * Show the form for editing the student's profile.
     */
    public function edit()
    {
        $user = Auth::user()->load(['college', 'program']);
        return view('student.profile.edit', compact('user'));
    }

    /**
     * Update the student's profile information (including password if provided).
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
            'age' => 'nullable|integer|min:15|max:100',
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

        if ($request->filled('age')) {
            $user->age = $request->age;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('student.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}