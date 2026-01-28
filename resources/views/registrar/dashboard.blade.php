@extends('layouts.registrar')

@section('title', 'Registrar Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Registrar Dashboard</h1>
        <p>Manage teachers and students</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalPending ?? 0 }}</div>
                <div class="stat-label">Pending Approvals</div>
            </div>
            <div class="stat-icon icon-pending">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalTeachers ?? 0 }}</div>
                <div class="stat-label">Total Teachers</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalStudents ?? 0 }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalApproved ?? 0 }}</div>
                <div class="stat-label">Approved Users</div>
            </div>
            <div class="stat-icon icon-logins">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Pending Approvals Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pending Approvals</div>
            <a href="{{ route('registrar.users.index') }}" class="view-all" style="display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-eye"></i>
                View All Users
            </a>
        </div>
        
        @if($pendingTeachers->isEmpty() && $pendingStudents->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3 style="color: var(--dark); margin-bottom: 12px;">No Pending Approvals</h3>
            <p style="color: var(--secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                All teachers and students have been approved.
            </p>
        </div>
        @else
        <!-- Pending List -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid var(--border);">
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Name</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Role</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Email</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">ID</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Registered</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Pending Teachers -->
                    @foreach($pendingTeachers as $teacher)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                                    {{ strtoupper(substr($teacher->f_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $teacher->f_name }} {{ $teacher->l_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <span style="display: inline-block; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                Teacher
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            <span style="color: var(--dark);">{{ $teacher->email }}</span>
                        </td>
                        <td style="padding: 16px;">
                            <span style="display: inline-block; padding: 4px 12px; background: #f3f4f6; color: var(--dark); border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                                {{ $teacher->employee_id }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 500; color: var(--dark);">{{ $teacher->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">{{ $teacher->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 8px;">
                                <form action="{{ route('registrar.users.approve', $teacher->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" title="Approve" 
                                            onclick="return confirm('Are you sure you want to approve this teacher?')"
                                            style="padding: 8px; background: #dcfce7; color: var(--success); border: none; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- Pending Students -->
                    @foreach($pendingStudents as $student)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                                    {{ strtoupper(substr($student->f_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $student->f_name }} {{ $student->l_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <span style="display: inline-block; padding: 4px 12px; background: #e0f2fe; color: #075985; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                Student
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            <span style="color: var(--dark);">{{ $student->email }}</span>
                        </td>
                        <td style="padding: 16px;">
                            <span style="display: inline-block; padding: 4px 12px; background: #f3f4f6; color: var(--dark); border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                                {{ $student->student_id }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 500; color: var(--dark);">{{ $student->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">{{ $student->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 8px;">
                                <form action="{{ route('registrar.users.approve', $student->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" title="Approve" 
                                            onclick="return confirm('Are you sure you want to approve this student?')"
                                            style="padding: 8px; background: #dcfce7; color: var(--success); border: none; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('registrar.users.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Add New User</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Create teacher or student</div>
                    </div>
                </a>
                <a href="{{ route('registrar.users.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #fce7f3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #db2777;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Manage Users</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">View all teachers & students</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick Statistics</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Pending Teachers</span>
                        <span style="font-weight: 600;">{{ $pendingTeachers->count() }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Pending Students</span>
                        <span style="font-weight: 600;">{{ $pendingStudents->count() }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Approval Rate</span>
                        <span style="font-weight: 600;">
                            @php
                                $totalPending = $pendingTeachers->count() + $pendingStudents->count();
                                $totalUsers = $totalTeachers + $totalStudents;
                                if ($totalUsers > 0) {
                                    $approvalRate = round((($totalUsers - $totalPending) / $totalUsers) * 100, 1);
                                    echo $approvalRate . '%';
                                } else {
                                    echo '0%';
                                }
                            @endphp
                        </span>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Today's Approvals</span>
                        <span style="font-weight: 600;">
                            @php
                                $todayApprovals = \App\Models\User::whereIn('role', [3, 4])
                                    ->where('is_approved', true)
                                    ->whereDate('approved_at', today())
                                    ->count();
                                echo $todayApprovals;
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
@endsection