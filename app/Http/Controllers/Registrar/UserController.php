<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Mail\UserApprovedMail;
use Illuminate\Validation\Rule;
use App\Traits\CacheManager;

class UserController extends Controller
{
    use CacheManager;
    
    /**
     * Display a listing of users (teachers and students only)
     */
    public function index()
    {
        // Get filters
        $search = request()->input('search');
        $role = request()->input('role');
        $status = request()->input('status');
        
        // Pre-define role names for view
        $roleNames = [
            3 => 'Teacher',
            4 => 'Student'
        ];
        
        // Get user statistics using cached method
        $stats = $this->getStats();
        
        // 🔥 FIXED: Don't cache pending views - always show fresh data
        if ($status === 'pending' || $search) {
            $query = User::whereIn('role', [3, 4])
                ->select(['id', 'f_name', 'l_name', 'email', 'role', 'is_approved', 'employee_id', 'student_id', 'created_at']);
            
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
            
            // Apply role filter
            if ($role && in_array($role, [3, 4])) {
                $query->where('role', $role);
            }
            
            // Apply status filter
            if ($status === 'pending') {
                $query->where('is_approved', false);
            }
            
            $users = $query->orderBy('created_at', 'desc')->paginate(20);
        } else {
            // Only cache non-pending, non-search views for better performance
            $cacheKey = 'registrar_users_index_' . md5(json_encode([
                'search' => $search,
                'role' => $role,
                'status' => $status,
                'page' => request()->get('page', 1)
            ]));
            
            // Cache for 2 minutes
            $users = Cache::remember($cacheKey, 120, function() use ($search, $role, $status) {
                $query = User::whereIn('role', [3, 4])
                    ->select(['id', 'f_name', 'l_name', 'email', 'role', 'is_approved', 'employee_id', 'student_id', 'created_at']);
                
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
                
                // Apply role filter
                if ($role && in_array($role, [3, 4])) {
                    $query->where('role', $role);
                }
                
                // Apply status filter
                if ($status === 'pending') {
                    $query->where('is_approved', false);
                }
                
                return $query->orderBy('created_at', 'desc')->paginate(20);
            });
        }
        
        return view('registrar.users.index', compact('users', 'stats', 'roleNames'));
    }
    
    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Get user stats for the header (cached)
        $stats = $this->getStats();
        
        // Cache role options for quick access (1 hour cache)
        $roleOptions = Cache::remember('registrar_user_role_options', 3600, function() {
            return [
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
        $suggestions = Cache::remember('registrar_user_form_suggestions', 1800, function() {
            $currentYear = now()->year;
            return [
                'employee_id_prefix' => 'EMP-' . $currentYear . '-',
                'student_id_prefix' => 'STU-' . $currentYear . '-',
                'current_year' => $currentYear,
                'month' => now()->format('m'),
                'random_suffix' => rand(1000, 9999)
            ];
        });

        return view('registrar.users.create', compact('roleOptions', 'stats', 'suggestions'));
    }
    
    /**
     * Store a newly created user in storage
     */
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
        
        // Create user
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
            'approved_by' => auth()->id(),
            'created_by' => auth()->id()
        ]);
        
        // 🔥 CRITICAL: Clear ALL registrar caches
        $this->clearAllRegistrarCaches();
        
        // Also clear admin caches since admin might see these users
        $this->clearAllAdminCaches();
        
        // Clear specific caches
        Cache::forget('registrar_user_role_options');
        Cache::forget('registrar_user_form_suggestions');
        Cache::forget('create_user_stats_' . auth()->id());
        
        // Cache the new user for quick access
        $cacheKey = 'registrar_user_show_' . $user->id;
        Cache::put($cacheKey, $user, 300);
        
        // Log the creation
        if (class_exists(\App\Helpers\AuditHelper::class)) {
            \App\Helpers\AuditHelper::log('create', "Created new user: {$user->email}", $user);
        }
        
