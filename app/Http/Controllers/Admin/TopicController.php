<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TopicController extends Controller
{
    public function index()
    {
        // Remove ->with('course') since we don't have course relationship
        $topics = Topic::latest()->paginate(10);
        
        // Calculate statistics (remove course-related stats)
        $publishedTopics = Topic::where('is_published', 1)->count();
        $draftTopics = Topic::where('is_published', 0)->count();
        $topicsThisMonth = Topic::whereMonth('created_at', now()->month)->count();
        
        // Count topics with video links
        $topicsWithVideo = Topic::whereNotNull('video_link')->count();

        return view('admin.topics.index', compact(
            'topics',
            'publishedTopics',
            'draftTopics',
            'topicsThisMonth',
            'topicsWithVideo'
        ));
    }

    public function create()
    {
        // No need for courses anymore
        return view('admin.topics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'video_link' => 'nullable|url|max:500',
        ]);

        // Add default values for missing fields
        $validated['is_published'] = 1; // Default to published
        $validated['order'] = Topic::max('order') + 1; // Auto-increment order

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
            'content' => 'required|string',
            'video_link' => 'nullable|url|max:500',
            'is_published' => 'boolean', // Add this if you want publish toggle
        ]);

        $topic->update($validated);
        
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $topic = Topic::findOrFail($id);
        $topic->delete();
        
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully.');
    }
}