<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Traits\CacheManager;

class CourseController extends Controller
{
    use CacheManager;
    
    public function index()
    {
        $teacherId = Auth::id();
        
        // Cache key based on teacher ID and pagination
        $cacheKey = 'teacher_courses_index_' . $teacherId . '_page_' . request('page', 1);
        
        // Cache for 5 minutes (300 seconds)
        $data = Cache::remember($cacheKey, 300, function() use ($teacherId) {
            // Get courses with student count (using students relationship)
            $courses = Course::where('teacher_id', $teacherId)
                ->select(['id', 'title', 'course_code', 'description', 'teacher_id', 'is_published', 'credits', 'status', 'created_at', 'updated_at'])
                ->withCount('students')
                ->with(['teacher' => function($query) {
                    $query->select(['id', 'f_name', 'l_name']);
                }])
                ->latest()
                ->paginate(10);
            
            // Calculate total students across all courses
            $totalStudents = 0;
            foreach ($courses as $course) {
                $totalStudents += $course->students_count;
            }
            
            // Get statistics in ONE optimized query
            $stats = Course::where('teacher_id', $teacherId)
                ->selectRaw('
                    COUNT(*) as total_courses,
                    SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as active_courses,
                    SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as courses_this_month
                ', [now()->month, now()->year])
                ->first();
            
            // Calculate average students per course
            $avgStudents = $courses->isNotEmpty() 
                ? round($totalStudents / $courses->count(), 1)
                : 0;
            
            return [
                'courses' => $courses,
                'activeCourses' => $stats->active_courses ?? 0,
                'coursesThisMonth' => $stats->courses_this_month ?? 0,
                'totalStudents' => $totalStudents,
                'avgStudents' => $avgStudents,
                'totalCourses' => $stats->total_courses ?? 0
            ];
        });
        
        return view('teacher.courses.index', $data);
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
            'description' => 'nullable|string',
            'credits' => 'required|numeric|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['teacher_id'] = Auth::id();
        
        // Ensure is_published is set (default to 0 if not)
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;
        
        $course = Course::create($validated);
        
        // Clear teacher course list caches
        $this->clearTeacherCourseCaches(Auth::id());
        
        return redirect()->route('teacher.courses.show', Crypt::encrypt($course->id))
            ->with('success', 'Course "' . $course->title . '" created successfully!');
    }

    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $cacheKey = 'teacher_course_show_' . $id . '_teacher_' . $teacherId;
            
            $course = Cache::remember($cacheKey, 600, function () use ($id, $teacherId) {
                return Course::where('id', $id)
                    ->where('teacher_id', $teacherId)
                    ->with([
                        'teacher:id,f_name,l_name',
                        'students:id,f_name,l_name,email',
                        'topics:id,title,content,description,created_at'
                    ])
                    ->withCount('students')
                    ->firstOrFail();
            });
            
            return view('teacher.courses.show', compact('course', 'encryptedId'));
            
        } catch (\Exception $e) {
            Log::error('Error accessing course', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.courses.index')
                ->with('error', 'Course not found or you do not have permission to view it.');
        }
    }

    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $cacheKey = 'teacher_course_edit_' . $id . '_teacher_' . $teacherId;
            
            $course = Cache::remember($cacheKey, 300, function() use ($id, $teacherId) {
                return Course::where('id', $id)
                    ->where('teacher_id', $teacherId)
                    ->firstOrFail();
            });
            
