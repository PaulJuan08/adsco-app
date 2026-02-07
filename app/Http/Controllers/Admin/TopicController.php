<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::latest()->paginate(10);
        
        $publishedTopics = Topic::where('is_published', 1)->count();
        $draftTopics = Topic::where('is_published', 0)->count();
        $topicsThisMonth = Topic::whereMonth('created_at', now()->month)->count();
        $topicsWithVideo = Topic::whereNotNull('video_link')->count();
        $topicsWithAttachment = Topic::whereNotNull('attachment')->count();
        $topicsWithLearningOutcomes = Topic::whereNotNull('learning_outcomes')->count();

        return view('admin.topics.index', compact(
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
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max, PDF only
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
        
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);
        return view('admin.topics.show', compact('topic'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);
        return view('admin.topics.edit', compact('topic'));
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
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max, PDF only
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
        
        return redirect()->route('admin.topics.show', $encryptedId)
            ->with('success', 'Topic updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);
        
        // Delete PDF file if exists
        if ($topic->pdf_file && file_exists(public_path($topic->pdf_file))) {
            unlink(public_path($topic->pdf_file));
        }
        
        $topic->delete();
        
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully.');
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