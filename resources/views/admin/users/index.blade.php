@extends('layouts.admin')

@section('title', 'User Management - Admin Dashboard')

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
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.users.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number">{{ number_format($stats['total']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-link">
                View all users <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.users.index') }}?status=pending" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-number">{{ number_format($stats['pending']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-link">
                Review now <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.users.index') }}?role=3" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Teachers</div>
                    <div class="stat-number">{{ number_format($stats['teachers']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="stat-link">
                View teachers <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.users.index') }}?role=4" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Students</div>
                    <div class="stat-number">{{ number_format($stats['students']) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="stat-link">
                View students <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Users List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-users" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        All Users
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search users by name or email..." id="search-users">
                        </div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add User
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="filters-section">
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

                    <!-- Spacer between filters and user list -->
                    <div class="section-spacer"></div>

                    @if($users->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state">
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
                            <div class="user-list-item" data-name="{{ strtolower($user->f_name . ' ' . $user->l_name) }}" data-email="{{ strtolower($user->email) }}" data-user-id="{{ $user->id }}">
                                <a href="{{ route('admin.users.show', Crypt::encrypt($user->id)) }}" class="item-clickable-area">
                                    <div class="item-avatar">
                                        {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                    </div>
                                    <div class="item-info">
                                        <div class="item-name">{{ $user->f_name }} {{ $user->l_name }}
                                            @if(!$user->is_approved)
                                                <span class="pending-badge">Pending</span>
                                            @endif
                                        </div>
                                        <div class="item-details">{{ $user->email }}</div>
                                        <div class="item-meta">
                                            @php
                                                $roleName = $roleNames[$user->role] ?? 'Unknown';
                                                $roleClass = 'badge-' . strtolower($roleName);
                                            @endphp
                                            <span class="item-badge {{ $roleClass }}">
                                                <i class="fas fa-user-tag"></i> {{ $roleName }}
                                            </span>
                                            <span class="item-badge badge-secondary">
                                                <i class="fas fa-calendar"></i> {{ $user->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($users instanceof \Illuminate\Pagination\AbstractPaginator && $users->hasPages())
                <div class="card-footer">
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
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
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
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        User Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <a href="{{ route('admin.users.index') }}?role=1" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--danger-light), var(--danger));">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Admins</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $stats['admins'] }}</div>
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}?role=2" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Registrars</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $stats['registrars'] }}</div>
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}?status=pending" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Pending Approval</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem; color: var(--warning);">{{ $stats['pending'] }}</div>
                        </a>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">New This Month</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $stats['this_month'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            User Management • Updated {{ now()->format('M d, Y') }}
        </p>
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
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-index.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-users');
        const userItems = document.querySelectorAll('.user-list-item[data-name]');
        
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
            const userItems = document.querySelectorAll('.user-list-item[data-name]');
            const csv = [];
            
            // Add headers
            const headers = ['Name', 'Email', 'Role', 'Status', 'Join Date', 'User ID'];
            csv.push(headers.join(','));
            
            // Add data rows
            userItems.forEach(item => {
                const cells = [];
                
                // Name
                const nameDiv = item.querySelector('.item-name');
                if (nameDiv) {
                    const nameText = nameDiv.textContent.replace('Pending', '').trim();
                    cells.push(`"${nameText}"`);
                }
                
                // Email
                const emailDiv = item.querySelector('.item-details');
                if (emailDiv) {
                    cells.push(`"${emailDiv.textContent.trim()}"`);
                }
                
                // Role (from role badge)
                const roleSpan = item.querySelector('.item-badge:first-child');
                if (roleSpan) {
                    const roleText = roleSpan.textContent.trim().split(' ').slice(1).join(' ');
                    cells.push(`"${roleText}"`);
                }
                
                // Status (check for pending badge)
                const pendingBadge = item.querySelector('.pending-badge');
                cells.push(`"${pendingBadge ? 'Pending' : 'Approved'}"`);
                
                // Join Date (from time badge)
                const timeSpan = item.querySelector('.item-badge.badge-secondary');
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

    });
</script>
@endpush