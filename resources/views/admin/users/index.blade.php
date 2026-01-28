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
    <div class="card">
        <div class="card-header">
            <div class="card-title">All Users</div>
            <div class="d-flex gap-2 align-items-center">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--secondary);"></i>
                    <input type="text" class="search-input" placeholder="Search users..." 
                           style="padding: 8px 12px 8px 36px; border: 1px solid var(--border); border-radius: 6px; width: 200px;">
                </div>
                <select style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; color: var(--secondary); font-size: 0.875rem;">
                    <option value="">All Roles</option>
                    <option value="1">Admin</option>
                    <option value="2">Registrar</option>
                    <option value="3">Teacher</option>
                    <option value="4">Student</option>
                </select>
                <a href="{{ route('admin.users.create') }}" class="view-all" style="display: flex; align-items: center; gap: 6px;">
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

        @if($users->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3 style="color: var(--dark); margin-bottom: 12px;">No users yet</h3>
            <p style="color: var(--secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                You haven't added any users yet. Start by adding the first user.
            </p>
            <a href="{{ route('admin.users.create') }}" 
               style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-user-plus"></i>
                Add First User
            </a>
        </div>
        @else
        <!-- Users List -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid var(--border);">
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">
                            Name
                        </th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Email</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Role</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Status</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Joined</th>
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
                                    <div style="font-size: 0.75rem; color: var(--secondary);">ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <span style="color: var(--dark);">{{ $user->email }}</span>
                        </td>
                        <td style="padding: 16px;">
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
                            <span style="display: inline-block; padding: 4px 12px; background: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                {{ $roleName }}
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
                            <div style="font-weight: 500; color: var(--dark);">{{ $user->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.users.show', Crypt::encrypt($user->id)) }}" title="View" style="padding: 8px; background: #e0e7ff; color: var(--primary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" title="Edit" style="padding: 8px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$user->is_approved)
                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" title="Approve" 
                                            onclick="return confirm('Are you sure you want to approve this user?')"
                                            style="padding: 8px; background: #dcfce7; color: var(--success); border: none; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" method="POST" style="display: inline;">
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
                <a href="{{ route('admin.users.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Add New User</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Create a new user account</div>
                    </div>
                </a>
                <a href="#" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #fce7f3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #db2777;">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Export Users</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Download as CSV</div>
                    </div>
                </a>
                <a href="#" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Bulk Actions</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Manage multiple users</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">User Statistics</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Users Created This Month</span>
                        <span style="font-weight: 600;">{{ \App\Models\User::whereMonth('created_at', now()->month)->count() }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Pending Approvals</span>
                        <span style="font-weight: 600;">{{ \App\Models\User::where('is_approved', false)->count() }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Active Users (Last 30 days)</span>
                        <span style="font-weight: 600;">0</span>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Average Registrations/Day</span>
                        <span style="font-weight: 600;">
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
</div>

<!-- Footer -->
<div class="footer">
    © {{ date('Y') }} ADSCO. All rights reserved. Version 1.0.0
</div>

@push('scripts')
<script>
    // Simple search functionality
    document.querySelector('.search-input')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const userName = row.querySelector('td:nth-child(1) .text-dark')?.textContent.toLowerCase() || '';
            const userEmail = row.querySelector('td:nth-child(2) span')?.textContent.toLowerCase() || '';
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection