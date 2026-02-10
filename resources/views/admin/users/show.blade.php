@extends('layouts.admin')

@section('title', 'User Details - Admin Dashboard')

@push('styles')
<style>
    /* Form Container */
    .form-container {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.125rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }

    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer-modern {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    /* User Details specific styles */
    .user-details-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 auto 1.5rem;
        border: 4px solid white;
        box-shadow: var(--shadow-lg);
    }
    
    .user-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0.5rem 0;
    }
    
    .status-approved {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    .status-pending {
        background: var(--warning-light);
        color: var(--warning-dark);
    }
    
    .status-unverified {
        background: var(--gray-100);
        color: var(--gray-600);
    }
    
    .detail-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-value {
        font-size: 1.125rem;
        color: var(--gray-900);
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .detail-subvalue {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin-top: -0.75rem;
        margin-bottom: 1rem;
    }
    
    .detail-section {
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
    }
    
    .detail-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .detail-section-title i {
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    /* Role Badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        background: var(--gray-100);
        color: var(--gray-700);
    }
    
    .role-admin {
        background: var(--danger-light);
        color: var(--danger-dark);
    }
    
    .role-registrar {
        background: var(--warning-light);
        color: var(--warning-dark);
    }
    
    .role-teacher {
        background: var(--info-light);
        color: var(--info-dark);
    }
    
    .role-student {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    /* Activity Items */
    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .activity-item:hover {
        background: var(--gray-50);
        transform: translateX(4px);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .activity-icon.login {
        background: var(--success-light);
        color: var(--success);
    }
    
    .activity-icon.update {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .activity-icon.create {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .activity-icon.delete {
        background: var(--danger-light);
        color: var(--danger);
    }
    
    .activity-details {
        flex: 1;
        min-width: 0;
    }
    
    .activity-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .activity-subtitle {
        font-size: 0.75rem;
        color: var(--gray-600);
    }
    
    .activity-time {
        font-size: 0.75rem;
        color: var(--gray-500);
        text-align: right;
        white-space: nowrap;
    }
    
    .empty-activity {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--gray-500);
    }
    
    .empty-activity i {
        font-size: 2rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .action-buttons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-edit {
        background: var(--primary-light);
        color: var(--primary-dark);
    }
    
    .btn-edit:hover {
        background: var(--primary);
        color: white;
    }
    
    .btn-approve {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    .btn-approve:hover {
        background: var(--success);
        color: white;
    }
    
    .btn-delete {
        background: var(--danger-light);
        color: var(--danger-dark);
    }
    
    .btn-delete:hover {
        background: var(--danger);
        color: white;
    }
    
    .btn-back {
        background: var(--gray-100);
        color: var(--gray-700);
    }
    
    .btn-back:hover {
        background: var(--gray-200);
        color: var(--gray-900);
    }
    
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        gap: 1rem;
    }

    .action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
    }

    .action-card:hover {
        background: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .action-primary {
        border-left: 4px solid var(--primary);
    }

    .action-success {
        border-left: 4px solid var(--success);
    }

    .action-info {
        border-left: 4px solid var(--info);
    }

    .action-warning {
        border-left: 4px solid var(--warning);
    }

    .action-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-sm);
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        color: var(--primary);
    }

    .action-success .action-icon {
        background: var(--success-light);
        color: var(--success);
    }

    .action-info .action-icon {
        background: var(--info-light);
        color: var(--info);
    }

    .action-warning .action-icon {
        background: var(--warning-light);
        color: var(--warning);
    }

    .action-content {
        flex: 1;
        min-width: 0;
    }

    .action-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.9375rem;
        margin-bottom: 0.125rem;
    }

    .action-subtitle {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .action-arrow {
        color: var(--gray-400);
        font-size: 0.875rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .user-details-avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }
        
        .detail-section {
            padding: 1rem;
        }
        
        .detail-value {
            font-size: 1rem;
        }
        
        .action-buttons-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <!-- User Profile Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-user-circle card-icon"></i>
                <h2 class="card-title">User Details: {{ $user->f_name }} {{ $user->l_name }}</h2>
            </div>
            <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" class="view-all-link">
                Edit Profile <i class="fas fa-edit"></i>
            </a>
        </div>
        
        <div class="card-body">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div class="user-details-avatar">
                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.5rem;">
                    {{ $user->f_name }} {{ $user->l_name }}
                </h3>
                <p style="color: var(--gray-600); margin-bottom: 1rem;">{{ $user->email }}</p>
                
                <div class="user-status-badge {{ $user->is_approved ? 'status-approved' : 'status-pending' }}">
                    <i class="fas {{ $user->is_approved ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $user->is_approved ? 'Account Approved' : 'Pending Approval' }}
                </div>
                
                @if(!$user->email_verified_at)
                <div class="user-status-badge status-unverified">
                    <i class="fas fa-exclamation-triangle"></i>
                    Email Not Verified
                </div>
                @endif
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-id-card"></i>
                        Personal Information
                    </div>
                    
                    <div>
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $user->f_name }} {{ $user->l_name }}</div>
                        
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value">{{ $user->email }}</div>
                        <div class="detail-subvalue">
                            @if($user->email_verified_at)
                                <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                Verified {{ $user->email_verified_at->format('M d, Y') }}
                            @else
                                <i class="fas fa-exclamation-circle" style="color: var(--warning);"></i>
                                Not verified
                            @endif
                        </div>
                        
                        @if($user->age)
                        <div class="detail-label">Age</div>
                        <div class="detail-value">{{ $user->age }} years</div>
                        @endif
                        
                        @if($user->sex)
                        <div class="detail-label">Gender</div>
                        <div class="detail-value">{{ ucfirst($user->sex) }}</div>
                        @endif
                        
                        @if($user->contact)
                        <div class="detail-label">Contact Number</div>
                        <div class="detail-value">{{ $user->contact }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-user-cog"></i>
                        Account Information
                    </div>
                    
                    <div>
                        <div class="detail-label">User ID</div>
                        <div class="detail-value">#{{ $user->id }}</div>
                        
                        <div class="detail-label">User Role</div>
                        <div class="detail-value">
                            @php
                                $roleName = $roleNames[$user->role] ?? 'Unknown';
                                $roleClass = 'role-' . strtolower($roleName);
                            @endphp
                            <span class="role-badge {{ $roleClass }}">
                                <i class="fas fa-user-tag"></i> {{ $roleName }}
                            </span>
                        </div>
                        
                        @if($user->employee_id)
                        <div class="detail-label">Employee ID</div>
                        <div class="detail-value">{{ $user->employee_id }}</div>
                        @endif
                        
                        @if($user->student_id)
                        <div class="detail-label">Student ID</div>
                        <div class="detail-value">{{ $user->student_id }}</div>
                        @endif
                        
                        <div class="detail-label">Account Created</div>
                        <div class="detail-value">{{ $user->created_at->format('F d, Y') }}</div>
                        <div class="detail-subvalue">
                            <i class="fas fa-clock"></i> {{ $user->created_at->diffForHumans() }}
                        </div>
                        
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">{{ $user->updated_at->format('F d, Y') }}</div>
                        <div class="detail-subvalue">
                            <i class="fas fa-clock"></i> {{ $user->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            
            @if($user->is_approved && $user->approved_at)
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-user-check"></i>
                    Approval Information
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div class="detail-label">Approval Date</div>
                        <div class="detail-value">{{ $user->approved_at->format('F d, Y') }}</div>
                        <div class="detail-subvalue">
                            <i class="fas fa-clock"></i> {{ $user->approved_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    @if($user->approved_by && $user->approvedBy)
                    <div>
                        <div class="detail-label">Approved By</div>
                        <div class="detail-value">
                            {{ $user->approvedBy->f_name }} {{ $user->approvedBy->l_name }}
                        </div>
                        <div class="detail-subvalue">
                            {{ $user->approvedBy->role_name }} #{{ $user->approved_by }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        <div class="card-footer-modern">
            <div class="action-buttons-grid">
                <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" class="action-btn btn-edit">
                    <i class="fas fa-edit"></i>
                    Edit User
                </a>
                
                @if(!$user->is_approved && (auth()->user()->isAdmin() || auth()->user()->isRegistrar()))
                <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST" id="approveForm">
                    @csrf
                    <button type="submit" class="action-btn btn-approve" id="approveButton">
                        <i class="fas fa-check"></i>
                        Approve User
                    </button>
                </form>
                @endif
                
                @if(auth()->user()->isAdmin() && $user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-delete" id="deleteButton">
                        <i class="fas fa-trash"></i>
                        Delete User
                    </button>
                </form>
                @endif
                
                <a href="{{ route('admin.users.index') }}" class="action-btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Card -->
    @if($activities->isNotEmpty())
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-history card-icon"></i>
                <h2 class="card-title">Recent Activity</h2>
            </div>
            <span class="view-all-link">
                {{ $activities->count() }} activities
            </span>
        </div>
        
        <div class="card-body">
            <div class="items-list">
                @foreach($activities as $activity)
                <div class="activity-item">
                    <div class="activity-icon {{ $activity->action_type ?? 'update' }}">
                        @php
                            $icon = match($activity->action_type ?? 'update') {
                                'login' => 'sign-in-alt',
                                'create' => 'plus-circle',
                                'delete' => 'trash-alt',
                                'approve' => 'check-circle',
                                default => 'edit'
                            };
                        @endphp
                        <i class="fas fa-{{ $icon }}"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-title">
                            {{ $activity->description ?? 'Activity recorded' }}
                        </div>
                        <div class="activity-subtitle">
                            {{ ucfirst($activity->action_type ?? 'update') }} action performed
                        </div>
                    </div>
                    <div class="activity-time">
                        {{ $activity->created_at->diffForHumans() }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-bolt card-icon"></i>
                <h2 class="card-title">Quick Actions</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div class="quick-actions-grid">
                <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" class="action-card action-primary">
                    <div class="action-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Edit User</div>
                        <div class="action-subtitle">Update user information</div>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
                
                @if(!$user->is_approved && (auth()->user()->isAdmin() || auth()->user()->isRegistrar()))
                <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="action-card action-success" style="width: 100%; text-align: left;">
                        <div class="action-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">Approve User</div>
                            <div class="action-subtitle">Grant access to this user</div>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </button>
                </form>
                @endif
                
                <a href="mailto:{{ $user->email }}" class="action-card action-info">
                    <div class="action-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Send Email</div>
                        <div class="action-subtitle">Contact this user</div>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="action-card action-warning">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">All Users</div>
                        <div class="action-subtitle">View all system users</div>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle approve button click
        const approveButton = document.getElementById('approveButton');
        if (approveButton) {
            approveButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to approve this user?\n\nOnce approved, the user will be able to login to the system.')) {
                    // Show loading state
                    const originalHTML = approveButton.innerHTML;
                    approveButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Approving...';
                    approveButton.disabled = true;
                    
                    // Submit the form
                    document.getElementById('approveForm').submit();
                }
            });
        }
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('WARNING: Are you sure you want to delete this user?\n\nThis action cannot be undone. All user data will be permanently removed.')) {
                    // Show loading state
                    const originalHTML = deleteButton.innerHTML;
                    deleteButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Deleting...';
                    deleteButton.disabled = true;
                    
                    // Submit the form
                    document.getElementById('deleteForm').submit();
                }
            });
        }
        
        // Show success message from session
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
        
        @if(session('warning'))
            showNotification('{{ session('warning') }}', 'warning');
        @endif
    });
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-sm);
            background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--warning)'};
            color: white;
            z-index: 9999;
            box-shadow: var(--shadow-lg);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            max-width: 400px;
        `;
        
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon}" style="font-size: 1.25rem;"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Add CSS animations if not present
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