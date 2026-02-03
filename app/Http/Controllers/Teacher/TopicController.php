<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    public function index()
    {
        // Teachers can see all topics (including those created by others)
        $topics = Topic::latest()->paginate(10);
        
        // Statistics for teacher dashboard
        $publishedTopics = Topic::where('is_published', 1)->count();
        $draftTopics = Topic::where('is_published', 0)->count();
        $topicsThisMonth = Topic::whereMonth('created_at', now()->month)->count();
        $topicsWithVideo = Topic::whereNotNull('video_link')->count();
        $topicsWithAttachment = Topic::whereNotNull('attachment')->count();
        $topicsWithLearningOutcomes = Topic::whereNotNull('learning_outcomes')->count();

        return view('teacher.topics.index', compact(
            'topics',
            'publishedTopics',
            'draftTopics',
            'topicsThisMonth',
            'topicsWithVideo',
            'topicsWithAttachment',
            'topicsWithLearningOutcomes'
        ));
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
        ]);

        // Add default values
        $validated['is_published'] = $validated['is_published'] ?? 1;
        $validated['order'] = Topic::max('order') + 1;

        Topic::create($validated);
        
        return redirect()->route('teacher.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);
        return view('teacher.topics.show', compact('topic'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);
        return view('teacher.topics.edit', compact('topic'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_link' => 'nullable|string|max:500',
            'attachment' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'learning_outcomes' => 'nullable|string|max:1000',
        ]);

        $topic->update($validated);
        
        return redirect()->route('teacher.topics.show', $encryptedId)
            ->with('success', 'Topic updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);
        $topic->delete();
        
        return redirect()->route('teacher.topics.index')
            ->with('success', 'Topic deleted successfully.');
    }
    
    /**
     * Get file type icon based on URL (Static method)
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
     * Get file type color (Static method)
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
     * Get file type name (Static method)
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