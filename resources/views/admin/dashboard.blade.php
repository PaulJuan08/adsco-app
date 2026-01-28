@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Header -->
    <div class="top-header">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="greeting">
                <h1>Welcome back, {{ auth()->user()->name }}</h1>
                <p>You have {{ $pendingApprovals }} approvals pending. Stay on top of things!</p>
            </div>
        </div>
        <div class="header-actions">
            <span class="badge badge-warning">
                <i class="fas fa-bell me-1"></i> {{ $pendingApprovals }} pending
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-icon icon-users">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-arrow-up text-success me-1"></i> All registered users
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $pendingApprovals }}</div>
                    <div class="stat-label">Pending Approvals</div>
                </div>
                <div class="stat-icon icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-exclamation-circle text-warning me-1"></i> Require attention
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $totalCourses }}</div>
                    <div class="stat-label">Total Courses</div>
                </div>
                <div class="stat-icon icon-courses">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-check-circle text-success me-1"></i> All active courses
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $todayLogins }}</div>
                    <div class="stat-label">Today's Logins</div>
                </div>
                <div class="stat-icon icon-logins">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-calendar-day text-info me-1"></i> Active today
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Pending Approvals Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Pending Approvals</h2>
                    <a href="{{ route('admin.users.index') }}?status=pending" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($pendingUsers->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending approvals</p>
                        <p class="text-sm">All users are approved and active</p>
                    </div>
                @else
                    @foreach($pendingUsers->take(3) as $user)
                    <div class="course-item">
                        <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $user->name ?? 'N/A' }}</div>
                            <div class="course-desc">{{ $user->email }}</div>
                            <div class="course-teacher">
                                @php
                                    $roleName = match($user->role) {
                                        1 => 'Admin',
                                        2 => 'Registrar',
                                        3 => 'Teacher',
                                        4 => 'Student',
                                        default => 'Unknown'
                                    };
                                @endphp
                                <span class="badge badge-primary">{{ $roleName }}</span>
                                <span class="ms-2">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div>
                            <button onclick="approveUser({{ $user->id }})" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Recent Activity Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Activity</h2>
                    <a href="{{ route('admin.audit-logs') }}" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($recentActivities->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>No recent activity</p>
                    </div>
                @else
                    @foreach($recentActivities as $activity)
                    <div class="announcement-item">
                        <div class="announcement-info">
                            <span class="announcement-badge">
                                {{ $activity->action ?? 'System' }}
                            </span>
                            <div class="announcement-title">
                                {{ $activity->user->name ?? 'System' }} - {{ $activity->description ?? 'Performed an action' }}
                            </div>
                            <div class="announcement-text">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Quick Actions</h2>
                </div>
                
                <div class="space-y-4">
                    <a href="{{ route('admin.users.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-1">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Add New User</div>
                            <div class="course-desc">Register staff or student</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.courses.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-2">
                            <i class="fas fa-book-medical"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Course</div>
                            <div class="course-desc">Setup new academic course</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.attendance') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">View Attendance</div>
                            <div class="course-desc">Check attendance records</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- System Overview Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">System Overview</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">{{ $activeEnrollments }}</div>
                            <div class="text-sm text-secondary">Active Enrollments</div>
                        </div>
                        <div class="stat-icon icon-courses" style="width: 40px; height: 40px;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">{{ $totalQuizzes }}</div>
                            <div class="text-sm text-secondary">Total Quizzes</div>
                        </div>
                        <div class="stat-icon icon-courses" style="width: 40px; height: 40px;">
                            <i class="fas fa-question-circle"></i>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">{{ $totalUsers - $pendingApprovals }}</div>
                            <div class="text-sm text-secondary">Active Users</div>
                        </div>
                        <div class="stat-icon icon-users" style="width: 40px; height: 40px;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2024 School Management System. All rights reserved.</p>
        <p class="text-sm mt-1">v1.0.0 • Last updated: {{ now()->format('M d, Y') }}</p>
    </footer>
@endsection

@push('styles')
<style>
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    .flex {
        display: flex;
    }
    
    .justify-between {
        justify-content: space-between;
    }
    
    .items-center {
        align-items: center;
    }
    
    .font-semibold {
        font-weight: 600;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb;
    }
    
    .rounded-lg {
        border-radius: 8px;
    }
    
    .p-3 {
        padding: 0.75rem;
    }
    
    .text-sm {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
function approveUser(userId) {
    if (confirm('Are you sure you want to approve this user?')) {
        // Show loading
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
        button.disabled = true;
        
        fetch(`/admin/users/${userId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('User approved successfully!', 'success');
                // Reload after 1.5 seconds
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Approval failed');
            }
        })
        .catch(error => {
            button.innerHTML = originalHTML;
            button.disabled = false;
            showNotification('Error: ' + error.message, 'error');
        });
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endpush