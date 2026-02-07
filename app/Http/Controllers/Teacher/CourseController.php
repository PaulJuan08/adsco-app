<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class CourseController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        
        // Get courses with student count (using students relationship instead of enrollments)
        $courses = Course::where('teacher_id', $teacherId)
            ->withCount('students')
            ->with('teacher')
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
            $totalStudents += $course->students_count;
        }
        
        // Calculate average students per course
        $avgStudents = $courses->isNotEmpty() 
            ? round($totalStudents / $courses->count(), 1)
            : 0;
        
        return view('teacher.courses.index', compact(
            'courses',
            'activeCourses',
            'coursesThisMonth',
            'totalStudents',
            'avgStudents'
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
            'description' => 'nullable|string',
            'credits' => 'required|numeric|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['teacher_id'] = Auth::id();
        
        // Ensure is_published is set (default to 0 if not)
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;
        
        $course = Course::create($validated);
        
        return redirect()->route('teacher.courses.show', Crypt::encrypt($course->id))
            ->with('success', 'Course "' . $course->title . '" created successfully!');
    }

    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Get course belonging to this teacher
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->with(['teacher', 'students', 'topics'])
                ->withCount('students')
                ->firstOrFail();
            
            Log::info('Teacher Course Show Debug', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'topics_count' => $course->topics->count(),
                'teacher_id' => Auth::id(),
            ]);
            
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
            
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
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
            
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
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
            
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            // Check if course has students before deleting
            if ($course->students()->exists()) {
                return redirect()->route('teacher.courses.index')
                    ->with('error', 'Cannot delete course with enrolled students.');
            }

            $course->delete();

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

    // ============ TOPIC MANAGEMENT METHODS (Similar to Admin) ============
    
    /**
     * Get available topics not yet added to the course
     */
    public function availableTopics($encryptedId)
    {
        try {
            // Decrypt the course ID
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            Log::info('Teacher Available Topics Request', [
                'course_id' => $id,
                'course_title' => $course->title,
                'teacher_id' => Auth::id()
            ]);
            
            // Get ALL topics from the database
            $allTopics = Topic::orderBy('title')
                ->get(['id', 'title', 'content', 'description', 'created_at']);
            
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
            Log::error('Error in teacher availableTopics', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id(),
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
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            Log::info('Teacher Add Topic Request', [
                'course_id' => $id,
                'topic_id' => $request->input('topic_id'),
                'teacher_id' => Auth::id()
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
            
            Log::info('Teacher added topic successfully', [
                'topic_id' => $topicId,
                'topic_title' => $topic->title
            ]);
            
            return response()->json([
                'success' => true,
                'topic' => $topic
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in teacher addTopic', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id(),
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
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            $request->validate([
                'topic_ids' => 'required|array',
                'topic_ids.*' => 'exists:topics,id'
            ]);
            
            $topicIds = $request->input('topic_ids');
            
            Log::info('Teacher Add Multiple Topics Request', [
                'course_id' => $id,
                'topic_ids' => $topicIds,
                'teacher_id' => Auth::id()
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
            
            Log::info('Teacher added topics successfully', [
                'added_count' => count($newTopicIds),
                'added_topics' => $topics->pluck('title')->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'topics' => $topics,
                'message' => count($newTopicIds) . ' topic(s) added successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in teacher addTopics', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id(),
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
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher owns this course
            $course = Course::where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();
            
            $request->validate([
                'topic_id' => 'required|exists:topics,id'
            ]);
            
            $topicId = $request->input('topic_id');
            
            Log::info('Teacher Remove Topic Request', [
                'course_id' => $id,
                'topic_id' => $topicId,
                'teacher_id' => Auth::id()
            ]);
            
            // Detach the topic
            $course->topics()->detach($topicId);
            
            // Get the topic for response
            $topic = Topic::find($topicId);
            
            Log::info('Teacher removed topic successfully', [
                'topic_id' => $topicId,
                'topic_title' => $topic->title
            ]);
            
            return response()->json([
                'success' => true,
                'topic' => $topic,
                'message' => 'Topic removed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in teacher removeTopic', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
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
        
        // Get enrollments for courses taught by this teacher
        $enrollments = Enrollment::whereIn('course_id', function($query) use ($teacherId) {
            $query->select('id')->from('courses')->where('teacher_id', $teacherId);
        })->with(['student', 'course'])
          ->latest()
          ->paginate(10);
        
        return view('teacher.enrollments', compact('enrollments'));
    }
}