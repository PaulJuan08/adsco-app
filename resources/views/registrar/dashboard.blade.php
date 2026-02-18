@extends('layouts.registrar')

@section('title', 'Registrar Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Welcome back, {{ Auth::user()->f_name }}</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-calendar-day"></i> {{ now()->format('l, F d, Y') }}
                        @if($totalPending > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $totalPending }} approval{{ $totalPending > 1 ? 's' : '' }} pending</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-number">{{ number_format($totalPending ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
            <a href="{{ route('registrar.users.index') }}?status=pending" class="stat-link">
                Review now <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Teachers</div>
                    <div class="stat-number">{{ number_format($totalTeachers ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <a href="{{ route('registrar.users.index') }}?role=teacher" class="stat-link">
                View teachers <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ number_format($totalStudents ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <a href="{{ route('registrar.users.index') }}?role=student" class="stat-link">
                View students <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Approved Users</div>
                    <div class="stat-number">{{ number_format($totalApproved ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <a href="{{ route('registrar.users.index') }}" class="stat-link">
                Manage users <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Pending Approvals Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user-clock" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Pending Approvals
                    </h2>
                    <a href="{{ route('registrar.users.index') }}?status=pending" class="stat-link">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body">
                    @if($pendingTeachers->isEmpty() && $pendingStudents->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="empty-title">All Clear!</h3>
                            <p class="empty-text">No pending approvals at the moment. All users are approved and active.</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($pendingTeachers as $teacher)
                            <div class="list-item">
                                <div class="item-avatar">
                                    {{ strtoupper(substr($teacher->f_name, 0, 1)) }}
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $teacher->f_name }} {{ $teacher->l_name }}</div>
                                    <div class="item-details">{{ $teacher->email }}</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-success">
                                            <i class="fas fa-chalkboard-teacher"></i> Teacher
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            {{ $teacher->employee_id }}
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $teacher->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <form action="{{ route('registrar.users.approve', $teacher->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Are you sure you want to approve this teacher?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($pendingStudents as $student)
                            <div class="list-item">
                                <div class="item-avatar">
                                    {{ strtoupper(substr($student->f_name, 0, 1)) }}
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $student->f_name }} {{ $student->l_name }}</div>
                                    <div class="item-details">{{ $student->email }}</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-primary">
                                            <i class="fas fa-user-graduate"></i> Student
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            {{ $student->student_id }}
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $student->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <form action="{{ route('registrar.users.approve', $student->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Are you sure you want to approve this student?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('registrar.users.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-title">Add New User</div>
                            <div class="action-subtitle">Create teacher or student</div>
                        </a>
                        
                        <a href="{{ route('registrar.users.index') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="action-title">Manage Users</div>
                            <div class="action-subtitle">View all teachers & students</div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-info">
                                <div class="item-name">Pending Teachers</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $pendingTeachers->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-info">
                                <div class="item-name">Pending Students</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $pendingStudents->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-info">
                                <div class="item-name">Approval Rate</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">
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
                            </div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-info">
                                <div class="item-name">Today's Approvals</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">
                                @php
                                    $todayApprovals = \App\Models\User::whereIn('role', [3, 4])
                                        ->where('is_approved', true)
                                        ->whereDate('approved_at', today())
                                        ->count();
                                    echo $todayApprovals;
                                @endphp
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} ADSCO. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Version 1.0.0 • Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First time' }}
        </p>
    </footer>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
/* Additional registrar-specific styles */
.pending-notice {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 500;
}

.separator {
    opacity: 0.5;
}
</style>
@endpush