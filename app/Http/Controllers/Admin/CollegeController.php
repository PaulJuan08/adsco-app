<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class CollegeController extends Controller
{
    // ── Colleges ──────────────────────────────────────────────────────────────

    public function index()
    {
        $colleges = College::withCount('students')->latest()->paginate(10);

        $totalColleges         = College::count();
        $totalStudents         = User::where('role', 4)->whereNotNull('college_id')->count();
        $avgStudentsPerCollege = $totalColleges > 0 ? round($totalStudents / $totalColleges) : 0;
        $activeColleges        = College::where('status', 1)->count();
        $inactiveColleges      = College::where('status', 0)->count();

        return view('admin.colleges.index', compact(
            'colleges', 'totalColleges', 'totalStudents',
            'avgStudentsPerCollege', 'activeColleges', 'inactiveColleges'
        ));
    }

    public function create()
    {
        return view('admin.colleges.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_name'                          => 'required|string|max:255|unique:colleges',
            'college_year'                          => 'required|string|max:255',
            'description'                           => 'nullable|string',
            'status'                                => 'required|in:0,1',
            // Optional inline programs on create
            'programs'                              => 'nullable|array',
            'programs.*.program_name'               => 'required_with:programs|string|max:255',
            'programs.*.program_code'               => 'nullable|string|max:50',
        ]);

        $college = College::create([
            'college_name' => $validated['college_name'],
            'college_year' => $validated['college_year'],
            'description'  => $validated['description'] ?? null,
            'status'       => $validated['status'],
        ]);

        if (!empty($validated['programs'])) {
            foreach ($validated['programs'] as $program) {
                $college->programs()->create([
                    'program_name' => $program['program_name'],
                    'program_code' => $program['program_code'] ?? null,
                    'description'  => $program['description'] ?? null,
                    'status'       => 1,
                ]);
            }
        }

        Log::info('New college created', ['id' => $college->id, 'name' => $college->college_name]);

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College created successfully!');
    }

    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);

            $college = College::withCount('students')->with('programs')->findOrFail($id);

            // Load programs with student counts
            $programs = $college->programs()
                ->withCount('students')
                ->orderBy('program_name')
                ->get();

            $collegeProgramIds = $programs->pluck('id')->toArray();

            $students = User::where('role', 4)
                ->where('college_id', $id)
                ->when(!empty($collegeProgramIds), function ($q) use ($collegeProgramIds) {
                    $q->whereIn('program_id', $collegeProgramIds);
                })
                ->with('program')
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id', 'college_year', 'program_id', 'created_at']) // Removed 'status'
                ->orderBy('f_name')
                ->orderBy('l_name')
                ->paginate(10);

            return view('admin.colleges.show', compact('college', 'students', 'programs'));

        } catch (\Exception $e) {
            Log::error('Error viewing college', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.colleges.index')
                ->with('error', 'College not found or invalid link. Error: ' . $e->getMessage());
        }
    }

    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $college = College::with('programs')->findOrFail($id);

            return view('admin.colleges.edit', compact('college'));
        } catch (\Exception $e) {
            Log::error('Error editing college', ['error' => $e->getMessage()]);
            return redirect()->route('admin.colleges.index')
                ->with('error', 'College not found or invalid link.');
        }
    }

    public function update(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $college = College::findOrFail($id);

            $validated = $request->validate([
                'college_name' => 'required|string|max:255|unique:colleges,college_name,' . $college->id,
                'college_year' => 'required|string|max:255',
                'description'  => 'nullable|string',
                'status'       => 'required|in:0,1',
            ]);

            $college->update($validated);

            return redirect()
                ->route('admin.colleges.show', Crypt::encrypt($college->id))
                ->with('success', 'College updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating college', ['error' => $e->getMessage()]);
            return redirect()->route('admin.colleges.index')
                ->with('error', 'Failed to update college.');
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $college = College::findOrFail($id);

            if ($college->students()->exists()) {
                return redirect()->route('admin.colleges.index')
                    ->with('error', 'Cannot delete college with enrolled students. Please reassign students first.');
            }

            // Delete all programs under this college first
            $college->programs()->delete();
            $college->delete();

            return redirect()->route('admin.colleges.index')
                ->with('success', 'College deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting college', ['error' => $e->getMessage()]);
            return redirect()->route('admin.colleges.index')
                ->with('error', 'Failed to delete college.');
        }
    }

    // ── Programs (sub-resource) ────────────────────────────────────────────────

    /**
     * Get available programs that can be added to a college.
     * GET /admin/colleges/{encryptedCollegeId}/available-programs
     */
    public function availablePrograms($encryptedCollegeId)
    {
        try {
            $id = Crypt::decrypt($encryptedCollegeId);
            $college = College::findOrFail($id);
            
            // Get all programs with specific columns
            $allPrograms = Program::select(['id', 'program_name', 'program_code', 'description', 'created_at'])
                ->orderBy('program_name')
                ->get();
            
            // Get current program IDs
            $currentProgramIds = $college->programs->pluck('id')->toArray();
            
            // Filter out programs already in the college
            $availablePrograms = $allPrograms->filter(function($program) use ($currentProgramIds) {
                return !in_array($program->id, $currentProgramIds);
            })->values();
            
            return response()->json($availablePrograms);
            
        } catch (\Exception $e) {
            Log::error('Error in availablePrograms', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to load programs',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a program to a college.
     * POST /admin/colleges/{encryptedCollegeId}/add-program
     */
    public function addProgram(Request $request, $encryptedCollegeId)
    {
        try {
            $id = Crypt::decrypt($encryptedCollegeId);
            $college = College::findOrFail($id);
            
            $request->validate([
                'program_id' => 'required|exists:programs,id'
            ]);
            
            $programId = $request->input('program_id');
            
            // Check if program is already attached
            if ($college->programs()->where('programs.id', $programId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program is already added to this college'
                ]);
            }
            
            // Get the program
            $program = Program::find($programId);
            
            // Update the program's college_id
            $program->update(['college_id' => $college->id]);
            
            return response()->json([
                'success' => true,
                'program' => $program
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in addProgram', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add program: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add multiple programs to a college.
     * POST /admin/colleges/{encryptedCollegeId}/add-programs
     */
    public function addPrograms(Request $request, $encryptedCollegeId)
    {
        try {
            $id = Crypt::decrypt($encryptedCollegeId);
            $college = College::findOrFail($id);
            
            $request->validate([
                'program_ids' => 'required|array',
                'program_ids.*' => 'exists:programs,id'
            ]);
            
            $programIds = $request->input('program_ids');
            
            // Filter out programs already attached
            $existingProgramIds = $college->programs->pluck('id')->toArray();
            $newProgramIds = array_diff($programIds, $existingProgramIds);
            
            if (empty($newProgramIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected programs are already added to this college'
                ]);
            }
            
            // Get the programs to attach
            $programs = Program::whereIn('id', $newProgramIds)->get();
            
            // Update the programs' college_id
            foreach ($programs as $program) {
                $program->update(['college_id' => $college->id]);
            }
            
            return response()->json([
                'success' => true,
                'programs' => $programs,
                'message' => count($newProgramIds) . ' program(s) added successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in addPrograms', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add programs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a program from a college.
     * POST /admin/colleges/{encryptedCollegeId}/remove-program
     */
    public function removeProgram(Request $request, $encryptedCollegeId)
    {
        try {
            $id = Crypt::decrypt($encryptedCollegeId);
            $college = College::findOrFail($id);
            
            $request->validate([
                'program_id' => 'required|exists:programs,id'
            ]);
            
            $programId = $request->input('program_id');
            
            // Check if program has students
            $program = Program::find($programId);
            if ($program->students()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove a program that has enrolled students. Please reassign students first.'
                ]);
            }
            
            // Check if program is attached to this college
            if ($program->college_id != $college->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program is not attached to this college'
                ]);
            }
            
            // Remove the program from the college (set college_id to null)
            $program->update(['college_id' => null]);
            
            return response()->json([
                'success' => true,
                'program' => $program,
                'message' => 'Program removed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in removeProgram', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove program: ' . $e->getMessage()
            ], 500);
        }
    }

    // ── AJAX endpoints (used by registration form & admin) ────────────────────

    /**
     * GET /api/registration/colleges
     * Returns all active colleges (id + college_name).
     */
    public function getActiveColleges()
    {
        $colleges = College::active()->orderBy('college_name')->get(['id', 'college_name', 'college_year']);
        return response()->json($colleges);
    }

    /**
     * GET /api/registration/colleges/{collegeId}/programs
     * Returns active degree programs for a given college.
     */
    public function getPrograms($collegeId)
    {
        try {
            $programs = Program::where('college_id', $collegeId)
                ->active()
                ->orderBy('program_name')
                ->get(['id', 'program_name', 'program_code', 'description']);

            return response()->json($programs);
        } catch (\Exception $e) {
            return response()->json(['error' => 'College not found'], 404);
        }
    }

    /**
     * GET /api/registration/colleges/{id}/years
     * Returns the year-level options for a college as an array of strings.
     */
    public function getYears($id)
    {
        try {
            $college = College::findOrFail($id);
            $years   = array_values(array_filter(array_map('trim', explode(',', $college->college_year))));
            return response()->json($years);
        } catch (\Exception $e) {
            return response()->json(['error' => 'College not found'], 404);
        }
    }

    /** Students list view for a college. */
    public function students($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $college = College::findOrFail($id);

            $students = User::where('role', 4)
                ->where('college_id', $id)
                ->with('program')
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id', 'program_id', 'created_at']) // Removed 'status'
                ->orderBy('f_name')
                ->orderBy('l_name')
                ->paginate(15);

            $totalStudents  = $students->total();
            $activeStudents = User::where('role', 4)->where('college_id', $id)->count(); // Removed status filter

            return view('admin.colleges.students', compact(
                'college', 'students', 'totalStudents', 'activeStudents'
            ));
        } catch (\Exception $e) {
            Log::error('Error viewing college students', ['error' => $e->getMessage()]);
            return redirect()->route('admin.colleges.show', Crypt::encrypt($college->id))
                ->with('error', 'Failed to load students.');
        }
    }
}