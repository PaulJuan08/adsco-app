@extends('layouts.admin')

@section('title', 'User Details - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-show.css') }}">
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

                {{-- Academic badges for students --}}
                @if($user->role == 4)
                <div style="display:flex;flex-wrap:wrap;gap:0.5rem;justify-content:center;margin-top:0.75rem;">
                    @if($user->college)
                        <span style="background:#ede9fe;color:#5b21b6;padding:0.375rem 0.875rem;border-radius:999px;font-size:0.8125rem;font-weight:600;">
                            <i class="fas fa-university"></i> {{ $user->college->college_name }}
                        </span>
                    @endif
                    @if($user->program)
                        <span style="background:#dbeafe;color:#1d4ed8;padding:0.375rem 0.875rem;border-radius:999px;font-size:0.8125rem;font-weight:600;">
                            <i class="fas fa-graduation-cap"></i> {{ $user->program->program_name }}
                        </span>
                    @endif
                    @if($user->college_year)
                        <span style="background:#dcfce7;color:#15803d;padding:0.375rem 0.875rem;border-radius:999px;font-size:0.8125rem;font-weight:600;">
                            <i class="fas fa-calendar-alt"></i> {{ $user->college_year }}
                        </span>
                    @endif
                </div>
                @endif
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

                    <div class="detail-row">
                        <div class="detail-label">Last Login</div>
                        <div class="detail-value">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->format('M d, Y h:i A') }}
                                <div class="detail-subvalue">{{ $user->last_login_at->diffForHumans() }}</div>
                            @else
                                Never logged in
                            @endif
                        </div>
                    </div>
                    
                </div>
            </div>

            {{-- ── Academic Information (students only) ── --}}
            @if($user->role == 4)
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-university"></i>
                    Academic Information
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
                    <div class="detail-row" style="flex-direction:column;gap:0.25rem;">
                        <div class="detail-label">College</div>
                        <div class="detail-value" style="text-align:left;">
                            @if($user->college)
                                <a href="{{ route('admin.colleges.show', Crypt::encrypt($user->college->id)) }}"
                                   style="color:#4f46e5;text-decoration:none;font-weight:600;">
                                    <i class="fas fa-university"></i> {{ $user->college->college_name }}
                                </a>
                            @else
                                <span style="color:#f59e0b;"><i class="fas fa-exclamation-circle"></i> Not assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-row" style="flex-direction:column;gap:0.25rem;">
                        <div class="detail-label">Program</div>
                        <div class="detail-value" style="text-align:left;">
                            @if($user->program)
                                <a href="{{ route('admin.programs.show', Crypt::encrypt($user->program->id)) }}"
                                   style="color:#4f46e5;text-decoration:none;font-weight:600;">
                                    <i class="fas fa-graduation-cap"></i> {{ $user->program->program_name }}
                                    @if($user->program->program_code)
                                        <span style="color:#6b7280;font-weight:400;">({{ $user->program->program_code }})</span>
                                    @endif
                                </a>
                            @else
                                <span style="color:#f59e0b;"><i class="fas fa-exclamation-circle"></i> Not assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-row" style="flex-direction:column;gap:0.25rem;">
                        <div class="detail-label">Year Level</div>
                        <div class="detail-value" style="text-align:left;">
                            @if($user->college_year)
                                <span style="background:#dcfce7;color:#15803d;padding:0.25rem 0.75rem;border-radius:999px;font-size:0.8125rem;font-weight:600;">
                                    {{ $user->college_year }}
                                </span>
                            @else
                                <span style="color:#f59e0b;"><i class="fas fa-exclamation-circle"></i> Not set</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

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