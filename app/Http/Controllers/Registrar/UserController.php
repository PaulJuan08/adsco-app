<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserApprovedMail;

class UserController extends Controller
{
    public function index()
    {
        // Registrar can only see teachers and students
        $users = User::whereIn('role', [3, 4]) // Teachers (3) and Students (4)
            ->with(['taughtCourses', 'enrolledCourses'])
            ->latest()
            ->paginate(20);
        
        return view('registrar.users.index', compact('users'));
    }
    
    public function create()
    {
        // Registrar can only create teachers and students
        return view('registrar.users.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:3,4', // Only teacher (3) or student (4)
            'employee_id' => 'required_if:role,3|nullable|unique:users,employee_id',
            'student_id' => 'required_if:role,4|nullable|unique:users,student_id',
            'age' => 'nullable|integer|min:15|max:100',
            'sex' => 'nullable|in:male,female',
            'contact' => 'nullable|string|max:20',
        ]);
        
        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'employee_id' => $request->employee_id,
            'student_id' => $request->student_id,
            'age' => $request->age,
            'sex' => $request->sex,
            'contact' => $request->contact,
            'is_approved' => true, // Auto-approve when created by registrar
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
        
        return redirect()->route('registrar.users.index')
            ->with('success', 'User created and approved successfully!');
    }
    
    public function show($id)
    {
        $user = User::whereIn('role', [3, 4])->findOrFail($id);
        
        return view('registrar.users.show', compact('user'));
    }
    
    public function edit($id)
    {
        $user = User::whereIn('role', [3, 4])->findOrFail($id);
        
        return view('registrar.users.edit', compact('user'));
    }
    
    public function update(Request $request, $id)
    {
        $user = User::whereIn('role', [3, 4])->findOrFail($id);
        
        $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:3,4',
            'employee_id' => 'required_if:role,3|nullable|unique:users,employee_id,' . $user->id,
            'student_id' => 'required_if:role,4|nullable|unique:users,student_id,' . $user->id,
            'age' => 'nullable|integer|min:15|max:100',
            'sex' => 'nullable|in:male,female',
            'contact' => 'nullable|string|max:20',
        ]);
        
        $user->update([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'role' => $request->role,
            'employee_id' => $request->employee_id,
            'student_id' => $request->student_id,
            'age' => $request->age,
            'sex' => $request->sex,
            'contact' => $request->contact,
        ]);
        
        return redirect()->route('registrar.users.show', $user->id)
            ->with('success', 'User updated successfully!');
    }
    
    public function destroy($id)
    {
        $user = User::whereIn('role', [3, 4])->findOrFail($id);
        
        // Check if user has related records before deleting
        if ($user->role == 3 && $user->taughtCourses()->exists()) {
            return redirect()->route('registrar.users.index')
                ->with('error', 'Cannot delete teacher with assigned courses.');
        }
        
        if ($user->role == 4 && $user->enrolledCourses()->exists()) {
            return redirect()->route('registrar.users.index')
                ->with('error', 'Cannot delete student with course enrollments.');
        }
        
        $user->delete();
        
        return redirect()->route('registrar.users.index')
            ->with('success', 'User deleted successfully!');
    }
    
    public function approve($id)
    {
        $user = User::whereIn('role', [3, 4])->findOrFail($id);
        
        if ($user->is_approved) {
            return redirect()->route('registrar.users.index')
                ->with('warning', 'User is already approved.');
        }
        
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
        
        // Send approval email
        try {
            if (class_exists(\App\Mail\UserApprovedMail::class)) {
                Mail::to($user->email)->send(new \App\Mail\UserApprovedMail($user));
            }
        } catch (\Exception $e) {
            // Log error but continue
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }
        
        return redirect()->route('registrar.users.index')
            ->with('success', 'User approved successfully!');
    }
}