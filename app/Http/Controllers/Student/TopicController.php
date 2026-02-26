<?php
// app/Http/Controllers/Student/TopicController.php

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
     * Display all published topics for enrolled (published) courses.
     */
    public function index()
    {
        $student   = Auth::user();
        $studentId = $student->id;

        $cacheKey = 'student_topics_index_' . $studentId . '_page_' . request('page', 1);

        $data = Cache::remember($cacheKey, 60, function () use ($studentId) {
            try {
                // Only active enrollments in published courses
                $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                    ->where('status', 'active')
                    ->whereHas('course', fn ($q) => $q->where('is_published', true))
                    ->pluck('course_id')
                    ->toArray();

                // Only published topics from those courses
                $allTopics = Topic::where('is_published', true)
                    ->whereHas('courses', fn ($q) => $q->whereIn('courses.id', $enrolledCourseIds))
                    ->with(['courses.teacher' => fn ($q) => $q->select(['id', 'f_name', 'l_name'])])
                    ->select(['id', 'title', 'content', 'video_link', 'attachment', 'pdf_file', 'created_at', 'is_published'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);

                $completedTopicIds = Progress::where('student_id', $studentId)
                    ->where('status', 'completed')
                    ->pluck('topic_id')
                    ->toArray();

                $totalTopics     = $allTopics->total();
                $completedTopics = count(array_intersect($allTopics->pluck('id')->toArray(), $completedTopicIds));

                $courses = Course::whereIn('id', $enrolledCourseIds)
                    ->where('is_published', true)
                    ->withCount(['topics as topics_count' => fn ($q) => $q->where('is_published', true)])
                    ->select(['id', 'title', 'course_code'])
                    ->get();

                $topicsWithVideo = Topic::where('is_published', true)
                    ->whereHas('courses', fn ($q) => $q->whereIn('courses.id', $enrolledCourseIds))
                    ->whereNotNull('video_link')
                    ->count();

                $recentlyCompleted = Progress::where('student_id', $studentId)
                    ->where('status', 'completed')
                    ->with(['topic' => fn ($q) => $q->select(['id', 'title'])
                        ->with(['courses' => fn ($q2) => $q2->select(['courses.id', 'courses.title', 'courses.course_code'])])])
                    ->orderBy('completed_at', 'desc')
                    ->take(5)
                    ->get();

                return compact(
                    'allTopics', 'completedTopicIds', 'totalTopics', 'completedTopics',
                    'courses', 'topicsWithVideo', 'recentlyCompleted', 'enrolledCourseIds'
                );

            } catch (\Exception $e) {
                \Log::error('TopicController@index: ' . $e->getMessage());
                return [
                    'allTopics'         => collect([]),
                    'completedTopicIds' => [],
                    'totalTopics'       => 0,
                    'completedTopics'   => 0,
                    'courses'           => collect([]),
                    'topicsWithVideo'   => 0,
                    'recentlyCompleted' => collect([]),
                    'enrolledCourseIds' => [],
                ];
            }
        });

        return view('student.topics.index', $data);
    }

    /**
     * Show a single published topic.
     */
    public function show($encryptedId)
    {
        try {
            $topicId   = Crypt::decrypt($encryptedId);
            $student   = Auth::user();
            $studentId = $student->id;

            // Cache topic details (no progress — that's always fresh)
            $cacheKey = 'student_topic_show_' . $topicId;
            $topicData = Cache::remember($cacheKey, 300, function () use ($topicId) {
                $topic = Topic::where('is_published', true)  // published only
                    ->with(['courses.teacher' => fn ($q) => $q->select(['id', 'f_name', 'l_name'])])
                    ->select(['id', 'title', 'content', 'video_link', 'attachment', 'pdf_file',
                              'created_at', 'updated_at', 'is_published'])
                    ->findOrFail($topicId);

                $course = $topic->courses->first();
                return compact('topic', 'course');
            });

            $topic  = $topicData['topic'];
            $course = $topicData['course'];

            if (!$course) {
                return redirect()->route('student.topics.index')
                    ->with('error', 'Topic is not associated with any course.');
            }

            // Verify student is enrolled in this course
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return redirect()->route('student.topics.index')
                    ->with('error', 'You are not enrolled in the course for this topic.');
            }

            // Fresh progress — never cached
            $progress        = Progress::where('student_id', $studentId)
                ->where('topic_id', $topicId)
                ->select(['status', 'completed_at', 'notes'])
                ->first();
            $isCompleted     = $progress && $progress->status === 'completed';
            $completionDate  = $progress?->completed_at;
            $notes           = $progress?->notes;

            // Stats (cached separately — light)
            $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->pluck('course_id')
                ->toArray();

            $totalTopics = Topic::where('is_published', true)
                ->whereHas('courses', fn ($q) => $q->whereIn('courses.id', $enrolledCourseIds))
                ->count();

            $completedTopics = Progress::where('student_id', $studentId)
                ->where('status', 'completed')
                ->count();

            // Pre-build encrypted course ID so the blade doesn't need to call Crypt inline
            $encryptedCourseId = Crypt::encrypt($course->id);

            return view('student.topics.show', compact(
                'topic', 'course', 'isCompleted', 'completionDate', 'notes',
                'totalTopics', 'completedTopics', 'enrolledCourseIds',
                'encryptedId', 'encryptedCourseId'
            ));

        } catch (\Exception $e) {
            \Log::error('TopicController@show: ' . $e->getMessage(), [
                'encryptedId' => $encryptedId,
                'student_id'  => Auth::id(),
            ]);
            return redirect()->route('student.topics.index')
                ->with('error', 'Topic not found.');
        }
    }

    /**
     * Mark topic as complete (AJAX).
     */
    public function markComplete(Request $request, $encryptedId)
    {
        try {
            $topicId   = Crypt::decrypt($encryptedId);
            $studentId = Auth::id();

            $topic    = Topic::with('courses')->findOrFail($topicId);
            $courseId = optional($topic->courses->first())->id;

            Progress::updateOrCreate(
                ['student_id' => $studentId, 'topic_id' => $topicId],
                ['status' => 'completed', 'completed_at' => now(), 'notes' => $request->input('notes', '')]
            );

            $this->clearTopicCaches($studentId, $topicId, $courseId);

            $completedTopics = Progress::where('student_id', $studentId)->where('status', 'completed')->count();
            $totalTopics     = $this->getTotalTopicsCount($studentId);
            $progressPct     = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Topic marked as completed!',
                'stats'   => compact('completedTopics', 'totalTopics') + ['progressPercentage' => $progressPct],
            ]);

        } catch (\Exception $e) {
            \Log::error('TopicController@markComplete: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to mark topic as complete.'], 500);
        }
    }

    /**
     * Mark topic as incomplete (AJAX).
     */
    public function markIncomplete(Request $request, $encryptedId)
    {
        try {
            $topicId   = Crypt::decrypt($encryptedId);
            $studentId = Auth::id();

            $topic    = Topic::with('courses')->findOrFail($topicId);
            $courseId = optional($topic->courses->first())->id;

            Progress::where('student_id', $studentId)
                ->where('topic_id', $topicId)
                ->update(['status' => 'incomplete', 'completed_at' => null]);

            $this->clearTopicCaches($studentId, $topicId, $courseId);

            $completedTopics = Progress::where('student_id', $studentId)->where('status', 'completed')->count();
            $totalTopics     = $this->getTotalTopicsCount($studentId);
            $progressPct     = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Topic marked as incomplete.',
                'stats'   => compact('completedTopics', 'totalTopics') + ['progressPercentage' => $progressPct],
            ]);

        } catch (\Exception $e) {
            \Log::error('TopicController@markIncomplete: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to mark topic as incomplete.'], 500);
        }
    }

    /**
     * Save notes for a topic (AJAX).
     */
    public function saveNotes(Request $request, $encryptedId)
    {
        try {
            $topicId   = Crypt::decrypt($encryptedId);
            $studentId = Auth::id();

            $progress = Progress::where('student_id', $studentId)->where('topic_id', $topicId)->first();

            Progress::updateOrCreate(
                ['student_id' => $studentId, 'topic_id' => $topicId],
                [
                    'notes'        => $request->input('notes'),
                    'status'       => $progress->status ?? 'in_progress',
                    'completed_at' => $progress->completed_at ?? null,
                ]
            );

            // Clear only the show cache (notes are not cached at index level)
            Cache::forget('student_topic_show_' . $topicId);

            return response()->json(['success' => true, 'message' => 'Notes saved successfully.']);

        } catch (\Exception $e) {
            \Log::error('TopicController@saveNotes: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save notes.'], 500);
        }
    }

    /**
     * Get the PDF public URL — handles old /storage/pdfs/ paths and bare filenames.
     */
    public static function getPdfUrl($pdfFile)
    {
        if (empty($pdfFile)) return null;
        if (str_contains($pdfFile, '/storage/')) return asset('pdf/' . basename($pdfFile));
        if (!str_contains($pdfFile, '/'))         return asset('pdf/' . $pdfFile);
        return asset($pdfFile);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    private function getTotalTopicsCount($studentId)
    {
        return Cache::remember('student_total_topics_' . $studentId, 60, function () use ($studentId) {
            $ids = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->pluck('course_id')
                ->toArray();

            if (empty($ids)) return 0;

            return Topic::where('is_published', true)
                ->whereHas('courses', fn ($q) => $q->whereIn('courses.id', $ids))
                ->count();
        });
    }

    private function clearTopicCaches($studentId, $topicId = null, $courseId = null)
    {
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('student_topics_index_' . $studentId . '_page_' . $page);
            Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
        }

        Cache::forget('student_total_topics_' . $studentId);
        Cache::forget('student_overall_stats_' . $studentId);
        Cache::forget('student_recent_activities_' . $studentId);
        Cache::forget('student_dashboard_' . $studentId);

        if ($topicId) {
            Cache::forget('student_topic_show_' . $topicId);
        }

        if ($courseId) {
            Cache::forget('student_course_show_' . $courseId);
            Cache::forget('student_course_progress_' . $studentId . '_' . $courseId);
        } else {
            $ids = Enrollment::where('student_id', $studentId)->where('status', 'active')->pluck('course_id');
            foreach ($ids as $cid) {
                Cache::forget('student_course_show_' . $cid);
                Cache::forget('student_course_progress_' . $studentId . '_' . $cid);
            }
        }
    }

    public function clearCache()
    {
        $this->clearTopicCaches(Auth::id());
        return redirect()->route('student.topics.index')->with('success', 'Topic caches cleared.');
    }
}