<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ProgramController extends Controller
{
    /**
     * Display a listing of programs for students.
     */
    public function index(Request $request)
    {
        $query = Program::with('college')
            ->withCount('students')
            ->where('status', 1); // Only show active programs

        // Filter by college
        if ($request->has('college_id') && $request->college_id) {
            $query->where('college_id', $request->college_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('program_name', 'like', "%{$search}%")
                  ->orWhere('program_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $programs = $query->paginate(12);

        // Get all active colleges for filter dropdown
        $colleges = College::where('status', 1)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);

        $totalPrograms = Program::where('status', 1)->count();
        $totalColleges = College::where('status', 1)->count();

        return view('student.programs.index', compact(
            'programs', 
            'colleges', 
            'totalPrograms', 
            'totalColleges'
        ));
    }

    /**
     * Display the specified program.
     */
    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            $program = Program::with('college')
                ->withCount('students')
                ->where('status', 1) // Only show active programs
                ->findOrFail($id);

            return view('student.programs.show', compact('program'));
        } catch (\Exception $e) {
            return redirect()->route('student.programs.index')
                ->with('error', 'Program not found.');
        }
    }
}