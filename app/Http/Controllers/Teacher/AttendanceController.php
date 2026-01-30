<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('teacher.attendance.index');
    }

    public function create()
    {
        return view('teacher.attendance.create');
    }

    public function store(Request $request)
    {
        // Store attendance logic
    }
}