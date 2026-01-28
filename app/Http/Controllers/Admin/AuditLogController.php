<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogController extends Controller
{
    public function index()
    {
        // Get filters
        $action = request()->input('action');
        $userId = request()->input('user_id');
        
        // Build query
        $query = AuditLog::with('user');
        
        // Apply action filter
        if ($action) {
            $query->where('action', $action);
        }
        
        // Apply user filter
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        // Get paginated results
        $logs = $query->latest()->paginate(20);
        
        return view('admin.audit-logs.index', compact('logs'));
    }
    
    // Optional: Add method to clear old logs
    public function clearOldLogs(Request $request)
    {
        // Delete logs older than 30 days
        $deleted = AuditLog::where('created_at', '<', now()->subDays(30))->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Cleared {$deleted} old log records."
        ]);
    }
}