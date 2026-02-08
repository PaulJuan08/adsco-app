<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Topic;
use App\Models\Progress;
use Illuminate\Support\Facades\Crypt;

class CourseController extends Controller
{
    /**
     * Display enrolled courses
     */
    public function index()
    {
        $student = Auth::user();
        
        // Get enrolled courses with relationships
        $enrolledCourses = Enrollment::where('student_id', $student->id)
            ->with(['course.teacher'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Calculate progress for each course
        foreach ($enrolledCourses as $enrollment) {
            $progress = $enrollment->course->getStudentProgress($student->id);
            $enrollment->course->progress = $progress;
            
            // Also store progress in enrollment for easy access
            $enrollment->progress = $progress;
        }
        
        // Get overall statistics
        $overallStats = $this->getOverallStats($student->id);
        
        // Get enrolled course IDs
        $enrolledCourseIds = $enrolledCourses->pluck('course_id')->toArray();
        
        // Get available courses
        $availableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->with(['teacher'])
            ->withCount(['students', 'topics'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($student);

        $totalAvailableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->count();

        $hasMoreAvailableCourses = $totalAvailableCourses > $availableCourses->count();
        
        return view('student.courses.index', compact(
            'enrolledCourses',
            'availableCourses',
            'hasMoreAvailableCourses',
            'overallStats',
            'recentActivities'
        ));
    }
    
    /**
     * Show course details and topics
     */
    public function show($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            
            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->firstOrFail();
            
            // Get course with topics (ordered)
            $course = Course::with(['teacher', 'topics' => function($query) {
                $query->orderBy('course_topics.order');
            }])
            ->withCount(['students', 'topics'])
            ->findOrFail($courseId);
            
            // Get topics separately for easier access in view
            $topics = $course->topics;
            
            // Get completed topic IDs for this student
            $completedTopicIds = Progress::where('student_id', $student->id)
                ->whereIn('topic_id', $topics->pluck('id'))
                ->where('status', 'completed')
                ->pluck('topic_id')
                ->toArray();
            
            // Calculate progress
            $completedTopics = count($completedTopicIds);
            $totalTopics = $topics->count();
            $remainingTopics = $totalTopics - $completedTopics;
            $progressPercentage = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
            
            // Get number of enrolled students
            $enrolledStudents = $course->students_count;
            
            // Calculate next topic (first incomplete topic)
            $nextTopic = null;
            foreach ($topics as $topic) {
                if (!in_array($topic->id, $completedTopicIds)) {
                    $nextTopic = $topic;
                    break;
                }
            }
            
            return view('student.courses.show', compact(
                'course',
                'enrollment',
                'topics', // Add this line
                'completedTopicIds',
                'completedTopics',
                'totalTopics',
                'remainingTopics',
                'progressPercentage',
                'enrolledStudents',
                'nextTopic',
                'encryptedId'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found or access denied.');
        }
    }

    /**
     * Get overall statistics for student - CHANGED FROM PRIVATE TO PUBLIC
     */
    public function getOverallStats($studentId)
    {
        // Get all enrolled courses
        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->pluck('course_id')
            ->toArray();
        
        if (empty($enrolledCourseIds)) {
            return [
                'total_courses' => 0,
                'completed_courses' => 0,
                'in_progress_courses' => 0,
                'total_topics' => 0,
                'completed_topics' => 0,
                'average_progress' => 0,
                'average_grade' => 0
            ];
        }
        
        // Get courses
        $courses = Course::whereIn('id', $enrolledCourseIds)->get();
        
        $totalCourses = count($enrolledCourseIds);
        $completedCourses = 0;
        $totalTopics = 0;
        $completedTopics = 0;
        $totalProgress = 0;
        
        foreach ($courses as $course) {
            $progress = $course->getStudentProgress($studentId);
            
            $totalTopics += $progress['total'];
            $completedTopics += $progress['completed'];
            $totalProgress += $progress['percentage'];
            
            // Consider course completed if progress is 100%
            if ($progress['percentage'] >= 100) {
                $completedCourses++;
            }
        }
        
        $inProgressCourses = $totalCourses - $completedCourses;
        $averageProgress = $totalCourses > 0 ? round($totalProgress / $totalCourses) : 0;
        
        // Get average grade
        $averageGrade = Enrollment::where('student_id', $studentId)
            ->whereNotNull('grade')
            ->avg('grade') ?? 0;
        
        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'in_progress_courses' => $inProgressCourses,
            'total_topics' => $totalTopics,
            'completed_topics' => $completedTopics,
            'average_progress' => $averageProgress,
            'average_grade' => round($averageGrade, 1)
        ];
    }
    
    /**
     * Get recent activities for the student
     */
    private function getRecentActivities($student)
    {
        $activities = [];
        
        // Get recent topic completions
        $recentCompletions = Progress::where('student_id', $student->id)
            ->with(['topic' => function($query) {
                $query->with(['courses']);
            }])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();
        
        foreach ($recentCompletions as $completion) {
            if ($completion->topic) {
                $course = $completion->topic->courses->first();
                if ($course) {
                    $activities[] = [
                        'type' => 'topic',
                        'text' => "Completed '{$completion->topic->title}' in {$course->title}",
                        'time' => $completion->completed_at ? $completion->completed_at->diffForHumans() : 'Recently',
                        'icon' => 'fas fa-check-circle',
                        'color' => 'success'
                    ];
                }
            }
        }
        
        // Get recent enrollments
        $recentEnrollments = Enrollment::where('student_id', $student->id)
            ->with('course')
            ->orderBy('enrolled_at', 'desc')
            ->take(5 - count($activities))
            ->get();
        
        foreach ($recentEnrollments as $enrollment) {
            $activities[] = [
                'type' => 'enrollment',
                'text' => "Enrolled in '{$enrollment->course->title}'",
                'time' => $enrollment->enrolled_at->diffForHumans(),
                'icon' => 'fas fa-user-plus',
                'color' => 'primary'
            ];
        }
        
        // Sort by time (newest first)
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 5);
    }
    
    /**
     * Enroll in a course
     */
    public function enroll(Request $request, $encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            
            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->first();
                
            if ($existingEnrollment) {
                return back()->with('error', 'You are already enrolled in this course.');
            }
            
            // Create enrollment
            Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $courseId,
                'enrolled_at' => now(),
                'status' => 'active'
            ]);
            
            return back()->with('success', 'Successfully enrolled in the course!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to enroll in the course.');
        }
    }
    
