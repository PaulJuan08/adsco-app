@extends('layouts.registrar')

@section('title', 'Manage Users - Registrar Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Manage Users</h1>
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
    @php
        $totalTeachers = \App\Models\User::where('role', 3)->count();
        $totalStudents = \App\Models\User::where('role', 4)->count();
        $pendingApprovals = \App\Models\User::whereIn('role', [3, 4])->where('is_approved', false)->count();
        $approvedUsers = \App\Models\User::whereIn('role', [3, 4])->where('is_approved', true)->count();
    @endphp
    
    <div class="stat-card stat-card-teachers">
        <div class="stat-header">
            <div class="stat-content">
                <div class="stat-number">{{ $totalTeachers }}</div>
                <div class="stat-label">Total Teachers</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card stat-card-students">
        <div class="stat-header">
            <div class="stat-content">
                <div class="stat-number">{{ $totalStudents }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card stat-card-pending">
        <div class="stat-header">
            <div class="stat-content">
                <div class="stat-number">{{ $pendingApprovals }}</div>
                <div class="stat-label">Pending Approvals</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card stat-card-approved">
        <div class="stat-header">
            <div class="stat-content">
                <div class="stat-number">{{ $approvedUsers }}</div>
                <div class="stat-label">Approved Users</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Users List Card -->
    <div class="content-grid-left">
        <div class="card">
            <div class="card-header">
                <div class="card-header-content">
                    <h2 class="card-title">All Users</h2>
                    <div class="card-header-actions">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Search users..." 
                                   data-search-table="#users-table">
                        </div>
                        <a href="{{ route('registrar.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add User</span>
                        </a>
                    </div>
                </div>
            </div>
            
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif

            @if($users->isEmpty())
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="empty-state-title">No users yet</h3>
                <p class="empty-state-description">
                    You haven't added any teachers or students yet.
                </p>
                <a href="{{ route('registrar.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Add First User
                </a>
            </div>
            @else
            <!-- Users List -->
            <div class="table-responsive">
                <table class="data-table" id="users-table">
                    <thead>
                        <tr>
                            <th class="table-header">Name</th>
                            <th class="table-header">Role</th>
                            <th class="table-header">Email</th>
                            <th class="table-header">ID</th>
                            <th class="table-header">Status</th>
                            <th class="table-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="table-row" data-searchable="true">
                            <td class="table-cell">
                                <div class="user-cell">
                                    <div class="user-avatar-small">
                                        {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                    </div>
                                    <div class="user-info-cell">
                                        <div class="user-name">{{ $user->f_name }} {{ $user->l_name }}</div>
                                        @if($user->phone)
                                        <div class="user-meta">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="table-cell">
                                @if($user->role == 3)
                                <span class="badge badge-teacher">
                                    Teacher
                                </span>
                                @else
                                <span class="badge badge-student">
                                    Student
                                </span>
                                @endif
                            </td>
                            <td class="table-cell">
                                <span class="email-cell">{{ $user->email }}</span>
                            </td>
                            <td class="table-cell">
                                <span class="id-badge">
                                    {{ $user->role == 3 ? $user->employee_id : $user->student_id }}
                                </span>
                            </td>
                            <td class="table-cell">
                                @if($user->is_approved)
                                <span class="status-badge status-approved">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Approved</span>
                                </span>
                                @else
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i>
                                    <span>Pending</span>
                                </span>
                                @endif
                            </td>
                            <td class="table-cell">
                                <div class="action-buttons">
                                    <a href="{{ route('registrar.users.show', Crypt::encrypt($user->id)) }}" 
                                       class="action-btn action-view" title="View">
                                        <i class="fas fa-eye"></i>
                                        <span class="action-text">View</span>
                                    </a>
                                    <a href="{{ route('registrar.users.edit', Crypt::encrypt($user->id)) }}" 
                                       class="action-btn action-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                        <span class="action-text">Edit</span>
                                    </a>
                                    @if(!$user->is_approved)
                                    <form action="{{ route('registrar.users.approve', Crypt::encrypt($user->id)) }}" 
                                          method="POST" class="action-form">
                                        @csrf
                                        <button type="submit" class="action-btn action-approve" title="Approve"
                                                onclick="return confirm('Are you sure you want to approve this user?')">
                                            <i class="fas fa-check"></i>
                                            <span class="action-text">Approve</span>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('registrar.users.destroy', $user->id) }}" 
                                          method="POST" class="action-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-delete" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash"></i>
                                            <span class="action-text">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                </div>
                <div class="pagination-links">
                    @if(!$users->onFirstPage())
                    <a href="{{ $users->previousPageUrl() }}" class="pagination-link">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </a>
                    @endif
                    
                    @foreach($users->getUrlRange(1, min(5, $users->lastPage())) as $page => $url)
                        @if($page == $users->currentPage())
                        <span class="pagination-current">{{ $page }}</span>
                        @else
                        <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="pagination-link">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div class="content-grid-right">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="{{ route('registrar.users.create') }}" class="quick-action-link">
                        <div class="quick-action-icon bg-primary">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">Add New User</div>
                            <div class="quick-action-subtitle">Create teacher or student</div>
                        </div>
                        <i class="fas fa-chevron-right quick-action-arrow"></i>
                    </a>
                    
                    <button type="button" id="print-report" class="quick-action-link">
                        <div class="quick-action-icon bg-pink">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">Print/Export Users</div>
                            <div class="quick-action-subtitle">Print user list or export as CSV</div>
                        </div>
                        <i class="fas fa-chevron-right quick-action-arrow"></i>
                    </button>
                    
                    <button type="button" id="export-csv" class="quick-action-link">
                        <div class="quick-action-icon bg-success">
                            <i class="fas fa-file-csv"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">Download CSV</div>
                            <div class="quick-action-subtitle">Export user data as CSV file</div>
                        </div>
                        <i class="fas fa-chevron-right quick-action-arrow"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Filter Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Filter Users</h2>
            </div>
            <div class="card-body">
                <div class="filter-section">
                    <div class="filter-label">Role</div>
                    <div class="filter-tags">
                        <a href="{{ request()->fullUrlWithQuery(['role' => '3']) }}" 
                           class="filter-tag {{ request('role') == '3' ? 'active' : '' }}">
                            Teachers
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['role' => '4']) }}" 
                           class="filter-tag {{ request('role') == '4' ? 'active' : '' }}">
                            Students
                        </a>
                        <a href="{{ request()->url() }}" 
                           class="filter-tag {{ !request('role') ? 'active' : '' }}">
                            All
                        </a>
                    </div>
                </div>
                
                <div class="filter-section">
                    <div class="filter-label">Status</div>
                    <div class="filter-tags">
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" 
                           class="filter-tag {{ request('status') == 'pending' ? 'active' : '' }}">
                            Pending
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" 
                           class="filter-tag {{ request('status') == 'approved' ? 'active' : '' }}">
                            Approved
                        </a>
                        <a href="{{ request()->url() }}" 
                           class="filter-tag {{ !request('status') ? 'active' : '' }}">
                            All
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Print Content -->
<div id="print-content" style="display: none;">
    <div style="padding: 20px; font-family: Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #4f46e5; margin-bottom: 5px;">ADSCO Users Report - Registrar</h1>
            <p style="color: #666; margin-bottom: 5px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
            <p style="color: #666; margin-bottom: 10px;">Generated by: {{ Auth::user()->f_name }} {{ Auth::user()->l_name }} (Registrar)</p>
            <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
        </div>
        
        <div style="margin-bottom: 30px;">
            <h2 style="color: #333; margin-bottom: 10px;">User Statistics Summary</h2>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #059669; margin-bottom: 5px;">{{ $totalTeachers }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Total Teachers</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #0369a1; margin-bottom: 5px;">{{ $totalStudents }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Total Students</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #d97706; margin-bottom: 5px;">{{ $pendingApprovals }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Pending Approvals</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px;">{{ $approvedUsers }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Approved Users</div>
                </div>
            </div>
        </div>
        
        <h2 style="color: #333; margin-bottom: 15px;">Users List</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background: #f3f4f6;">
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Name</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Role</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Email</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">ID</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Status</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Created Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->f_name }} {{ $user->l_name }}</td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">
                        @if($user->role == 3)
                            Teacher
                        @else
                            Student
                        @endif
                    </td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->email }}</td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->role == 3 ? $user->employee_id : $user->student_id }}</td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">
                        @if($user->is_approved)
                            <span style="color: #059669; font-weight: 500;">Approved</span>
                        @else
                            <span style="color: #d97706; font-weight: 500;">Pending</span>
                        @endif
                    </td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
            <p style="color: #6b7280; font-size: 14px;">
                Total Users: {{ $users->total() }} | 
                Generated by: {{ Auth::user()->f_name }} {{ Auth::user()->l_name }} | 
                Page 1 of 1
            </p>
        </div>
    </div>
