<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\CacheManager;
use Illuminate\Support\Facades\Log;

class TopicController extends Controller
{
    use CacheManager;
    
    /**
     * Returns a base query scoped to topics the teacher has access to:
     * created by them, OR attached to a course they teach (primary or co-teacher).
     */
    private function teacherTopicQuery($teacherId)
    {
        return Topic::where(function ($q) use ($teacherId) {
            $q->where('created_by', $teacherId)
              ->orWhereHas('courses', function ($cq) use ($teacherId) {
                  $cq->where('teacher_id', $teacherId)
                     ->orWhereHas('teachers', function ($tq) use ($teacherId) {
                         $tq->where('users.id', $teacherId);
                     });
              });
        });
    }

    public function index()
    {
        $teacherId = Auth::id();

        $topics = $this->teacherTopicQuery($teacherId)
            ->with(['creator', 'updater', 'courses'])
            ->select(['id', 'title', 'video_link', 'attachment', 'pdf_file', 'is_published', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by'])
            ->latest()
            ->paginate(10);

        // Get courses related to this teacher for the filter dropdown
        $courses = Course::where(function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId)
                  ->orWhereHas('teachers', function ($tq) use ($teacherId) {
                      $tq->where('users.id', $teacherId);
                  });
            })
            ->select(['id', 'title', 'course_code'])
            ->orderBy('title')
            ->get();

        // Statistics scoped to teacher's topics
        $stats = $this->teacherTopicQuery($teacherId)->selectRaw('
            COUNT(*) as total_topics,
            SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_topics,
            SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as draft_topics,
            SUM(CASE WHEN video_link IS NOT NULL THEN 1 ELSE 0 END) as topics_with_video,
            SUM(CASE WHEN attachment IS NOT NULL THEN 1 ELSE 0 END) as topics_with_attachment,
            SUM(CASE WHEN learning_outcomes IS NOT NULL THEN 1 ELSE 0 END) as topics_with_learning_outcomes,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as topics_this_month
        ', [now()->month, now()->year])
        ->first();
        
        return view('teacher.topics.index', [
            'topics' => $topics,
            'courses' => $courses, // Pass courses to the view for the filter dropdown
            'publishedTopics' => $stats->published_topics ?? 0,
            'draftTopics' => $stats->draft_topics ?? 0,
            'topicsThisMonth' => $stats->topics_this_month ?? 0,
            'topicsWithVideo' => $stats->topics_with_video ?? 0,
            'topicsWithAttachment' => $stats->topics_with_attachment ?? 0,
            'topicsWithLearningOutcomes' => $stats->topics_with_learning_outcomes ?? 0,
            'totalTopics' => $stats->total_topics ?? 0
        ]);
    }

    public function create()
    {
        return view('teacher.topics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_link' => 'nullable|string|max:500',
            'attachment' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'learning_outcomes' => 'nullable|string|max:1000',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Add default values
        $validated['is_published'] = $validated['is_published'] ?? 1;
        $validated['order'] = Topic::max('order') + 1;
        $validated['created_by'] = Auth::id();

        // 🔥 SIMPLE PDF UPLOAD - Works on both local and live
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Store directly in public/pdf folder
            $file->move(public_path('pdf'), $fileName);
            
            // Save just the filename in database
            $validated['pdf_file'] = $fileName;
        }

        $topic = Topic::create($validated);
        
        // Clear all caches
        $this->clearTeacherTopicCaches(Auth::id());
        $this->clearAdminTopicCaches();
        $this->clearAdminDashboardCaches();
        $this->clearTeacherDashboardCaches(Auth::id());
        
        Log::info('New topic created by teacher - ID: ' . $topic->id . ', Title: ' . $topic->title);
        
        return redirect()->route('teacher.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();

            // Verify access
            $this->teacherTopicQuery($teacherId)->where('id', $id)->firstOrFail();

            $cacheKey = 'teacher_topic_show_' . $id;

            $topic = Cache::remember($cacheKey, 600, function() use ($id) {
                return Topic::with(['courses', 'creator', 'updater'])
                    ->select(['id', 'title', 'video_link', 'attachment', 'pdf_file', 'is_published', 'order', 'learning_outcomes', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by'])
                    ->findOrFail($id);
            });
            
            // Debug log to check PDF file
            Log::info('Teacher loading topic show page', [
                'topic_id' => $topic->id,
                'pdf_file' => $topic->pdf_file,
                'pdf_url' => self::getPdfUrl($topic->pdf_file)
            ]);
            
            // FIX: Pass both topic AND encryptedId to the view
            return view('teacher.topics.show', compact('topic', 'encryptedId'));
            
        } catch (\Exception $e) {
            Log::error('Teacher error showing topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.topics.index')
                ->with('error', 'Topic not found or invalid link.');
        }
    }

    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();

            // Verify access
            $this->teacherTopicQuery($teacherId)->where('id', $id)->firstOrFail();

            $cacheKey = 'teacher_topic_edit_' . $id;

            $topic = Cache::remember($cacheKey, 300, function() use ($id) {
                return Topic::findOrFail($id);
            });
            
            return view('teacher.topics.edit', compact('topic'));
            
        } catch (\Exception $e) {
            Log::error('Teacher error editing topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.topics.index')
                ->with('error', 'Topic not found or invalid link.');
        }
    }

    public function update(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $topic = $this->teacherTopicQuery(Auth::id())->where('id', $id)->firstOrFail();

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'video_link' => 'nullable|string|max:500',
                'attachment' => 'nullable|string|max:500',
                'is_published' => 'boolean',
                'learning_outcomes' => 'nullable|string|max:1000',
                'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            ]);

            // 🔥 SIMPLE PDF UPDATE - Works on both local and live
            if ($request->hasFile('pdf_file')) {
                // Delete old file if exists
                if ($topic->pdf_file && file_exists(public_path('pdf/' . $topic->pdf_file))) {
                    unlink(public_path('pdf/' . $topic->pdf_file));
                }
                
                $file = $request->file('pdf_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Store directly in public/pdf folder
                $file->move(public_path('pdf'), $fileName);
                
                // Save just the filename
                $validated['pdf_file'] = $fileName;
            } else {
                // Keep existing pdf_file if not uploading new one
                $validated['pdf_file'] = $topic->pdf_file;
            }

            $validated['updated_by'] = Auth::id();
            $topic->update($validated);

            // Clear caches
            $this->clearTeacherTopicCaches(Auth::id());
            Cache::forget('teacher_topic_show_' . $id);
            Cache::forget('teacher_topic_edit_' . $id);
            
            $courses = $topic->courses;
            foreach ($courses as $course) {
                $this->clearStudentCachesForCourse($course->id);
            }
            
            return redirect()->route('teacher.topics.show', $encryptedId)
                ->with('success', 'Topic updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Teacher error updating topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.topics.index')
                ->with('error', 'Failed to update topic.');
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $topic = $this->teacherTopicQuery(Auth::id())->where('id', $id)->firstOrFail();
            
            $courses = $topic->courses;
            
            // 🔥 SIMPLE PDF DELETION
            if ($topic->pdf_file && file_exists(public_path('pdf/' . $topic->pdf_file))) {
                unlink(public_path('pdf/' . $topic->pdf_file));
            }
            
            $topic->delete();
            
            // Clear caches
            $this->clearTeacherTopicCaches(Auth::id());
            Cache::forget('teacher_topic_show_' . $id);
            Cache::forget('teacher_topic_edit_' . $id);
            
            foreach ($courses as $course) {
                $this->clearStudentCachesForCourse($course->id);
            }
            
            return redirect()->route('teacher.topics.index')
                ->with('success', 'Topic deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Teacher error deleting topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.topics.index')
                ->with('error', 'Topic not found or invalid link.');
        }
    }

    /**
     * Manual cache clearing endpoint for teachers
     */
    public function clearCache()
    {
        $teacherId = Auth::id();
        $this->clearTeacherTopicCaches($teacherId);
        $this->clearAdminDashboardCaches();
        $this->clearTeacherDashboardCaches($teacherId);
        
        return redirect()->route('teacher.topics.index')
            ->with('success', 'Topic caches cleared successfully.');
    }
    
    /**
     * 🔥 HELPER METHOD - Get PDF URL (handles both old and new formats)
     */
    public static function getPdfUrl($pdfFile)
    {
        if (empty($pdfFile)) {
            return null;
        }
        
        // If it's already a full URL
        if (filter_var($pdfFile, FILTER_VALIDATE_URL)) {
            return $pdfFile;
        }
        
        // Extract just the filename
        if (str_contains($pdfFile, '/')) {
            $filename = basename($pdfFile);
        } else {
            $filename = $pdfFile;
        }
        
        // Clean filename - remove any special characters that might cause issues
        $filename = preg_replace('/[^a-zA-Z0-9_\-\s\.\(\)]/', '', $filename);
        
        // Check which folder the file exists in
        $possiblePaths = [
            'pdf' => public_path('pdf/' . $filename),
            'pdfs' => public_path('pdfs/' . $filename),
            'storage/pdf' => public_path('storage/pdf/' . $filename),
            'storage/pdfs' => public_path('storage/pdfs/' . $filename),
        ];
        
        $foundFolder = null;
        foreach ($possiblePaths as $folder => $path) {
            if (file_exists($path)) {
                $foundFolder = $folder;
                Log::info('PDF file found in ' . $folder . ' folder: ' . $filename);
                break;
            }
        }
        
        if (!$foundFolder) {
            Log::warning('PDF file not found in any location: ' . $filename);
            // Default to pdf folder
            $foundFolder = 'pdf';
        }
        
        // Return secure route with encrypted filename
        try {
            return route('pdf.view', ['encryptedFilename' => Crypt::encrypt($filename)]);
        } catch (\Exception $e) {
            Log::error('PDF URL encryption failed: ' . $e->getMessage());
            // Fallback to direct URL - use the correct folder
            return asset($foundFolder . '/' . $filename);
        }
    }
    
    /**
     * Get file type icon based on URL
     */
    public static function getFileIcon($url)
    {
        if (empty($url)) return 'fas fa-file';
        
        $url = strtolower($url);
        
        if (Str::contains($url, ['.pdf', 'pdf?', 'pdf#'])) {
            return 'fas fa-file-pdf';
        } elseif (Str::contains($url, ['.doc', '.docx'])) {
            return 'fas fa-file-word';
        } elseif (Str::contains($url, ['.xls', '.xlsx', '.csv'])) {
            return 'fas fa-file-excel';
        } elseif (Str::contains($url, ['.ppt', '.pptx'])) {
            return 'fas fa-file-powerpoint';
        } elseif (Str::contains($url, ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'])) {
            return 'fas fa-file-image';
        } elseif (Str::contains($url, ['.zip', '.rar', '.tar', '.gz'])) {
            return 'fas fa-file-archive';
        } elseif (Str::contains($url, '.txt')) {
            return 'fas fa-file-alt';
        } elseif (Str::contains($url, '.mp4') || Str::contains($url, '.avi') || Str::contains($url, '.mov')) {
            return 'fas fa-file-video';
        } elseif (Str::contains($url, '.mp3') || Str::contains($url, '.wav')) {
            return 'fas fa-file-audio';
        }
        
        return 'fas fa-file';
    }
    
    /**
     * Get file type color
     */
    public static function getFileColor($url)
    {
        if (empty($url)) return '#6b7280';
        
        $url = strtolower($url);
        
        if (Str::contains($url, ['.pdf', 'pdf?', 'pdf#'])) {
            return '#dc2626'; // Red for PDF
        } elseif (Str::contains($url, ['.doc', '.docx'])) {
            return '#2563eb'; // Blue for Word
        } elseif (Str::contains($url, ['.xls', '.xlsx', '.csv'])) {
            return '#059669'; // Green for Excel
        } elseif (Str::contains($url, ['.ppt', '.pptx'])) {
            return '#d97706'; // Amber for PowerPoint
        } elseif (Str::contains($url, ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'])) {
            return '#7c3aed'; // Purple for images
        } elseif (Str::contains($url, ['.zip', '.rar', '.tar', '.gz'])) {
            return '#92400e'; // Brown for archives
        }
        
        return '#6b7280'; // Gray for others
    }
    
    /**
     * Get file type name
     */
    public static function getFileType($url)
    {
        if (empty($url)) return 'File';
        
        $url = strtolower($url);
        
        if (Str::contains($url, ['.pdf', 'pdf?', 'pdf#'])) {
            return 'PDF Document';
        } elseif (Str::contains($url, ['.doc', '.docx'])) {
            return 'Word Document';
        } elseif (Str::contains($url, ['.xls', '.xlsx', '.csv'])) {
            return 'Excel Spreadsheet';
        } elseif (Str::contains($url, ['.ppt', '.pptx'])) {
            return 'PowerPoint Presentation';
        } elseif (Str::contains($url, ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg'])) {
            return 'Image File';
        } elseif (Str::contains($url, ['.zip', '.rar', '.tar', '.gz'])) {
            return 'Archive File';
        } elseif (Str::contains($url, '.txt')) {
            return 'Text File';
        }
        
        return 'File';
    }

    /**
     * Publish or unpublish a topic
     * 
     * @param string $encryptedId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish($encryptedId)
    {
        Log::info('=== TOPIC PUBLISH REQUEST STARTED ===', [
            'encryptedId' => $encryptedId,
            'method' => request()->method(),
            'ajax' => request()->ajax(),
            'wantsJson' => request()->wantsJson(),
            'url' => request()->fullUrl(),
        ]);

        try {
            $id = Crypt::decrypt($encryptedId);
            $topic = $this->teacherTopicQuery(Auth::id())->where('id', $id)->firstOrFail();

            // Toggle publish status
            $oldStatus = $topic->is_published;
            // AFTER (correct)
            $newStatus = !$topic->is_published; // calculate BEFORE update
            $topic->update(['is_published' => $newStatus]);
            $status = $newStatus ? 'published' : 'unpublished';
            
            // Clear ALL related caches
            $this->clearTeacherTopicCaches(Auth::id());
            $this->clearAdminTopicCaches();
            Cache::forget('teacher_topic_show_' . $id);
            Cache::forget('admin_topic_show_' . $id);

            // CRITICAL: Clear the course-level cache for every course this topic belongs to
            $courses = $topic->courses;
            foreach ($courses as $course) {
                Cache::forget('teacher_course_show_' . $course->id . '_teacher_' . Auth::id());
                Cache::forget('teacher_available_topics_' . $course->id);
                $this->clearStudentCachesForCourse($course->id);
            }
            
            Log::info('Topic status toggled', [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'courses_cache_cleared' => $courses->pluck('id')->toArray(),
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Topic {$status} successfully!",
                    'topic' => [
                        'id' => $topic->id,
                        'is_published' => $newStatus,
                    ]
                ]);
            }
            
            return redirect()->route('teacher.topics.show', $encryptedId)
                ->with('success', "Topic {$status} successfully!");
                
        } catch (\Exception $e) {
            Log::error('Teacher error publishing topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage(),
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update topic status. ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('teacher.topics.index')
                ->with('error', 'Failed to update topic status.');
        }
    }
}