<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Topic;
use App\Models\Progress;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display enrolled courses
     */
    public function index()
    {
        $student = Auth::user();
        $studentId = $student->id;
        
        // Cache key based on student ID and page
        $cacheKey = 'student_courses_index_' . $studentId . '_page_' . request('page', 1);
        
        // Cache for 1 minute only
        $data = Cache::remember($cacheKey, 60, function() use ($student, $studentId) {
            // Get enrolled courses with relationships
            $enrolledCourses = Enrollment::where('student_id', $studentId)
                ->with([
                    'course.teacher' => function($query) {
                        $query->select(['id', 'f_name', 'l_name', 'employee_id']);
                    },
                    'course.topics' => function($query) {
                        // Only need ID for counting
                        $query->select(['topics.id'])
                              ->orderBy('course_topics.order');
                    }
                ])
                ->select(['id', 'course_id', 'student_id', 'enrolled_at', 'status', 'grade'])
                ->orderBy('enrolled_at', 'desc')
                ->paginate(12);
            
            // Get enrolled course IDs
            $enrolledCourseIds = $enrolledCourses->pluck('course_id')->toArray();
            
            // ✅ FIXED: Get ALL completed topics WITHOUT using course_id column
            $completedTopicsMap = [];
            if (!empty($enrolledCourseIds)) {
                // Get all completed topics for this student
                $completedTopicIds = Progress::where('student_id', $studentId)
                    ->where('status', 'completed')
                    ->pluck('topic_id')
                    ->toArray();
                
                // If there are completed topics, find which courses they belong to
                if (!empty($completedTopicIds)) {
                    $topicCourses = DB::table('course_topics')
                        ->whereIn('topic_id', $completedTopicIds)
                        ->whereIn('course_id', $enrolledCourseIds)
                        ->select('course_id', 'topic_id')
                        ->get();
                    
                    // Count completed topics per course
                    foreach ($topicCourses as $tc) {
                        if (!isset($completedTopicsMap[$tc->course_id])) {
                            $completedTopicsMap[$tc->course_id] = 0;
                        }
                        $completedTopicsMap[$tc->course_id]++;
                    }
                }
            }
            
            // Calculate progress for EACH enrollment
            foreach ($enrolledCourses as $enrollment) {
                $course = $enrollment->course;
                $courseId = $course->id;
                
                // Get total topics count from loaded relationship
                $totalTopics = $course->topics->count();
                
                // Get completed topics count from our map
                $completedTopics = $completedTopicsMap[$courseId] ?? 0;
                
                // Calculate percentage
                $progressPercentage = $totalTopics > 0 
                    ? round(($completedTopics / $totalTopics) * 100) 
                    : 0;
                
                // Determine if completed
                $isCompleted = $enrollment->grade !== null || $progressPercentage >= 100;
                
                // Set progress_data on the enrollment object
                $enrollment->progress_data = [
                    'total' => $totalTopics,
                    'completed' => $completedTopics,
                    'percentage' => $progressPercentage,
                    'is_completed' => $isCompleted
                ];
                
                // Also set on course for backward compatibility
                $course->progress_data = $enrollment->progress_data;
            }
            
            // Get overall statistics
            $overallStats = $this->getOverallStats($studentId, $enrolledCourseIds, $completedTopicsMap);
            
            // Get available courses
            $availableCourses = Course::where('is_published', true)
                ->whereNotIn('id', $enrolledCourseIds)
                ->with(['teacher' => function($query) {
                    $query->select(['id', 'f_name', 'l_name']);
                }])
                ->withCount(['students', 'topics'])
                ->select(['id', 'title', 'course_code', 'description', 'teacher_id', 'credits', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
            
            $totalAvailableCourses = Course::where('is_published', true)
                ->whereNotIn('id', $enrolledCourseIds)
                ->count();

            $hasMoreAvailableCourses = $totalAvailableCourses > $availableCourses->count();
            
            // Get recent activities
            $recentActivities = $this->getRecentActivities($student);
            
            return [
                'enrolledCourses' => $enrolledCourses,
                'availableCourses' => $availableCourses,
                'hasMoreAvailableCourses' => $hasMoreAvailableCourses,
                'overallStats' => $overallStats,
                'recentActivities' => $recentActivities,
                'enrolledCourseIds' => $enrolledCourseIds
            ];
        });
        
        return view('student.courses.index', [
            'enrolledCourses' => $data['enrolledCourses'],
            'availableCourses' => $data['availableCourses'],
            'hasMoreAvailableCourses' => $data['hasMoreAvailableCourses'],
            'overallStats' => $data['overallStats'],
            'recentActivities' => $data['recentActivities']
        ]);
    }
    
    /**
     * Show course details and topics
     */
    public function show($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $studentId = Auth::id();

            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();

            if (!$enrollment) {
                return redirect()->route('student.courses.index')
                    ->with('error', 'Access denied.');
            }

            // Cache course data but NOT progress
            $cacheKey = 'student_course_show_' . $courseId;
            $course = Cache::remember($cacheKey, 600, function() use ($courseId) {
                return Course::with(['teacher', 'topics' => function($query) {
                        $query->orderBy('course_topics.order')
                              ->select(['topics.id', 'topics.title', 'topics.description', 
                                        'topics.content', 'topics.video_link', 'topics.attachment', 
                                        'topics.pdf_file']);
                    }])
                    ->withCount(['students', 'topics'])
                    ->findOrFail($courseId);
            });

            $topics = $course->topics;

            // Get fresh progress data
            $completedTopicIds = Progress::where('student_id', $studentId)
                ->whereIn('topic_id', $topics->pluck('id'))
                ->where('status', 'completed')
                ->pluck('topic_id')
                ->toArray();

            $completedTopics = count($completedTopicIds);
            $totalTopics = $topics->count();
            $remainingTopics = $totalTopics - $completedTopics;
            $progressPercentage = $totalTopics > 0
                ? round(($completedTopics / $totalTopics) * 100)
                : 0;

            $courseProgress = [
                'total' => $totalTopics,
                'completed' => $completedTopics,
                'remaining' => $remainingTopics,
                'percentage' => $progressPercentage
            ];

            $nextTopic = $topics->first(function ($topic) use ($completedTopicIds) {
                return !in_array($topic->id, $completedTopicIds);
            });

            return view('student.courses.show', compact(
                'course',
                'enrollment',
                'topics',
                'completedTopicIds',
                'courseProgress',
                'nextTopic',
                'encryptedId'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in course show', [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found or access denied.');
        }
    }

    /**
     * Get overall statistics for student
     */
    public function getOverallStats($studentId, $enrolledCourseIds = null, $completedTopicsMap = null)
    {
        if ($enrolledCourseIds === null) {
            $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->pluck('course_id')
                ->toArray();
        }
        
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
        
        // Get courses with topics count
        $courses = Course::whereIn('id', $enrolledCourseIds)
            ->withCount('topics')
            ->select(['id'])
            ->get();
        
        $totalCourses = count($enrolledCourseIds);
        $completedCourses = 0;
        $totalTopics = 0;
        $completedTopics = 0;
        $totalProgress = 0;
        
        // ✅ FIXED: Get completed topics WITHOUT using course_id column
        if ($completedTopicsMap === null) {
            $completedTopicsMap = [];
            
            // Get all completed topics for this student
            $completedTopicIds = Progress::where('student_id', $studentId)
                ->where('status', 'completed')
                ->pluck('topic_id')
                ->toArray();
            
            // If there are completed topics, find which courses they belong to
            if (!empty($completedTopicIds)) {
                $topicCourses = DB::table('course_topics')
                    ->whereIn('topic_id', $completedTopicIds)
                    ->whereIn('course_id', $enrolledCourseIds)
                    ->select('course_id', 'topic_id')
                    ->get();
                
                // Count completed topics per course
                foreach ($topicCourses as $tc) {
                    if (!isset($completedTopicsMap[$tc->course_id])) {
                        $completedTopicsMap[$tc->course_id] = 0;
                    }
                    $completedTopicsMap[$tc->course_id]++;
                }
            }
        }
        
        // Get grades
        $grades = Enrollment::where('student_id', $studentId)
            ->whereIn('course_id', $enrolledCourseIds)
            ->pluck('grade', 'course_id')
            ->toArray();
        
        foreach ($courses as $course) {
            $courseId = $course->id;
            $courseTotalTopics = $course->topics_count;
            $courseCompletedTopics = $completedTopicsMap[$courseId] ?? 0;
            $courseGrade = $grades[$courseId] ?? null;
            
            $totalTopics += $courseTotalTopics;
            $completedTopics += $courseCompletedTopics;
            
            $courseProgress = $courseTotalTopics > 0 
                ? round(($courseCompletedTopics / $courseTotalTopics) * 100) 
                : 0;
            $totalProgress += $courseProgress;
            
            // Consider course completed if grade exists or progress is 100%
            if ($courseGrade !== null || $courseProgress >= 100) {
                $completedCourses++;
            }
        }
        
        $inProgressCourses = $totalCourses - $completedCourses;
        $averageProgress = $totalCourses > 0 ? round($totalProgress / $totalCourses) : 0;
        
        // Get average grade
        $averageGrade = !empty($grades) ? round(array_sum($grades) / count($grades), 1) : 0;
        
        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'in_progress_courses' => $inProgressCourses,
            'total_topics' => $totalTopics,
            'completed_topics' => $completedTopics,
            'average_progress' => $averageProgress,
            'average_grade' => $averageGrade
        ];
    }
    
    /**
     * Get recent activities for the student
     */
    private function getRecentActivities($student)
    {
        $studentId = $student->id;
        $activities = [];
        
        // Get recent topic completions
        $recentCompletions = Progress::where('student_id', $studentId)
            ->with(['topic' => function($query) {
                $query->select(['id', 'title'])
                      ->with(['courses' => function($q) {
                          $q->select(['courses.id', 'courses.title']);
                      }]);
            }])
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
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
                        'time' => $completion->completed_at->diffForHumans(),
                        'icon' => 'fas fa-check-circle',
                        'color' => 'success'
                    ];
                }
            }
        }
        
        // Get recent enrollments
        $recentEnrollments = Enrollment::where('student_id', $studentId)
            ->with(['course' => function($query) {
                $query->select(['id', 'title']);
            }])
            ->orderBy('enrolled_at', 'desc')
            ->take(5)
            ->get();
        
        foreach ($recentEnrollments as $enrollment) {
            if ($enrollment->course) {
                // Check if this enrollment is already in activities
                $exists = false;
                foreach ($activities as $activity) {
                    if ($activity['type'] === 'enrollment' && strpos($activity['text'], $enrollment->course->title) !== false) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    $activities[] = [
                        'type' => 'enrollment',
                        'text' => "Enrolled in '{$enrollment->course->title}'",
                        'time' => $enrollment->enrolled_at->diffForHumans(),
                        'icon' => 'fas fa-user-plus',
                        'color' => 'primary'
                    ];
                }
            }
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
            $studentId = $student->id;
            
            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();
                
            if ($existingEnrollment) {
                return back()->with('error', 'You are already enrolled in this course.');
            }
            
            // Create enrollment
            Enrollment::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'enrolled_at' => now(),
                'status' => 'active'
            ]);
            
            // Clear all student-related caches
            $this->clearStudentCaches($studentId);
            
            return back()->with('success', 'Successfully enrolled in the course!');
            
        } catch (\Exception $e) {
            \Log::error('Error enrolling in course', [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
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
            $studentId = Auth::id();
            
            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->select(['id', 'course_id', 'student_id', 'enrolled_at', 'status'])
                ->firstOrFail();
            
            $course = Course::with(['topics' => function($query) {
                    $query->orderBy('course_topics.order')
                          ->select(['topics.id', 'topics.title', 'topics.content', 
                                    'topics.video_link', 'topics.attachment', 'topics.pdf_file']);
                }])
                ->select(['id', 'title', 'description'])
                ->findOrFail($courseId);
            
            return view('student.courses.topics', compact('course', 'enrollment'));
            
        } catch (\Exception $e) {
            \Log::error('Error showing course topics', [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
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
            $studentId = Auth::id();
            
            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->select(['id', 'course_id', 'student_id', 'enrolled_at', 'status'])
                ->firstOrFail();
            
            $course = Course::with(['topics' => function($query) {
                    $query->orderBy('course_topics.order')
                          ->select(['topics.id', 'topics.title', 'topics.content', 
                                    'topics.video_link', 'topics.attachment', 'topics.pdf_file', 'topics.created_at']);
                }])
                ->select(['id', 'title', 'description'])
                ->findOrFail($courseId);
            
            return view('student.courses.materials', compact('course', 'enrollment'));
            
        } catch (\Exception $e) {
            \Log::error('Error showing course materials', [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
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
            $studentId = Auth::id();
            
            // Verify enrollment
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->select(['id', 'course_id', 'student_id', 'enrolled_at', 'status', 'grade'])
                ->firstOrFail();
            
            $course = Course::with(['teacher' => function($query) {
                    $query->select(['id', 'f_name', 'l_name']);
                }])
                ->select(['id', 'title', 'course_code', 'teacher_id'])
                ->findOrFail($courseId);
            
            // In a real app, you would fetch grades from your grades table
            $grades = [
                'quizzes' => [],
                'assignments' => [],
                'exams' => []
            ];
            
            return view('student.courses.grades', compact('course', 'enrollment', 'grades'));
            
        } catch (\Exception $e) {
            \Log::error('Error showing course grades', [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found or access denied.');
        }
    }

    /**
     * Clear all student-related caches
     */
    public function clearStudentCaches($studentId)
    {
        // Clear index pages (assuming up to 5 pages)
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
        }
        
        // Clear overall stats
        Cache::forget('student_overall_stats_' . $studentId);
        
        // Clear recent activities
        Cache::forget('student_recent_activities_' . $studentId);
        
        // Clear dashboard
        Cache::forget('student_dashboard_' . $studentId);
        
        // Clear all course-specific caches for this student
        $enrolledCourses = Enrollment::where('student_id', $studentId)
            ->pluck('course_id')
            ->toArray();
        
        foreach ($enrolledCourses as $courseId) {
            Cache::forget('student_course_show_' . $courseId);
            Cache::forget('student_course_progress_' . $studentId . '_' . $courseId);
            Cache::forget('student_course_topics_' . $courseId . '_student_' . $studentId);
            Cache::forget('student_course_materials_' . $courseId . '_student_' . $studentId);
            Cache::forget('student_course_grades_' . $courseId . '_student_' . $studentId);
        }
    }

    /**
     * Manual cache clearing endpoint
     */
    public function clearCache()
    {
        $studentId = Auth::id();
        $this->clearStudentCaches($studentId);
        
        return redirect()->route('student.courses.index')
            ->with('success', 'All course caches cleared successfully.');
    }
}