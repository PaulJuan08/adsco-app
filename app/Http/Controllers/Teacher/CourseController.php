<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;

class CourseController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        
        // Get courses with enrollment count
        $courses = Course::where('teacher_id', $teacherId)
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'active');
            }])
            ->latest()
            ->paginate(10);
        
        // Calculate statistics
        $activeCourses = Course::where('teacher_id', $teacherId)
            ->where('is_published', true)
            ->count();
        
        $coursesThisMonth = Course::where('teacher_id', $teacherId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Calculate total students across all courses
        $totalStudents = 0;
        foreach ($courses as $course) {
            $totalStudents += $course->enrollments_count;
        }
        
        return view('teacher.courses.index', compact(
            'courses',
            'activeCourses',
            'coursesThisMonth',
            'totalStudents'
        ));
    }

    public function create()
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:20|unique:courses,course_code',
            'description' => 'nullable|string|max:255',
            'credits' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,upcoming,archived',
            'thumbnail' => 'nullable|url|max:255',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['teacher_id'] = Auth::id();
        
        // Ensure is_published is set (default to 0 if not)
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;
        
        $course = Course::create($validated);
        
        return redirect()->route('teacher.courses.index')
            ->with('success', 'Course "' . $course->title . '" created successfully!');
    }

    public function show($id)
    {
        $course = Course::where('id', $id)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();
        
        // Get enrollments separately
        $enrollments = Enrollment::where('course_id', $course->id)
            ->with('student')
            ->where('status', 'active')
            ->get();
        
        return view('teacher.courses.show', compact('course', 'enrollments'));
    }

    public function edit($id)
    {
        $course = Course::where('id', $id)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();
        
        return view('teacher.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('id', $id)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'description' => 'nullable|string|max:255',
            'credits' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,upcoming,archived',
            'thumbnail' => 'nullable|url|max:255',
            'is_published' => 'nullable|boolean',
        ]);
        
        // Update the is_published value
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;
        
        $course->update($validated);
        
        return redirect()->route('teacher.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();

            $course->delete();

            return redirect()->route('teacher.courses.index')
                ->with('success', 'Course "' . $course->title . '" deleted successfully!');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('teacher.courses.index')
                ->with('error', 'Course not found or you do not have permission to delete it.');
        }
    }

    public function enrollments()
    {
        $teacherId = Auth::id();
        
        $enrollments = Enrollment::whereIn('course_id', function($query) use ($teacherId) {
            $query->select('id')->from('courses')->where('teacher_id', $teacherId);
        })->with(['student', 'course'])
          ->latest()
          ->paginate(10);
        
        return view('teacher.enrollments', compact('enrollments'));
    }
}