<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Traits\CacheManager;

class CourseController extends Controller
{
    use CacheManager;
    
    public function index()
    {
        // Cache key based on URL and filters
        $cacheKey = 'admin_courses_index_page_' . md5(json_encode([
            'search' => request('search'),
            'status' => request('status'),
            'has_teacher' => request('has_teacher'),
            'sort' => request('sort'),
            'page' => request('page', 1),
            'user_id' => auth()->id()
        ]));
        
        // Cache for 5 minutes (300 seconds)
        $data = Cache::remember($cacheKey, 300, function() {
            // Get courses with specific columns only
            $courses = Course::select(['id', 'title', 'course_code', 'description', 'teacher_id', 'is_published', 'credits', 'status', 'created_at'])
                ->withCount('students')
                ->with(['teacher' => function($query) {
                    $query->select(['id', 'f_name', 'l_name', 'employee_id']);
                }])
                ->latest()
                ->paginate(10);
            
            // Get all statistics in ONE optimized query instead of multiple queries
            $stats = Course::selectRaw('
                COUNT(*) as total_courses,
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as active_courses,
                SUM(CASE WHEN teacher_id IS NOT NULL THEN 1 ELSE 0 END) as assigned_teachers,
                SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as courses_this_month
            ', [now()->month, now()->year])
            ->first();
            
            // Get average students per course
            $avgStudents = Cache::remember('avg_students_per_course', 300, function() {
                return Course::withCount('students')
                    ->get()
                    ->avg('students_count') ?? 0;
            });
            
            // Get total students count (cached)
            $totalStudents = Cache::remember('total_students_count', 600, function() {
                return User::where('role', 4)->count();
            });
            
            // Get draft count
            $draftCount = Cache::remember('draft_courses_count', 300, function() {
                return Course::where('is_published', false)->count();
            });
            
            return [
                'courses' => $courses,
                'activeCourses' => $stats->active_courses ?? 0,
                'assignedTeachers' => $stats->assigned_teachers ?? 0,
                'totalStudents' => $totalStudents,
                'coursesThisMonth' => $stats->courses_this_month ?? 0,
                'avgStudents' => round($avgStudents, 1),
                'draftCount' => $draftCount,
                'totalCourses' => $stats->total_courses ?? 0
            ];
        });
        
        return view('admin.courses.index', $data);
    }
    
    public function create()
    {
        // Cache teachers for 10 minutes
        $teachers = Cache::remember('all_teachers', 600, function() {
            return User::where('role', 3)
                ->select(['id', 'f_name', 'l_name', 'employee_id'])
                ->orderBy('f_name')
                ->get();
        });
        
        return view('admin.courses.create', compact('teachers'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
            'is_published' => 'nullable|boolean',
            'credits' => 'nullable|integer|min:1',
            'status' => 'nullable|string|in:active,inactive',
        ]);
        
        // Create the course
        $course = Course::create([
            'title' => $validated['title'],
            'course_code' => $validated['course_code'],
            'description' => $validated['description'] ?? null,
            'teacher_id' => $validated['teacher_id'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'credits' => $validated['credits'] ?? 3,
            'status' => $validated['status'] ?? 'active',
        ]);
        
        // Clear all course-related caches
        $this->clearAdminCourseCaches();
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }
    
    public function show($course)
    {
        try {
            // Decode first
            $encryptedId = urldecode($course);

            // Decrypt the decoded value
            $id = Crypt::decrypt($encryptedId);

            $cacheKey = 'course_show_' . $id;
            $course = Cache::remember($cacheKey, 600, function () use ($id) {
                return Course::with([
                        'teacher:id,f_name,l_name,employee_id,email',
                        'students:id,f_name,l_name,email',
                        'topics:id,title,content,is_published,order,created_at'
                    ])
                    ->withCount('students')
                    ->findOrFail($id);
            });

            return view('admin.courses.show', compact('course', 'encryptedId'));

        } catch (\Exception $e) {
            Log::error('Error showing course', [
                'encryptedId' => $course,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.courses.index')
                ->with('error', 'Course not found or invalid link.');
        }
    }
    
    public function edit($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            
            // Cache course for edit (5 minutes for fresh data)
            $cacheKey = 'course_edit_' . $id;
            $course = Cache::remember($cacheKey, 300, function() use ($id) {
                return Course::findOrFail($id);
            });
            
            // Cache teachers
            $teachers = Cache::remember('all_teachers', 600, function() {
                return User::where('role', 3)
                    ->select(['id', 'f_name', 'l_name', 'employee_id'])
                    ->orderBy('f_name')
                    ->get();
            });
            
            return view('admin.courses.edit', compact('course', 'teachers', 'encryptedId'));
            
        } catch (\Exception $e) {
            Log::error('Error editing course', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.courses.index')
                ->with('error', 'Course not found or invalid link.');
        }
    }
    
    public function update(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'course_code' => 'required|string|max:50|unique:courses,course_code,' . $course->id,
                'description' => 'nullable|string',
                'teacher_id' => 'nullable|exists:users,id',
                'is_published' => 'nullable|boolean',
                'credits' => 'nullable|integer|min:1',
                'status' => 'nullable|string|in:active,inactive',
            ]);
            
            $course->update([
                'title' => $validated['title'],
                'course_code' => $validated['course_code'],
                'description' => $validated['description'] ?? null,
                'teacher_id' => $validated['teacher_id'] ?? null,
                'is_published' => $validated['is_published'] ?? false,
                'credits' => $validated['credits'] ?? $course->credits,
                'status' => $validated['status'] ?? $course->status,
            ]);
            
            // Clear all course-related caches
            $this->clearAdminCourseCaches();
            Cache::forget('course_show_' . $id);
            Cache::forget('course_edit_' . $id);
            
            // Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return redirect()->route('admin.courses.show', urlencode(Crypt::encrypt($course->id)))
                ->with('success', 'Course updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Error updating course', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.courses.index')
                ->with('error', 'Failed to update course.');
        }
    }
    
    public function destroy($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            // Check if course has students before deleting
            if ($course->students()->exists()) {
                return redirect()->route('admin.courses.index')
                    ->with('error', 'Cannot delete course with enrolled students.');
            }
            
            $course->delete();
            
            // Clear all course-related caches
            $this->clearAdminCourseCaches();
            Cache::forget('course_show_' . $id);
            Cache::forget('course_edit_' . $id);
            
            return redirect()->route('admin.courses.index')
                ->with('success', 'Course deleted successfully!');
                
        } catch (\Exception $e) {
            Log::error('Error deleting course', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.courses.index')
                ->with('error', 'Failed to delete course.');
        }
    }
    
    // ============ OPTIMIZED TOPIC MANAGEMENT METHODS ============
    
    public function availableTopics($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            
            // Cache available topics for 5 minutes
            $cacheKey = 'available_topics_' . $id;
            $availableTopics = Cache::remember($cacheKey, 300, function() use ($id) {
                $course = Course::findOrFail($id);
                
                // Get all topics with specific columns
                $allTopics = Topic::select(['id', 'title', 'content', 'created_at'])
                    ->orderBy('title')
                    ->get();
                
                // Get current topic IDs
                $currentTopicIds = $course->topics->pluck('id')->toArray();
                
                // Filter out topics already in the course
                return $allTopics->filter(function($topic) use ($currentTopicIds) {
                    return !in_array($topic->id, $currentTopicIds);
                })->values();
            });
            
            return response()->json($availableTopics);
            
        } catch (\Exception $e) {
            Log::error('Error in availableTopics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to load topics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addTopic(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
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
            
            // Get the added topic
            $topic = Topic::find($topicId);
            
            // Clear relevant caches
            Cache::forget('available_topics_' . $id);
            Cache::forget('course_show_' . $id);
            
            // 🔥 IMPORTANT: Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return response()->json([
                'success' => true,
                'topic' => $topic
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in addTopic', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add topic: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addTopics(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            $request->validate([
                'topic_ids' => 'required|array',
                'topic_ids.*' => 'exists:topics,id'
            ]);
            
            $topicIds = $request->input('topic_ids');
            
            // Filter out topics already attached
            $existingTopicIds = $course->topics->pluck('id')->toArray();
            $newTopicIds = array_diff($topicIds, $existingTopicIds);
            
            if (empty($newTopicIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected topics are already added to this course'
                ]);
            }
            
            // Attach the topics
            $course->topics()->attach($newTopicIds);
            
            // Get the added topics
            $topics = Topic::whereIn('id', $newTopicIds)->get();
            
            // Clear relevant caches
            Cache::forget('available_topics_' . $id);
            Cache::forget('course_show_' . $id);
            
            // 🔥 IMPORTANT: Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return response()->json([
                'success' => true,
                'topics' => $topics,
                'message' => count($newTopicIds) . ' topic(s) added successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in addTopics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add topics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeTopic(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            $request->validate([
                'topic_id' => 'required|exists:topics,id'
            ]);
            
            $topicId = $request->input('topic_id');
            
            // Check if topic is attached
            if (!$course->topics()->where('topics.id', $topicId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Topic is not attached to this course'
                ]);
            }
            
            // Detach the topic
            $course->topics()->detach($topicId);
            
            // Get the removed topic for response
            $topic = Topic::find($topicId);
            
            // Clear relevant caches
            Cache::forget('available_topics_' . $id);
            Cache::forget('course_show_' . $id);
            
            // 🔥 IMPORTANT: Clear student caches for this course
            $this->clearStudentCachesForCourse($id);
            
            return response()->json([
                'success' => true,
                'topic' => $topic,
                'message' => 'Topic removed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in removeTopic', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove topic: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Manual cache clearing endpoint
     */
    public function clearCache()
    {
        $this->clearAdminCourseCaches();
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course caches cleared successfully.');
    }
}