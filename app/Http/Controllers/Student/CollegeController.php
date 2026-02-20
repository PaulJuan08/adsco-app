<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CollegeController extends Controller
{
    /**
     * Display a listing of colleges for students.
     */
    public function index(Request $request)
    {
        $query = College::withCount('programs')
            ->withCount('students');

        // Filter by status (only show active colleges to students)
        $query->where('status', 1);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('college_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $colleges = $query->latest()->paginate(12);

        $totalColleges = College::where('status', 1)->count();
        $totalPrograms = Program::whereHas('college', function($q) {
            $q->where('status', 1);
        })->count();

        return view('student.colleges.index', compact('colleges', 'totalColleges', 'totalPrograms'));
    }

    /**
     * Display the specified college.
     */
    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            $college = College::withCount('programs')
                ->withCount('students')
                ->where('status', 1) // Only show active colleges
                ->findOrFail($id);
            
            // Get programs under this college
            $programs = Program::where('college_id', $id)
                ->where('status', 1) // Only show active programs
                ->orderBy('program_name')
                ->get();

            return view('student.colleges.show', compact('college', 'programs'));
        } catch (\Exception $e) {
            return redirect()->route('student.colleges.index')
                ->with('error', 'College not found.');
        }
    }

    /**
     * Get programs for a specific college (AJAX).
     */
    public function getPrograms($collegeId)
    {
        try {
            $programs = Program::where('college_id', $collegeId)
                ->where('status', 1)
                ->orderBy('program_name')
                ->get(['id', 'program_name', 'program_code', 'description']);

            return response()->json($programs);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Programs not found'], 404);
        }
    }
}