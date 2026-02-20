<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Mail\UserApprovedMail;
use App\Models\College;
use App\Models\Program;

class UserController extends Controller
{
    public function index()
    {
        // Get filters
        $search = request()->input('search');
        $role = request()->input('role');
        $status = request()->input('status');
        $page = request()->get('page', 1);
        
        // Pre-define role names for view
        $roleNames = [
            1 => 'Admin',
            2 => 'Registrar',
            3 => 'Teacher',
            4 => 'Student'
        ];
        
        // Get user statistics using cached method
        $stats = $this->getStats();
        
        // Check if we need to bypass cache (after deletion)
        $bypassCache = session()->get('bypass_cache', false);
        
        // Create cache key based on filters
        $cacheKey = 'users_index_' . md5(json_encode([
            'search' => $search,
            'role' => $role,
            'status' => $status,
            'page' => $page
        ]));
        
        // If we need to bypass cache or this is a search, clear this specific cache
        if ($bypassCache || $search) {
            Cache::forget($cacheKey);
            if ($bypassCache) {
                session()->forget('bypass_cache');
            }
        }
        
        // Cache for 1 minute (reduced from 2 for fresher data)
        $users = Cache::remember($cacheKey, 60, function() use ($search, $role, $status) {
            $query = User::select(['id', 'f_name', 'l_name', 'email', 'role', 'is_approved', 'created_at']);
            
            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Apply role filter
            if ($role && in_array($role, [1, 2, 3, 4])) {
                $query->where('role', $role);
            }
            
            // Apply status filter
            if ($status === 'pending') {
                $query->where('is_approved', false);
            }
            
            return $query->orderBy('created_at', 'desc')->paginate(10);
        });
        
        return view('admin.users.index', compact('users', 'stats', 'roleNames'));
    }
    
    public function create()
    {
        // Only admin can create users
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only admins can create users.');
        }

        // Use the main getStats() method instead of separate cache
        $stats = $this->getStats();

        // Cache role options for quick access (1 hour cache)
        $roleOptions = Cache::remember('user_role_options', 3600, function() {
            return [
                1 => [
                    'name' => 'Admin', 
                    'icon' => 'user-shield', 
                    'color' => 'danger',
                    'description' => 'Full system access and management',
                    'id_required' => false
                ],
                2 => [
                    'name' => 'Registrar', 
                    'icon' => 'clipboard-list', 
                    'color' => 'primary',
                    'description' => 'Manage student registrations and records',
                    'id_required' => true,
                    'id_type' => 'employee_id'
                ],
                3 => [
                    'name' => 'Teacher', 
                    'icon' => 'chalkboard-teacher', 
                    'color' => 'success',
                    'description' => 'Create courses and manage students',
                    'id_required' => true,
                    'id_type' => 'employee_id'
                ],
                4 => [
                    'name' => 'Student', 
                    'icon' => 'user-graduate', 
                    'color' => 'info',
                    'description' => 'Enroll in courses and view materials',
                    'id_required' => true,
                    'id_type' => 'student_id'
                ]
            ];
        });

        // Cache form suggestions
        $suggestions = Cache::remember('user_form_suggestions', 1800, function() {
            $currentYear = now()->year;
            return [
                'employee_id_prefix' => 'EMP-' . $currentYear . '-',
                'student_id_prefix' => 'STU-' . $currentYear . '-',
                'current_year' => $currentYear,
                'month' => now()->format('m'),
                'random_suffix' => rand(1000, 9999)
            ];
        });

        // Pass active colleges for the academic section
        $colleges = College::where('status', 1)
                        ->orderBy('college_name')
                        ->get(['id', 'college_name', 'college_year']);

