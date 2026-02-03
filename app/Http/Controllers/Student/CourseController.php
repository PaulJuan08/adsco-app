<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display available courses and enrolled courses
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get enrolled courses
        $enrolledCourses = $user->enrollments()
            ->with(['course.teacher', 'course.topics'])
            ->get()
            ->pluck('course');
        
        // Get available courses (not enrolled yet)
        $availableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourses->pluck('id')->toArray())
            ->with('teacher')
            ->orderBy('title')
            ->get();
        
        // Calculate statistics
        $enrolledCount = $enrolledCourses->count();
        $completedCount = $enrolledCourses->whereNotNull('grade')->count();
        $averageGrade = $enrolledCourses->whereNotNull('grade')->avg('grade') ?? 0;
        
        return view('student.courses.index', compact(
            'availableCourses',
            'enrolledCourses',
            'enrolledCount',
            'completedCount',
            'averageGrade'
        ));
    }

    /**
     * Enroll in a course
     */
    public function enroll(Request $request, $courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $user = Auth::user();
            
            // Check if already enrolled
            if ($user->enrollments()->where('course_id', $courseId)->exists()) {
                return redirect()->back()
                    ->with('error', 'You are already enrolled in this course.');
            }
            
            // Check if course is published
            if (!$course->is_published) {
                return redirect()->back()
                    ->with('error', 'This course is not available for enrollment.');
            }
            
            // Enroll the student
            DB::beginTransaction();
            
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);
            
            // Add student to course (pivot table)
            $course->students()->attach($user->id);
            
            DB::commit();
            
            return redirect()->route('student.courses.show', Crypt::encrypt($courseId))
                ->with('success', 'Successfully enrolled in ' . $course->title);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to enroll: ' . $e->getMessage());
        }
    }

    /**
     * View a specific course
     */
    public function show($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $user = Auth::user();
            
            $course = Course::with(['teacher', 'topics' => function($query) {
                $query->where('is_published', true)
                      ->orderBy('order');
            }])->findOrFail($courseId);
            
            // Check if student is enrolled
            $isEnrolled = $user->enrollments()
                ->where('course_id', $courseId)
                ->exists();
            
            if (!$isEnrolled) {
                return redirect()->route('student.courses.index')
                    ->with('error', 'You need to enroll in this course first.');
            }
            
            // Get student's enrollment info
            $enrollment = $user->enrollments()
                ->where('course_id', $courseId)
                ->first();
            
            return view('student.courses.show', compact(
                'course',
                'enrollment',
                'isEnrolled'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found.');
        }
    }

    /**
     * View course topics
     */
    public function topics($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $user = Auth::user();
            
            $course = Course::with(['topics' => function($query) {
                $query->where('is_published', true)
                      ->orderBy('order');
            }])->findOrFail($courseId);
            
            // Check if student is enrolled
            $isEnrolled = $user->enrollments()
                ->where('course_id', $courseId)
                ->exists();
            
            if (!$isEnrolled) {
                return redirect()->route('student.courses.index')
                    ->with('error', 'You need to enroll in this course first.');
            }
            
            return view('student.courses.topics', compact('course'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found.');
        }
    }

    /**
     * View a specific topic
     */
    public function showTopic($encryptedCourseId, $encryptedTopicId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedCourseId);
            $topicId = Crypt::decrypt($encryptedTopicId);
            $user = Auth::user();
            
            // Check enrollment
            $isEnrolled = $user->enrollments()
                ->where('course_id', $courseId)
                ->exists();
            
            if (!$isEnrolled) {
                return redirect()->route('student.courses.index')
                    ->with('error', 'You need to enroll in this course first.');
            }
            
            $course = Course::findOrFail($courseId);
            $topic = Topic::where('id', $topicId)
                ->where('is_published', true)
                ->firstOrFail();
            
            // Check if topic belongs to course
            if (!$course->topics()->where('topics.id', $topicId)->exists()) {
                abort(404, 'Topic not found in this course');
            }
            
            // Get next and previous topics
            $allTopics = $course->topics()
                ->where('is_published', true)
                ->orderBy('order')
                ->get();
            
            $currentIndex = $allTopics->search(function($item) use ($topicId) {
                return $item->id == $topicId;
            });
            
            $previousTopic = $currentIndex > 0 ? $allTopics[$currentIndex - 1] : null;
            $nextTopic = $currentIndex < ($allTopics->count() - 1) ? $allTopics[$currentIndex + 1] : null;
            
            return view('student.courses.topic-show', compact(
                'course',
                'topic',
                'previousTopic',
                'nextTopic'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Topic not found.');
        }
    }

    /**
     * Get course materials
     */
    public function materials($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $user = Auth::user();
            
            // Check enrollment
            $isEnrolled = $user->enrollments()
                ->where('course_id', $courseId)
                ->exists();
            
            if (!$isEnrolled) {
                return redirect()->route('student.courses.index')
                    ->with('error', 'You need to enroll in this course first.');
            }
            
            $course = Course::with(['topics' => function($query) {
                $query->where('is_published', true)
                      ->orderBy('order');
            }])->findOrFail($courseId);
            
            // Get all materials (attachments) from topics
            $materials = [];
            foreach ($course->topics as $topic) {
                if ($topic->attachment) {
                    $materials[] = [
                        'topic' => $topic,
                        'attachment' => $topic->attachment,
                        'type' => $this->getFileType($topic->attachment),
                        'icon' => $this->getFileIcon($topic->attachment),
                        'color' => $this->getFileColor($topic->attachment),
                    ];
                }
            }
            
            return view('student.courses.materials', compact('course', 'materials'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found.');
        }
    }

    /**
     * View course grades
     */
    public function grades($encryptedId)
    {
        try {
            $courseId = Crypt::decrypt($encryptedId);
            $user = Auth::user();
            
            $enrollment = $user->enrollments()
                ->where('course_id', $courseId)
                ->firstOrFail();
            
            $course = Course::with(['teacher'])->findOrFail($courseId);
            
            // Get quiz attempts for this course
            $quizAttempts = $user->quizAttempts()
                ->whereHas('quiz', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->with('quiz')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('student.courses.grades', compact(
                'course',
                'enrollment',
                'quizAttempts'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found.');
        }
    }

    /**
     * Helper methods for file type detection
     */
    private function getFileIcon($url)
    {
        if (empty($url)) return 'fas fa-file';
        
        $url = strtolower($url);
        
        if (str_contains($url, ['.pdf', 'pdf?', 'pdf#'])) {
            return 'fas fa-file-pdf';
        } elseif (str_contains($url, ['.doc', '.docx'])) {
            return 'fas fa-file-word';
        } elseif (str_contains($url, ['.xls', '.xlsx', '.csv'])) {
            return 'fas fa-file-excel';
        } elseif (str_contains($url, ['.ppt', '.pptx'])) {
            return 'fas fa-file-powerpoint';
        } elseif (str_contains($url, ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'])) {
            return 'fas fa-file-image';
        } elseif (str_contains($url, ['.zip', '.rar', '.tar', '.gz'])) {
            return 'fas fa-file-archive';
        } elseif (str_contains($url, '.txt')) {
            return 'fas fa-file-alt';
        } elseif (str_contains($url, '.mp4') || str_contains($url, '.avi') || str_contains($url, '.mov')) {
            return 'fas fa-file-video';
        } elseif (str_contains($url, '.mp3') || str_contains($url, '.wav')) {
            return 'fas fa-file-audio';
        }
        
        return 'fas fa-file';
    }
    
    private function getFileColor($url)
    {
        if (empty($url)) return '#6b7280';
        
        $url = strtolower($url);
        
        if (str_contains($url, ['.pdf', 'pdf?', 'pdf#'])) {
            return '#dc2626';
        } elseif (str_contains($url, ['.doc', '.docx'])) {
            return '#2563eb';
        } elseif (str_contains($url, ['.xls', '.xlsx', '.csv'])) {
            return '#059669';
        } elseif (str_contains($url, ['.ppt', '.pptx'])) {
            return '#d97706';
        } elseif (str_contains($url, ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'])) {
            return '#7c3aed';
        } elseif (str_contains($url, ['.zip', '.rar', '.tar', '.gz'])) {
            return '#92400e';
        }
        
        return '#6b7280';
    }
    
    private function getFileType($url)
    {
        if (empty($url)) return 'File';
        
        $url = strtolower($url);
        
        if (str_contains($url, ['.pdf', 'pdf?', 'pdf#'])) {
            return 'PDF Document';
        } elseif (str_contains($url, ['.doc', '.docx'])) {
            return 'Word Document';
        } elseif (str_contains($url, ['.xls', '.xlsx', '.csv'])) {
            return 'Excel Spreadsheet';
        } elseif (str_contains($url, ['.ppt', '.pptx'])) {
            return 'PowerPoint Presentation';
        } elseif (str_contains($url, ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'])) {
            return 'Image File';
        } elseif (str_contains($url, ['.zip', '.rar', '.tar', '.gz'])) {
            return 'Archive File';
        } elseif (str_contains($url, '.txt')) {
            return 'Text File';
        }
        
        return 'File';
    }
}