@extends('layouts.admin')

@section('title', 'User Management - Admin Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>User Management</h1>
        <p>Manage all system users and their permissions</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    @php
        // Get counts directly from database since $users is paginated
        $adminCount = \App\Models\User::where('role', 1)->count();
        $registrarCount = \App\Models\User::where('role', 2)->count();
        $teacherCount = \App\Models\User::where('role', 3)->count();
        $studentCount = \App\Models\User::where('role', 4)->count();
    @endphp
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $adminCount }}</div>
                <div class="stat-label">Admins</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $registrarCount }}</div>
                <div class="stat-label">Registrars</div>
            </div>
            <div class="stat-icon icon-pending">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $teacherCount }}</div>
                <div class="stat-label">Teachers</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $studentCount }}</div>
                <div class="stat-label">Students</div>
            </div>
            <div class="stat-icon icon-logins">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Users List Card -->
    <div class="card users-card">
        <div class="card-header">
            <div class="card-title">All Users</div>
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search users..." id="search-users">
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Add User
                </a>
            </div>
        </div>
        
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        @if($users->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>No users yet</h3>
            <p>You haven't added any users yet. Start by adding the first user.</p>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Add First User
            </a>
        </div>
        @else
        <!-- Users List -->
        <div class="table-responsive">
            <table class="users-table" id="users-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="hide-on-mobile">Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="hide-on-tablet">Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="user-row" data-name="{{ strtolower($user->f_name . ' ' . $user->l_name) }}" data-email="{{ strtolower($user->email) }}">
                        <td>
                            <div class="user-info-cell">
                                <div class="user-avatar-small">
                                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $user->f_name }} {{ $user->l_name }}</div>
                                    <div class="user-email-mobile">{{ $user->email }}</div>
                                    <div class="user-id">ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="hide-on-mobile">
                            <span class="user-email">{{ $user->email }}</span>
                        </td>
                        <td>
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
                            <span class="role-badge" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_approved)
                            <span class="status-badge status-approved">
                                <i class="fas fa-check-circle"></i>
                                Approved
                            </span>
                            @else
                            <span class="status-badge status-pending">
                                <i class="fas fa-clock"></i>
                                Pending
                            </span>
                            @endif
                        </td>
                        <td class="hide-on-tablet">
                            <div class="created-date">{{ $user->created_at->format('M d, Y') }}</div>
                            <div class="created-ago">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.users.show', Crypt::encrypt($user->id)) }}" 
                                   class="btn-icon view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" 
                                   class="btn-icon edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$user->is_approved)
                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline-form">
                                    @csrf
                                    <button type="submit" class="btn-icon approve" title="Approve"
                                            onclick="return confirm('Are you sure you want to approve this user?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" method="POST" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i>
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
                @if($users->onFirstPage())
                <span class="pagination-btn disabled">Previous</span>
                @else
                <a href="{{ $users->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif
                
                @foreach(range(1, min(5, $users->lastPage())) as $page)
                    @if($page == $users->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                    @else
                    <a href="{{ $users->url($page) }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="pagination-btn">Next</a>
                @else
                <span class="pagination-btn disabled">Next</span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div class="sidebar-container">
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="quick-actions">
                <a href="{{ route('admin.users.create') }}" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Add New User</div>
                        <div class="quick-action-subtitle">Create a new user account</div>
                    </div>
                </a>
                <button id="print-report" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Print/Export Users</div>
                        <div class="quick-action-subtitle">Print user list or export as CSV</div>
                    </div>
                </button>
                <button id="export-csv" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-file-csv"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Download CSV</div>
                        <div class="quick-action-subtitle">Export user data as CSV file</div>
                    </div>
                </button>
            </div>
        </div>
        
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">User Statistics</div>
            </div>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="stat-label">Users Created This Month</span>
                    <span class="stat-value">{{ \App\Models\User::whereMonth('created_at', now()->month)->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Pending Approvals</span>
                    <span class="stat-value">{{ \App\Models\User::where('is_approved', false)->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Active Users (Last 30 days)</span>
                    <span class="stat-value">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Average Registrations/Day</span>
                    <span class="stat-value">
                        @php
                            $totalDays = max(1, \Carbon\Carbon::parse(\App\Models\User::min('created_at'))->diffInDays(now()));
                            $avg = \App\Models\User::count() / $totalDays;
                            echo round($avg, 1);
                        @endphp
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    © {{ date('Y') }} ADSCO. All rights reserved. Version 1.0.0
</div>

<!-- Hidden Print Div -->
<div id="print-content" style="display: none;">
    <div style="padding: 20px; font-family: Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #4f46e5; margin-bottom: 5px;">ADSCO User Management Report</h1>
            <p style="color: #666; margin-bottom: 10px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
            <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
        </div>
        
        <div style="margin-bottom: 30px;">
            <h2 style="color: #333; margin-bottom: 10px;">Summary Statistics</h2>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px;">{{ $adminCount }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Admins</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #7c3aed; margin-bottom: 5px;">{{ $registrarCount }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Registrars</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #059669; margin-bottom: 5px;">{{ $teacherCount }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Teachers</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #0369a1; margin-bottom: 5px;">{{ $studentCount }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Students</div>
                </div>
            </div>
        </div>
        
        <h2 style="color: #333; margin-bottom: 15px;">User List</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background: #f3f4f6;">
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Name</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Email</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Role</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Status</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Joined Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                @php
                    $roleName = match($user->role) {
                        1 => 'Admin',
                        2 => 'Registrar',
                        3 => 'Teacher',
                        4 => 'Student',
                        default => 'Unknown'
                    };
                @endphp
                <tr>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->f_name }} {{ $user->l_name }}</td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->email }}</td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $roleName }}</td>
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
                Generated by: {{ Auth::user()->name }} | 
                Page 1 of 1
            </p>
        </div>
    </div>
