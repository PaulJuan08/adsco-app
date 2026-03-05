<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['creator', 'updater'])
            ->latest()
            ->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'type'         => 'required|in:info,warning,success,danger',
            'end_date'     => 'nullable|date|after_or_equal:today',
            'is_published' => 'sometimes|boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['created_by']   = auth()->id();
        $validated['updated_by']   = auth()->id();

        Announcement::create($validated);

        return back()->with('success', 'Announcement created successfully.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'type'         => 'required|in:info,warning,success,danger',
            'end_date'     => 'nullable|date',
            'is_published' => 'sometimes|boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['updated_by']   = auth()->id();

        $announcement->update($validated);

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    public function togglePublish(Announcement $announcement)
    {
        $announcement->update([
            'is_published' => !$announcement->is_published,
            'updated_by'   => auth()->id(),
        ]);

        $status = $announcement->is_published ? 'published' : 'unpublished';

        return back()->with('success', "Announcement {$status}.");
    }
}
