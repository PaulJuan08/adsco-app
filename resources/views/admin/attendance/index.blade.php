@extends('layouts.admin')

@section('title', 'Attendance Management - Admin Dashboard')

@push('styles')
<!-- Datepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Additional styles specific to attendance page */
    .icon-present { background: #dcfce7; color: var(--success); }
    .icon-absent { background: #fee2e2; color: var(--danger); }
    .icon-late { background: #fef3c7; color: var(--warning); }
    .icon-total { background: #e0e7ff; color: var(--primary); }
    
    .date-filter {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .badge-success { background-color: var(--success) !important; }
    .badge-danger { background-color: var(--danger) !important; }
    .badge-warning { background-color: var(--warning) !important; }
    .badge-secondary { background-color: var(--secondary) !important; }
    
    .avatar-sm {
        width: 36px;
        height: 36px;
    }
    
    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 50%;
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Attendance Management</h1>
        <p>Track and manage attendance records</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $presentCount }}</div>
                <div class="stat-label">Present</div>
            </div>
            <div class="stat-icon icon-present">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-check-circle" style="margin-right: 4px;"></i> On time
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $absentCount }}</div>
                <div class="stat-label">Absent</div>
            </div>
            <div class="stat-icon icon-absent">
                <i class="fas fa-user-times"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-times-circle" style="margin-right: 4px;"></i> Not present
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $lateCount }}</div>
                <div class="stat-label">Late</div>
            </div>
            <div class="stat-icon icon-late">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-hourglass-half" style="margin-right: 4px;"></i> Arrived late
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-icon icon-total">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-user-friends" style="margin-right: 4px;"></i> All registered
        </div>
    </div>
</div>

