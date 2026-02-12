<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Progress;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    /**
     * Display all topics for the student
     */
    public function index()
    {
        $student = Auth::user();
        $studentId = $student->id;
        
        // Cache key based on student ID and page
        $cacheKey = 'student_topics_index_' . $studentId . '_page_' . request('page', 1);
        
        // Cache for 1 minute only
        $data = Cache::remember($cacheKey, 60, function() use ($student, $studentId) {
            try {
                // Get all enrolled courses
                $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                    ->where('status', 'active')
                    ->pluck('course_id')
                    ->toArray();
                
                // Get topics through course_topics pivot table
                $allTopics = Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                        $query->whereIn('courses.id', $enrolledCourseIds);
                    })
                    ->with(['courses.teacher' => function($query) {
                        $query->select(['id', 'f_name', 'l_name']);
                    }])
                    ->select(['id', 'title', 'content', 'video_link', 'attachment', 'pdf_file', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
                
                // Get completed topic IDs
                $completedTopicIds = Progress::where('student_id', $studentId)
                    ->where('status', 'completed')
                    ->pluck('topic_id')
                    ->toArray();
                
                // Calculate stats
                $totalTopics = $allTopics->total();
                $completedTopics = count(array_intersect(
                    $allTopics->pluck('id')->toArray(),
                    $completedTopicIds
                ));
                
                // Get courses for filter
                $courses = Course::whereIn('id', $enrolledCourseIds)
                    ->withCount(['topics'])
                    ->select(['id', 'title', 'course_code'])
                    ->get();
                
                // Get topics with video
                $topicsWithVideo = Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                        $query->whereIn('courses.id', $enrolledCourseIds);
                    })
                    ->whereNotNull('video_link')
                    ->count();
                
                // Get recently completed topics
                $recentlyCompleted = Progress::where('student_id', $studentId)
                    ->where('status', 'completed')
                    ->with(['topic' => function($query) {
                        $query->select(['id', 'title']);
                    }])
                    ->orderBy('completed_at', 'desc')
                    ->take(5)
                    ->get();
                
                return [
                    'allTopics' => $allTopics,
                    'completedTopicIds' => $completedTopicIds,
                    'totalTopics' => $totalTopics,
                    'completedTopics' => $completedTopics,
                    'courses' => $courses,
                    'topicsWithVideo' => $topicsWithVideo,
                    'recentlyCompleted' => $recentlyCompleted,
                    'enrolledCourseIds' => $enrolledCourseIds
                ];
                
            } catch (\Exception $e) {
                \Log::error('Error in TopicController@index: ' . $e->getMessage());
                
                return [
                    'allTopics' => collect([]),
                    'completedTopicIds' => [],
                    'totalTopics' => 0,
                    'completedTopics' => 0,
                    'courses' => collect([]),
                    'topicsWithVideo' => 0,
                    'recentlyCompleted' => collect([]),
                    'enrolledCourseIds' => []
                ];
            }
        });
        
        return view('student.topics.index', $data);
    }
    
    /**
     * Show a specific topic
     */
    public function show($encryptedId)
    {
        try {
            $topicId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            $studentId = $student->id;
            
            // Cache key for topic details - 5 minutes
            $cacheKey = 'student_topic_show_' . $topicId . '_student_' . $studentId;
            
            $data = Cache::remember($cacheKey, 300, function() use ($topicId, $student, $studentId) {
                // Get the topic with courses and teacher info
                $topic = Topic::with(['courses.teacher' => function($query) {
                        $query->select(['id', 'f_name', 'l_name']);
                    }])
                    ->select(['id', 'title', 'content', 'video_link', 'attachment', 'pdf_file', 'created_at', 'updated_at'])
                    ->findOrFail($topicId);
                
                // Get the first course for this topic
                $course = $topic->courses->first();
                
                if (!$course) {
                    return redirect()->route('student.topics.index')
                        ->with('error', 'Topic not associated with any course.');
                }
                
                // Check if topic is completed
                $progress = Progress::where('student_id', $studentId)
                    ->where('topic_id', $topicId)
                    ->select(['status', 'completed_at', 'notes'])
                    ->first();
                
                $isCompleted = $progress && $progress->status === 'completed';
                $completionDate = $progress ? $progress->completed_at : null;
                $notes = $progress ? $progress->notes : null;
                
                // Get enrolled course IDs for stats
                $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                    ->where('status', 'active')
                    ->pluck('course_id')
                    ->toArray();
                
                // Get total topics for enrolled courses
                $totalTopics = Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                    $query->whereIn('courses.id', $enrolledCourseIds);
                })->count();
                
                // Get completed topics count
                $completedTopics = Progress::where('student_id', $studentId)
                    ->where('status', 'completed')
                    ->count();
                
                return [
                    'topic' => $topic,
                    'course' => $course,
                    'isCompleted' => $isCompleted,
                    'completionDate' => $completionDate,
                    'notes' => $notes,
                    'totalTopics' => $totalTopics,
                    'completedTopics' => $completedTopics,
                    'enrolledCourseIds' => $enrolledCourseIds
                ];
            });
            
            return view('student.topics.show', array_merge($data, [
                'encryptedId' => $encryptedId
            ]));
            
        } catch (\Exception $e) {
            \Log::error('Error in TopicController@show: ' . $e->getMessage(), [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('student.topics.index')
                ->with('error', 'Topic not found.');
        }
    }
    
    /**
     * Mark topic as complete
     */
    public function markComplete(Request $request, $encryptedId)
    {
        try {
            $topicId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            $studentId = $student->id;
            
            // Get topic and its course
            $topic = Topic::with('courses')->findOrFail($topicId);
            $course = $topic->courses->first();
            $courseId = $course ? $course->id : null;
            
            // Mark as complete
            Progress::updateOrCreate([
                'student_id' => $studentId,
                'topic_id' => $topicId
            ], [
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->input('notes', '')
            ]);
            
            // Clear ALL caches
            $this->clearTopicCaches($studentId, $topicId, $courseId);
            
            // Clear course index caches
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
            }
            
            // Get updated stats
            $completedTopics = Progress::where('student_id', $studentId)
                ->where('status', 'completed')
                ->count();
                
            $totalTopics = $this->getTotalTopicsCount($studentId);
            $progressPercentage = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
            
            return response()->json([
                'success' => true,
                'message' => 'Topic marked as completed!',
                'stats' => [
                    'completedTopics' => $completedTopics,
                    'totalTopics' => $totalTopics,
                    'progressPercentage' => $progressPercentage
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in TopicController@markComplete: ' . $e->getMessage(), [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark topic as complete.'
            ], 500);
        }
    }

    /**
     * Get total topics count for student
     */
    private function getTotalTopicsCount($studentId)
    {
        // Cache total topics count for 1 minute
        $cacheKey = 'student_total_topics_' . $studentId;
        
        return Cache::remember($cacheKey, 60, function() use ($studentId) {
            // Get enrolled course IDs
            $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->pluck('course_id')
                ->toArray();
            
            if (empty($enrolledCourseIds)) {
                return 0;
            }
            
            return Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                $query->whereIn('courses.id', $enrolledCourseIds);
            })->count();
        });
    }
    
    /**
     * Mark topic as incomplete
     */
    public function markIncomplete(Request $request, $encryptedId)
    {
        try {
            $topicId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            $studentId = $student->id;
            
            // Get topic and its course
            $topic = Topic::with('courses')->findOrFail($topicId);
            $course = $topic->courses->first();
            $courseId = $course ? $course->id : null;
            
            // Update status to incomplete
            Progress::where('student_id', $studentId)
                ->where('topic_id', $topicId)
                ->update([
                    'status' => 'incomplete',
                    'completed_at' => null
                ]);
            
            // Clear ALL caches
            $this->clearTopicCaches($studentId, $topicId, $courseId);
            
            // Clear course index caches
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
            }
            
            // Get updated stats
            $completedTopics = Progress::where('student_id', $studentId)
                ->where('status', 'completed')
                ->count();
                
            $totalTopics = $this->getTotalTopicsCount($studentId);
            $progressPercentage = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
            
            return response()->json([
                'success' => true,
                'message' => 'Topic marked as incomplete.',
                'stats' => [
                    'completedTopics' => $completedTopics,
                    'totalTopics' => $totalTopics,
                    'progressPercentage' => $progressPercentage
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in TopicController@markIncomplete: ' . $e->getMessage(), [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark topic as incomplete.'
            ], 500);
        }
    }
    
    /**
     * Save notes for a topic
     */
    public function saveNotes(Request $request, $encryptedId)
    {
        try {
            $topicId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            $studentId = $student->id;
            
            // Get current progress
            $progress = Progress::where('student_id', $studentId)
                ->where('topic_id', $topicId)
                ->first();
            
            // Save notes
            Progress::updateOrCreate([
                'student_id' => $studentId,
                'topic_id' => $topicId
            ], [
                'notes' => $request->input('notes'),
                'status' => $progress->status ?? 'in_progress',
                'completed_at' => $progress->completed_at ?? null
            ]);
            
            // Clear topic show cache
            Cache::forget('student_topic_show_' . $topicId . '_student_' . $studentId);
            
            return response()->json([
                'success' => true,
                'message' => 'Notes saved successfully.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in TopicController@saveNotes: ' . $e->getMessage(), [
                'encryptedId' => $encryptedId,
                'student_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save notes.'
            ], 500);
        }
    }
    
    /**
     * Clear all topic-related caches for a student
     */
    private function clearTopicCaches($studentId, $topicId = null, $courseId = null)
    {
        // Clear index pages (assuming up to 5 pages)
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('student_topics_index_' . $studentId . '_page_' . $page);
            Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
        }
        
        // Clear total topics count
        Cache::forget('student_total_topics_' . $studentId);
        
        // Clear specific topic cache if provided
        if ($topicId) {
            Cache::forget('student_topic_show_' . $topicId . '_student_' . $studentId);
        }
        
        // Clear course-specific caches
        if ($courseId) {
            Cache::forget('student_course_show_' . $courseId);
            Cache::forget('student_course_progress_' . $studentId . '_' . $courseId);
        } else {
            // Clear all enrolled courses caches
            $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->pluck('course_id')
                ->toArray();
            
            foreach ($enrolledCourseIds as $cid) {
                Cache::forget('student_course_show_' . $cid);
                Cache::forget('student_course_progress_' . $studentId . '_' . $cid);
            }
        }
        
        // Clear overall stats
        Cache::forget('student_overall_stats_' . $studentId);
        
        // Clear recent activities
        Cache::forget('student_recent_activities_' . $studentId);
        
        // Clear dashboard
        Cache::forget('student_dashboard_' . $studentId);
    }
    
    /**
     * Manual cache clearing endpoint
     */
    public function clearCache()
    {
        $studentId = Auth::id();
        $this->clearTopicCaches($studentId);
        
        return redirect()->route('student.topics.index')
            ->with('success', 'All topic caches cleared successfully.');
    }
}