<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('students')
            ->with('teacher')
            ->latest()
            ->paginate(10);
        
        // Calculate statistics - use is_published instead of is_active
        $activeCourses = Course::where('is_published', true)->count();
        $assignedTeachers = Course::whereNotNull('teacher_id')->count();
        
        // Get total students count
        $totalStudents = User::where('role', 4)->count(); // Role 4 = student
        
        // Additional statistics for sidebar
        $coursesThisMonth = Course::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Calculate average students per course
        $avgStudents = $courses->isNotEmpty() 
            ? round($courses->sum('students_count') / $courses->count(), 1)
            : 0;
        
        return view('admin.courses.index', compact(
            'courses',
            'activeCourses',
            'assignedTeachers',
            'totalStudents',
            'coursesThisMonth',
            'avgStudents'
        ));
    }

    public function create()
    {
        $teachers = User::where('role', 3)->get(); // Role 3 = teacher
        return view('admin.courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
            'is_published' => 'nullable|boolean',
            'credits' => 'nullable|integer|min:1',
            'status' => 'nullable|string|in:active,inactive',
        ]);
        
        // Create the course with default values
        Course::create([
            'title' => $validated['title'],
            'course_code' => $validated['course_code'],
            'description' => $validated['description'] ?? null,
            'teacher_id' => $validated['teacher_id'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'credits' => $validated['credits'] ?? 3,
            'status' => $validated['status'] ?? 'active',
        ]);
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function show($id)
    {
        $course = Course::with(['teacher', 'students'])
            ->withCount('students')
            ->findOrFail($id);
        
        return view('admin.courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $teachers = User::where('role', 3)->get(); // Role 3 = teacher
        
        return view('admin.courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses,course_code,' . $course->id,
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
            'is_published' => 'nullable|boolean',
            'credits' => 'nullable|integer|min:1',
            'status' => 'nullable|string|in:active,inactive',
        ]);
        
        // Update the course
        $course->update([
            'title' => $validated['title'],
            'course_code' => $validated['course_code'],
            'description' => $validated['description'] ?? null,
            'teacher_id' => $validated['teacher_id'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'credits' => $validated['credits'] ?? $course->credits,
            'status' => $validated['status'] ?? $course->status,
        ]);
        
        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', 'Course updated successfully!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        // Check if course has students before deleting
        if ($course->students()->exists()) {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Cannot delete course with enrolled students.');
        }
        
        $course->delete();
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }
}