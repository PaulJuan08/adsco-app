<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\College;
use App\Models\Program;
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
        
        // Get courses with student count and topics count
        $courses = Course::where('teacher_id', $teacherId)
            ->select(['id', 'title', 'course_code', 'description', 'teacher_id', 'is_published', 'credits', 'status', 'created_at', 'updated_at', 'created_by'])
            ->withCount(['students', 'topics'])
            ->with([
                'teacher' => function($query) {
                    $query->select(['id', 'f_name', 'l_name']);
                },
                'creator' => function($query) {  // ADD THIS
                    $query->select(['id', 'f_name', 'l_name', 'role']);
                }
            ])
            ->latest()
            ->paginate(10);
        
        // Calculate total students across all courses
        $totalStudents = 0;
        $publishedCourses = 0;
        $draftCourses = 0;
        
        foreach ($courses as $course) {
            $totalStudents += $course->students_count;
            
            if ($course->is_published) {
                $publishedCourses++;
            } else {
                $draftCourses++;
            }
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
        
        // Get total topics count for the teacher
        $totalTopics = \App\Models\Topic::whereHas('courses', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->count();
        
        // Get draft count for teacher's courses
        $draftCount = Course::where('teacher_id', $teacherId)
            ->where('is_published', false)
            ->count();
        
        return view('teacher.courses.index', [
            'courses' => $courses,
            'activeCourses' => $stats->active_courses ?? 0,
            'coursesThisMonth' => $stats->courses_this_month ?? 0,
            'totalStudents' => $totalStudents,
            'avgStudents' => $avgStudents,
            'totalCourses' => $stats->total_courses ?? 0,
            'publishedCourses' => $publishedCourses,
            'draftCourses' => $draftCourses,
            'draftCount' => $draftCount,
            'totalTopics' => $totalTopics,
        ]);
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
            'credits' => 'required|numeric|min:1|max:10',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['teacher_id'] = Auth::id();
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;
        $validated['status'] = 'active';
        $validated['created_by'] = Auth::id();
        
        $course = Course::create($validated);
        
        $this->clearTeacherCourseCaches(Auth::id());
        $this->clearAdminCourseCaches();
        
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
                        'topics:id,title,content,description,is_published,created_at'
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
                'credits' => 'required|numeric|min:1|max:10',
                'is_published' => 'nullable|boolean',
            ]);
            
            $validated['is_published'] = $request->has('is_published') ? 1 : 0;
            
            $course->update($validated);
            
            $this->clearTeacherCourseCaches($teacherId);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            Cache::forget('teacher_course_edit_' . $id . '_teacher_' . $teacherId);
            Cache::forget('available_topics_' . $id);
            
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
            
            if ($course->students()->exists()) {
                return redirect()->route('teacher.courses.index')
                    ->with('error', 'Cannot delete course with enrolled students.');
            }

            $course->delete();
            
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
    
    public function availableTopics($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            $cacheKey = 'teacher_available_topics_' . $id;
            
            $availableTopics = Cache::remember($cacheKey, 300, function() use ($id, $course, $teacherId) {
                $allTopics = Topic::where('created_by', $teacherId)
                    ->select(['id', 'title', 'content', 'description', 'is_published', 'created_at'])
                    ->orderBy('title')
                    ->get();
                
                $currentTopicIds = $course->topics->pluck('id')->toArray();
                
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
    
    public function addTopic(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            $request->validate([
                'topic_id' => 'required|exists:topics,id'
            ]);
            
            $topicId = $request->input('topic_id');
            
            if ($course->topics()->where('topics.id', $topicId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Topic is already added to this course'
                ]);
            }
            
            $course->topics()->attach($topicId);
            
            $topic = Topic::select(['id', 'title', 'content', 'description', 'is_published', 'created_at'])->find($topicId);
            $topic->encrypted_id = urlencode(Crypt::encrypt($topic->id));
            
            Cache::forget('teacher_available_topics_' . $id);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            
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
    
    public function addTopics(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            $request->validate([
                'topic_ids' => 'required|array',
                'topic_ids.*' => 'exists:topics,id'
            ]);
            
            $topicIds = $request->input('topic_ids');
            
            $existingTopicIds = $course->topics()->whereIn('topics.id', $topicIds)->pluck('topics.id')->toArray();
            $newTopicIds = array_diff($topicIds, $existingTopicIds);
            
            if (empty($newTopicIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected topics are already added to this course'
                ]);
            }
            
            $course->topics()->attach($newTopicIds);
            
            $topics = Topic::select(['id', 'title', 'content', 'description', 'is_published', 'created_at'])
                ->whereIn('id', $newTopicIds)
                ->get()
                ->map(function($topic) {
                    $topic->encrypted_id = urlencode(Crypt::encrypt($topic->id));
                    return $topic;
                });
            
            Cache::forget('teacher_available_topics_' . $id);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            
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
    
    public function removeTopic(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();
            
            $request->validate([
                'topic_id' => 'required|exists:topics,id'
            ]);
            
            $topicId = $request->input('topic_id');
            
            $course->topics()->detach($topicId);
            
            $topic = Topic::select(['id', 'title', 'content', 'description', 'is_published', 'created_at'])->find($topicId);
            
            Cache::forget('teacher_available_topics_' . $id);
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            
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
    
    public function clearCache()
    {
        $teacherId = Auth::id();
        $this->clearTeacherCourseCaches($teacherId);
        
        $courses = Course::where('teacher_id', $teacherId)->get();
        foreach ($courses as $course) {
            Cache::forget('teacher_course_show_' . $course->id . '_teacher_' . $teacherId);
            Cache::forget('teacher_course_edit_' . $course->id . '_teacher_' . $teacherId);
            Cache::forget('teacher_available_topics_' . $course->id);
        }
        
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('teacher_enrollments_' . $teacherId . '_page_' . $page);
        }
        
        return redirect()->route('teacher.courses.index')
            ->with('success', 'Teacher course caches cleared successfully.');
    }

    /**
     * Publish/Unpublish course
     */
    public function publish($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $course = Course::where('id', $id)
                ->where('teacher_id', $teacherId)
                ->firstOrFail();

            $course->update(['is_published' => !$course->is_published]);
            $status = $course->is_published ? 'published' : 'unpublished';

            // Clear caches
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . $teacherId);
            Cache::forget('teacher_course_edit_' . $id . '_teacher_' . $teacherId);
            $this->clearTeacherCourseCaches($teacherId);

            return redirect()->route('teacher.courses.show', $encryptedId)
                ->with('success', "Course {$status} successfully!");

        } catch (\Exception $e) {
            Log::error('Teacher error publishing course', [
                'encryptedId' => $encryptedId, 
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->route('teacher.courses.index')
                ->with('error', 'Failed to update course status.');
        }
    }

    /**
     * Access modal for teacher (shows students and enrollment status)
     */
    public function accessModal($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            
            $course = Course::where('teacher_id', auth()->id())->findOrFail($id);

            $students = User::where('role', 4) // Students only
                ->with(['program', 'college'])
                ->orderBy('f_name')
                ->paginate(20);

            $enrolledStudentIds = $course->students()->pluck('users.id')->toArray();

            $colleges = College::where('status', 1)
                ->orderBy('college_name')
                ->get();

            $encryptedCourseId = $encryptedId;

            return view('teacher.courses.partials.access-modal', compact(
                'course', 'students', 'enrolledStudentIds', 'colleges', 'encryptedCourseId'
            ));

        } catch (\Exception $e) {
            Log::error('Teacher error loading access modal', [
                'encryptedId' => $encryptedId, 
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to load access modal'], 500);
        }
    }

    /**
     * Toggle enrollment for a student
     */
    public function toggleEnrollment(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id = Crypt::decrypt($encryptedId);
            
            $course = Course::where('teacher_id', auth()->id())->findOrFail($id);

            $request->validate(['student_id' => 'required|exists:users,id']);

            $studentId = $request->student_id;
            $enrolled = $course->enrollments()->where('student_id', $studentId)->exists();

            if ($enrolled) {
                $course->enrollments()->where('student_id', $studentId)->delete();
                $message = 'Student removed from course.';
                $enrolled = false;
            } else {
                $course->enrollments()->create([
                    'student_id' => $studentId,
                    'enrolled_at' => now(),
                    'status' => 'active',
                ]);
                $message = 'Student enrolled successfully.';
                $enrolled = true;
            }

            // Clear caches
            Cache::forget('teacher_course_show_' . $id . '_teacher_' . auth()->id());
            $this->clearTeacherCourseCaches($id);

            return response()->json([
                'success' => true, 
                'message' => $message, 
                'enrolled' => $enrolled
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Teacher error toggling enrollment', [
                'encryptedId' => $encryptedId, 
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update enrollment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get programs by college (for AJAX)
     */
    public function getProgramsByCollege($collegeId)
    {
        $programs = \App\Models\Program::where('college_id', $collegeId)
            ->where('status', 1)
            ->orderBy('program_name')
            ->get(['id', 'program_name', 'code']);
        
        return response()->json($programs);
    }
}