</div>

<style>
/* Responsive Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .content-grid {
        grid-template-columns: 2fr 1fr;
    }
}

.content-grid-left {
    min-width: 0;
}

.content-grid-right {
    min-width: 0;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid var(--border);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--secondary);
    font-weight: 500;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.stat-card-teachers .stat-icon {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
}

.stat-card-students .stat-icon {
    background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
}

.stat-card-pending .stat-icon {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
}

.stat-card-approved .stat-icon {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

/* Card Header */
.card-header-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
}

@media (min-width: 768px) {
    .card-header-content {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.card-header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.search-container {
    position: relative;
    min-width: 200px;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary);
    font-size: 0.875rem;
}

.search-input {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

/* Alerts */
.alert {
    margin: 0 1.5rem 1.5rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.alert-success {
    background: #dcfce7;
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state-icon {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state-title {
    font-size: 1rem;
    font-weight: 500;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.empty-state-description {
    font-size: 0.875rem;
    color: var(--secondary);
    margin-bottom: 1.5rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Table */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

@media (max-width: 1024px) {
    .data-table {
        min-width: 100%;
    }
}

.table-header {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--secondary);
    font-size: 0.875rem;
    background: #f9fafb;
    border-bottom: 2px solid var(--border);
}

.table-row {
    border-bottom: 1px solid var(--border);
    transition: background-color 0.2s;
}

.table-row:hover {
    background-color: #f9fafb;
}

.table-cell {
    padding: 1rem;
    vertical-align: middle;
}

/* User Cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar-small {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    flex-shrink: 0;
}

.user-info-cell {
    min-width: 0;
}

.user-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.125rem;
}

.user-meta {
    font-size: 0.75rem;
    color: var(--secondary);
}

/* Badges & Status */
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-teacher {
    background: #dcfce7;
    color: #166534;
}

.badge-student {
    background: #e0f2fe;
    color: #075985;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-approved {
    background: #dcfce7;
    color: #166534;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.id-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #f3f4f6;
    color: var(--dark);
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.email-cell {
    color: var(--dark);
    word-break: break-word;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.375rem 0.625rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.2s;
    white-space: nowrap;
    border: none;
    cursor: pointer;
}

.action-view {
    background: #e0e7ff;
    color: var(--primary);
}

.action-view:hover {
    background: #c7d2fe;
}

.action-edit {
    background: #f3f4f6;
    color: var(--secondary);
}

.action-edit:hover {
    background: #e5e7eb;
}

.action-approve {
    background: #dcfce7;
    color: var(--success);
}

.action-approve:hover {
    background: #bbf7d0;
}

.action-delete {
    background: #fee2e2;
    color: var(--danger);
}

.action-delete:hover {
    background: #fecaca;
}

.action-text {
    display: inline;
}

@media (max-width: 640px) {
    .action-text {
        display: none;
    }
    
    .action-btn {
        padding: 0.5rem;
        width: 2rem;
        height: 2rem;
        justify-content: center;
    }
}

.action-form {
    display: inline;
    margin: 0;
    padding: 0;
}

/* Pagination */
.pagination-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

@media (min-width: 768px) {
    .pagination-container {
        flex-direction: row;
    }
}

.pagination-info {
    color: var(--secondary);
    font-size: 0.875rem;
}

.pagination-links {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.pagination-link {
    padding: 0.5rem 0.75rem;
    background: var(--primary-light);
    color: var(--primary);
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.2s;
}

.pagination-link:hover {
    background: var(--primary);
    color: white;
}

.pagination-current {
    padding: 0.5rem 0.75rem;
    background: var(--primary);
    color: white;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quick-action-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
}

.quick-action-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    transform: translateY(-1px);
}

.quick-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.bg-primary {
    background: #4f46e5;
}

.bg-pink {
    background: #db2777;
}

.bg-success {
    background: #10b981;
}

.quick-action-content {
    flex: 1;
    min-width: 0;
}

.quick-action-title {
    font-weight: 500;
    font-size: 0.875rem;
}

.quick-action-subtitle {
    font-size: 0.75rem;
    color: #6b7280;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.quick-action-arrow {
    color: #9ca3af;
    margin-left: auto;
    flex-shrink: 0;
}

/* Filter Section */
.filter-section {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border);
}

.filter-section:last-child {
    border-bottom: none;
}

.filter-label {
    color: var(--secondary);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.filter-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-tag {
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.75rem;
    transition: all 0.2s;
}

.filter-tag.active {
    background: var(--primary);
    color: white;
}

.filter-tag:not(.active) {
    background: #f3f4f6;
    color: var(--secondary);
}

.filter-tag:not(.active):hover {
    background: #e5e7eb;
}

/* Mobile Optimizations */
@media (max-width: 640px) {
    .card-body {
        padding: 1rem;
    }
    
    .table-cell {
        padding: 0.75rem;
    }
    
    .card-header-actions {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-container {
        min-width: auto;
        width: 100%;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .user-cell {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .user-avatar-small {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }
}
</style>

@push('scripts')
<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInputs = document.querySelectorAll('.search-input[data-search-table]');
        
        searchInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const tableSelector = this.getAttribute('data-search-table');
                const table = document.querySelector(tableSelector);
                
                if (!table) return;
                
                const rows = table.querySelectorAll('tbody tr[data-searchable="true"]');
                
                rows.forEach(row => {
                    let found = false;
                    const cells = row.querySelectorAll('td');
                    
                    cells.forEach(cell => {
                        const text = cell.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            found = true;
                        }
                    });
                    
                    row.style.display = found ? '' : 'none';
                });
            });
        });
    });

    // Print functionality
    document.getElementById('print-report')?.addEventListener('click', function() {
        const printContent = document.getElementById('print-content').innerHTML;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Users Report - Registrar</title>
                <style>
                    @media print {
                        @page {
                            size: landscape;
                            margin: 0.5in;
                        }
                        body {
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        table {
                            page-break-inside: auto;
                        }
                        tr {
                            page-break-inside: avoid;
                            page-break-after: auto;
                        }
                    }
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                    }
                    h1, h2, h3 {
                        margin-top: 0;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 12px;
                    }
                    th {
                        background-color: #f3f4f6 !important;
                        -webkit-print-color-adjust: exact;
                    }
                    td, th {
                        border: 1px solid #ddd;
                        padding: 8px;
                    }
                    .summary-grid {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 15px;
                        margin-bottom: 20px;
                    }
                    .summary-item {
                        background: #f9fafb;
                        padding: 15px;
                        border-radius: 8px;
                        border: 1px solid #e5e7eb;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                ${printContent}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 100);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    });

    // Export to CSV functionality
    document.getElementById('export-csv')?.addEventListener('click', function() {
        const table = document.getElementById('users-table');
        const rows = table.querySelectorAll('tr');
        const csv = [];
        
        // Add headers (excluding the Actions column)
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => {
            if (!th.textContent.includes('Actions')) {
                headers.push(th.textContent.trim());
            }
        });
        csv.push(headers.join(','));
        
        // Add data rows
        table.querySelectorAll('tbody tr').forEach(row => {
            const cells = [];
            const columns = row.querySelectorAll('td');
            
            // Name
            const nameDiv = columns[0].querySelector('.user-name');
            cells.push(`"${nameDiv ? nameDiv.textContent.trim() : ''}"`);
            
            // Role
            const roleSpan = columns[1].querySelector('.badge');
            cells.push(`"${roleSpan ? roleSpan.textContent.trim() : ''}"`);
            
            // Email
            const emailSpan = columns[2].querySelector('.email-cell');
            cells.push(`"${emailSpan ? emailSpan.textContent.trim() : ''}"`);
            
            // ID
            const idSpan = columns[3].querySelector('.id-badge');
            cells.push(`"${idSpan ? idSpan.textContent.trim() : ''}"`);
            
            // Status
            const statusSpan = columns[4].querySelector('.status-badge');
            cells.push(`"${statusSpan ? statusSpan.textContent.trim() : ''}"`);
            
            csv.push(cells.join(','));
        });
        
        // Create and download CSV file
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', `registrar_users_export_${new Date().toISOString().slice(0,10)}.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        alert('CSV file has been downloaded successfully!');
    });
</script>
@endpush
@endsection