        return view('admin.users.create', compact('roleOptions', 'stats', 'suggestions', 'colleges'));
    }
    
    public function store(Request $request)
    {
        // Only admin can create users
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only admins can create users.');
        }

        // Determine which ID field is required based on role
        $idRules = [];
        $role = $request->role;
        
        if (in_array($role, [2, 3])) { // Registrar or Teacher
            $idRules['employee_id'] = 'required|string|max:50|unique:users,employee_id';
        } elseif ($role == 4) { // Student
            $idRules['student_id'] = 'required|string|max:50|unique:users,student_id';
        }
        
        $request->validate(array_merge([
            'f_name' => 'required|string|max:50',
            'l_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:1,2,3,4',
            'password' => 'required|string|min:8|confirmed',
            'age' => 'nullable|integer|min:15|max:100',
            'sex' => 'nullable|in:male,female',
            'contact' => 'nullable|string|max:20',
            'college_id'   => 'nullable|exists:colleges,id',
            'program_id'   => 'nullable|exists:programs,id',
            'college_year' => 'nullable|string|max:50',
        ], $idRules));
        
        $userData = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'age' => $request->age,
            'sex' => $request->sex,
            'contact' => $request->contact,
            'created_by' => auth()->id()
        ];
        
        // Add appropriate ID based on role
        if ($request->filled('employee_id')) {
            $userData['employee_id'] = $request->employee_id;
        }
        if ($request->filled('student_id')) {
            $userData['student_id'] = $request->student_id;
        }

        // Academic assignment for students
        if ($role == 4) {
            if ($request->filled('program_id')) {
                $program = Program::find($request->program_id);
                $userData['program_id'] = $program->id;
                $userData['college_id'] = $program->college_id; // derived from program
            } elseif ($request->filled('college_id')) {
                $userData['college_id'] = $request->college_id;
            }
            if ($request->filled('college_year')) {
                $userData['college_year'] = $request->college_year;
            }
        }
        
        $user = User::create($userData);
        
        // Clear all user-related caches
        $this->clearUserCaches();
        
        // Clear specific caches
        Cache::forget('user_role_options');
        Cache::forget('user_form_suggestions');
        Cache::forget('create_user_stats_' . auth()->id());
        
        // Cache the new user for quick access
        $cacheKey = 'user_show_' . $user->id;
        Cache::put($cacheKey, $user, 300);
        
        // Log the creation
        if (class_exists(\App\Helpers\AuditHelper::class)) {
            \App\Helpers\AuditHelper::log('create', "Created new user: {$user->email}", $user);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.')
            ->with('user_id', $user->id);
    }
    
    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Cache individual user show for 5 minutes with relationships
            $cacheKey = 'user_show_detail_' . $id;
            $user = Cache::remember($cacheKey, 300, function() use ($id) {
                return User::with(['approvedBy', 'createdBy', 'college', 'program'])->findOrFail($id);
            });
            
            // Get user stats for the header (cached)
            $stats = Cache::remember('user_show_stats_' . auth()->id(), 300, function() {
                return $this->getStats();
            });
            
            // Cache role names for display
            $roleNames = Cache::remember('user_role_names', 3600, function() {
                return [
                    1 => 'Admin',
                    2 => 'Registrar',
                    3 => 'Teacher',
                    4 => 'Student'
                ];
            });
            
            // Get activity logs if available (cached for 2 minutes)
            $activities = Cache::remember('user_activities_' . $id, 120, function() use ($user) {
                $activities = collect();
                
                // Check if user has auditLogs relationship
                if (method_exists($user, 'auditLogs')) {
                    try {
                        $activities = $user->auditLogs()
                            ->latest()
                            ->take(10)
                            ->get();
                    } catch (\Exception $e) {
                        \Log::error('Failed to fetch user audit logs: ' . $e->getMessage());
                    }
                }
                
                return $activities;
            });
            
            // Get user statistics for the sidebar (cached)
            $userStats = Cache::remember('user_detailed_stats_' . $id, 300, function() use ($user) {
                return $this->getUserDetailedStats($user);
            });
            
            return view('admin.users.show', compact('user', 'stats', 'roleNames', 'activities', 'userStats'));
            
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Only admin can edit users, or users can edit their own profile
            if (!auth()->user()->isAdmin() && auth()->user()->id != $user->id) {
                abort(403, 'Unauthorized action. Only admins can edit other users.');
            }
            
            // Cache individual user for edit (2 minutes for fresh data)
            $cacheKey = 'user_edit_detail_' . $id;
            $user = Cache::remember($cacheKey, 120, function() use ($id) {
                return User::with(['approvedBy', 'college', 'program'])->findOrFail($id);
            });
            
            // Cache role options
            $roleOptions = Cache::remember('user_role_options', 3600, function() {
                return [
                    1 => [
                        'name' => 'Admin', 
                        'icon' => 'user-shield', 
                        'color' => 'danger',
                        'description' => 'Full system access and management',
                        'id_required' => false
                    ],
                    2 => [
                        'name' => 'Registrar', 
                        'icon' => 'clipboard-list', 
                        'color' => 'primary',
                        'description' => 'Manage student registrations and records',
                        'id_required' => true,
                        'id_type' => 'employee_id'
                    ],
                    3 => [
                        'name' => 'Teacher', 
                        'icon' => 'chalkboard-teacher', 
                        'color' => 'success',
                        'description' => 'Create courses and manage students',
                        'id_required' => true,
                        'id_type' => 'employee_id'
                    ],
                    4 => [
                        'name' => 'Student', 
                        'icon' => 'user-graduate', 
                        'color' => 'info',
                        'description' => 'Enroll in courses and view materials',
                        'id_required' => true,
                        'id_type' => 'student_id'
                    ]
                ];
            });
            
            // Get user stats for the header
            $stats = Cache::remember('user_edit_stats_' . auth()->id(), 300, function() {
                return $this->getStats();
            });
            
            // Cache form suggestions
            $suggestions = Cache::remember('user_form_suggestions', 1800, function() {
                $currentYear = now()->year;
                return [
                    'employee_id_prefix' => 'EMP-' . $currentYear . '-',
                    'student_id_prefix' => 'STU-' . $currentYear . '-',
                    'current_year' => $currentYear
                ];
            });

            // Pass active colleges for the academic section
            $colleges = College::where('status', 1)
                            ->orderBy('college_name')
                            ->get(['id', 'college_name', 'college_year']);
            
            return view('admin.users.edit', compact('user', 'encryptedId', 'roleOptions', 'stats', 'suggestions', 'colleges'));
            
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    public function update(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Only admin can update users, or users can update their own profile
            if (!auth()->user()->isAdmin() && auth()->user()->id != $user->id) {
                abort(403, 'Unauthorized action. Only admins can update other users.');
            }
            
            // Determine which ID field validation is needed based on current and new role
            $idRules = [];
            $role = $request->role;
            
            // Only admin can change roles
            if ($role != $user->role && !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized action. Only admins can change user roles.');
            }
            
            // If role is changing to registrar or teacher
            if (in_array($role, [2, 3]) && !in_array($user->role, [2, 3])) {
                $idRules['employee_id'] = 'required|string|max:50|unique:users,employee_id';
            }
            // If role is changing to student
            elseif ($role == 4 && $user->role != 4) {
                $idRules['student_id'] = 'required|string|max:50|unique:users,student_id';
            }
            // If role is same but ID might need updating
            elseif (in_array($role, [2, 3]) && in_array($user->role, [2, 3])) {
                $idRules['employee_id'] = 'required|string|max:50|unique:users,employee_id,' . $id;
            }
            elseif ($role == 4 && $user->role == 4) {
                $idRules['student_id'] = 'required|string|max:50|unique:users,student_id,' . $id;
            }
            
            $request->validate(array_merge([
                'f_name' => 'required|string|max:50',
                'l_name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:1,2,3,4',
                'password' => 'nullable|string|min:8|confirmed',
                'age' => 'nullable|integer|min:15|max:100',
                'sex' => 'nullable|in:male,female',
                'contact' => 'nullable|string|max:20',
                'college_id'   => 'nullable|exists:colleges,id',
                'program_id'   => 'nullable|exists:programs,id',
                'college_year' => 'nullable|string|max:50',
            ], $idRules));
            
            // Prepare update data
            $updateData = [
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'age' => $request->age,
                'sex' => $request->sex,
                'contact' => $request->contact
            ];
            
            // Only admin can change role and role-specific fields
            if (auth()->user()->isAdmin()) {
                $updateData['role'] = $request->role;
                
                // Handle ID fields based on role
                if (in_array($request->role, [2, 3])) {
                    $updateData['employee_id'] = $request->employee_id;
                    $updateData['student_id'] = null;
                    // Clear academic fields when switching away from student
                    $updateData['college_id'] = null;
                    $updateData['program_id'] = null;
                    $updateData['college_year'] = null;
                    
                } elseif ($request->role == 4) {
                    $updateData['student_id'] = $request->student_id;
                    $updateData['employee_id'] = null;
                    
                    // Program is the source of truth; college is derived
                    if ($request->filled('program_id')) {
                        $program = Program::find($request->program_id);
                        $updateData['program_id'] = $program->id;
                        $updateData['college_id'] = $program->college_id;
                    } elseif ($request->filled('college_id')) {
                        $updateData['college_id'] = $request->college_id;
                        $updateData['program_id'] = null;
                    } else {
                        $updateData['college_id'] = null;
                        $updateData['program_id'] = null;
                    }
                    $updateData['college_year'] = $request->filled('college_year') ? $request->college_year : null;
                    
                } else {
                    // Admin role - clear all role-specific fields
                    $updateData['employee_id'] = null;
                    $updateData['student_id'] = null;
                    $updateData['college_id'] = null;
                    $updateData['program_id'] = null;
                    $updateData['college_year'] = null;
                }
            }
            
            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }
            
            $user->update($updateData);
            
            // Clear user-related caches
            $this->clearUserCaches($id);
            
            // Log the update
            if (class_exists(\App\Helpers\AuditHelper::class)) {
                \App\Helpers\AuditHelper::log('update', "Updated user: {$user->email}", $user);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.')
                ->with('user_id', $user->id);
                
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Only admin can delete users
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized action. Only admins can delete users.');
            }
            
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You cannot delete your own account.');
            }
            
            // Store user info for logging
            $userEmail = $user->email;
            $userName = $user->f_name . ' ' . $user->l_name;
            $userRole = $user->role;
            $userCollegeId = $user->college_id;
            $userProgramId = $user->program_id;
            
            // Delete the user
            $user->delete();
            
            // ============ THOROUGH CACHE CLEARING ============
            
            // Clear user-related caches
            $this->clearUserCaches($id);
            
            // Clear college-specific caches if this was a student
            if ($userRole == 4) {
                if ($userCollegeId) {
                    Cache::forget('college_students_' . $userCollegeId);
                    Cache::forget('college_' . $userCollegeId . '_stats');
                    Cache::forget('college_programs_' . $userCollegeId);
                }
                if ($userProgramId) {
                    Cache::forget('program_students_' . $userProgramId);
                    Cache::forget('program_' . $userProgramId . '_stats');
                }
            }
            
            // Clear all user index caches aggressively
            $this->clearAllUserIndexCaches();
            
            // Set session flag to bypass cache on next index load
            session()->flash('bypass_cache', true);
            
            // Log the deletion
            if (class_exists(\App\Helpers\AuditHelper::class)) {
                \App\Helpers\AuditHelper::log('delete', "Deleted user: {$userEmail}", null, [
                    'user_id' => $id,
                    'user_name' => $userName
                ]);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', "User {$userName} deleted successfully.");
                
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
    
    public function approve($encryptedId)
    {
        try {
            // Only admin and registrar can approve users
            if (!auth()->user()->isAdmin() && !auth()->user()->isRegistrar()) {
                abort(403, 'Unauthorized action. Only admins and registrars can approve users.');
            }
            
            // Decrypt the ID
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Check if user is already approved
            if ($user->is_approved) {
                return redirect()->route('admin.users.index')
                    ->with('warning', 'User is already approved.');
            }
            
            // Update user approval status
            $user->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);
            
            // ============ 🔥 CRITICAL: CLEAR ALL CACHES ============
            
            // Clear specific user caches
            Cache::forget('user_show_detail_' . $id);
            Cache::forget('user_edit_detail_' . $id);
            Cache::forget('user_detailed_stats_' . $id);
            Cache::forget('user_activities_' . $id);
            
            // Clear ALL user index caches (this is what's missing)
            $this->clearAllUserIndexCaches();
            
            // Clear dashboard caches for ALL admins
            $admins = User::where('role', 1)->pluck('id')->toArray();
            foreach ($admins as $adminId) {
                Cache::forget('admin_dashboard_' . $adminId);
            }
            
            // Clear registrar dashboard caches
            $registrars = User::where('role', 2)->pluck('id')->toArray();
            foreach ($registrars as $registrarId) {
                Cache::forget('registrar_dashboard_' . $registrarId);
            }
            
            // Clear stats caches
            Cache::forget('user_stats');
            Cache::forget('pending_users_count');
            Cache::forget('users_this_month');
            
            // Set session flag to bypass cache on next load
            session()->flash('bypass_cache', true);
            
            // Send approval email
            try {
                if (class_exists(\App\Mail\UserApprovedMail::class)) {
                    Mail::to($user->email)->queue(new \App\Mail\UserApprovedMail($user));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }
            
            // Log the approval action
            if (class_exists(\App\Helpers\AuditHelper::class)) {
                \App\Helpers\AuditHelper::log('approve', "Approved user: {$user->email}", $user);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User approved successfully!')
                ->with('user_id', $user->id);
                
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Invalid user ID provided.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        } catch (\Exception $e) {
            \Log::error('Error approving user: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all user-related caches
     * 
     * @param int|null $userId Specific user ID to clear
     */
    private function clearUserCaches($userId = null)
    {
        // Clear specific user caches
        if ($userId) {
            Cache::forget('user_show_detail_' . $userId);
            Cache::forget('user_edit_detail_' . $userId);
            Cache::forget('user_activities_' . $userId);
            Cache::forget('user_detailed_stats_' . $userId);
            Cache::forget('approver_user_' . $userId);
        }
        
        // Clear dashboard caches for ALL admins
        $admins = User::where('role', 1)->pluck('id')->toArray();
        foreach ($admins as $adminId) {
            Cache::forget('admin_dashboard_' . $adminId);
        }
        
        // Clear registrar dashboard caches
        $registrars = User::where('role', 2)->pluck('id')->toArray();
        foreach ($registrars as $registrarId) {
            Cache::forget('registrar_dashboard_' . $registrarId);
        }
        
        // Clear user stats caches
        Cache::forget('user_stats');
        Cache::forget('pending_users_count');
        Cache::forget('users_this_month');
        
        // Clear create/edit specific caches
        Cache::forget('create_user_stats_' . auth()->id());
        Cache::forget('user_edit_stats_' . auth()->id());
        Cache::forget('user_show_stats_' . auth()->id());
        
        // Clear shared caches
        Cache::forget('user_role_options');
        Cache::forget('user_form_suggestions');
        Cache::forget('user_role_names');
        
        // Clear tag-based caches if available
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['users', 'users_index'])->flush();
        }
    }
    
    /**
     * Clear all user index caches for all possible filter combinations
     */
    private function clearAllUserIndexCaches()
    {
        // Clear up to page 20 to be safe
        for ($page = 1; $page <= 20; $page++) {
            // All possible role values
            $roles = [null, 1, 2, 3, 4];
            
            // All possible status values
            $statuses = [null, 'pending'];
            
            // Common search patterns
            $searches = [null, '', 'a', 'e', 'i', 'o', 'u', 'admin', 'teacher', 'student', 'registrar'];
            
            foreach ($roles as $role) {
                foreach ($statuses as $status) {
                    foreach ($searches as $search) {
                        // Clear admin user index cache
                        $adminCacheKey = 'users_index_' . md5(json_encode([
                            'search' => $search,
                            'role' => $role,
                            'status' => $status,
                            'page' => $page
                        ]));
                        Cache::forget($adminCacheKey);
                        
                        // Clear registrar user index cache
                        $registrarCacheKey = 'registrar_users_index_' . md5(json_encode([
                            'search' => $search,
                            'role' => $role,
                            'status' => $status,
                            'page' => $page
                        ]));
                        Cache::forget($registrarCacheKey);
                    }
                }
            }
        }
        
        // Also clear any caches with actual user-specific search terms
        // Get all users to clear their name-based cache keys
        $users = User::select('id', 'f_name', 'l_name', 'email')->get();
        foreach ($users as $user) {
            $searchTerms = [
                strtolower($user->f_name),
                strtolower($user->l_name),
                strtolower($user->f_name . ' ' . $user->l_name),
                strtolower($user->email)
            ];
            
            foreach ($searchTerms as $term) {
                if (strlen($term) > 2) {
                    for ($page = 1; $page <= 5; $page++) {
                        $cacheKey = 'users_index_' . md5(json_encode([
                            'search' => $term,
                            'role' => null,
                            'status' => null,
                            'page' => $page
                        ]));
                        Cache::forget($cacheKey);
                    }
                }
            }
        }
        
        \Log::info('All user index caches cleared after approval');
    }
    
    /**
     * Force clear all caches (emergency use)
     */
    public function forceClearAllCaches()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        Cache::flush();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'All caches cleared successfully.');
    }
    
    /**
     * Optimized user statistics (for dashboard or sidebar)
     */
    public function getStats()
    {
        return Cache::remember('user_stats', 300, function() {
            $stats = User::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN role = 1 THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN role = 2 THEN 1 ELSE 0 END) as registrars,
                SUM(CASE WHEN role = 3 THEN 1 ELSE 0 END) as teachers,
                SUM(CASE WHEN role = 4 THEN 1 ELSE 0 END) as students
            ')->first();
            
            // Add this month's count
            $thisMonth = User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            return [
                'total' => $stats->total ?? 0,
                'pending' => $stats->pending ?? 0,
                'admins' => $stats->admins ?? 0,
                'registrars' => $stats->registrars ?? 0,
                'teachers' => $stats->teachers ?? 0,
                'students' => $stats->students ?? 0,
                'this_month' => $thisMonth ?? 0
            ];
        });
    }
    
    /**
     * Get detailed statistics for a specific user
     */
    private function getUserDetailedStats(User $user)
    {
        return Cache::remember('detailed_user_stats_' . $user->id, 300, function() use ($user) {
            $stats = [
                'login_count' => 0,
                'course_count' => 0,
                'enrollment_count' => 0,
                'quiz_attempts' => 0,
                'assignments_submitted' => 0,
                'completed_topics' => 0,
                'gpa' => 0.0
            ];
            
            try {
                // Get login count from audit logs if available
                if (method_exists($user, 'auditLogs')) {
                    $stats['login_count'] = $user->auditLogs()
                        ->where('action', 'like', '%login%')
                        ->count();
                }
                
                // Get course count for teachers
                if ($user->isTeacher()) {
                    $stats['course_count'] = $user->taughtCourses()->count();
                }
                
                // Get enrollment count for students
                if ($user->isStudent()) {
                    $stats['enrollment_count'] = $user->enrollments()->count();
                    
                    // Get quiz attempts
                    if (method_exists($user, 'quizAttempts')) {
                        $stats['quiz_attempts'] = $user->quizAttempts()->count();
                    }
                    
                    // Get assignments submitted
                    if (method_exists($user, 'submittedAssignments')) {
                        $stats['assignments_submitted'] = $user->submittedAssignments()->count();
                    }
                    
                    // Get completed topics
                    if (method_exists($user, 'completedTopics')) {
                        $stats['completed_topics'] = $user->completedTopics()->count();
                    }
                    
                    // Get GPA
                    if ($user->enrollments()->whereNotNull('grade')->exists()) {
                        $stats['gpa'] = round($user->enrollments()
                            ->whereNotNull('grade')
                            ->avg('grade'), 2);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to get user detailed stats: ' . $e->getMessage());
            }
            
            return $stats;
        });
    }
}