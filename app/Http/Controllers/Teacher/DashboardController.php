<?php
// app/Http/Controllers/Teacher/DashboardController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Assignment;
use App\Models\Quiz;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        
        // Get teacher's courses with counts
        $myCourses = Course::where('teacher_id', $teacherId)
            ->withCount(['enrollments as enrollments_count' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();
        
        // Get total students across all courses
        $totalStudents = Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })
            ->where('status', 'active')
            ->distinct('student_id')
            ->count();
        
        // Get upcoming assignments
        $upcomingAssignments = Assignment::whereHas('course', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->where('due_date', '>', now())
            ->where('is_published', 1)
            ->with('course')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
        
        // Get upcoming quizzes
        $upcomingQuizzes = Quiz::where('available_until', '>', now())
            ->where('is_published', 1)
            ->orderBy('available_until', 'asc')
            ->take(5)
            ->get();
        
        // Get recent enrollments
        $recentEnrollments = Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })
            ->with(['student:id,f_name,l_name', 'course:id,title'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('teacher.dashboard', [
            'myCourses' => $myCourses,
            'totalStudents' => $totalStudents,
            'recentEnrollments' => $recentEnrollments,
            'upcomingAssignments' => $upcomingAssignments,
            'upcomingQuizzes' => $upcomingQuizzes,
        ]);
    }
}