            return view('teacher.courses.edit', compact('course', 'encryptedId'));
            
        } catch (\Exception $e) {
            Log::error('Error accessing course for edit', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.courses.index')
                ->with('error', 'Course not found or you do not have permission to edit it.');
        }
    }

    public function update(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
        
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
                'description' => 'nullable|string',
                'credits' => 'required|numeric|min:0',
                'is_published' => 'nullable|boolean',
            ]);
            
            // Update the is_published value
            $validated['is_published'] = $request->has('is_published') ? 1 : 0;
            
            $course->update($validated);
            
            // Clear all course-related caches for this teacher
            $this->clearTeacherCourseCaches($teacherId);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            Cache::forget('teacher_course_edit_' . $id . '_teacher_' . $teacherId);
            Cache::forget('available_topics_' . $id);
            
            // Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return redirect()->route('teacher.courses.show', Crypt::encrypt($course->id))
                ->with('success', 'Course updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Error updating course', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.courses.index')
                ->with('error', 'Failed to update course.');
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            // Check if course has students before deleting
            if ($course->students()->exists()) {
                return redirect()->route('teacher.courses.index')
                    ->with('error', 'Cannot delete course with enrolled students.');
            }

            $course->delete();
            
            // Clear all course-related caches for this teacher
            $this->clearTeacherCourseCaches($teacherId);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            Cache::forget('teacher_course_edit_' . $id . '_teacher_' . $teacherId);
            Cache::forget('available_topics_' . $id);

            return redirect()->route('teacher.courses.index')
                ->with('success', 'Course "' . $course->title . '" deleted successfully!');
                
        } catch (\Exception $e) {
            Log::error('Error deleting course', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.courses.index')
                ->with('error', 'Course not found or you do not have permission to delete it.');
        }
    }

    // ============ TOPIC MANAGEMENT METHODS ============
    
    /**
     * Get available topics not yet added to the course
     */
    public function availableTopics($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            // Cache available topics for 5 minutes
            $cacheKey = 'teacher_available_topics_' . $id;
            
            $availableTopics = Cache::remember($cacheKey, 300, function() use ($id, $course) {
                // Get ALL topics from the database with specific columns
                $allTopics = Topic::select(['id', 'title', 'content', 'description', 'created_at'])
                    ->orderBy('title')
                    ->get();
                
                // Get topics already in this course via pivot table
                $currentTopicIds = $course->topics->pluck('id')->toArray();
                
                // Filter out topics already in the course
                return $allTopics->filter(function($topic) use ($currentTopicIds) {
                    return !in_array($topic->id, $currentTopicIds);
                })->values();
            });
            
            return response()->json($availableTopics);
            
        } catch (\Exception $e) {
            Log::error('Error in teacher availableTopics', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id()
            ]);
            
            return response()->json([
                'error' => 'Failed to load topics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Add a single topic to the course
     */
    public function addTopic(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            $request->validate([
                'topic_id' => 'required|exists:topics,id'
            ]);
            
            $topicId = $request->input('topic_id');
            
            // Check if topic is already attached
            if ($course->topics()->where('topics.id', $topicId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Topic is already added to this course'
                ]);
            }
            
            // Attach the topic
            $course->topics()->attach($topicId);
            
            // Reload the topic with fresh data
            $topic = Topic::select(['id', 'title', 'content', 'description', 'created_at'])->find($topicId);
            
            // Clear relevant caches
            Cache::forget('teacher_available_topics_' . $id);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            
            // 🔥 IMPORTANT: Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return response()->json([
                'success' => true,
                'topic' => $topic
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in teacher addTopic', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add topic: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Add multiple topics to the course
     */
    public function addTopics(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            $request->validate([
                'topic_ids' => 'required|array',
                'topic_ids.*' => 'exists:topics,id'
            ]);
            
            $topicIds = $request->input('topic_ids');
            
            // Filter out topics already attached
            $existingTopicIds = $course->topics()->whereIn('topics.id', $topicIds)->pluck('topics.id')->toArray();
            $newTopicIds = array_diff($topicIds, $existingTopicIds);
            
            if (empty($newTopicIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected topics are already added to this course'
                ]);
            }
            
            // Attach new topics
            $course->topics()->attach($newTopicIds);
            
            // Get the added topics
            $topics = Topic::select(['id', 'title', 'content', 'description', 'created_at'])
                ->whereIn('id', $newTopicIds)
                ->get();
            
            // Clear relevant caches
            Cache::forget('teacher_available_topics_' . $id);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            
            // 🔥 IMPORTANT: Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return response()->json([
                'success' => true,
                'topics' => $topics,
                'message' => count($newTopicIds) . ' topic(s) added successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in teacher addTopics', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add topics: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove a topic from the course
     */
    public function removeTopic(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            $request->validate([
                'topic_id' => 'required|exists:topics,id'
            ]);
            
            $topicId = $request->input('topic_id');
            
            // Detach the topic
            $course->topics()->detach($topicId);
            
            // Get the topic for response
            $topic = Topic::select(['id', 'title', 'content', 'description', 'created_at'])->find($topicId);
            
            // Clear relevant caches
            Cache::forget('teacher_available_topics_' . $id);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            
            // 🔥 IMPORTANT: Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return response()->json([
                'success' => true,
                'topic' => $topic,
                'message' => 'Topic removed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in teacher removeTopic', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove topic: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ ENROLLMENTS ============
    
    public function enrollments()
    {
        $teacherId = Auth::id();
        
        $cacheKey = 'teacher_enrollments_' . $teacherId . '_page_' . request('page', 1);
        
        $enrollments = Cache::remember($cacheKey, 300, function() use ($teacherId) {
            return Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                    $query->select('id')->from('courses')->where('teacher_id', $teacherId);
                })
                ->with(['student' => function($query) {
                    $query->select(['id', 'f_name', 'l_name', 'email']);
                }, 'course' => function($query) {
                    $query->select(['id', 'title', 'course_code']);
                }])
                ->latest()
                ->paginate(10);
        });
        
        return view('teacher.enrollments', compact('enrollments'));
    }
    
    /**
     * Manual cache clearing endpoint for teachers
     */
    public function clearCache()
    {
        $teacherId = Auth::id();
        $this->clearTeacherCourseCaches($teacherId);
        
        // Clear all course-specific caches
        $courses = Course::where('teacher_id', $teacherId)->get();
        foreach ($courses as $course) {
            Cache::forget('teacher_course_show_' . $course->id . '_teacher_' . $teacherId);
            Cache::forget('teacher_course_edit_' . $course->id . '_teacher_' . $teacherId);
            Cache::forget('teacher_available_topics_' . $course->id);
        }
        
        // Clear enrollments cache
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('teacher_enrollments_' . $teacherId . '_page_' . $page);
        }
        
        return redirect()->route('teacher.courses.index')
            ->with('success', 'Teacher course caches cleared successfully.');
    }
}