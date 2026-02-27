@extends('layouts.admin')

@section('title', 'User Management - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Dashboard Header — consistent with dashboard.css -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->l_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">User Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-users"></i> Manage all system users and permissions
                        <span class="separator">•</span>
                        <span class="pending-notice">
                            <i class="fas fa-users"></i> {{ $stats['total'] }} users · {{ $stats['pending'] }} pending
                        </span>
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.users.create') }}" class="top-action-btn">
                    <i class="fas fa-plus-circle"></i> Add User
                </a>
                <a href="{{ route('admin.users.index') }}?status=pending" class="top-action-btn">
                    <i class="fas fa-user-clock"></i> Pending ({{ $stats['pending'] }})
                </a>
            </div>
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
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-link">View all users <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.users.index') }}?status=pending" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending Approval</div>
                    <div class="stat-number">{{ number_format($stats['pending']) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-link">Review now <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.users.index') }}?role=3" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Teachers</div>
                    <div class="stat-number">{{ number_format($stats['teachers']) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            </div>
            <div class="stat-link">View teachers <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.users.index') }}?role=4" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Students</div>
                    <div class="stat-number">{{ number_format($stats['students']) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            </div>
            <div class="stat-link">View students <i class="fas fa-arrow-right"></i></div>
        </a>
    </div>

    <!-- Users List — full width -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-users" style="color: var(--primary); margin-right: 0.5rem;"></i>
                All Users
            </h2>
            <div class="header-actions-bar">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search users by name or email..." id="search-users">
                </div>
                <div class="filter-container">
                    <select class="form-select" id="role-filter">
                        <option value="">All Roles</option>
                        <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Admin</option>
                        <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Registrar</option>
                        <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Teacher</option>
                        <option value="4" {{ request('role') == '4' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>
                <div class="filter-container">
                    <select class="form-select" id="status-filter">
                        <option value="">All Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <button id="print-report" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
                <button id="export-csv" class="btn btn-secondary">
                    <i class="fas fa-file-csv"></i> Export
                </button>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add User
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
            @if(session('warning'))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            </div>
            @endif
            @if(session('info'))
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
            </div>
            @endif

            @if($users->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-users"></i></div>
                    <h3 class="empty-title">No users found</h3>
                    <p class="empty-text">No users match your current filters.</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Your First User
                    </a>
                    <div class="empty-hint">
                        <i class="fas fa-lightbulb"></i> Try adjusting your search or filters
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="users-table" id="users-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th class="hide-on-mobile">Email</th>
                                <th class="hide-on-tablet">Role</th>
                                <th class="hide-on-tablet">Status</th>
                                <th class="hide-on-tablet">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            @php
                                try { 
                                    $encryptedId = Crypt::encrypt($user->id); 
                                } catch (\Exception $e) { 
                                    $encryptedId = ''; 
                                }
                                
                                $roleClass = '';
                                $roleIcon = '';
                                
                                switch($user->role) {
                                    case 1:
                                        $roleClass = 'badge-admin';
                                        $roleIcon = 'fa-user-cog';
                                        $roleName = 'Admin';
                                        break;
                                    case 2:
                                        $roleClass = 'badge-registrar';
                                        $roleIcon = 'fa-user-tie';
                                        $roleName = 'Registrar';
                                        break;
                                    case 3:
                                        $roleClass = 'badge-teacher';
                                        $roleIcon = 'fa-chalkboard-teacher';
                                        $roleName = 'Teacher';
                                        break;
                                    case 4:
                                        $roleClass = 'badge-student';
                                        $roleIcon = 'fa-user-graduate';
                                        $roleName = 'Student';
                                        break;
                                    default:
                                        $roleClass = 'badge-unknown';
                                        $roleIcon = 'fa-user';
                                        $roleName = 'Unknown';
                                }
                            @endphp
                            <tr class="clickable-row"
                                data-href="{{ $encryptedId ? route('admin.users.show', $encryptedId) : '#' }}"
                                data-name="{{ strtolower($user->f_name . ' ' . $user->l_name) }}"
                                data-email="{{ strtolower($user->email) }}"
                                data-role="{{ $user->role }}"
                                data-status="{{ $user->is_approved ? 'approved' : 'pending' }}"
                                data-user-id="{{ $user->id }}"
                                data-encrypted="{{ $encryptedId }}">
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-icon user-{{ ($loop->index % 3) + 1 }}">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $user->f_name }} {{ $user->l_name }}</div>
                                            @if($user->email)
                                            <div class="user-email-mobile hide-on-desktop">{{ $user->email }}</div>
                                            @endif
                                            <div class="user-mobile-info">
                                                <div class="role-mobile">
                                                    <i class="fas {{ $roleIcon }}"></i> {{ $roleName }}
                                                </div>
                                                @if(!$user->is_approved)
                                                <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                                @else
                                                <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Approved</span>
                                                @endif
                                                @if(is_null($user->email_verified_at))
                                                <span class="item-badge badge-info"><i class="fas fa-envelope"></i> Unverified</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-on-mobile">
                                    <span class="user-email">{{ $user->email }}</span>
                                    @if(is_null($user->email_verified_at))
                                    <span class="email-unverified-badge">Unverified</span>
                                    @endif
                                </td>
                                <td class="hide-on-tablet">
                                    <span class="item-badge {{ $roleClass }}">
                                        <i class="fas {{ $roleIcon }}"></i> {{ $roleName }}
                                    </span>
                                </td>
                                <td class="hide-on-tablet">
                                    @if($user->is_approved)
                                        <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Approved</span>
                                    @else
                                        <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                    @endif
                                </td>
                                <td class="hide-on-tablet">
                                    <span class="item-date">{{ $user->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

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

                @foreach(range(1, $users->lastPage()) as $page)
                    @if($page == $users->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @elseif(abs($page - $users->currentPage()) <= 2)
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

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            User Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>

    <!-- Hidden Print Content -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5;">ADSCO User Management Report</h1>
                <p style="color: #666;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;">User Statistics Summary</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #eef2ff; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #4f46e5;">{{ $stats['total'] }}</div>
                        <div style="font-size: 14px; color: #4f46e5;">Total Users</div>
                    </div>
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #d97706;">{{ $stats['admins'] }}</div>
                        <div style="font-size: 14px; color: #d97706;">Admins</div>
                    </div>
                    <div style="background: #dbeafe; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $stats['registrars'] }}</div>
                        <div style="font-size: 14px; color: #2563eb;">Registrars</div>
                    </div>
                    <div style="background: #dcfce7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #059669;">{{ $stats['teachers'] }}</div>
                        <div style="font-size: 14px; color: #059669;">Teachers</div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div style="background: #e0f2fe; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 20px; font-weight: bold; color: #0284c7;">{{ $stats['students'] }}</div>
                        <div style="font-size: 14px; color: #0284c7;">Students</div>
                    </div>
                    <div style="background: #f1f5f9; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 20px; font-weight: bold; color: #d97706;">{{ $stats['pending'] }}</div>
                        <div style="font-size: 14px; color: #d97706;">Pending</div>
                    </div>
                    <div style="background: #f1f5f9; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 20px; font-weight: bold; color: #059669;">{{ $stats['unverified'] }}</div>
                        <div style="font-size: 14px; color: #059669;">Unverified</div>
                    </div>
                </div>
            </div>
            
            <h2 style="color: #333; margin-bottom: 15px;">User List</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Name</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Email</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Role</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Verified</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Joined</th>
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
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->is_approved ? 'Approved' : 'Pending' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Clickable rows
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function (e) {
            if (e.target.closest('a, button')) return;
            const href = this.dataset.href;
            if (href && href !== '#') window.location.href = href;
        });
    });

    // Search + filters
    const searchInput = document.getElementById('search-users');
    const roleFilter = document.getElementById('role-filter');
    const statusFilter = document.getElementById('status-filter');

    function filterRows() {
        const term = searchInput?.value.toLowerCase() ?? '';
        const role = roleFilter?.value ?? '';
        const status = statusFilter?.value ?? '';
        
        document.querySelectorAll('.clickable-row').forEach(row => {
            const matchSearch = !term || 
                row.dataset.name.includes(term) || 
                row.dataset.email.includes(term);
            const matchRole = !role || row.dataset.role === role;
            const matchStatus = !status || row.dataset.status === status;
            
            row.style.display = matchSearch && matchRole && matchStatus ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterRows);
    
    // Role filter — server-side redirect
    roleFilter?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        if (this.value) {
            url.searchParams.set('role', this.value);
        } else {
            url.searchParams.delete('role');
        }
        // Preserve status filter if exists
        if (statusFilter?.value) {
            url.searchParams.set('status', statusFilter.value);
        } else {
            url.searchParams.delete('status');
        }
        window.location.href = url.toString();
    });

    // Status filter — server-side redirect
    statusFilter?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        if (this.value) {
            url.searchParams.set('status', this.value);
        } else {
            url.searchParams.delete('status');
        }
        // Preserve role filter if exists
        if (roleFilter?.value) {
            url.searchParams.set('role', roleFilter.value);
        } else {
            url.searchParams.delete('role');
        }
        window.location.href = url.toString();
    });

    // Print functionality
    document.getElementById('print-report')?.addEventListener('click', function () {
        const content = document.getElementById('print-content').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>User Report</title>
            <style>
                body{font-family:Arial,sans-serif;padding:20px;} 
                table{width:100%;border-collapse:collapse;} 
                th{background:#f3f4f6;}
                @media print {
                    @page { size: landscape; margin: 0.5in; }
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
            </head><body>${content}<script>
                window.onload=()=>{ window.print(); setTimeout(()=>window.close(), 100); }
            <\/script></body></html>`);
        win.document.close();
    });

    // Export CSV
    document.getElementById('export-csv')?.addEventListener('click', function () {
        const rows = [['Name', 'Email', 'Role', 'Status', 'Verified', 'Joined']];
        document.querySelectorAll('#users-table tbody tr').forEach(row => {
            if (row.style.display === 'none') return;
            const cols = row.querySelectorAll('td');
            
            // Get role text
            let roleText = 'Unknown';
            const roleBadge = cols[2]?.querySelector('.item-badge');
            if (roleBadge) {
                roleText = roleBadge.textContent.trim();
            }
            
            // Get status
            let status = 'Unknown';
            const statusBadge = cols[3]?.querySelector('.item-badge');
            if (statusBadge) {
                status = statusBadge.textContent.trim();
            }
            
            // Get verified status
            const emailCell = cols[1]?.querySelector('.user-email');
            const hasUnverifiedBadge = cols[1]?.querySelector('.email-unverified-badge');
            const verified = hasUnverifiedBadge ? 'No' : 'Yes';
            
            rows.push([
                cols[0]?.querySelector('.user-name')?.textContent.trim() ?? '',
                emailCell?.textContent.trim() ?? '',
                roleText,
                status,
                verified,
                cols[4]?.querySelector('.item-date')?.textContent.trim() ?? '',
            ].map(v => `"${v.replace(/"/g, '""')}"`));
        });
        const blob = new Blob(['\uFEFF' + rows.map(r => r.join(',')).join('\n')], { type: 'text/csv;charset=utf-8;' });
        const a = Object.assign(document.createElement('a'), { 
            href: URL.createObjectURL(blob), 
            download: `users_${new Date().toISOString().slice(0,10)}.csv` 
        });
        document.body.appendChild(a); a.click(); a.remove();
    });

});
</script>
@endpush