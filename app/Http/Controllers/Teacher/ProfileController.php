<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
    public function edit(Request $request)
    {
        $user = Auth::user();
        if ($request->ajax()) {
            $html = view('profile._form', [
                'formAction' => route('teacher.profile.update'),
                'user'       => $user,
            ])->render();
            return response()->json(['html' => $html]);
        }
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
            'f_name'        => 'required|string|max:50',
            'l_name'        => 'required|string|max:50',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'contact'       => 'nullable|string|max:20',
            'sex'           => 'nullable|in:male,female',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->email  = $request->email;

        if ($request->filled('contact')) $user->contact = $request->contact;
        if ($request->filled('sex'))     $user->sex     = $request->sex;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle profile photo
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $request->file('profile_photo')->store('profile-photos', 'public');
        } elseif ($request->boolean('remove_photo') && $user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->profile_photo = null;
        }

        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully.', 'redirect' => route('teacher.profile.show')]);
        }

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