    /**
     * Show topics for a course
     */
    public function topics($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            
            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->firstOrFail();
            
            $course = Course::with(['topics'])->findOrFail($courseId);
            
            return view('student.courses.topics', compact('course', 'enrollment'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found or access denied.');
        }
    }
    
    /**
     * Show course materials
     */
    public function materials($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            
            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->firstOrFail();
            
            $course = Course::with(['topics'])->findOrFail($courseId);
            
            return view('student.courses.materials', compact('course', 'enrollment'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found or access denied.');
        }
    }
    
    /**
     * Show grades for a course
     */
    public function grades($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            
            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->firstOrFail();
            
            $course = Course::with(['teacher'])->findOrFail($courseId);
            
            // In a real app, you would fetch grades from your grades table
            $grades = [
                // Mock data - replace with actual grade retrieval
                'quizzes' => [],
                'assignments' => [],
                'exams' => []
            ];
            
            return view('student.courses.grades', compact('course', 'enrollment', 'grades'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found or access denied.');
        }
    }
    
    public function available()
    {
        $student = Auth::user();
        
        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->pluck('course_id')
            ->toArray();
        
        // Get all available courses
        $availableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->with(['teacher'])
            ->withCount(['students', 'topics'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('student.courses.available', compact('availableCourses'));
    }
}