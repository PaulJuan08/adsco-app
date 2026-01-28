<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance records.
     */
    public function index()
    {
        // Get date filter
        $date = request()->input('date', today()->format('Y-m-d'));
        
        // Get attendance for the selected date
        $attendances = Attendance::whereDate('date', $date)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get statistics
        $totalUsers = User::count();
        
        // Calculate status based on check_in and check_out times
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        
        foreach ($attendances as $attendance) {
            // Define status logic based on your business rules
            // Example: If check_in exists, consider present
            if ($attendance->check_in) {
                $presentCount++;
                
                // Check if late (after 8:30 AM for example)
                $checkInTime = Carbon::parse($attendance->check_in);
                $lateThreshold = Carbon::parse('08:30:00');
                
                if ($checkInTime->greaterThan($lateThreshold)) {
                    $lateCount++;
                }
            } else {
                $absentCount++;
            }
        }
        
        return view('admin.attendance.index', compact(
            'attendances', 
            'date',
            'totalUsers',
            'presentCount',
            'absentCount',
            'lateCount'
        ));
    }
}