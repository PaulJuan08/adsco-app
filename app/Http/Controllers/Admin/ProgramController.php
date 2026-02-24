<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\College;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Traits\CacheManager;
use Illuminate\Support\Facades\Cache;

class ProgramController extends Controller
{
    use CacheManager;

    /**
     * Display a listing of programs.
     */
    public function index(Request $request)
    {
        $query = Program::with('college')
            ->withCount('students');

        if ($request->filled('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_name', 'like', "%{$search}%")
                  ->orWhere('program_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $programs = $query->latest()->paginate(15);

        $totalPrograms        = Program::count();
        $activePrograms       = Program::where('status', 1)->count();
        $inactivePrograms     = Program::where('status', 0)->count();
        $totalStudents        = User::whereNotNull('program_id')->count();
        $programsWithStudents = Program::has('students')->count();

        $colleges = College::orderBy('college_name')->get(['id', 'college_name']);

        return view('admin.programs.index', compact(
            'programs',
            'totalPrograms',
            'activePrograms',
            'inactivePrograms',
            'totalStudents',
            'programsWithStudents',
            'colleges'
        ));
    }

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        $colleges = College::orderBy('college_name')->get(['id', 'college_name']);
        return view('admin.programs.create', compact('colleges'));
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_id'   => 'required|exists:colleges,id',
            'program_name' => 'required|string|max:255',
            'program_code' => 'nullable|string|max:50|unique:programs,program_code',
            'description'  => 'nullable|string',
            'status'       => 'required|in:0,1',
        ]);

        $program = Program::create($validated);

        Log::info('New program created', [
            'id'        => $program->id,
            'name'      => $program->program_name,
            'college_id'=> $program->college_id,
        ]);

        $this->clearProgramCaches($program->college_id);

