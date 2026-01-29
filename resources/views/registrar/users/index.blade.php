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
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalTeachers }}</div>
                <div class="stat-label">Total Teachers</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalStudents }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $pendingApprovals }}</div>
                <div class="stat-label">Pending Approvals</div>
            </div>
            <div class="stat-icon icon-pending">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $approvedUsers }}</div>
                <div class="stat-label">Approved Users</div>
            </div>
            <div class="stat-icon icon-logins">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Users List Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">All Users</div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--secondary);"></i>
                    <input type="text" class="search-input" placeholder="Search users..." 
                           style="padding: 8px 12px 8px 36px; border: 1px solid var(--border); border-radius: 6px; width: 200px;">
                </div>
                <a href="{{ route('registrar.users.create') }}" class="view-all" style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-plus-circle"></i>
                    Add User
                </a>
            </div>
        </div>
        
        @if(session('success'))
        <div style="margin: 0 1.5rem 1.5rem; padding: 12px; background: #dcfce7; color: #065f46; border-radius: 8px; font-size: 0.875rem;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div style="margin: 0 1.5rem 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.875rem;">
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
            {{ session('error') }}
        </div>
        @endif

        @if($users->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3 style="color: var(--dark); margin-bottom: 12px;">No users yet</h3>
            <p style="color: var(--secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                You haven't added any teachers or students yet.
            </p>
            <a href="{{ route('registrar.users.create') }}" 
               style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-user-plus"></i>
                Add First User
            </a>
        </div>
        @else
        <!-- Users List -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;" id="users-table">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid var(--border);">
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Name</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Role</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Email</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">ID</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Status</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $user->f_name }} {{ $user->l_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @if($user->role == 3)
                            <span style="display: inline-block; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                Teacher
                            </span>
                            @else
                            <span style="display: inline-block; padding: 4px 12px; background: #e0f2fe; color: #075985; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                Student
                            </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <span style="color: var(--dark);">{{ $user->email }}</span>
                        </td>
                        <td style="padding: 16px;">
                            <span style="display: inline-block; padding: 4px 12px; background: #f3f4f6; color: var(--dark); border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                                {{ $user->role == 3 ? $user->employee_id : $user->student_id }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            @if($user->is_approved)
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #dcfce7; color: #166534; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                Approved
                            </span>
                            @else
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                <i class="fas fa-clock" style="font-size: 10px;"></i>
                                Pending
                            </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('registrar.users.show', Crypt::encrypt($user->id)) }}" 
                                title="View" style="padding: 8px; background: #e0e7ff; color: var(--primary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('registrar.users.edit', Crypt::encrypt($user->id)) }}" title="Edit" style="padding: 8px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$user->is_approved)
                                <form action="{{ route('registrar.users.approve', Crypt::encrypt($user->id)) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" title="Approve" 
                                            onclick="return confirm('Are you sure you want to approve this user?')"
                                            style="padding: 8px; background: #dcfce7; color: var(--success); border: none; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('registrar.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            style="padding: 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 6px; cursor: pointer;">
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
            </div>
            <div style="display: flex; gap: 8px;">
                @if($users->onFirstPage())
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Previous
                </span>
                @else
                <a href="{{ $users->previousPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Previous
                </a>
                @endif
                
                @foreach(range(1, min(5, $users->lastPage())) as $page)
                    @if($page == $users->currentPage())
                    <span style="padding: 8px 12px; background: var(--primary); color: white; border-radius: 6px; font-size: 0.875rem;">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $users->url($page) }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach
                
                @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Next
                </a>
                @else
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Next
                </span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('registrar.users.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Add New User</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Create teacher or student</div>
                    </div>
                </a>
                <button id="print-report" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s; border: none; background: transparent; width: 100%; cursor: pointer;">
                    <div style="width: 36px; height: 36px; background: #fce7f3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #db2777;">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Print/Export Users</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Print user list or export as CSV</div>
                    </div>
                </button>
                <button id="export-csv" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s; border: none; background: transparent; width: 100%; cursor: pointer;">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <i class="fas fa-file-csv"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Download CSV</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Export user data as CSV file</div>
                    </div>
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Filter Users</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 8px;">Role</div>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ request()->fullUrlWithQuery(['role' => '3']) }}" 
                           style="padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 6px; text-decoration: none; font-size: 0.75rem;">
                            Teachers
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['role' => '4']) }}" 
                           style="padding: 4px 12px; background: #e0f2fe; color: #075985; border-radius: 6px; text-decoration: none; font-size: 0.75rem;">
                            Students
                        </a>
                        <a href="{{ request()->url() }}" 
                           style="padding: 4px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none; font-size: 0.75rem;">
                            All
                        </a>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 8px;">Status</div>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" 
                           style="padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 6px; text-decoration: none; font-size: 0.75rem;">
                            Pending
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" 
                           style="padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 6px; text-decoration: none; font-size: 0.75rem;">
                            Approved
                        </a>
                        <a href="{{ request()->url() }}" 
                           style="padding: 4px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none; font-size: 0.75rem;">
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

<!-- Footer -->
<div class="footer">
    © {{ date('Y') }} ADSCO. All rights reserved. Version 1.0.0
</div>

@push('scripts')
<script>
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(1) div:nth-child(2) div:nth-child(1)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(3) span').textContent.toLowerCase();
                const id = row.querySelector('td:nth-child(4) span').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm) || id.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Print functionality
    document.getElementById('print-report')?.addEventListener('click', function() {
        // Get the print content
        const printContent = document.getElementById('print-content').innerHTML;
        
        // Create a new window for printing
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
        // Get table data
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
            const nameDiv = columns[0].querySelector('div:nth-child(2) div:nth-child(1)');
            cells.push(`"${nameDiv ? nameDiv.textContent.trim() : ''}"`);
            
            // Role
            const roleSpan = columns[1].querySelector('span');
            cells.push(`"${roleSpan ? roleSpan.textContent.trim() : ''}"`);
            
            // Email
            const emailSpan = columns[2].querySelector('span');
            cells.push(`"${emailSpan ? emailSpan.textContent.trim() : ''}"`);
            
            // ID
            const idSpan = columns[3].querySelector('span');
            cells.push(`"${idSpan ? idSpan.textContent.trim() : ''}"`);
            
            // Status
            const statusSpan = columns[4].querySelector('span');
            cells.push(`"${statusSpan ? statusSpan.textContent.trim() : ''}"`);
            
            // Created Date (add if available in your data)
            // For now, adding placeholder or we can add it if the data is available
            cells.push(`"{{ \Carbon\Carbon::now()->format('Y-m-d') }}"`);
            
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