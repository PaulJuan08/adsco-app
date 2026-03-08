<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\Request;

class LegalPageController extends Controller
{
    public function index()
    {
        // Ensure all three types exist (create stubs if missing)
        foreach (array_keys(LegalPage::TYPES) as $type) {
            LegalPage::firstOrCreate(
                ['type' => $type],
                [
                    'title'      => LegalPage::TYPES[$type],
                    'content'    => '',
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );
        }

        $pages = LegalPage::with(['creator', 'updater'])
            ->orderByRaw("FIELD(type, 'privacy_policy', 'terms_conditions', 'cookie_policy')")
            ->get()
            ->keyBy('type');

        return view('admin.legals.index', compact('pages'));
    }

    public function update(Request $request, LegalPage $legalPage)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'is_published' => 'sometimes|boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['updated_by']   = auth()->id();

        // Set created_by if it was never set (stub had null)
        if (!$legalPage->created_by) {
            $validated['created_by'] = auth()->id();
        }

        $legalPage->update($validated);

        return back()->with('success', $legalPage->getTypeLabel() . ' updated successfully.');
    }

    public function togglePublish(LegalPage $legalPage)
    {
        $legalPage->update([
            'is_published' => !$legalPage->is_published,
            'updated_by'   => auth()->id(),
        ]);

        $status = $legalPage->is_published ? 'published' : 'unpublished';
        return back()->with('success', $legalPage->getTypeLabel() . " {$status} successfully.");
    }
}
