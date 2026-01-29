<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\UserApprovedMail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Get search query
        $search = request()->input('search');
        $role = request()->input('role');
        
        // Build query - Registrar can only see teachers (3) and students (4)
        $query = User::whereIn('role', [3, 4]);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                  ->orWhere('l_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        // Apply role filter - only 3 (Teacher) or 4 (Student)
        if ($role && in_array($role, [3, 4])) {
            $query->where('role', $role);
        }
        
        // Get paginated results with 20 per page
        $users = $query->with(['taughtCourses', 'enrolledCourses'])
                       ->orderBy('created_at', 'desc')
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
            'f_name' => 'required|string|max:50',
            'l_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'role' => ['required', Rule::in([3, 4])], // Only Teacher (3) or Student (4)
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required_if:role,3|nullable|string|max:50|unique:users',
            'student_id' => 'required_if:role,4|nullable|string|max:50|unique:users',
            'age' => 'nullable|integer|min:15|max:100',
            'sex' => 'nullable|string|in:male,female',
            'contact' => 'nullable|string|max:20',
        ]);
        
        // Create user with encrypted data (if using Encryptable trait)
        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'employee_id' => $request->employee_id,
            'student_id' => $request->student_id,
            'age' => $request->age,
            'sex' => $request->sex,
            'contact' => $request->contact,
            'is_approved' => true, // Auto-approve users created by registrar
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
        
        return redirect()->route('registrar.users.index')
            ->with('success', 'User created successfully.');
    }
    
    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Check if registrar can view this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                abort(403, 'Unauthorized action. Registrar can only view teachers and students.');
            }
            
            // Pass the encrypted ID to the view
            return view('registrar.users.show', compact('user', 'encryptedId'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Check if registrar can edit this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                abort(403, 'Unauthorized action. Registrar can only edit teachers and students.');
            }
            
            // Pass the encrypted ID to the view
            return view('registrar.users.edit', compact('user', 'encryptedId'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    public function update(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Check if registrar can update this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                abort(403, 'Unauthorized action. Registrar can only update teachers and students.');
            }
            
            $request->validate([
                'f_name' => 'required|string|max:50',
                'l_name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => ['required', Rule::in([3, 4])], // Only Teacher or Student
                'password' => 'nullable|string|min:8|confirmed',
                'employee_id' => 'required_if:role,3|nullable|string|max:50|unique:users,employee_id,' . $id,
                'student_id' => 'required_if:role,4|nullable|string|max:50|unique:users,student_id,' . $id,
                'age' => 'nullable|integer|min:15|max:100',
                'sex' => 'nullable|string|in:male,female',
                'contact' => 'nullable|string|max:20',
                'is_approved' => 'nullable|boolean',
            ]);
            
            // Prepare update data
            $updateData = [
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'role' => $request->role,
                'employee_id' => $request->employee_id,
                'student_id' => $request->student_id,
                'age' => $request->age,
                'sex' => $request->sex,
                'contact' => $request->contact,
            ];
            
            // Handle ID fields based on role
            if ($request->role == 3) { // Teacher
                $updateData['employee_id'] = $request->employee_id;
                $updateData['student_id'] = null; // Clear student_id
            } elseif ($request->role == 4) { // Student
                $updateData['student_id'] = $request->student_id;
                $updateData['employee_id'] = null; // Clear employee_id
            }
            
            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            
            // Update approval status if provided
            if ($request->has('is_approved')) {
                $updateData['is_approved'] = $request->is_approved;
                if ($request->is_approved && !$user->is_approved) {
                    $updateData['approved_at'] = now();
                    $updateData['approved_by'] = auth()->id();
                }
            }
            
            $user->update($updateData);
            
            return redirect()->route('registrar.users.show', $encryptedId)
                ->with('success', 'User updated successfully.');
                
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Check if registrar can delete this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                abort(403, 'Unauthorized action. Registrar can only delete teachers and students.');
            }
            
            $user->delete();
            
            return redirect()->route('registrar.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    public function approve($encryptedId)
    {
        try {
            // Decrypt the ID
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Check if registrar can approve this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                return redirect()->route('registrar.users.index')
                    ->with('error', 'Unauthorized action. Registrar can only approve teachers and students.');
            }
            
            // Check if user is already approved
            if ($user->is_approved) {
                return redirect()->route('registrar.users.show', $encryptedId)
                    ->with('warning', 'User is already approved.');
            }
            
            // Update user approval status
            $user->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);
            
            // Send approval email (optional - comment out if not needed)
            try {
                if (class_exists(\App\Mail\UserApprovedMail::class)) {
                    Mail::to($user->email)->send(new \App\Mail\UserApprovedMail($user));
                }
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }
            
            // Log the approval action
            if (class_exists(\App\Helpers\AuditHelper::class)) {
                \App\Helpers\AuditHelper::log('approve', "Approved user: {$user->email}", $user);
            }
            
            return redirect()->route('registrar.users.show', $encryptedId)
                ->with('success', 'User approved successfully!');
                
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('registrar.users.index')
                ->with('error', 'Invalid user ID provided.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('registrar.users.index')
                ->with('error', 'User not found.');
        } catch (\Exception $e) {
            return redirect()->route('registrar.users.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}