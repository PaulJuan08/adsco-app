@extends('layouts.admin')

@section('title', 'User Details - Admin Dashboard')

@push('styles')
<style>
    /* Additional styles for user details page */
    .avatar-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 2rem;
        margin: 0 auto 1rem;
    }
    
    .badge-success { background-color: var(--success) !important; }
    .badge-warning { background-color: var(--warning) !important; }
    .badge-danger { background-color: var(--danger) !important; }
    .badge-primary { background-color: var(--primary) !important; }
    .badge-info { background-color: #0ea5e9 !important; }
    
    .border-top {
        border-top: 1px solid var(--border) !important;
    }
    
    .text-muted {
        color: var(--secondary) !important;
    }
    
    .small {
        font-size: 0.875rem;
    }
    
    .mb-3 {
        margin-bottom: 1rem;
    }
    
    .mt-4 {
        margin-top: 1.5rem;
    }
    
    .pt-3 {
        padding-top: 1rem;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>User Details</h1>
        <p>View detailed information about this user</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- User Details Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">User Information</div>
            <a href="{{ route('admin.users.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i>
                Back to Users
            </a>
        </div>
        
        <div style="padding: 1.5rem;">
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem; text-align: center;">
                <div class="avatar-lg" style="background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); color: white;">
                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                </div>
                <h2 style="margin: 0.5rem 0; color: var(--dark);">{{ $user->f_name }} {{ $user->l_name }}</h2>
                <p style="color: var(--secondary); margin-bottom: 1rem;">{{ $user->email }}</p>
                
                @php
                    $roleName = match($user->role) {
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
                    $color = $roleColors[$user->role] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];
                @endphp
                
                <span style="display: inline-block; padding: 6px 16px; background: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                    {{ $roleName }}
                </span>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <h3 style="font-size: 1rem; color: var(--dark); margin-bottom: 1rem;">Personal Information</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">First Name</div>
                        <div style="font-weight: 500; color: var(--dark);">{{ $user->f_name }}</div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Last Name</div>
                        <div style="font-weight: 500; color: var(--dark);">{{ $user->l_name }}</div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Email Address</div>
                        <div style="font-weight: 500; color: var(--dark);">{{ $user->email }}</div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">User ID</div>
                        <div style="font-weight: 500; color: var(--dark);">#{{ $user->id }}</div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div>
                    <h3 style="font-size: 1rem; color: var(--dark); margin-bottom: 1rem;">Account Information</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Account Status</div>
                        <div>
                            @if($user->is_approved)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                    Approved
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-clock" style="font-size: 10px;"></i>
                                    Pending Approval
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Email Verified</div>
                        <div>
                            @if($user->email_verified_at)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                    Verified
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-times-circle" style="font-size: 10px;"></i>
                                    Not Verified
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Account Created</div>
                        <div style="font-weight: 500; color: var(--dark);">
                            {{ $user->created_at->format('F d, Y \a\t h:i A') }}
                        </div>
                        <div style="color: var(--secondary); font-size: 0.75rem;">
                            {{ $user->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Last Login</div>
                        <div style="font-weight: 500; color: var(--dark);">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </div>
                    </div>
                    
                    @if($user->is_approved && $user->approved_at)
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Approved On</div>
                        <div style="font-weight: 500; color: var(--dark);">
                            {{ $user->approved_at->format('F d, Y') }}
                        </div>
                        @if($user->approved_by)
                        <div style="color: var(--secondary); font-size: 0.75rem;">
                            By Admin #{{ $user->approved_by }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Account Actions -->
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 1rem;">Account Actions</div>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" 
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #e0e7ff; color: var(--primary); border-radius: 6px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-edit"></i>
                        Edit User
                    </a>
                    
                    @if(!$user->is_approved)
                    <button onclick="approveUser('{{ Crypt::encrypt($user->id) }}')" 
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #dcfce7; color: var(--success); border-radius: 6px; border: none; font-weight: 500; cursor: pointer;">
                        <i class="fas fa-check"></i>
                        Approve User
                    </button>
                    @endif
                    
                    <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" 
                          method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this user?')"
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #fee2e2; color: var(--danger); border-radius: 6px; border: none; font-weight: 500; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                            Delete User
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.users.index') }}" 
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Stats</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Account Age</span>
                        <span style="font-weight: 600;">
                            {{ $user->created_at->diffForHumans(null, true) }}
                        </span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Login Count</span>
                        <span style="font-weight: 600;">
                            {{ $user->login_count ?? 0 }}
                        </span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Last Updated</span>
                        <span style="font-weight: 600;">
                            {{ $user->updated_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Account Type</span>
                        <span style="font-weight: 600;">{{ $roleName }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Activity</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem;">Created Courses</div>
                    <div style="font-weight: 600; margin-top: 4px;">
                        @php
                            $courseCount = \App\Models\Course::where('teacher_id', $user->id)->count();
                            echo $courseCount;
                        @endphp
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem;">Enrolled Courses</div>
                    <div style="font-weight: 600; margin-top: 4px;">
                        @php
                            $enrolledCount = $user->enrolledCourses()->count();
                            echo $enrolledCount;
                        @endphp
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem;">Recent Logins</div>
                    <div style="font-weight: 600; margin-top: 4px;">
                        @php
                            $recentLogins = \App\Models\AuditLog::where('user_id', $user->id)
                                ->where('action', 'login')
                                ->count();
                            echo $recentLogins;
                        @endphp
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="color: var(--secondary); font-size: 0.875rem;">Actions Performed</div>
                    <div style="font-weight: 600; margin-top: 4px;">
                        @php
                            $actionsCount = \App\Models\AuditLog::where('user_id', $user->id)->count();
                            echo $actionsCount;
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approveUser(userId) {
    if (confirm('Are you sure you want to approve this user?')) {
        // Use direct URL instead of named route to avoid errors
        fetch(`/admin/users/${userId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('User approved successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error approving user: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            showNotification('Network error. Please try again.', 'error');
        });
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
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
        display: flex;
        align-items: center;
        gap: 0.75rem;
    `;
    
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add animation keyframes
if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
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
}
</script>
@endpush
@endsection