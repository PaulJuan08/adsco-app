<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Program; // Add this import
use App\Mail\UserApprovedMail;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        // Fix the validation rules
        $request->validate([
            'f_name' => 'required|string|max:50',
            'l_name' => 'required|string|max:50',
            'age' => 'required|integer|min:15|max:100',
            'sex' => 'required|in:male,female',
            'contact' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:2,3,4', // Only allow Registrar(2), Teacher(3), Student(4) to register
            'employee_id' => 'required_if:role,2,3|nullable|unique:users,employee_id',
            'student_id' => 'required_if:role,4|nullable|unique:users,student_id',
            // Add academic field validation for students
            'college_id' => 'required_if:role,4|nullable|exists:colleges,id',
            'program_id' => 'required_if:role,4|nullable|exists:programs,id',
            'college_year' => 'required_if:role,4|nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        // Prepare user data
        $userData = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'age' => $request->age,
            'sex' => $request->sex,
            'contact' => $request->contact,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'is_approved' => false // Not approved by default
        ];
        
        // Handle employee ID for Registrar/Teacher
        if (in_array($request->role, [2, 3])) {
            $userData['employee_id'] = $request->employee_id;
        }
        
        // Handle student-specific fields
        if ($request->role == 4) {
            $userData['student_id'] = $request->student_id;
            
            // ── IMPORTANT: Set program and derive college from program ──
            if ($request->filled('program_id')) {
                $program = Program::find($request->program_id);
                $userData['program_id'] = $program->id;
                $userData['college_id'] = $program->college_id; // Derived from program
            } elseif ($request->filled('college_id')) {
                $userData['college_id'] = $request->college_id;
            }
            
            if ($request->filled('college_year')) {
                $userData['college_year'] = $request->college_year;
            }
        }
        
        // Create user
        $user = User::create($userData);
        
        // If admin self-registers, auto-approve (but admin shouldn't be able to register via this form)
        // This is kept for backward compatibility but role 1 is not allowed in validation anymore
        
        // 🔥 CRITICAL: Clear ALL caches when new user registers
        $this->clearAllCachesOnRegistration();
        
        return redirect()->route('login')
            ->with('success', 'Registration submitted successfully. Your account is pending approval.');
    }
    
    /**
     * Clear ALL caches when a new user registers
     * This ensures both admin AND registrar see new pending users immediately
     */
    private function clearAllCachesOnRegistration()
    {
        try {
            // ============ 1. CLEAR ADMIN DASHBOARDS ============
            $admins = User::where('role', 1)->pluck('id')->toArray();
            foreach ($admins as $adminId) {
                Cache::forget('admin_dashboard_' . $adminId);
            }
            
            // ============ 2. CLEAR REGISTRAR DASHBOARDS ============
            $registrars = User::where('role', 2)->pluck('id')->toArray();
            foreach ($registrars as $registrarId) {
                Cache::forget('registrar_dashboard_' . $registrarId);
            }
            
            // ============ 3. 🔥 CRITICAL: CLEAR ADMIN USER INDEX CACHES ============
            $this->clearAdminUserIndexCaches();
            
            // ============ 4. 🔥 CRITICAL: CLEAR REGISTRAR USER INDEX CACHES ============
            $this->clearRegistrarUserIndexCaches();
            
            // ============ 5. CLEAR STATS CACHES ============
            Cache::forget('user_stats');
            Cache::forget('pending_users_count');
            Cache::forget('users_this_month');
            Cache::forget('registrar_user_stats');
            Cache::forget('registrar_pending_users_count');
            Cache::forget('registrar_users_this_month');
            
            // ============ 6. CLEAR COLLEGE/PROGRAM RELATED CACHES ============
            // Clear all college-related caches
            $colleges = \App\Models\College::pluck('id')->toArray();
            foreach ($colleges as $collegeId) {
                Cache::forget('college_programs_' . $collegeId);
                Cache::forget('college_students_' . $collegeId);
            }
            
            // Clear all program-related caches
            $programs = \App\Models\Program::pluck('id')->toArray();
            foreach ($programs as $programId) {
                Cache::forget('program_students_' . $programId);
            }
            
            \Log::info('All caches cleared after new registration (Admin + Registrar)');
        } catch (\Exception $e) {
            \Log::error('Error clearing caches: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all admin user index caches
     */
    private function clearAdminUserIndexCaches()
    {
        // Clear all possible filter combinations for pages 1-10
        for ($page = 1; $page <= 10; $page++) {
            // All filter combinations for admin
            $filterCombinations = [
                ['role' => null, 'status' => null],
                ['role' => null, 'status' => 'pending'],
                ['role' => 1, 'status' => null],
                ['role' => 2, 'status' => null],
                ['role' => 3, 'status' => null],
                ['role' => 3, 'status' => 'pending'],
                ['role' => 4, 'status' => null],
                ['role' => 4, 'status' => 'pending'],
            ];
            
            foreach ($filterCombinations as $filters) {
                // Clear without search
                $cacheKey = 'users_index_' . md5(json_encode([
                    'search' => null,
                    'role' => $filters['role'],
                    'status' => $filters['status'],
                    'page' => $page
                ]));
                Cache::forget($cacheKey);
                
                // Clear with wildcard search
                $cacheKeyWithSearch = 'users_index_' . md5(json_encode([
                    'search' => '*',
                    'role' => $filters['role'],
                    'status' => $filters['status'],
                    'page' => $page
                ]));
                Cache::forget($cacheKeyWithSearch);
            }
        }
        
        \Log::info('Admin user index caches cleared');
    }
    
    /**
     * Clear all registrar user index caches
     */
    private function clearRegistrarUserIndexCaches()
    {
        // Clear all possible filter combinations for pages 1-10
        for ($page = 1; $page <= 10; $page++) {
            // Registrars typically only see teachers and students
            $filterCombinations = [
                ['role' => null, 'status' => null],
                ['role' => null, 'status' => 'pending'],
                ['role' => 3, 'status' => null],
                ['role' => 3, 'status' => 'pending'],
                ['role' => 4, 'status' => null],
                ['role' => 4, 'status' => 'pending'],
            ];
            
            foreach ($filterCombinations as $filters) {
                // Clear without search
                $cacheKey = 'registrar_users_index_' . md5(json_encode([
                    'search' => null,
                    'role' => $filters['role'],
                    'status' => $filters['status'],
                    'page' => $page
                ]));
                Cache::forget($cacheKey);
                
                // Clear with wildcard search
                $cacheKeyWithSearch = 'registrar_users_index_' . md5(json_encode([
                    'search' => '*',
                    'role' => $filters['role'],
                    'status' => $filters['status'],
                    'page' => $page
                ]));
                Cache::forget($cacheKeyWithSearch);
            }
        }
        
        \Log::info('Registrar user index caches cleared');
    }
    
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        // Determine which field is being used
        if ($request->has('login')) {
            $loginField = 'login';
            $loginValue = $request->login;
        } elseif ($request->has('email')) {
            $loginField = 'email';
            $loginValue = $request->email;
        } else {
            return back()->withErrors([
                'email' => 'Email or ID is required.'
            ]);
        }
        
        $request->validate([
            $loginField => 'required|string',
            'password' => 'required|string'
        ]);
        
        $loginType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'id';
        
        $credentials = [];
        $user = null;
        
        if ($loginType === 'email') {
            $user = User::where('email', $loginValue)->first();
            if ($user) {
                $credentials['email'] = $loginValue;
            }
        } else {
            $user = User::where('employee_id', $loginValue)
                ->orWhere('student_id', $loginValue)
                ->first();
            
            if ($user) {
                $credentials['email'] = $user->email;
            }
        }
        
        if (!$user) {
            return back()->withErrors([
                $loginField => 'The provided credentials do not match our records.'
            ]);
        }
        
        $credentials['password'] = $request->password;
        
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            
            // Check if user is approved
            if (!$user->is_approved) {
                Auth::logout();
                return back()->withErrors([
                    $loginField => 'Your account is pending approval. Please wait for admin confirmation.'
                ]);
            }
            
            // Update last login
            $user->last_login_at = now();
            $user->save();
            
            $request->session()->regenerate();
            
            // Log the login action
            if (class_exists(\App\Models\AuditLog::class)) {
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'login',
                    'description' => "User {$user->email} logged in",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }
            
            return redirect()->intended(route('dashboard'));
        }
        
        return back()->withErrors([
            $loginField => 'The provided credentials do not match our records.'
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}