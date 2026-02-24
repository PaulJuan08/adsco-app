<?php
// app/Http/Controllers/Registrar/DashboardController.php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Topic;

class DashboardController extends Controller
{
    public function index()
    {
        $cacheKey = 'registrar_dashboard_' . auth()->id();
        
        $data = Cache::remember($cacheKey, 300, function() {
            // Get user counts in one query
            $teacherStudentCounts = User::selectRaw('
                SUM(CASE WHEN role = 3 AND is_approved = 0 THEN 1 ELSE 0 END) as pending_teachers,
                SUM(CASE WHEN role = 4 AND is_approved = 0 THEN 1 ELSE 0 END) as pending_students,
                SUM(CASE WHEN role = 3 AND is_approved = 1 THEN 1 ELSE 0 END) as approved_teachers,
                SUM(CASE WHEN role = 4 AND is_approved = 1 THEN 1 ELSE 0 END) as approved_students,
                SUM(CASE WHEN role IN (3, 4) AND is_approved = 0 THEN 1 ELSE 0 END) as total_pending,
                SUM(CASE WHEN role IN (3, 4) AND is_approved = 1 THEN 1 ELSE 0 END) as total_approved,
                SUM(CASE WHEN role IN (3, 4) AND email_verified_at IS NULL THEN 1 ELSE 0 END) as unverified
            ')->first();
            
            // Get pending teachers and students separately
            $pendingTeachers = User::where('role', 3)
                ->where('is_approved', false)
                ->select(['id', 'f_name', 'l_name', 'email', 'created_at'])
                ->get();
            
            $pendingStudents = User::where('role', 4)
                ->where('is_approved', false)
                ->select(['id', 'f_name', 'l_name', 'email', 'created_at'])
                ->get();
            
            // Get unverified teachers and students
            $unverifiedTeachers = User::where('role', 3)
                ->whereNull('email_verified_at')
                ->select(['id', 'f_name', 'l_name', 'email', 'created_at'])
                ->get();
            
            $unverifiedStudents = User::where('role', 4)
                ->whereNull('email_verified_at')
                ->select(['id', 'f_name', 'l_name', 'email', 'created_at'])
                ->get();
            
            $totalTopics = Topic::count();
            
            return [
                'pendingTeachers' => $pendingTeachers,
                'pendingStudents' => $pendingStudents,
                'unverifiedTeachers' => $unverifiedTeachers,
                'unverifiedStudents' => $unverifiedStudents,
                'unverifiedCount' => $teacherStudentCounts->unverified ?? 0,
                'totalTeachers' => $teacherStudentCounts->approved_teachers ?? 0,
                'totalStudents' => $teacherStudentCounts->approved_students ?? 0,
                'totalPending' => $teacherStudentCounts->total_pending ?? 0,
                'totalApproved' => $teacherStudentCounts->total_approved ?? 0,
                'totalTopics' => $totalTopics,
            ];
        });
        
        return view('registrar.dashboard', $data);
    }
    
    public function clearCache()
    {
        Cache::forget('registrar_dashboard_' . auth()->id());
        return redirect()->back()->with('success', 'Dashboard cache cleared successfully.');
    }
}