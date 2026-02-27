<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Topic;
use App\Models\College;
use App\Models\Program;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Traits\CacheManager;

class CourseController extends Controller
{
    use CacheManager;

    // ============================================================
    // INDEX
    // ============================================================

    public function index()
    {
        $courses = Course::select(['id', 'title', 'course_code', 'description', 'teacher_id', 'is_published', 'credits', 'status', 'created_at', 'created_by'])
            ->withCount('students')
            ->with([
                'teacher' => function ($query) {
                    $query->select(['id', 'f_name', 'l_name', 'employee_id']);
                },
                'creator' => function ($query) {  // ADD THIS
                    $query->select(['id', 'f_name', 'l_name', 'role']);
                }
            ])
            ->latest()
            ->paginate(10);

        $stats = Course::selectRaw('
            COUNT(*) as total_courses,
            SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as active_courses,
            COUNT(DISTINCT teacher_id) as assigned_teachers,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as courses_this_month
        ', [now()->month, now()->year])
        ->first();

        $avgStudents   = Course::withCount('students')->get()->avg('students_count') ?? 0;
        $totalStudents = Enrollment::distinct('student_id')->count('student_id');
        $draftCount    = Course::where('is_published', false)->count();

        return view('admin.courses.index', [
            'courses'          => $courses,
            'activeCourses'    => $stats->active_courses    ?? 0,
            'assignedTeachers' => $stats->assigned_teachers ?? 0,
            'totalStudents'    => $totalStudents,
            'coursesThisMonth' => $stats->courses_this_month ?? 0,
            'avgStudents'      => round($avgStudents, 1),
            'draftCount'       => $draftCount,
            'totalCourses'     => $stats->total_courses ?? 0,
        ]);
    }

    // ============================================================
    // CREATE / STORE
    // ============================================================

    public function create()
    {
        $teachers = Cache::remember('all_teachers', 600, function () {
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
            'title'        => 'required|string|max:255',
            'course_code'  => 'required|string|max:50|unique:courses',
            'description'  => 'nullable|string',
            'teacher_id'   => 'nullable|exists:users,id',
            'is_published' => 'nullable|boolean',
            'credits'      => 'nullable|integer|min:1|max:10',
            'status'       => 'nullable|string|in:active,inactive',
        ]);

        $course = Course::create([
            'title'        => $validated['title'],
            'course_code'  => $validated['course_code'],
            'description'  => $validated['description'] ?? null,
            'teacher_id'   => $validated['teacher_id']  ?? null,
            'is_published' => $request->has('is_published') ? true : false,
            'credits'      => $validated['credits']      ?? 3,
            'status'       => $validated['status']       ?? 'active',
            'created_by'   => auth()->id(),
        ]);

        $this->clearAdminCourseCaches();

        if ($course->teacher_id) {
            $this->clearTeacherCourseCaches($course->teacher_id);
        }

        Log::info('New course created — ID: ' . $course->id . ', Title: ' . $course->title);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    // ============================================================
    // SHOW
    // ============================================================

    public function show($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);

            $course = Cache::remember('course_show_' . $id, 600, function () use ($id) {
                return Course::with([
                    'teacher:id,f_name,l_name,employee_id,email',
                    'students:id,f_name,l_name,email,student_id',
                    'topics:id,title,content,is_published,order,created_at,description',
                    'creator:id,f_name,l_name',
                ])
                ->withCount('students')
                ->withCount('topics')
                ->findOrFail($id);
            });

            $course->published_topics_count = $course->topics()->where('is_published', true)->count();

            return view('admin.courses.show', compact('course', 'encryptedId'));

        } catch (\Exception $e) {
            Log::error('Error showing course', ['encryptedId' => $encryptedId, 'error' => $e->getMessage()]);
            return redirect()->route('admin.courses.index')->with('error', 'Course not found or invalid link.');
        }
    }

    // ============================================================
    // EDIT / UPDATE
    // ============================================================

    public function edit($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);

            $course = Cache::remember('course_edit_' . $id, 300, function () use ($id) {
                return Course::findOrFail($id);
            });

            $teachers = Cache::remember('all_teachers', 600, function () {
                return User::where('role', 3)
                    ->select(['id', 'f_name', 'l_name', 'employee_id'])
                    ->orderBy('f_name')
                    ->get();
            });