<!-- Date Filter -->
<div class="date-filter">
    <form method="GET" action="{{ route('admin.attendance') }}" style="display: flex; align-items: center; gap: 1rem;">
        <div style="flex-grow: 1;">
            <label style="display: block; color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Select Date</label>
            <input type="date" 
                   name="date" 
                   value="{{ $date }}"
                   style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; width: 100%;"
                   onchange="this.form.submit()">
        </div>
        <div style="margin-top: 20px;">
            <button type="button" onclick="setDate('today')" style="padding: 8px 16px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; cursor: pointer; margin-right: 8px;">
                Today
            </button>
            <button type="button" onclick="setDate('yesterday')" style="padding: 8px 16px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; cursor: pointer;">
                Yesterday
            </button>
        </div>
    </form>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Attendance List Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Attendance for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</div>
            <div style="color: var(--secondary); font-size: 0.875rem;">
                {{ $attendances->total() }} records
            </div>
        </div>
        
        @if($attendances->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3 style="color: var(--dark); margin-bottom: 12px;">No Attendance Records</h3>
            <p style="color: var(--secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                No attendance records found for the selected date.
            </p>
        </div>
        @else
        <!-- Attendance List -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid var(--border);">
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">#</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">User</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Role</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Status</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Check-in Time</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Check-out Time</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px;">{{ $loop->iteration }}</td>
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                    {{ strtoupper(substr($attendance->user->f_name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--dark);">
                                        {{ $attendance->user->f_name ?? 'Unknown' }} {{ $attendance->user->l_name ?? '' }}
                                    </div>
                                    <div style="color: var(--secondary); font-size: 0.75rem;">
                                        {{ $attendance->user->email ?? 'No email' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $roleName = match($attendance->user->role ?? 0) {
                                    1 => 'Admin',
                                    2 => 'Registrar',
                                    3 => 'Teacher',
                                    4 => 'Student',
                                    default => 'Unknown'
                                };
                                $roleColors = [
                                    1 => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                    2 => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                                    3 => ['bg' => '#dcfce7', 'text' => '#166534'],
                                    4 => ['bg' => '#e0f2fe', 'text' => '#075985']
                                ];
                                $color = $roleColors[$attendance->user->role ?? 0] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];
                            @endphp
                            <span style="display: inline-block; padding: 4px 10px; background: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $statusColors = [
                                    'present' => ['bg' => '#dcfce7', 'text' => '#166534', 'icon' => 'fa-check'],
                                    'absent' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'fa-times'],
                                    'late' => ['bg' => '#fef3c7', 'text' => '#92400e', 'icon' => 'fa-clock']
                                ];
                                $statusColor = $statusColors[$attendance->status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280', 'icon' => 'fa-question'];
                            @endphp
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                <i class="fas {{ $statusColor['icon'] }}" style="font-size: 10px;"></i>
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            @if($attendance->check_in)
                                <div style="font-weight: 500; color: var(--dark);">{{ $attendance->check_in }}</div>
                                <div style="color: var(--secondary); font-size: 0.75rem;">Morning</div>
                            @else
                                <span style="color: var(--secondary);">-</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($attendance->check_out)
                                <div style="font-weight: 500; color: var(--dark);">{{ $attendance->check_out }}</div>
                                <div style="color: var(--secondary); font-size: 0.75rem;">Afternoon</div>
                            @else
                                <span style="color: var(--secondary);">-</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $attendance->remarks ?? 'No remarks' }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($attendances instanceof \Illuminate\Pagination\AbstractPaginator)
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries
            </div>
            <div style="display: flex; gap: 8px;">
                @if($attendances->onFirstPage())
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Previous
                </span>
                @else
                <a href="{{ $attendances->previousPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Previous
                </a>
                @endif
                
                @foreach(range(1, min(5, $attendances->lastPage())) as $page)
                    @if($page == $attendances->currentPage())
                    <span style="padding: 8px 12px; background: var(--primary); color: white; border-radius: 6px; font-size: 0.875rem;">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $attendances->url($page) }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach
                
                @if($attendances->hasMorePages())
                <a href="{{ $attendances->nextPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Next
                </a>
                @else
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Next
                </span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <button onclick="printAttendance()" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s; border: none; background: transparent; width: 100%; cursor: pointer;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-print"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Print Report</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Generate PDF/Print</div>
                    </div>
                </button>
                <a href="#" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #fce7f3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #db2777;">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Export Data</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Download as CSV</div>
                    </div>
                </a>
                <a href="#" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Send Reminders</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Notify absent users</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Attendance Statistics</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Today's Attendance Rate</span>
                        <span style="font-weight: 600;">
                            @if($totalUsers > 0)
                                {{ round(($presentCount / $totalUsers) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">This Week's Average</span>
                        <span style="font-weight: 600;">
                            @php
                                $weekAverage = 0; // You'll need to calculate this in your controller
                                echo $weekAverage > 0 ? round($weekAverage, 1).'%' : '0%';
                            @endphp
                        </span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Most Punctual Role</span>
                        <span style="font-weight: 600;">-</span>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Monthly Average</span>
                        <span style="font-weight: 600;">
                            @php
                                $monthAverage = 0; // You'll need to calculate this in your controller
                                echo $monthAverage > 0 ? round($monthAverage, 1).'%' : '0%';
                            @endphp
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    © {{ date('Y') }} ADSCO. All rights reserved. Version 1.0.0
</div>

@push('scripts')
<!-- Flatpickr Datepicker -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize datepicker
    flatpickr("input[type=date]", {
        dateFormat: "Y-m-d",
        defaultDate: "{{ $date }}"
    });
    
    // Set date to today or yesterday
    function setDate(type) {
        const today = new Date();
        let dateInput;
        
        if (type === 'today') {
            dateInput = today.toISOString().split('T')[0];
        } else if (type === 'yesterday') {
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            dateInput = yesterday.toISOString().split('T')[0];
        }
        
        document.querySelector('input[name="date"]').value = dateInput;
        document.querySelector('form').submit();
    }
    
    // Print attendance report
    function printAttendance() {
        const printContent = `
            <html>
            <head>
                <title>Attendance Report - {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { color: #111827; margin-bottom: 10px; }
                    h3 { color: #374151; margin-top: 20px; }
                    .header { margin-bottom: 30px; }
                    .summary { background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
                    th { background-color: #f3f4f6; font-weight: 600; }
                    .badge { padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }
                    .present { background: #dcfce7; color: #166534; }
                    .absent { background: #fee2e2; color: #991b1b; }
                    .late { background: #fef3c7; color: #92400e; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Attendance Report</h1>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
                    <p><strong>Generated:</strong> {{ now()->format('F d, Y h:i A') }}</p>
                </div>
                
                <div class="summary">
                    <h3>Summary</h3>
                    <p><strong>Present:</strong> {{ $presentCount }}</p>
                    <p><strong>Absent:</strong> {{ $absentCount }}</p>
                    <p><strong>Late:</strong> {{ $lateCount }}</p>
                    <p><strong>Total Users:</strong> {{ $totalUsers }}</p>
                    <p><strong>Attendance Rate:</strong> 
                        @if($totalUsers > 0)
                            {{ round(($presentCount / $totalUsers) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
                
                <h3>Attendance Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $attendance->user->f_name ?? 'Unknown' }} {{ $attendance->user->l_name ?? '' }}</td>
                            <td>
                                @php
                                    $roleName = match($attendance->user->role ?? 0) {
                                        1 => 'Admin',
                                        2 => 'Registrar',
                                        3 => 'Teacher',
                                        4 => 'Student',
                                        default => 'Unknown'
                                    };
                                @endphp
                                {{ $roleName }}
                            </td>
                            <td>
                                <span class="badge {{ $attendance->status }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td>{{ $attendance->check_in ?? '-' }}</td>
                            <td>{{ $attendance->check_out ?? '-' }}</td>
                            <td>{{ $attendance->remarks ?? 'No remarks' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </body>
            </html>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }
</script>
@endpush
@endsection