<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Progress;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Crypt;

class TopicController extends Controller
{
    /**
     * Display all topics for the student
     */
    public function index()
    {
        $student = Auth::user();
        
        try {
            // Get all enrolled courses
            $enrolledCourseIds = Enrollment::where('student_id', $student->id)
                ->pluck('course_id')
                ->toArray();
            
            // Check if topics table has course_id column
            $hasCourseIdColumn = \Schema::hasColumn('topics', 'course_id');
            
            if ($hasCourseIdColumn) {
                // Get all topics from enrolled courses
                $allTopics = Topic::whereIn('course_id', $enrolledCourseIds)
                    ->with(['course.teacher'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
            } else {
                // Get topics through course_topics pivot table
                $allTopics = Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                    $query->whereIn('courses.id', $enrolledCourseIds);
                })
                ->with(['courses.teacher'])
                ->orderBy('created_at', 'desc')
                ->paginate(12);
            }
            
            // Get completed topic IDs
            $completedTopicIds = $student->completedTopics()->pluck('topic_id')->toArray();
            
            // Calculate stats
            $totalTopics = $allTopics->total();
            $completedTopics = count(array_intersect(
                $allTopics->pluck('id')->toArray(),
                $completedTopicIds
            ));
            
            // Get courses for filter
            $courses = Course::whereIn('id', $enrolledCourseIds)
                ->withCount(['topics'])
                ->get();
            
            // Get topics with video
            if ($hasCourseIdColumn) {
                $topicsWithVideo = Topic::whereIn('course_id', $enrolledCourseIds)
                    ->whereNotNull('video_link')
                    ->count();
            } else {
                // Count topics with video through pivot table
                $topicsWithVideo = Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                    $query->whereIn('courses.id', $enrolledCourseIds);
                })
                ->whereNotNull('video_link')
                ->count();
            }
            
            // Get recently completed topics
            $recentlyCompleted = Progress::where('student_id', $student->id)
                ->where('status', 'completed')
                ->with('topic')
                ->orderBy('completed_at', 'desc')
                ->take(5)
                ->get();
            
            return view('student.topics.index', compact(
                'allTopics',
                'completedTopicIds',
                'totalTopics',
                'completedTopics',
                'courses',
                'topicsWithVideo',
                'recentlyCompleted'
            ));
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in TopicController@index: ' . $e->getMessage());
            
            // Return empty data with error message
            return view('student.topics.index', [
                'allTopics' => collect([]),
                'completedTopicIds' => [],
                'totalTopics' => 0,
                'completedTopics' => 0,
                'courses' => collect([]),
                'topicsWithVideo' => 0,
                'recentlyCompleted' => collect([]),
            ])->with('error', 'Unable to load topics. Please contact administrator.');
        }
    }
    
    /**
     * Show a specific topic
     */
    public function show($encryptedId)
    {
        try {
            $topicId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            
            // Get the topic with courses and teacher info
            $topic = Topic::with(['courses.teacher'])
                ->findOrFail($topicId);
            
            // Get the first course for this topic (if any)
            $course = $topic->courses->first();
            
            if (!$course) {
                // Still allow access even without course association
                // This is for topics that might be standalone
                $course = new \stdClass();
                $course->id = 0;
                $course->title = 'General Topics';
                $course->course_code = 'GEN';
            }
            
            // Check if topic is completed - but allow access regardless
            $isCompleted = Progress::where('student_id', $student->id)
                ->where('topic_id', $topicId)
                ->where('status', 'completed')
                ->exists();
            
            // Get completion date if completed
            $completionDate = null;
            if ($isCompleted) {
                $progress = Progress::where('student_id', $student->id)
                    ->where('topic_id', $topicId)
                    ->first();
                $completionDate = $progress->completed_at ?? now();
            }
            
            // Get enrolled course IDs for stats
            $enrolledCourseIds = Enrollment::where('student_id', $student->id)
                ->pluck('course_id')
                ->toArray();
            
            // Get total topics for enrolled courses
            $hasCourseIdColumn = \Schema::hasColumn('topics', 'course_id');
            
            if ($hasCourseIdColumn) {
                $totalTopics = Topic::whereIn('course_id', $enrolledCourseIds)->count();
            } else {
                $totalTopics = Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                    $query->whereIn('courses.id', $enrolledCourseIds);
                })->count();
            }
            
            // Get completed topics count
            $completedTopics = Progress::where('student_id', $student->id)
                ->where('status', 'completed')
                ->count();
            
            return view('student.topics.show', compact(
                'topic',
                'course',
                'isCompleted',
                'completionDate',
                'encryptedId',
                'totalTopics',
                'completedTopics'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error in TopicController@show: ' . $e->getMessage());
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
            
            // Get topic
            $topic = Topic::findOrFail($topicId);
            
            // Mark as complete
            Progress::updateOrCreate([
                'student_id' => $student->id,
                'topic_id' => $topicId
            ], [
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->input('notes', '')
            ]);
            
            // Get updated stats
            $completedTopics = Progress::where('student_id', $student->id)
                ->where('status', 'completed')
                ->count();
                
            $totalTopics = $this->getTotalTopicsCount($student->id);
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
            \Log::error('Error in TopicController@markComplete: ' . $e->getMessage());
            
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
        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->pluck('course_id')
            ->toArray();
        
        // Check if topics table has course_id column
        $hasCourseIdColumn = \Schema::hasColumn('topics', 'course_id');
        
        if ($hasCourseIdColumn) {
            return Topic::whereIn('course_id', $enrolledCourseIds)->count();
        } else {
            return Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                $query->whereIn('courses.id', $enrolledCourseIds);
            })->count();
        }
    }
    
    /**
     * Mark topic as incomplete
     */
    public function markIncomplete(Request $request, $encryptedId)
    {
        try {
            $topicId = Crypt::decrypt($encryptedId);
            $student = Auth::user();
            
            // Update status to incomplete
            Progress::where('student_id', $student->id)
                ->where('topic_id', $topicId)
                ->update([
                    'status' => 'incomplete', 
                    'completed_at' => null
                ]);
            
            // Get updated stats
            $completedTopics = Progress::where('student_id', $student->id)
                ->where('status', 'completed')
                ->count();
                
            $totalTopics = $this->getTotalTopicsCount($student->id);
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
            \Log::error('Error in TopicController@markIncomplete: ' . $e->getMessage());
            
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
            
            // Save notes
            Progress::updateOrCreate([
                'student_id' => $student->id,
                'topic_id' => $topicId
            ], [
                'notes' => $request->input('notes'),
                'status' => Progress::where('student_id', $student->id)
                    ->where('topic_id', $topicId)
                    ->value('status') ?? 'in_progress'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Notes saved successfully.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in TopicController@saveNotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save notes.'
            ], 500);
        }
    }
}