        return redirect()
            ->route('admin.programs.show', Crypt::encrypt($program->id))
            ->with('success', 'Program created successfully!');
    }

    /**
     * Display the specified program with its students.
     */
    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);

            $program = Program::with('college')
                ->withCount('students')
                ->findOrFail($id);

            // Only show students that belong to THIS program
            $students = User::where('program_id', $id)
                ->where('role', 4)
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id', 'college_year', 'program_id', 'college_id', 'created_at'])
                ->orderBy('f_name')
                ->orderBy('l_name')
                ->paginate(15);

            return view('admin.programs.show', compact('program', 'students'));
        } catch (\Exception $e) {
            Log::error('Error viewing program', [
                'error'       => $e->getMessage(),
                'encryptedId' => $encryptedId,
            ]);
            return redirect()
                ->route('admin.programs.index')
                ->with('error', 'Program not found or invalid link.');
        }
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit($encryptedId)
    {
        try {
            $id      = Crypt::decrypt($encryptedId);
            $program = Program::with('college')->findOrFail($id);
            $colleges = College::orderBy('college_name')->get(['id', 'college_name']);

            return view('admin.programs.edit', compact('program', 'colleges'));
        } catch (\Exception $e) {
            Log::error('Error editing program', ['error' => $e->getMessage()]);
            return redirect()
                ->route('admin.programs.index')
                ->with('error', 'Program not found or invalid link.');
        }
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, $encryptedId)
    {
        try {
            $id      = Crypt::decrypt($encryptedId);
            $program = Program::findOrFail($id);

            $validated = $request->validate([
                'college_id'   => 'required|exists:colleges,id',
                'program_name' => 'required|string|max:255',
                'program_code' => 'nullable|string|max:50|unique:programs,program_code,' . $program->id,
                'description'  => 'nullable|string',
                'status'       => 'required|in:0,1',
            ]);

            $oldCollegeId = $program->college_id;
            $program->update($validated);

            $this->clearProgramCaches($oldCollegeId);
            if ($oldCollegeId != $program->college_id) {
                $this->clearProgramCaches($program->college_id);
            }

            return redirect()
                ->route('admin.programs.show', Crypt::encrypt($program->id))
                ->with('success', 'Program updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating program', ['error' => $e->getMessage()]);
            return redirect()
                ->route('admin.programs.index')
                ->with('error', 'Failed to update program.');
        }
    }

    /**
     * Remove the specified program.
     */
    public function destroy($encryptedId)
    {
        try {
            $id      = Crypt::decrypt($encryptedId);
            $program = Program::findOrFail($id);

            if ($program->students()->exists()) {
                return redirect()
                    ->route('admin.programs.index')
                    ->with('error', 'Cannot delete program with enrolled students. Please reassign students first.');
            }

            $collegeId = $program->college_id;
            $program->delete();

            $this->clearProgramCaches($collegeId);

            return redirect()
                ->route('admin.programs.index')
                ->with('success', 'Program deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting program', ['error' => $e->getMessage()]);
            return redirect()
                ->route('admin.programs.index')
                ->with('error', 'Failed to delete program.');
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // STUDENT MANAGEMENT WITHIN A PROGRAM
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * AJAX — Search students not yet in this program.
     * GET /admin/programs/{encryptedId}/students/search?q=...
     */
    public function searchStudents(Request $request, $encryptedId)
    {
        try {
            $id      = Crypt::decrypt($encryptedId);
            $program = Program::findOrFail($id);

            $q = $request->input('q', '');

            // Exclude students already in this program
            $students = User::where('role', 4)
                ->where('program_id', '!=', $id)
                ->where(function ($query) use ($q) {
                    $query->where('f_name', 'like', "%{$q}%")
                          ->orWhere('l_name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('student_id', 'like', "%{$q}%");
                })
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id', 'program_id', 'college_id', 'college_year'])
                ->limit(10)
                ->get();

            return response()->json($students);
        } catch (\Exception $e) {
            Log::error('Error searching students for program', ['error' => $e->getMessage()]);
            return response()->json([], 500);
        }
    }

    /**
     * Assign an existing student to this program (and update their college).
     * POST /admin/programs/{encryptedId}/students/assign
     */
    public function assignStudent(Request $request, $encryptedId)
    {
        try {
            $id      = Crypt::decrypt($encryptedId);
            $program = Program::with('college')->findOrFail($id);

            $request->validate([
                'student_id'   => 'required|exists:users,id',
                'college_year' => 'nullable|string|max:50',
            ]);

            $student = User::where('role', 4)->findOrFail($request->student_id);

            $updateData = [
                'program_id' => $program->id,
                'college_id' => $program->college_id, // auto-set college from program
            ];

            if ($request->filled('college_year')) {
                $updateData['college_year'] = $request->college_year;
            }

            $student->update($updateData);

            Log::info('Student assigned to program', [
                'student_id' => $student->id,
                'program_id' => $program->id,
                'college_id' => $program->college_id,
            ]);

            $this->clearProgramCaches($program->college_id);

            return redirect()
                ->route('admin.programs.show', $encryptedId)
                ->with('success', "{$student->f_name} {$student->l_name} has been assigned to {$program->program_name}.");
        } catch (\Exception $e) {
            Log::error('Error assigning student to program', ['error' => $e->getMessage()]);
            return redirect()
                ->route('admin.programs.show', $encryptedId)
                ->with('error', 'Failed to assign student: ' . $e->getMessage());
        }
    }

    /**
     * Unassign (remove) a student from this program without deleting the user account.
     * DELETE /admin/programs/{encryptedId}/students/unassign
     */
    public function unassignStudent(Request $request, $encryptedId)
    {
        try {
            $id      = Crypt::decrypt($encryptedId);
            $program = Program::findOrFail($id);

            $request->validate([
                'student_id' => 'required|exists:users,id',
            ]);

            $student = User::where('role', 4)
                ->where('program_id', $id)
                ->findOrFail($request->student_id);

            // Clear program and college assignment
            $student->update([
                'program_id' => null,
                'college_id' => null,
                'college_year' => null,
            ]);

            Log::info('Student unassigned from program', [
                'student_id' => $student->id,
                'program_id' => $id,
            ]);

            $this->clearProgramCaches($program->college_id);

            return redirect()
                ->route('admin.programs.show', $encryptedId)
                ->with('success', "{$student->f_name} {$student->l_name} has been removed from this program.");
        } catch (\Exception $e) {
            Log::error('Error unassigning student from program', ['error' => $e->getMessage()]);
            return redirect()
                ->route('admin.programs.show', $encryptedId)
                ->with('error', 'Failed to remove student: ' . $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // CACHE HELPERS
    // ═══════════════════════════════════════════════════════════════════════════

    protected function clearProgramCaches($collegeId = null)
    {
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget('programs_index_page_' . $page);
        }

        if ($collegeId) {
            Cache::forget('college_programs_' . $collegeId);
        }

        Log::info('Program caches cleared' . ($collegeId ? ' for college: ' . $collegeId : ''));
    }
}