@extends('layouts.admin')

@section('title', 'User Details - Admin Dashboard')

@push('styles')
<style>
    /* Modern Card Design - Smaller Container */
    .form-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin: 1.5rem auto;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        max-width: 1200px;
        width: 95%;
    }

    .form-container:hover {
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Action Buttons Container - At the Top */
    .top-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .top-action-btn {
        color: white;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .top-action-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        gap: 0.75rem;
    }

    .top-action-btn.delete-btn {
        background: rgba(245, 101, 101, 0.3);
    }

    .top-action-btn.delete-btn:hover {
        background: rgba(245, 101, 101, 0.5);
    }

    .card-body {
        padding: 1.5rem 1.75rem;
    }

    /* User Avatar - Compact */
    .user-avatar-section {
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .user-details-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 auto 1rem;
        border: 4px solid white;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        position: relative;
        transition: all 0.3s ease;
    }

    .user-details-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
    }

    .user-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }

    .user-email {
        color: #4a5568;
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .user-status-container {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .user-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.8125rem;
        font-weight: 600;
        margin: 0.25rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }
    
    .status-pending {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
    }
    
    .user-status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    }
    
    /* Details Sections - Compact */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .detail-section {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .detail-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px 3px 0 0;
    }
    
    .detail-section:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }
    
    .detail-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
    }
    
    .detail-section-title i {
        color: #667eea;
        font-size: 1.125rem;
        width: 20px;
        text-align: center;
    }
    
    .detail-row {
        display: grid;
        grid-template-columns: 110px 1fr;
        align-items: start;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .detail-row:hover {
        background: #f7fafc;
    }
    
    .detail-label {
        font-size: 0.8125rem;
        color: #4a5568;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-value {
        font-size: 1rem;
        color: #1a202c;
        font-weight: 600;
        line-height: 1.5;
    }
    
    .detail-subvalue {
        font-size: 0.75rem;
        color: #718096;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* Role Badge - Compact */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.8125rem;
        font-weight: 600;
        background: #edf2f7;
        color: #2d3748;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .role-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    
    .role-admin {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }
    
    .role-registrar {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }
    
    .role-teacher {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }
    
    .role-student {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }
    
    /* Stats Cards - Compact */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        margin: 0 auto 0.75rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.125rem;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Role mapping based on user role value */
    .role-badge.role-admin {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }
    
    .role-badge.role-registrar {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }
    
    .role-badge.role-teacher {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }
    
    .role-badge.role-student {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    /* Success/Error Messages */
    .message-success {
        margin-top: 1.25rem;
        padding: 0.875rem 1.25rem;
        background: #f0fff4;
        color: #276749;
        border-radius: 10px;
        border-left: 4px solid #48bb78;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .message-error {
        margin-top: 1.25rem;
        padding: 0.875rem 1.25rem;
        background: #fff5f5;
        color: #c53030;
        border-radius: 10px;
        border-left: 4px solid #f56565;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 1280px) {
        .form-container {
            max-width: 1100px;
            width: 90%;
        }
    }

    @media (max-width: 768px) {
        .form-container {
            width: 95%;
            margin: 1rem auto;
        }

        .card-header {
            padding: 1rem 1.25rem;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .top-actions {
            align-self: stretch;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .top-action-btn {
            flex: 1;
            justify-content: center;
        }

        .card-body {
            padding: 1.25rem;
        }

        .details-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .detail-row {
            grid-template-columns: 1fr;
            gap: 0.25rem;
        }

        .user-details-avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        .user-name {
            font-size: 1.25rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .form-container {
            width: 98%;
            margin: 0.75rem auto;
            border-radius: 16px;
        }

        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.125rem;
        }

        .user-status-container {
            flex-direction: column;
            align-items: stretch;
        }

        .user-status-badge {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .detail-section {
            padding: 1rem 1.25rem;
        }

        .detail-section-title {
            font-size: 0.9375rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- User Profile Card - Smaller Container -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-user-circle card-icon"></i>
                <h2 class="card-title">User Profile</h2>
            </div>
            <div class="top-actions">
                <!-- Edit Button -->
                <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                <!-- Delete Button - Only for Admin and not current user -->
                @if(auth()->user()->isAdmin() && $user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="top-action-btn delete-btn" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                @endif
                
                <!-- Back Button -->
                <a href="{{ route('admin.users.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- User Avatar & Basic Info -->
            <div class="user-avatar-section">
                <div class="user-details-avatar">
                    {{ strtoupper(substr($user->f_name, 0, 1)) }}{{ strtoupper(substr($user->l_name, 0, 1)) }}
                </div>
                <h3 class="user-name">{{ $user->f_name }} {{ $user->l_name }}</h3>
                <p class="user-email">{{ $user->email }}</p>
                
                <div class="user-status-container">
                    <div class="user-status-badge {{ $user->is_approved ? 'status-approved' : 'status-pending' }}">
                        <i class="fas {{ $user->is_approved ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $user->is_approved ? 'Approved' : 'Pending' }}
                    </div>
                    
                    @php
                        $roleDisplay = match($user->role) {
                            1 => 'Admin',
                            2 => 'Registrar',
                            3 => 'Teacher',
                            4 => 'Student',
                            default => 'Unknown'
                        };
                        $roleClass = strtolower($roleDisplay);
                    @endphp
                    
                    <div class="user-status-badge role-badge role-{{ $roleClass }}">
                        <i class="fas fa-user-tag"></i>
                        {{ $roleDisplay }}
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div class="stat-value">#{{ $user->id }}</div>
                    <div class="stat-label">User ID</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value">{{ $user->created_at->format('M Y') }}</div>
                    <div class="stat-label">Joined</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-value">
                        @php
                            $accountType = match($user->role) {
                                1 => 'Admin',
                                2 => 'Registrar',
                                3 => 'Teacher',
                                4 => 'Student',
                                default => 'User'
                            };
                        @endphp
                        {{ $accountType }}
                    </div>
                    <div class="stat-label">Account Type</div>
                </div>
                
                @if($user->is_approved && $user->approved_at)
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-value">{{ $user->approved_at->format('M Y') }}</div>
                    <div class="stat-label">Approved</div>
                </div>
                @endif
            </div>

            <!-- Detailed Information -->
            <div class="details-grid">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-id-card"></i>
                        Personal Information
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $user->f_name }} {{ $user->l_name }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">{{ $user->email }}</div>
                    </div>
                    
                    @if($user->age)
                    <div class="detail-row">
                        <div class="detail-label">Age</div>
                        <div class="detail-value">{{ $user->age }} years</div>
                    </div>
                    @endif
                    
                    @if($user->sex)
                    <div class="detail-row">
                        <div class="detail-label">Gender</div>
                        <div class="detail-value">{{ ucfirst($user->sex) }}</div>
                    </div>
                    @endif
                    
                    @if($user->contact)
                    <div class="detail-row">
                        <div class="detail-label">Contact</div>
                        <div class="detail-value">{{ $user->contact }}</div>
                    </div>
                    @endif
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-user-cog"></i>
                        Account Information
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">User Role</div>
                        <div class="detail-value">
                            <span class="role-badge role-{{ $roleClass }}">
                                <i class="fas fa-user-tag"></i> {{ $roleDisplay }}
                            </span>
                        </div>
                    </div>
                    
                    @if($user->employee_id)
                    <div class="detail-row">
                        <div class="detail-label">Employee ID</div>
                        <div class="detail-value">{{ $user->employee_id }}</div>
                    </div>
                    @endif
                    
                    @if($user->student_id)
                    <div class="detail-row">
                        <div class="detail-label">Student ID</div>
                        <div class="detail-value">{{ $user->student_id }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approval Information -->
            @if($user->is_approved && $user->approved_at)
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-user-check"></i>
                    Approval Information
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="detail-row">
                        <div class="detail-label">Approval Date</div>
                        <div class="detail-value">
                            {{ $user->approved_at->format('M d, Y') }}
                            <div class="detail-subvalue">
                                <i class="fas fa-clock"></i> {{ $user->approved_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    
                    @if($user->approved_by && $user->approvedBy)
                    <div class="detail-row">
                        <div class="detail-label">Approved By</div>
                        <div class="detail-value">
                            {{ $user->approvedBy->f_name }} {{ $user->approvedBy->l_name }}
                        </div>
                        <div class="detail-subvalue">
                            {{ $roleNames[$user->approvedBy->role] ?? 'Admin' }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Approve Button for Pending Users -->
            @if(!$user->is_approved && (auth()->user()->isAdmin() || auth()->user()->isRegistrar()))
            <div style="margin-top: 1.5rem; text-align: center;">
                <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST" id="approveForm" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="top-action-btn" id="approveButton" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); border: none; padding: 0.75rem 2rem;">
                        <i class="fas fa-check-circle"></i> Approve User
                    </button>
                </form>
            </div>
            @endif

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="message-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="message-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle approve button click
        const approveButton = document.getElementById('approveButton');
        if (approveButton) {
            approveButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Approve User?',
                    text: 'This will grant the user access to the system.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#48bb78',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        approveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
                        approveButton.disabled = true;
                        document.getElementById('approveForm').submit();
                    }
                });
            });
        }
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete User?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        deleteButton.disabled = true;
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        }
        
        // Show notifications from session
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'success',
                title: '{{ session('success') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: '{{ session('error') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        @if(session('warning'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'warning',
                title: '{{ session('warning') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
</script>
@endpush