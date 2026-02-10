@extends('layouts.admin')

@section('title', 'User Management - Admin Dashboard')

@section('content')
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar-large">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">User Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-users"></i> Manage all system users and permissions
                        @if($stats['pending'] > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $stats['pending'] }} pending approval{{ $stats['pending'] > 1 ? 's' : '' }}</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($stats['pending'] > 0)
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-user-clock"></i>
                    <span class="badge-count">{{ $stats['pending'] }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Action Required</div>
                    <div class="alert-subtitle">{{ $stats['pending'] }} user{{ $stats['pending'] > 1 ? 's' : '' }} awaiting approval</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number">{{ number_format($stats['total']) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-users"></i> All registered users
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.users.index') }}" class="stat-link">
                    View all users <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-number">{{ number_format($stats['pending']) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-exclamation-circle"></i> Require attention
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.users.index') }}?status=pending" class="stat-link">
                    Review now <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Teachers</div>
                    <div class="stat-number">{{ number_format($stats['teachers']) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-chalkboard-teacher"></i> Teaching staff
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.users.index') }}?role=3" class="stat-link">
                    View teachers <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Students</div>
                    <div class="stat-number">{{ number_format($stats['students']) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-user-graduate"></i> Registered students
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.users.index') }}?role=4" class="stat-link">
                    View students <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Users List Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-users card-icon"></i>
                        <h2 class="card-title-modern">All Users</h2>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="view-all-link">
                        Add User <i class="fas fa-user-plus"></i>
                    </a>
                </div>
                
                <div class="card-body-modern">
                    <!-- Search and Filters -->
                    <div class="filters-section">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search users by name or email..." id="search-users">
                        </div>
                        <div class="filter-buttons">
                            <a href="{{ route('admin.users.index') }}" class="filter-btn {{ !request()->has('role') && !request()->has('status') ? 'active' : '' }}">
                                All Users
                            </a>
                            <a href="{{ route('admin.users.index') }}?status=pending" class="filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">
                                <i class="fas fa-clock"></i> Pending
                            </a>
                            <a href="{{ route('admin.users.index') }}?role=1" class="filter-btn {{ request('role') == '1' ? 'active' : '' }}">
                                <i class="fas fa-user-cog"></i> Admin
                            </a>
                            <a href="{{ route('admin.users.index') }}?role=2" class="filter-btn {{ request('role') == '2' ? 'active' : '' }}">
                                <i class="fas fa-user-tie"></i> Registrar
                            </a>
                            <a href="{{ route('admin.users.index') }}?role=3" class="filter-btn {{ request('role') == '3' ? 'active' : '' }}">
                                <i class="fas fa-chalkboard-teacher"></i> Teachers
                            </a>
                            <a href="{{ route('admin.users.index') }}?role=4" class="filter-btn {{ request('role') == '4' ? 'active' : '' }}">
                                <i class="fas fa-user-graduate"></i> Students
                            </a>
                        </div>
                    </div>

                    @if($users->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state-modern">
                            <div class="empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="empty-title">No users found</h3>
                            <p class="empty-text">No users match your current filters</p>
                            <p class="empty-subtext">Try adjusting your search or filters</p>
                        </div>
                    @else
                        <!-- Users List -->
                        <div class="items-list">
                            @foreach($users as $user)
                            <div class="list-item user-item" data-name="{{ strtolower($user->f_name . ' ' . $user->l_name) }}" data-email="{{ strtolower($user->email) }}" data-user-id="{{ $user->id }}">
                                <div class="item-avatar">
                                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                </div>
                                <div class="item-details">
                                    <div class="item-title">{{ $user->f_name }} {{ $user->l_name }}
                                        @if(!$user->is_approved)
                                            <span class="pending-badge">Pending</span>
                                        @endif
                                    </div>
                                    <div class="item-subtitle">{{ $user->email }}</div>
                                    <div class="item-meta">
                                        @php
                                            $roleName = $roleNames[$user->role] ?? 'Unknown';
                                            $roleClass = 'role-' . strtolower($roleName);
                                        @endphp
                                        <span class="role-badge {{ $roleClass }}">
                                            <i class="fas fa-user-tag"></i> {{ $roleName }}
                                        </span>
                                        <span class="time-badge">
                                            <i class="fas fa-calendar"></i> {{ $user->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.users.show', Crypt::encrypt($user->id)) }}" 
                                        class="btn-view btn-view-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" 
                                        class="btn-view btn-view-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$user->is_approved)
                                            @if(auth()->user()->isAdmin() || auth()->user()->isRegistrar())
                                            <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" 
                                                method="POST" 
                                                class="inline-form">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn-approve" 
                                                        title="Approve"
                                                        onclick="return confirm('Are you sure you want to approve {{ $user->f_name }} {{ $user->l_name }}?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                        <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" method="POST" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn-delete" 
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete {{ $user->f_name }} {{ $user->l_name }}?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($users instanceof \Illuminate\Pagination\AbstractPaginator && $users->hasPages())
                <div class="card-footer-modern">
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
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-bolt card-icon"></i>
                        <h2 class="card-title-modern">Quick Actions</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.users.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add New User</div>
                                <div class="action-subtitle">Create a new user account</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <button id="print-report" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-print"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Print Report</div>
                                <div class="action-subtitle">Print user list report</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                        
                        <button id="export-csv" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Export CSV</div>
                                <div class="action-subtitle">Download user data as CSV</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                        
                        <a href="{{ route('admin.users.index') }}?status=pending" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Review Pending</div>
                                <div class="action-subtitle">Approve waiting users</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-chart-pie card-icon"></i>
                        <h2 class="card-title-modern">User Statistics</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="overview-list">
                        <div class="overview-item">
                            <div class="overview-icon">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <div class="overview-details">
                                <div class="overview-value">{{ $stats['admins'] }}</div>
                                <div class="overview-label">Admins</div>
                            </div>
                            <div class="overview-trend trend-neutral">
                                <i class="fas fa-minus"></i>
                            </div>
                        </div>
                        
                        <div class="overview-item">
                            <div class="overview-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="overview-details">
                                <div class="overview-value">{{ $stats['registrars'] }}</div>
                                <div class="overview-label">Registrars</div>
                            </div>
                            <div class="overview-trend trend-neutral">
                                <i class="fas fa-minus"></i>
                            </div>
                        </div>
                        
                        <div class="overview-item">
                            <div class="overview-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="overview-details">
                                <div class="overview-value">{{ $stats['pending'] }}</div>
                                <div class="overview-label">Pending Approval</div>
                            </div>
                            <div class="overview-trend {{ $stats['pending'] > 0 ? 'trend-warning' : 'trend-neutral' }}">
                                <i class="fas {{ $stats['pending'] > 0 ? 'fa-exclamation' : 'fa-minus' }}"></i>
                            </div>
                        </div>
                        
                        <div class="overview-item">
                            <div class="overview-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="overview-details">
                                <div class="overview-value">{{ $stats['this_month'] }}</div>
                                <div class="overview-label">This Month</div>
                            </div>
                            <div class="overview-trend trend-neutral">
                                <i class="fas fa-minus"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-content">
            <p class="footer-text">© {{ date('Y') }} School Management System. All rights reserved.</p>
            <p class="footer-meta">
                <span><i class="fas fa-users"></i> User Management</span>
                <span class="separator">•</span>
                <span><i class="fas fa-calendar"></i> {{ now()->format('M d, Y') }}</span>
            </p>
        </div>
    </footer>

    <!-- Hidden Print Div -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5; margin-bottom: 5px;">User Management Report</h1>
                <p style="color: #666; margin-bottom: 10px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;">Summary Statistics</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #eef2ff; padding: 15px; border-radius: 8px; border: 1px solid #c7d2fe;">
                        <div style="font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px;">{{ $stats['admins'] }}</div>
                        <div style="font-size: 14px; color: #4f46e5;">Admins</div>
                    </div>
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 24px; font-weight: bold; color: #d97706; margin-bottom: 5px;">{{ $stats['registrars'] }}</div>
                        <div style="font-size: 14px; color: #d97706;">Registrars</div>
                    </div>
                    <div style="background: #dcfce7; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0;">
                        <div style="font-size: 24px; font-weight: bold; color: #059669; margin-bottom: 5px;">{{ $stats['teachers'] }}</div>
                        <div style="font-size: 14px; color: #059669;">Teachers</div>
                    </div>
                    <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd;">
                        <div style="font-size: 24px; font-weight: bold; color: #0284c7; margin-bottom: 5px;">{{ $stats['students'] }}</div>
                        <div style="font-size: 14px; color: #0284c7;">Students</div>
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
                        $roleName = $roleNames[$user->role] ?? 'Unknown';
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
                    Generated by: {{ Auth::user()->f_name }} {{ Auth::user()->l_name }} | 
                    Page 1 of {{ $users->lastPage() }}
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Add to existing CSS */
    .btn-icon.approve.loading {
        pointer-events: none;
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-icon.approve.loading i.fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    /* All existing styles remain the same... */
    /* Just adding the improved button styles */
    
    .btn-icon.approve {
        color: #10b981;
        background: #dcfce7;
    }
    
    .btn-icon.approve:hover {
        background: #bbf7d0;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    
    .btn-icon.approve:active {
        transform: translateY(0);
    }
    
    .btn-icon.approve.loading {
        pointer-events: none;
        opacity: 0.6;
    }
    
    .btn-icon.approve.loading i {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Improved status badge animation */
    .status-badge {
        transition: all 0.3s ease;
    }
    
    .status-approved {
        background: #dcfce7;
        color: #166534;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Color Variables - Same as dashboard */
    :root {
        --primary: #4f46e5;
        --primary-light: #eef2ff;
        --primary-dark: #3730a3;
        
        --success: #10b981;
        --success-light: #d1fae5;
        --success-dark: #059669;
        
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --warning-dark: #d97706;
        
        --info: #06b6d4;
        --info-light: #cffafe;
        --info-dark: #0891b2;
        
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --danger-dark: #dc2626;
        
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        
        --radius: 12px;
        --radius-sm: 8px;
        --radius-lg: 16px;
    }

    /* Dashboard Header - Same as dashboard */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }
    
    .user-greeting {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    
    .user-avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        border: 3px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }
    
    .greeting-text {
        color: white;
    }
    
    .welcome-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: white;
    }
    
    .welcome-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .separator {
        opacity: 0.5;
    }
    
    .pending-notice {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .header-alert {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .alert-badge {
        position: relative;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }
    
    .badge-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--danger);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        border: 2px solid var(--primary);
    }
    
    .alert-text {
        color: white;
    }
    
    .alert-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .alert-subtitle {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* Stats Grid - Same as dashboard */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-200);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-content {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-meta {
        font-size: 0.875rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .stat-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }
    
    .stat-primary .stat-icon-wrapper {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .stat-warning .stat-icon-wrapper {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .stat-success .stat-icon-wrapper {
        background: var(--success-light);
        color: var(--success);
    }
    
    .stat-info .stat-icon-wrapper {
        background: var(--info-light);
        color: var(--info);
    }
    
    .stat-footer {
        background: var(--gray-50);
        padding: 0.75rem 1.5rem;
        border-top: 1px solid var(--gray-200);
    }
    
    .stat-link {
        color: var(--gray-700);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .stat-link:hover {
        color: var(--primary);
        gap: 0.75rem;
    }

    /* Content Grid - Same as dashboard */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Dashboard Cards - Same as dashboard */
    .dashboard-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header-modern {
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
        font-size: 1.25rem;
        color: var(--primary);
    }
    
    .card-title-modern {
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
    
    .card-body-modern {
        padding: 1.5rem;
    }
    
    .card-footer-modern {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Filters Section */
    .filters-section {
        margin-bottom: 1.5rem;
    }
    
    .search-container {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .search-container i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: white;
    }
    
    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }
    
    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 0.5rem 1rem;
        background: var(--gray-100);
        color: var(--gray-700);
        border: none;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .filter-btn:hover {
        background: var(--gray-200);
        transform: translateY(-1px);
    }
    
    .filter-btn.active {
        background: var(--primary);
        color: white;
    }

    /* Empty States - Same as dashboard */
    .empty-state-modern {
        text-align: center;
        padding: 3rem 1.5rem;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: var(--gray-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--gray-400);
    }
    
    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 0.5rem 0;
    }
    
    .empty-text {
        font-size: 1rem;
        color: var(--gray-600);
        margin: 0 0 0.25rem 0;
    }
    
    .empty-subtext {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin: 0;
    }

    /* Items List - Same as dashboard */
    .items-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
    }
    
    .list-item:hover {
        background: white;
        box-shadow: var(--shadow-md);
        transform: translateX(4px);
    }
    
    .item-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    
    .item-details {
        flex: 1;
        min-width: 0;
    }
    
    .item-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 1rem;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .pending-badge {
        background: var(--warning-light);
        color: var(--warning-dark);
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .item-subtitle {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .item-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .role-badge,
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .role-admin {
        background: var(--danger-light);
        color: var(--danger-dark);
    }
    
    .role-registrar {
        background: var(--info-light);
        color: var(--info-dark);
    }
    
    .role-teacher {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    .role-student {
        background: var(--primary-light);
        color: var(--primary-dark);
    }
    
    .time-badge {
        background: var(--gray-100);
        color: var(--gray-700);
    }
    
    .item-actions {
        flex-shrink: 0;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-approve,
    .btn-delete,
    .btn-view {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-approve {
        background: var(--success-light);
        color: var(--success);
    }
    
    .btn-approve:hover {
        background: var(--success);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-delete {
        background: var(--danger-light);
        color: var(--danger);
    }
    
    .btn-delete:hover {
        background: var(--danger);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-view {
        background: var(--gray-100);
        color: var(--gray-700);
        text-decoration: none;
    }
    
    .btn-view:hover {
        background: var(--gray-200);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-view-primary:hover {
        background: var(--primary);
        color: white;
    }
    
    .btn-view-warning:hover {
        background: var(--warning);
        color: white;
    }
    
    .inline-form {
        display: inline;
    }

    /* Pagination */
    .pagination-info {
        font-size: 0.875rem;
        color: var(--gray-600);
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
        background: var(--gray-100);
        color: var(--gray-700);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    
    .pagination-btn:hover:not(.disabled):not(.active) {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .pagination-btn.active {
        background: var(--primary);
        color: white;
    }
    
    .pagination-btn.disabled {
        background: var(--gray-200);
        color: var(--gray-400);
        cursor: not-allowed;
    }

    /* Quick Actions - Same as dashboard */
    .quick-actions-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: all 0.2s ease;
        border: 2px solid;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }
    
    .action-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }
    
    .action-primary {
        background: var(--primary-light);
        border-color: var(--primary);
    }
    
    .action-primary:hover {
        background: var(--primary);
    }
    
    .action-primary:hover .action-title,
    .action-primary:hover .action-subtitle,
    .action-primary:hover .action-icon,
    .action-primary:hover .action-arrow {
        color: white;
    }
    
    .action-success {
        background: var(--success-light);
        border-color: var(--success);
    }
    
    .action-success:hover {
        background: var(--success);
    }
    
    .action-success:hover .action-title,
    .action-success:hover .action-subtitle,
    .action-success:hover .action-icon,
    .action-success:hover .action-arrow {
        color: white;
    }
    
    .action-warning {
        background: var(--warning-light);
        border-color: var(--warning);
    }
    
    .action-warning:hover {
        background: var(--warning);
    }
    
    .action-warning:hover .action-title,
    .action-warning:hover .action-subtitle,
    .action-warning:hover .action-icon,
    .action-warning:hover .action-arrow {
        color: white;
    }
    
    .action-info {
        background: var(--info-light);
        border-color: var(--info);
    }
    
    .action-info:hover {
        background: var(--info);
    }
    
    .action-info:hover .action-title,
    .action-info:hover .action-subtitle,
    .action-info:hover .action-icon,
    .action-info:hover .action-arrow {
        color: white;
    }
    
    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-icon {
        color: var(--primary);
    }
    
    .action-success .action-icon {
        color: var(--success);
    }
    
    .action-warning .action-icon {
        color: var(--warning);
    }
    
    .action-info .action-icon {
        color: var(--info);
    }
    
    .action-content {
        flex: 1;
    }
    
    .action-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-title {
        color: var(--primary-dark);
    }
    
    .action-success .action-title {
        color: var(--success-dark);
    }
    
    .action-warning .action-title {
        color: var(--warning-dark);
    }
    
    .action-info .action-title {
        color: var(--info-dark);
    }
    
    .action-subtitle {
        font-size: 0.875rem;
        color: var(--gray-600);
        transition: all 0.2s ease;
    }
    
    .action-arrow {
        font-size: 1.125rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-arrow {
        color: var(--primary);
    }
    
    .action-success .action-arrow {
        color: var(--success);
    }
    
    .action-warning .action-arrow {
        color: var(--warning);
    }
    
    .action-info .action-arrow {
        color: var(--info);
    }

    /* Overview List - Same as dashboard */
    .overview-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .overview-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
    }
    
    .overview-item:hover {
        background: white;
        box-shadow: var(--shadow);
    }
    
    .overview-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .overview-details {
        flex: 1;
    }
    
    .overview-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .overview-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }
    
    .overview-trend {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    
    .trend-up {
        background: var(--success-light);
        color: var(--success);
    }
    
    .trend-down {
        background: var(--danger-light);
        color: var(--danger);
    }
    
    .trend-warning {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .trend-neutral {
        background: var(--gray-100);
        color: var(--gray-500);
    }

    /* Footer - Same as dashboard */
    .dashboard-footer {
        background: white;
        border-top: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: var(--shadow-sm);
    }
    
    .footer-content {
        text-align: center;
    }
    
    .footer-text {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0 0 0.5rem 0;
    }
    
    .footer-meta {
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin: 0;
    }
    
    /* Responsive Design - Same as dashboard */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-avatar-large {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 1.875rem;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .footer-meta {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .separator {
            display: none;
        }
        
        .action-buttons {
            flex-wrap: wrap;
        }
        
        .filter-buttons {
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
        
        .filter-btn {
            white-space: nowrap;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-users');
        const userItems = document.querySelectorAll('.user-item');
        
        if (searchInput && userItems.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                userItems.forEach(item => {
                    const userName = item.dataset.name || '';
                    const userEmail = item.dataset.email || '';
                    
                    if (searchTerm === '' || userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
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
            const userItems = document.querySelectorAll('.user-item');
            const csv = [];
            
            // Add headers
            const headers = ['Name', 'Email', 'Role', 'Status', 'Join Date', 'User ID'];
            csv.push(headers.join(','));
            
            // Add data rows
            userItems.forEach(item => {
                const cells = [];
                
                // Name (from title div)
                const nameDiv = item.querySelector('.item-title');
                if (nameDiv) {
                    const nameText = nameDiv.textContent.replace('Pending', '').trim();
                    cells.push(`"${nameText}"`);
                }
                
                // Email (from subtitle div)
                const emailDiv = item.querySelector('.item-subtitle');
                if (emailDiv) {
                    cells.push(`"${emailDiv.textContent.trim()}"`);
                }
                
                // Role (from role badge)
                const roleSpan = item.querySelector('.role-badge');
                if (roleSpan) {
                    // Get just the role name (remove icon)
                    const roleText = roleSpan.textContent.trim().split(' ').slice(1).join(' ');
                    cells.push(`"${roleText}"`);
                }
                
                // Status (check for pending badge)
                const pendingBadge = item.querySelector('.pending-badge');
                cells.push(`"${pendingBadge ? 'Pending' : 'Approved'}"`);
                
                // Join Date (from time badge)
                const timeSpan = item.querySelector('.time-badge');
                if (timeSpan) {
                    const timeText = timeSpan.textContent.replace('Joined ', '');
                    cells.push(`"${timeText}"`);
                }
                
                // User ID
                const userId = item.dataset.userId || '';
                cells.push(`"${userId}"`);
                
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
        });

        // Loading state for approve buttons
        document.querySelectorAll('form.inline-form').forEach(form => {
            if (form.querySelector('.btn-approve')) {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('.btn-approve');
                    const originalHTML = button.innerHTML;
                    
                    // Add loading spinner
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;
                    button.style.opacity = '0.7';
                    button.style.cursor = 'not-allowed';
                    
                    // Revert after 2 seconds (in case something goes wrong)
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.disabled = false;
                        button.style.opacity = '1';
                        button.style.cursor = 'pointer';
                    }, 2000);
                });
            }
        });
    });
</script>
@endpush