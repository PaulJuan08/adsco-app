<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Topic; // Add this import
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Eager load all necessary relationships including topics
        $course = Course::with(['teacher', 'students', 'topics'])
            ->withCount('students')
            ->findOrFail($id);
        
        // DEBUG: Check what's being loaded
        Log::info('Course Show Debug', [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'topics_count' => $course->topics->count(),
            'topics' => $course->topics->toArray(),
        ]);
        
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

    // ============ TOPIC MANAGEMENT METHODS ============
    
    /**
     * Get available topics not yet added to the course
     */
    public function availableTopics($encryptedId)
    {
        try {
            // Decrypt the course ID
            $id = decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            Log::info('Available Topics Request', [
                'course_id' => $id,
                'course_title' => $course->title
            ]);
            
            // Get ALL topics from the database (ignore course_id field)
            $allTopics = Topic::orderBy('title')
                ->get(['id', 'title', 'content', 'created_at']);
            
            Log::info('All topics from database', [
                'total_topics' => $allTopics->count(),
                'topics' => $allTopics->pluck('title')->toArray()
            ]);
            
            // Get topics already in this course via pivot table
            $currentTopicIds = $course->topics->pluck('id')->toArray();
            
            Log::info('Current course topics (via pivot)', [
                'current_topic_ids' => $currentTopicIds,
                'current_topic_count' => count($currentTopicIds)
            ]);
            
            // Filter out topics already in the course
            $availableTopics = $allTopics->filter(function($topic) use ($currentTopicIds) {
                return !in_array($topic->id, $currentTopicIds);
            })->values();
            
            Log::info('Available topics after filtering', [
                'available_count' => $availableTopics->count(),
                'available_topics' => $availableTopics->pluck('title')->toArray()
            ]);
            
            return response()->json($availableTopics);
            
        } catch (\Exception $e) {
            Log::error('Error in availableTopics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            $id = decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            Log::info('Add Topic Request', [
                'course_id' => $id,
                'topic_id' => $request->input('topic_id')
            ]);
            
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
            $topic = Topic::find($topicId);
            
            Log::info('Topic added successfully', [
                'topic_id' => $topicId,
                'topic_title' => $topic->title
            ]);
            
            return response()->json([
                'success' => true,
                'topic' => $topic
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in addTopic', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            $id = decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            $request->validate([
                'topic_ids' => 'required|array',
                'topic_ids.*' => 'exists:topics,id'
            ]);
            
            $topicIds = $request->input('topic_ids');
            
            Log::info('Add Multiple Topics Request', [
                'course_id' => $id,
                'topic_ids' => $topicIds
            ]);
            
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
            $topics = Topic::whereIn('id', $newTopicIds)->get();
            
            Log::info('Topics added successfully', [
                'added_count' => count($newTopicIds),
                'added_topics' => $topics->pluck('title')->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'topics' => $topics,
                'message' => count($newTopicIds) . ' topic(s) added successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in addTopics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            $id = decrypt($encryptedId);
            $course = Course::findOrFail($id);
            
            $request->validate([
                'topic_id' => 'required|exists:topics,id'
            ]);
            
            $topicId = $request->input('topic_id');
            
            Log::info('Remove Topic Request', [
                'course_id' => $id,
                'topic_id' => $topicId
            ]);
            
            // Detach the topic
            $course->topics()->detach($topicId);
            
            // Get the topic for response
            $topic = Topic::find($topicId);
            
            Log::info('Topic removed successfully', [
                'topic_id' => $topicId,
                'topic_title' => $topic->title
            ]);
            
            return response()->json([
                'success' => true,
                'topic' => $topic,
                'message' => 'Topic removed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in removeTopic', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove topic: ' . $e->getMessage()
            ], 500);
        }
    }
}