        return redirect()->route('registrar.users.index')
            ->with('success', 'User created successfully.')
            ->with('user_id', $user->id);
    }
    
    /**
     * Display the specified user
     */
    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Cache individual user show for 5 minutes with relationships
            $cacheKey = 'registrar_user_show_detail_' . $id;
            $user = Cache::remember($cacheKey, 300, function() use ($id) {
                return User::with(['approvedBy', 'createdBy'])->findOrFail($id);
            });
            
            // Check if registrar can view this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                abort(403, 'Unauthorized action. Registrar can only view teachers and students.');
            }
            
            // Get user stats for the header (cached)
            $stats = Cache::remember('registrar_user_show_stats_' . auth()->id(), 300, function() {
                return $this->getStats();
            });
            
            // Cache role names for display
            $roleNames = Cache::remember('registrar_user_role_names', 3600, function() {
                return [
                    3 => 'Teacher',
                    4 => 'Student'
                ];
            });
            
            // Get activity logs if available (cached for 2 minutes)
            $activities = Cache::remember('registrar_user_activities_' . $id, 120, function() use ($user) {
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
            $userStats = Cache::remember('registrar_user_detailed_stats_' . $id, 300, function() use ($user) {
                return $this->getUserDetailedStats($user);
            });
            
            return view('registrar.users.show', compact('user', 'encryptedId', 'stats', 'roleNames', 'activities', 'userStats'));
            
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    /**
     * Show the form for editing the specified user
     */
    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Cache individual user for edit (2 minutes for fresh data)
            $cacheKey = 'registrar_user_edit_detail_' . $id;
            $user = Cache::remember($cacheKey, 120, function() use ($id) {
                return User::with(['approvedBy'])->findOrFail($id);
            });
            
            // Check if registrar can edit this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                abort(403, 'Unauthorized action. Registrar can only edit teachers and students.');
            }
            
            // Cache role options
            $roleOptions = Cache::remember('registrar_user_role_options', 3600, function() {
                return [
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
            $stats = Cache::remember('registrar_user_edit_stats_' . auth()->id(), 300, function() {
                return $this->getStats();
            });
            
            // Cache form suggestions
            $suggestions = Cache::remember('registrar_user_form_suggestions', 1800, function() {
                $currentYear = now()->year;
                return [
                    'employee_id_prefix' => 'EMP-' . $currentYear . '-',
                    'student_id_prefix' => 'STU-' . $currentYear . '-',
                    'current_year' => $currentYear
                ];
            });
            
            return view('registrar.users.edit', compact('user', 'encryptedId', 'roleOptions', 'stats', 'suggestions'));
            
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    /**
     * Update the specified user in storage
     */
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
            
            // 🔥 Clear all caches
            $this->clearAllRegistrarCaches($id);
            $this->clearAllAdminCaches($id);
            
            // Clear specific caches
            Cache::forget('registrar_user_show_detail_' . $id);
            Cache::forget('registrar_user_edit_detail_' . $id);
            Cache::forget('registrar_user_activities_' . $id);
            Cache::forget('registrar_user_edit_stats_' . auth()->id());
            
            // Log the update
            if (class_exists(\App\Helpers\AuditHelper::class)) {
                \App\Helpers\AuditHelper::log('update', "Updated user: {$user->email}", $user);
            }
            
            return redirect()->route('registrar.users.show', $encryptedId)
                ->with('success', 'User updated successfully.')
                ->with('user_id', $user->id);
                
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    /**
     * Remove the specified user from storage
     */
    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::findOrFail($id);
            
            // Check if registrar can delete this user (only teachers and students)
            if ($user->role != 3 && $user->role != 4) {
                abort(403, 'Unauthorized action. Registrar can only delete teachers and students.');
            }
            
            // Get user email for logging before deletion
            $userEmail = $user->email;
            
            $user->delete();
            
            // 🔥 Clear all caches
            $this->clearAllRegistrarCaches($id);
            $this->clearAllAdminCaches($id);
            
            // Clear specific caches
            Cache::forget('registrar_user_show_detail_' . $id);
            Cache::forget('registrar_user_edit_detail_' . $id);
            Cache::forget('registrar_user_activities_' . $id);
            
            // Log the deletion
            if (class_exists(\App\Helpers\AuditHelper::class)) {
                \App\Helpers\AuditHelper::log('delete', "Deleted user: {$userEmail}", null, ['user_id' => $id]);
            }
            
            return redirect()->route('registrar.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid user ID');
        }
    }
    
    /**
     * Approve a pending user
     */
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
            
            // 🔥 Clear ALL caches - this is critical for pending users to disappear
            $this->clearUserApprovalCaches($user);
            
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
            
            return redirect()->route('registrar.users.show', $encryptedId)
                ->with('success', 'User approved successfully!')
                ->with('user_id', $user->id);
                
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
    
    /**
     * Clear all user-related caches
     * 
     * @param int|null $userId Specific user ID to clear
     */
    private function clearUserCaches($userId = null)
    {
        // 🔥 FIXED: Properly clear ALL registrar user index caches
        for ($page = 1; $page <= 10; $page++) {
            // All possible filter combinations
            $filterCombinations = [
                ['role' => null, 'status' => null],
                ['role' => null, 'status' => 'pending'],
                ['role' => 3, 'status' => null],
                ['role' => 3, 'status' => 'pending'],
                ['role' => 4, 'status' => null],
                ['role' => 4, 'status' => 'pending'],
            ];
            
            foreach ($filterCombinations as $filters) {
                $cacheKey = 'registrar_users_index_' . md5(json_encode([
                    'search' => null,
                    'role' => $filters['role'],
                    'status' => $filters['status'],
                    'page' => $page
                ]));
                Cache::forget($cacheKey);
                
                // Also clear with search parameter
                $cacheKeyWithSearch = 'registrar_users_index_' . md5(json_encode([
                    'search' => '*',
                    'role' => $filters['role'],
                    'status' => $filters['status'],
                    'page' => $page
                ]));
                Cache::forget($cacheKeyWithSearch);
            }
        }
        
        // Clear specific user caches
        if ($userId) {
            Cache::forget('registrar_user_show_detail_' . $userId);
            Cache::forget('registrar_user_edit_detail_' . $userId);
            Cache::forget('registrar_user_activities_' . $userId);
            Cache::forget('registrar_user_detailed_stats_' . $userId);
            Cache::forget('approver_user_' . $userId);
            Cache::forget('registrar_user_show_' . $userId);
        }
        
        // Clear dashboard caches for ALL registrars, not just current user
        $registrars = User::where('role', 2)->pluck('id')->toArray();
        foreach ($registrars as $registrarId) {
            Cache::forget('registrar_dashboard_' . $registrarId);
        }
        
        // Clear user stats caches
        Cache::forget('registrar_user_stats');
        Cache::forget('registrar_pending_users_count');
        Cache::forget('registrar_users_this_month');
        
        // Clear create/edit specific caches
        Cache::forget('create_user_stats_' . auth()->id());
        Cache::forget('registrar_user_edit_stats_' . auth()->id());
        Cache::forget('registrar_user_show_stats_' . auth()->id());
        
        // Clear shared caches
        Cache::forget('registrar_user_role_options');
        Cache::forget('registrar_user_form_suggestions');
        Cache::forget('registrar_user_role_names');
    }
    
    /**
     * Optimized user statistics (for dashboard or sidebar)
     */
    public function getStats()
    {
        return Cache::remember('registrar_user_stats', 300, function() {
            $stats = User::whereIn('role', [3, 4])
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN role = 3 THEN 1 ELSE 0 END) as teachers,
                    SUM(CASE WHEN role = 4 THEN 1 ELSE 0 END) as students,
                    SUM(CASE WHEN role = 3 AND is_approved = 1 THEN 1 ELSE 0 END) as approved_teachers,
                    SUM(CASE WHEN role = 4 AND is_approved = 1 THEN 1 ELSE 0 END) as approved_students
                ')->first();
            
            // Add this month's count
            $thisMonth = User::whereIn('role', [3, 4])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            return [
                'total' => $stats->total ?? 0,
                'pending' => $stats->pending ?? 0,
                'teachers' => $stats->teachers ?? 0,
                'students' => $stats->students ?? 0,
                'approved_teachers' => $stats->approved_teachers ?? 0,
                'approved_students' => $stats->approved_students ?? 0,
                'this_month' => $thisMonth
            ];
        });
    }
    
    /**
     * Get detailed statistics for a specific user
     */
    private function getUserDetailedStats(User $user)
    {
        return Cache::remember('registrar_detailed_user_stats_' . $user->id, 300, function() use ($user) {
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
                if ($user->role == 3) { // Teacher
                    if (method_exists($user, 'taughtCourses')) {
                        $stats['course_count'] = $user->taughtCourses()->count();
                    }
                }
                
                // Get enrollment count for students
                if ($user->role == 4) { // Student
                    if (method_exists($user, 'enrollments')) {
                        $stats['enrollment_count'] = $user->enrollments()->count();
                    }
                    
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
                    if (method_exists($user, 'enrollments') && $user->enrollments()->whereNotNull('grade')->exists()) {
                        $stats['gpa'] = $user->enrollments()
                            ->whereNotNull('grade')
                            ->avg('grade');
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to get user detailed stats: ' . $e->getMessage());
            }
            
            return $stats;
        });
    }
    
    /**
     * Manual cache clearing endpoint
     */
    public function clearCache()
    {
        $this->clearAllRegistrarCaches();
        $this->clearAllAdminCaches();
        
        return redirect()->route('registrar.users.index')
            ->with('success', 'All registrar caches cleared successfully.');
    }
}