</div>

<style>
    /* Responsive CSS Variables */
    :root {
        --primary: #4361ee;
        --primary-light: #e0e7ff;
        --secondary: #6c757d;
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --light: #f8f9fa;
        --dark: #343a40;
        --border: #e9ecef;
    }

    /* Responsive Grid Layout */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .sidebar-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .content-grid {
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .header-actions {
            flex-direction: column;
        }
    }

    /* Search Container */
    .search-container {
        position: relative;
        min-width: 200px;
    }

    .search-container i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--secondary);
    }

    .search-container input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .search-container input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    @media (max-width: 768px) {
        .search-container {
            min-width: unset;
            width: 100%;
        }
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
    }

    .btn:hover {
        background: #4f46e5;
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead {
        background: #f9fafb;
        border-bottom: 2px solid var(--border);
    }

    .users-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--secondary);
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .users-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .users-table tbody tr:hover {
        background: #f9fafb;
    }

    /* User Info Cell */
    .user-info-cell {
        display: flex;
        gap: 0.75rem;
        align-items: center;
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

    .user-details {
        min-width: 0;
    }

    .user-name {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .user-email-mobile {
        font-size: 0.875rem;
        color: var(--secondary);
        display: none;
    }

    .user-id {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    /* Hide Columns for Mobile */
    .hide-on-mobile {
        display: table-cell;
    }

    .hide-on-tablet {
        display: table-cell;
    }

    @media (max-width: 768px) {
        .hide-on-tablet {
            display: none;
        }
        
        .user-email-mobile {
            display: block;
        }
    }

    @media (max-width: 576px) {
        .hide-on-mobile {
            display: none;
        }
        
        .users-table th,
        .users-table td {
            padding: 0.75rem;
        }
    }

    /* Badges */
    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 0.625rem;
    }

    .status-approved {
        background: #dcfce7;
        color: #166534;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
    }

    .btn-icon.view {
        color: #3b82f6;
    }

    .btn-icon.edit {
        color: #f59e0b;
    }

    .btn-icon.approve {
        color: #10b981;
    }

    .btn-icon.delete {
        color: #ef4444;
    }

    .btn-icon:hover {
        background: rgba(0,0,0,0.05);
        transform: translateY(-1px);
    }

    .inline-form {
        display: inline;
    }

    /* Created Date */
    .created-date {
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .created-ago {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.125rem;
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--secondary);
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--secondary);
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Alert */
    .alert {
        margin: 0 1.5rem 1.5rem;
        padding: 0.75rem 1rem;
        background: #dcfce7;
        color: #065f46;
        border-radius: 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert i {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .alert {
            margin: 0 1rem 1rem;
        }
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
    }

    @media (min-width: 768px) {
        .pagination-container {
            flex-direction: row;
        }
    }

    .pagination-info {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .pagination-links {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-btn {
        padding: 0.5rem 0.75rem;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .pagination-btn:hover:not(.disabled):not(.active) {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.active {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.disabled {
        background: #f3f4f6;
        color: var(--secondary);
        cursor: not-allowed;
    }

    /* Sidebar */
    .sidebar-card {
        margin-bottom: 1.5rem;
    }

    .sidebar-card:last-child {
        margin-bottom: 0;
    }

    .quick-actions {
        padding: 0.5rem;
    }

    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--dark);
        transition: background 0.2s;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }

    .quick-action-item:hover {
        background: #f9fafb;
    }

    .quick-action-icon {
        width: 36px;
        height: 36px;
        background: #e0e7ff;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        flex-shrink: 0;
    }

    .quick-action-content {
        text-align: left;
        flex: 1;
        min-width: 0;
    }

    .quick-action-title {
        font-weight: 500;
        margin-bottom: 0.125rem;
    }

    .quick-action-subtitle {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    /* Stats List */
    .stats-list {
        padding: 0.5rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .stat-value {
        font-weight: 600;
        color: var(--dark);
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .top-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }

    .greeting h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .greeting p {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
    }

    /* Footer */
    .footer {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--secondary);
        font-size: 0.875rem;
        border-top: 1px solid var(--border);
        margin-top: 2rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .icon-users {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #4f46e5;
    }

    .icon-pending {
        background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        color: #db2777;
    }

    .icon-courses {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }

    .icon-logins {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-users');
        const userRows = document.querySelectorAll('.user-row');
        
        if (searchInput && userRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                userRows.forEach(row => {
                    const userName = row.dataset.name || '';
                    const userEmail = row.dataset.email || '';
                    
                    if (searchTerm === '' || userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Print functionality
        document.getElementById('print-report')?.addEventListener('click', function() {
            const printContent = document.getElementById('print-content').innerHTML;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>User Management Report</title>
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
                        }
                        th {
                            background-color: #f3f4f6 !important;
                            -webkit-print-color-adjust: exact;
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
            
            // Add headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            csv.push(headers.join(','));
            
            // Add data rows
            table.querySelectorAll('tbody tr').forEach(row => {
                const cells = [];
                const columns = row.querySelectorAll('td');
                
                // Name
                const nameDiv = columns[0].querySelector('.user-name');
                cells.push(`"${nameDiv ? nameDiv.textContent.trim() : ''}"`);
                
                // Email (use mobile email if visible, otherwise desktop email)
                const emailElement = columns[1].querySelector('.user-email') || columns[0].querySelector('.user-email-mobile');
                cells.push(`"${emailElement ? emailElement.textContent.trim() : ''}"`);
                
                // Role
                const roleSpan = columns[2].querySelector('.role-badge');
                cells.push(`"${roleSpan ? roleSpan.textContent.trim() : ''}"`);
                
                // Status
                const statusSpan = columns[3].querySelector('.status-badge');
                cells.push(`"${statusSpan ? statusSpan.textContent.trim() : ''}"`);
                
                // Join Date
                const joinDateDiv = columns[4]?.querySelector('.created-date');
                cells.push(`"${joinDateDiv ? joinDateDiv.textContent.trim() : ''}"`);
                
                csv.push(cells.join(','));
            });
            
            // Create and download CSV file
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `users_export_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Show success message
            alert('CSV file has been downloaded successfully!');
        });
    });
</script>
@endpush
@endsection