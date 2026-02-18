<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Traits\CacheManager;

class TopicController extends Controller
{
    use CacheManager;
    
    public function index()
    {
        // Cache key based on page and filters
        $cacheKey = 'admin_topics_index_page_' . request('page', 1);
        
        // Cache for 5 minutes (300 seconds)
        $data = Cache::remember($cacheKey, 300, function() {
            // Get topics with specific columns only
            $topics = Topic::select(['id', 'title', 'video_link', 'attachment', 'pdf_file', 'is_published', 'order', 'created_at', 'updated_at'])
                ->latest()
                ->paginate(10);
            
            // Get all statistics in ONE optimized query
            $stats = Topic::selectRaw('
                COUNT(*) as total_topics,
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_topics,
                SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as draft_topics,
                SUM(CASE WHEN video_link IS NOT NULL THEN 1 ELSE 0 END) as topics_with_video,
                SUM(CASE WHEN attachment IS NOT NULL THEN 1 ELSE 0 END) as topics_with_attachment,
                SUM(CASE WHEN learning_outcomes IS NOT NULL THEN 1 ELSE 0 END) as topics_with_learning_outcomes,
                SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as topics_this_month
            ', [now()->month, now()->year])
            ->first();
            
            return [
                'topics' => $topics,
                'publishedTopics' => $stats->published_topics ?? 0,
                'draftTopics' => $stats->draft_topics ?? 0,
                'topicsThisMonth' => $stats->topics_this_month ?? 0,
                'topicsWithVideo' => $stats->topics_with_video ?? 0,
                'topicsWithAttachment' => $stats->topics_with_attachment ?? 0,
                'topicsWithLearningOutcomes' => $stats->topics_with_learning_outcomes ?? 0,
                'totalTopics' => $stats->total_topics ?? 0
            ];
        });
        
        return view('admin.topics.index', $data);
    }

    public function create()
    {
        return view('admin.topics.create');
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

        // Handle PDF file upload
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pdfs', $fileName, 'public');
            $validated['pdf_file'] = '/storage/' . $filePath;
        }

        Topic::create($validated);
        
        // Clear all topic-related caches
        $this->clearAdminTopicCaches();
        
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            $cacheKey = 'admin_topic_show_' . $id;
            
            $topic = Cache::remember($cacheKey, 600, function() use ($id) {
                return Topic::select(['id', 'title', 'video_link', 'attachment', 'pdf_file', 'is_published', 'order', 'learning_outcomes', 'created_at', 'updated_at'])
                    ->findOrFail($id);
            });
            
            return view('admin.topics.show', compact('topic'));
            
        } catch (\Exception $e) {
            \Log::error('Error showing topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.topics.index')
                ->with('error', 'Topic not found or invalid link.');
        }
    }

    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            $cacheKey = 'admin_topic_edit_' . $id;
            
            $topic = Cache::remember($cacheKey, 300, function() use ($id) {
                return Topic::findOrFail($id);
            });
            
            return view('admin.topics.edit', compact('topic'));
            
        } catch (\Exception $e) {
            \Log::error('Error editing topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.topics.index')
                ->with('error', 'Topic not found or invalid link.');
        }
    }

    public function update(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $topic = Topic::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'video_link' => 'nullable|string|max:500',
                'attachment' => 'nullable|string|max:500',
                'is_published' => 'boolean',
                'learning_outcomes' => 'nullable|string|max:1000',
                'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            ]);

            // Handle PDF file upload
            if ($request->hasFile('pdf_file')) {
                // Delete old file if exists
                if ($topic->pdf_file && file_exists(public_path($topic->pdf_file))) {
                    unlink(public_path($topic->pdf_file));
                }
                
                $file = $request->file('pdf_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('pdfs', $fileName, 'public');
                $validated['pdf_file'] = '/storage/' . $filePath;
            } else {
                // Keep existing pdf_file if not uploading new one
                $validated['pdf_file'] = $topic->pdf_file;
            }

            $topic->update($validated);
            
            // Clear all topic-related caches
            $this->clearAdminTopicCaches();
            Cache::forget('admin_topic_show_' . $id);
            Cache::forget('admin_topic_edit_' . $id);
            
            // When topic is updated, clear student caches for all courses that use this topic
            $courses = $topic->courses;
            foreach ($courses as $course) {
                $this->clearStudentCachesForCourse($course->id);
            }
            
            return redirect()->route('admin.topics.show', $encryptedId)
                ->with('success', 'Topic updated successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error updating topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.topics.index')
                ->with('error', 'Failed to update topic.');
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $topic = Topic::findOrFail($id);
            
            // Get all courses that use this topic before deletion
            $courses = $topic->courses;
            
            // Delete PDF file if exists
            if ($topic->pdf_file && file_exists(public_path($topic->pdf_file))) {
                unlink(public_path($topic->pdf_file));
            }
            
            $topic->delete();
            
            // Clear all topic-related caches
            $this->clearAdminTopicCaches();
            Cache::forget('admin_topic_show_' . $id);
            Cache::forget('admin_topic_edit_' . $id);
            
            // Clear student caches for all affected courses
            foreach ($courses as $course) {
                $this->clearStudentCachesForCourse($course->id);
            }
            
            return redirect()->route('admin.topics.index')
                ->with('success', 'Topic deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting topic', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.topics.index')
                ->with('error', 'Topic not found or invalid link.');
        }
    }

    /**
     * Manual cache clearing endpoint
     */
    public function clearCache()
    {
        $this->clearAdminTopicCaches();
        
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic caches cleared successfully.');
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
}