            return view('admin.courses.edit', compact('course', 'teachers', 'encryptedId'));

        } catch (\Exception $e) {
            Log::error('Error editing course', ['encryptedId' => $encryptedId, 'error' => $e->getMessage()]);
            return redirect()->route('admin.courses.index')->with('error', 'Course not found or invalid link.');
        }
    }

    public function update(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $validated = $request->validate([
                'title'        => 'required|string|max:255',
                'course_code'  => 'required|string|max:50|unique:courses,course_code,' . $course->id,
                'description'  => 'nullable|string',
                'teacher_id'   => 'nullable|exists:users,id',
                'is_published' => 'nullable|boolean',
                'credits'      => 'nullable|integer|min:1|max:10',
                'status'       => 'nullable|string|in:active,inactive',
            ]);

            $course->update([
                'title'        => $validated['title'],
                'course_code'  => $validated['course_code'],
                'description'  => $validated['description'] ?? null,
                'teacher_id'   => $validated['teacher_id']  ?? null,
                'is_published' => $request->has('is_published') ? true : false,
                'credits'      => $validated['credits']      ?? $course->credits,
                'status'       => $validated['status']       ?? $course->status,
            ]);

            $this->clearAdminCourseCaches();
            Cache::forget('course_show_' . $id);
            Cache::forget('course_edit_' . $id);
            $this->clearStudentCachesForCourse($id);

            return redirect()->route('admin.courses.show', urlencode(Crypt::encrypt($course->id)))
                ->with('success', 'Course updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating course', ['encryptedId' => $encryptedId, 'error' => $e->getMessage()]);
            return redirect()->route('admin.courses.index')->with('error', 'Failed to update course.');
        }
    }

    // ============================================================
    // DESTROY
    // ============================================================

    public function destroy($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            if ($course->students()->exists()) {
                return redirect()->route('admin.courses.index')
                    ->with('error', 'Cannot delete course with enrolled students.');
            }

            $course->delete();

            $this->clearAdminCourseCaches();
            Cache::forget('course_show_' . $id);
            Cache::forget('course_edit_' . $id);

            return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting course', ['encryptedId' => $encryptedId, 'error' => $e->getMessage()]);
            return redirect()->route('admin.courses.index')->with('error', 'Failed to delete course.');
        }
    }

    // ============================================================
    // PUBLISH / UNPUBLISH
    // ============================================================

    public function publish($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $course->update(['is_published' => !$course->is_published]);
            $status = $course->is_published ? 'published' : 'unpublished';

            $this->clearAdminCourseCaches();
            Cache::forget('course_show_' . $id);

            return redirect()->route('admin.courses.show', $encryptedId)
                ->with('success', "Course {$status} successfully!");

        } catch (\Exception $e) {
            Log::error('Error publishing course', ['encryptedId' => $encryptedId, 'error' => $e->getMessage()]);
            return redirect()->route('admin.courses.index')
                ->with('error', 'Failed to update course status. ' . $e->getMessage());
        }
    }

    // ============================================================
    // ACCESS MANAGEMENT
    // ============================================================

    public function accessModal($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $students = User::where('role', 4)
                ->with(['program', 'college'])
                ->orderBy('f_name')
                ->paginate(20);

            $enrolledStudentIds = $course->students()->pluck('users.id')->toArray();

            $colleges = College::where('status', 1)
                ->orderBy('college_name')
                ->get();

            $encryptedCourseId = $encryptedId;

            return view('admin.courses.partials.access-modal', compact(
                'course', 'students', 'enrolledStudentIds', 'colleges', 'encryptedCourseId'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading access modal', ['encryptedId' => $encryptedId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load access modal'], 500);
        }
    }

    public function toggleEnrollment(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $request->validate(['student_id' => 'required|exists:users,id']);

            $studentId = $request->student_id;
            $enrolled  = $this->isEnrolled($course, $studentId);

            if ($enrolled) {
                $course->enrollments()->where('student_id', $studentId)->delete();
                $message  = 'Student removed from course.';
                $enrolled = false;
            } else {
                $course->enrollments()->create([
                    'student_id'  => $studentId,
                    'enrolled_at' => now(),
                    'status'      => 'active',
                ]);
                $message  = 'Student enrolled successfully.';
                $enrolled = true;
            }

            Cache::forget('course_show_' . $id);
            $this->clearStudentCachesForCourse($id);

            return response()->json(['success' => true, 'message' => $message, 'enrolled' => $enrolled]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Error toggling enrollment', ['encryptedId' => $encryptedId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update enrollment: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    // TOPIC MANAGEMENT
    // ============================================================

    /**
     * Return topics that are NOT yet attached to this course.
     * Includes is_published so the modal status badge works.
     * NOT cached — must always reflect the live state.
     */
    public function availableTopics($encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $currentTopicIds = $course->topics()->pluck('topics.id')->toArray();

            $availableTopics = Topic::select(['id', 'title', 'content', 'description', 'is_published', 'created_at'])
                ->whereNotIn('id', $currentTopicIds)
                ->orderBy('title')
                ->get();

            return response()->json($availableTopics);

        } catch (\Exception $e) {
            Log::error('Error in availableTopics', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load topics', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Attach a single topic to the course.
     * Returns the topic with encrypted_id so the JS publish button
     * works immediately — no page reload required.
     */
    public function addTopic(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $request->validate(['topic_id' => 'required|exists:topics,id']);

            $topicId = $request->input('topic_id');

            if ($course->topics()->where('topics.id', $topicId)->exists()) {
                return response()->json(['success' => false, 'message' => 'Topic is already added to this course']);
            }

            $course->topics()->attach($topicId);

            $topic               = Topic::findOrFail($topicId);
            $topic->encrypted_id = urlencode(Crypt::encrypt($topic->id));

            Cache::forget('course_show_' . $id);
            $this->clearStudentCachesForCourse($id);

            return response()->json(['success' => true, 'topic' => $topic]);

        } catch (\Exception $e) {
            Log::error('Error in addTopic', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to add topic: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Attach multiple topics to the course at once.
     * Returns each topic with encrypted_id so publish buttons work immediately.
     */
    public function addTopics(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $request->validate([
                'topic_ids'   => 'required|array',
                'topic_ids.*' => 'exists:topics,id',
            ]);

            $topicIds         = $request->input('topic_ids');
            $existingTopicIds = $course->topics()->pluck('topics.id')->toArray();
            $newTopicIds      = array_values(array_diff($topicIds, $existingTopicIds));

            if (empty($newTopicIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected topics are already added to this course',
                ]);
            }

            $course->topics()->attach($newTopicIds);

            $topics = Topic::whereIn('id', $newTopicIds)
                ->get(['id', 'title', 'description', 'content', 'is_published'])
                ->map(function ($topic) {
                    $topic->encrypted_id = urlencode(Crypt::encrypt($topic->id));
                    return $topic;
                });

            Cache::forget('course_show_' . $id);
            $this->clearStudentCachesForCourse($id);

            return response()->json([
                'success' => true,
                'topics'  => $topics,
                'message' => count($newTopicIds) . ' topic(s) added successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error in addTopics', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to add topics: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Detach a topic from the course.
     */
    public function removeTopic(Request $request, $encryptedId)
    {
        try {
            $encryptedId = urldecode($encryptedId);
            $id          = Crypt::decrypt($encryptedId);
            $course      = Course::findOrFail($id);

            $request->validate(['topic_id' => 'required|exists:topics,id']);

            $topicId = $request->input('topic_id');

            if (!$course->topics()->where('topics.id', $topicId)->exists()) {
                return response()->json(['success' => false, 'message' => 'Topic is not attached to this course']);
            }

            $course->topics()->detach($topicId);

            $topic = Topic::find($topicId, ['id', 'title', 'description', 'content', 'is_published']);

            Cache::forget('course_show_' . $id);
            $this->clearStudentCachesForCourse($id);

            return response()->json(['success' => true, 'topic' => $topic, 'message' => 'Topic removed successfully']);

        } catch (\Exception $e) {
            Log::error('Error in removeTopic', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to remove topic: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    // CACHE HELPERS
    // ============================================================

    private function clearStudentCachesForCourse($courseId)
    {
        $course = Course::find($courseId);
        if ($course) {
            $studentIds = $course->students()->pluck('users.id')->toArray();
            foreach ($studentIds as $studentId) {
                Cache::forget('student_courses_'     . $studentId);
                Cache::forget('student_enrollments_' . $studentId);
                Cache::forget('student_progress_'    . $studentId . '_' . $courseId);
            }
        }
        Cache::forget('course_students_'    . $courseId);
        Cache::forget('course_enrollments_' . $courseId);
        Cache::forget('available_topics_'   . $courseId);
    }

    private function isEnrolled($course, $studentId): bool
    {
        return $course->enrollments()->where('student_id', $studentId)->exists();
    }

    private function isCourseFull($course): bool
    {
        if (!$course->max_students) {
            return false;
        }
        return $course->students()->count() >= $course->max_students;
    }

    public function clearCache()
    {
        $this->clearAdminCourseCaches();
        return redirect()->route('admin.courses.index')->with('success', 'Course caches cleared successfully.');
    }
}