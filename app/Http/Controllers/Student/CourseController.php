<?php
// app/Http/Controllers/Student/CourseController.php

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
     * Display enrolled courses (published only).
     */
    public function index()
    {
        $student   = Auth::user();
        $studentId = $student->id;

        $cacheKey = 'student_courses_index_' . $studentId . '_page_' . request('page', 1);

        $data = Cache::remember($cacheKey, 60, function () use ($student, $studentId) {

            // ── Only return published courses ──────────────────────────────
            $enrolledCourses = Enrollment::where('student_id', $studentId)
                ->whereHas('course', fn ($q) => $q->where('is_published', true))
                ->with([
                    'course.teacher' => fn ($q) => $q->select(['id', 'f_name', 'l_name', 'employee_id']),
                    // Only load published topics for count purposes
                    'course.topics'  => fn ($q) => $q->where('is_published', true)
                                                      ->select(['topics.id'])
                                                      ->orderBy('course_topics.order'),
                ])
                ->select(['id', 'course_id', 'student_id', 'enrolled_at', 'status', 'grade'])
                ->orderBy('enrolled_at', 'desc')
                ->paginate(12);

            $enrolledCourseIds = $enrolledCourses->pluck('course_id')->toArray();

            // ── Completed topics map (no course_id column on progress) ─────
            $completedTopicsMap = [];
            if (!empty($enrolledCourseIds)) {
                $completedTopicIds = Progress::where('student_id', $studentId)
                    ->where('status', 'completed')
                    ->pluck('topic_id')
                    ->toArray();

                if (!empty($completedTopicIds)) {
                    $topicCourses = DB::table('course_topics')
                        ->whereIn('topic_id', $completedTopicIds)
                        ->whereIn('course_id', $enrolledCourseIds)
                        ->select('course_id', 'topic_id')
                        ->get();

                    foreach ($topicCourses as $tc) {
                        $completedTopicsMap[$tc->course_id] = ($completedTopicsMap[$tc->course_id] ?? 0) + 1;
                    }
                }
            }

            // ── Calculate per-enrollment progress ─────────────────────────
            foreach ($enrolledCourses as $enrollment) {
                $course      = $enrollment->course;
                $courseId    = $course->id;
                $totalTopics = $course->topics->count(); // published topics only

                $completedTopics  = $completedTopicsMap[$courseId] ?? 0;
                $progressPct      = $totalTopics > 0
                    ? round(($completedTopics / $totalTopics) * 100)
                    : 0;
                $isCompleted      = $enrollment->grade !== null || $progressPct >= 100;

                $enrollment->progress_data = [
                    'total'        => $totalTopics,
                    'completed'    => $completedTopics,
                    'percentage'   => $progressPct,
                    'is_completed' => $isCompleted,
                ];
                $course->progress_data = $enrollment->progress_data;
            }

            $overallStats      = $this->getOverallStats($studentId, $enrolledCourseIds, $completedTopicsMap);
            $recentActivities  = $this->getRecentActivities($student);

            return compact('enrolledCourses', 'overallStats', 'recentActivities', 'enrolledCourseIds');
        });

        return view('student.courses.index', [
            'enrolledCourses'  => $data['enrolledCourses'],
            'overallStats'     => $data['overallStats'],
            'recentActivities' => $data['recentActivities'],
        ]);
    }

    /**
     * Show a single course's details and published topics.
     */
    public function show($encryptedId)
    {
        try {
            $courseId  = Crypt::decrypt($encryptedId);
            $studentId = Auth::id();

            // ── Verify enrollment ─────────────────────────────────────────
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();

            if (!$enrollment) {
                return redirect()->route('student.courses.index')
                    ->with('error', 'You are not enrolled in this course.');
            }

            // ── Course data (cached, shared across students) ──────────────
            $cacheKey = 'student_course_show_' . $courseId;
            $course   = Cache::remember($cacheKey, 600, function () use ($courseId) {
                return Course::where('is_published', true)   // must be published
                    ->with(['teacher',
                        'topics' => fn ($q) => $q->where('is_published', true) // published topics only
                                                  ->orderBy('course_topics.order')
                                                  ->select([
                                                      'topics.id', 'topics.title', 'topics.description',
                                                      'topics.content', 'topics.video_link',
                                                      'topics.attachment', 'topics.pdf_file',
                                                      'topics.is_published',
                                                  ]),
                    ])
                    ->withCount(['students',
                        'topics as topics_count'           => fn ($q) => $q->where('is_published', true),
                    ])
                    ->findOrFail($courseId);
            });

            $topics = $course->topics;

            // ── Fresh progress data ───────────────────────────────────────
            $completedTopicIds = Progress::where('student_id', $studentId)
                ->whereIn('topic_id', $topics->pluck('id'))
                ->where('status', 'completed')
                ->pluck('topic_id')
                ->toArray();

            $completedTopicsCount = count($completedTopicIds);
            $totalTopics          = $topics->count();
            $progressPercentage   = $totalTopics > 0
                ? round(($completedTopicsCount / $totalTopics) * 100)
                : 0;

            $courseProgress = [
                'total'      => $totalTopics,
                'completed'  => $completedTopicsCount,
                'remaining'  => $totalTopics - $completedTopicsCount,
                'percentage' => $progressPercentage,
            ];

            $nextTopic = $topics->first(fn ($t) => !in_array($t->id, $completedTopicIds));

            return view('student.courses.show', compact(
                'course', 'enrollment', 'topics',
                'completedTopicIds', 'courseProgress', 'nextTopic', 'encryptedId'
            ));

        } catch (\Exception $e) {
            \Log::error('Student CourseController@show error', [
                'encryptedId' => $encryptedId,
                'student_id'  => Auth::id(),
                'error'       => $e->getMessage(),
            ]);
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found or access denied.');
        }
    }

    /**
     * Overall statistics for a student (published courses/topics only).
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
                'total_courses'       => 0,
                'completed_courses'   => 0,
                'in_progress_courses' => 0,
                'total_topics'        => 0,
                'completed_topics'    => 0,
                'average_progress'    => 0,
                'average_grade'       => 0,
            ];
        }

        // Count published topics per course only
        $courses = Course::whereIn('id', $enrolledCourseIds)
            ->where('is_published', true)
            ->withCount(['topics as topics_count' => fn ($q) => $q->where('is_published', true)])
            ->select(['id'])
            ->get();

        if ($completedTopicsMap === null) {
            $completedTopicsMap  = [];
            $completedTopicIds   = Progress::where('student_id', $studentId)
                ->where('status', 'completed')
                ->pluck('topic_id')
                ->toArray();

            if (!empty($completedTopicIds)) {
                $topicCourses = DB::table('course_topics')
                    ->whereIn('topic_id', $completedTopicIds)
                    ->whereIn('course_id', $enrolledCourseIds)
                    ->select('course_id', 'topic_id')
                    ->get();

                foreach ($topicCourses as $tc) {
                    $completedTopicsMap[$tc->course_id] = ($completedTopicsMap[$tc->course_id] ?? 0) + 1;
                }
            }
        }

        $grades = Enrollment::where('student_id', $studentId)
            ->whereIn('course_id', $enrolledCourseIds)
            ->pluck('grade', 'course_id')
            ->toArray();

        $totalCourses     = $courses->count();
        $completedCourses = 0;
        $totalTopics      = 0;
        $completedTopics  = 0;
        $totalProgress    = 0;

        foreach ($courses as $course) {
            $courseId              = $course->id;
            $courseTotalTopics     = $course->topics_count;
            $courseCompletedTopics = $completedTopicsMap[$courseId] ?? 0;
            $courseGrade           = $grades[$courseId] ?? null;

            $totalTopics     += $courseTotalTopics;
            $completedTopics += $courseCompletedTopics;

            $courseProgress  = $courseTotalTopics > 0
                ? round(($courseCompletedTopics / $courseTotalTopics) * 100)
                : 0;
            $totalProgress  += $courseProgress;

            if ($courseGrade !== null || $courseProgress >= 100) {
                $completedCourses++;
            }
        }

        return [
            'total_courses'       => $totalCourses,
            'completed_courses'   => $completedCourses,
            'in_progress_courses' => $totalCourses - $completedCourses,
            'total_topics'        => $totalTopics,
            'completed_topics'    => $completedTopics,
            'average_progress'    => $totalCourses > 0 ? round($totalProgress / $totalCourses) : 0,
            'average_grade'       => !empty($grades) ? round(array_sum($grades) / count($grades), 1) : 0,
        ];
    }

    /**
     * Recent activities (topic completions + enrollments).
     */
    private function getRecentActivities($student)
    {
        $studentId  = $student->id;
        $activities = [];

        $recentCompletions = Progress::where('student_id', $studentId)
            ->with(['topic' => fn ($q) => $q->select(['id', 'title'])
                ->with(['courses' => fn ($q2) => $q2->select(['courses.id', 'courses.title'])])])
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
                        'type'  => 'topic',
                        'text'  => "Completed '{$completion->topic->title}' in {$course->title}",
                        'time'  => $completion->completed_at->diffForHumans(),
                        'icon'  => 'fas fa-check-circle',
                        'color' => 'success',
                    ];
                }
            }
        }

        $recentEnrollments = Enrollment::where('student_id', $studentId)
            ->with(['course' => fn ($q) => $q->select(['id', 'title'])])
            ->orderBy('enrolled_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentEnrollments as $enrollment) {
            if ($enrollment->course) {
                $exists = collect($activities)->contains(
                    fn ($a) => $a['type'] === 'enrollment' && str_contains($a['text'], $enrollment->course->title)
                );
                if (!$exists) {
                    $activities[] = [
                        'type'  => 'enrollment',
                        'text'  => "Enrolled in '{$enrollment->course->title}'",
                        'time'  => $enrollment->enrolled_at->diffForHumans(),
                        'icon'  => 'fas fa-user-plus',
                        'color' => 'primary',
                    ];
                }
            }
        }

        return array_slice($activities, 0, 5);
    }

    /**
     * Clear all student-related caches.
     */
    public function clearStudentCaches($studentId)
    {
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
        }
        Cache::forget('student_overall_stats_' . $studentId);
        Cache::forget('student_recent_activities_' . $studentId);
        Cache::forget('student_dashboard_' . $studentId);

        $enrolledCourses = Enrollment::where('student_id', $studentId)->pluck('course_id');
        foreach ($enrolledCourses as $courseId) {
            Cache::forget('student_course_show_' . $courseId);
            Cache::forget('student_course_progress_' . $studentId . '_' . $courseId);
        }
    }

    public function clearCache()
    {
        $this->clearStudentCaches(Auth::id());
        return redirect()->route('student.courses.index')
            ->with('success', 'Course caches cleared.');
    }
}