@extends('layouts.admin')

@section('title', 'Audit Logs - Admin Dashboard')

@push('styles')
<style>
    /* Additional styles specific to audit logs page */
    .icon-total { background: #e0e7ff; color: var(--primary); }
    .icon-today { background: #dcfce7; color: var(--success); }
    .icon-users { background: #fce7f3; color: #db2777; }
    .icon-system { background: #fef3c7; color: var(--warning); }
    
    /* Activity Timeline */
    .activity-timeline {
        position: relative;
        padding-left: 20px;
    }
    
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }
    
    .activity-item {
        position: relative;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .activity-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .activity-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--primary);
        border: 2px solid white;
        z-index: 1;
    }
    
    .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        margin-right: 1rem;
    }
    
    .icon-login { background: #dcfce7; color: var(--success); }
    .icon-user { background: #e0e7ff; color: var(--primary); }
    .icon-system { background: #fef3c7; color: var(--warning); }
    .icon-delete { background: #fee2e2; color: var(--danger); }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-title {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }
    
    .activity-description {
        color: var(--secondary);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .activity-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: #9ca3af;
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@php
    // Helper functions for the view
    function getActionIcon($action) {
        $icons = [
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'create' => 'plus-circle',
            'update' => 'edit',
            'delete' => 'trash',
            'approve' => 'check-circle',
            'reject' => 'times-circle'
        ];
        return $icons[$action] ?? 'history';
    }
    
    function getActionColor($action) {
        $colors = [
            'login' => ['bg' => '#dcfce7', 'text' => '#166534'],
            'logout' => ['bg' => '#f3f4f6', 'text' => '#6b7280'],
            'create' => ['bg' => '#e0f2fe', 'text' => '#075985'],
            'update' => ['bg' => '#fef3c7', 'text' => '#92400e'],
            'delete' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
            'approve' => ['bg' => '#dcfce7', 'text' => '#166534'],
            'reject' => ['bg' => '#fee2e2', 'text' => '#991b1b']
        ];
        return $colors[$action] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];
    }
@endphp

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Audit Logs</h1>
        <p>System activity and user actions history</p>
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
        $totalLogs = $logs->total();
        $todayLogs = \App\Models\AuditLog::whereDate('created_at', today())->count();
        $userLogs = \App\Models\AuditLog::whereNotNull('user_id')->count();
        $systemLogs = \App\Models\AuditLog::whereNull('user_id')->count();
    @endphp
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalLogs }}</div>
                <div class="stat-label">Total Logs</div>
            </div>
            <div class="stat-icon icon-total">
                <i class="fas fa-history"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-database" style="margin-right: 4px;"></i> All time records
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $todayLogs }}</div>
                <div class="stat-label">Today</div>
            </div>
            <div class="stat-icon icon-today">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-clock" style="margin-right: 4px;"></i> Last 24 hours
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $userLogs }}</div>
                <div class="stat-label">User Actions</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-user-cog"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-users" style="margin-right: 4px;"></i> By users
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $systemLogs }}</div>
                <div class="stat-label">System Events</div>
            </div>
            <div class="stat-icon icon-system">
                <i class="fas fa-server"></i>
            </div>
        </div>
        <div style="color: var(--secondary); font-size: 0.75rem;">
            <i class="fas fa-cog" style="margin-right: 4px;"></i> Automated
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Audit Logs Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Activity History</div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <select style="padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; color: var(--secondary); font-size: 0.875rem; width: 150px;" id="actionFilter">
                    <option value="">All Actions</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="create">Create</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                </select>
                <select style="padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; color: var(--secondary); font-size: 0.875rem; width: 150px;" id="userFilter">
                    <option value="">All Users</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->f_name }} {{ $user->l_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        @if($logs->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <h3 style="color: var(--dark); margin-bottom: 12px;">No Audit Logs</h3>
            <p style="color: var(--secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                No activity records found in the system.
            </p>
        </div>
        @else
        <!-- Activity Timeline -->
        <div class="activity-timeline">
            @foreach($logs as $log)
            <div class="activity-item" style="display: flex;">
                <div class="activity-icon {{ $log->action == 'login' ? 'icon-login' : ($log->action == 'delete' ? 'icon-delete' : 'icon-user') }}">
                    <i class="fas fa-{{ getActionIcon($log->action) }}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">
                        @if($log->user)
                            {{ $log->user->f_name }} {{ $log->user->l_name }}
                        @else
                            System
                        @endif
                        @php
                            $color = getActionColor($log->action);
                        @endphp
                        <span style="display: inline-block; padding: 2px 8px; background: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 12px; font-size: 0.75rem; font-weight: 500; margin-left: 8px;">
                            {{ ucfirst($log->action) }}
                        </span>
                    </div>
                    <div class="activity-description">
                        {{ $log->description ?? $log->action . ' action performed' }}
                    </div>
                    <div class="activity-meta">
                        <span>
                            <i class="fas fa-clock" style="margin-right: 4px;"></i>
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                        <span>
                            <i class="fas fa-calendar" style="margin-right: 4px;"></i>
                            {{ $log->created_at->format('M d, Y h:i A') }}
                        </span>
                        @if($log->ip_address)
                        <span>
                            <i class="fas fa-network-wired" style="margin-right: 4px;"></i>
                            {{ $log->ip_address }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($logs instanceof \Illuminate\Pagination\AbstractPaginator)
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
            </div>
            <div style="display: flex; gap: 8px;">
                @if($logs->onFirstPage())
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Previous
                </span>
                @else
                <a href="{{ $logs->previousPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Previous
                </a>
                @endif
                
                @foreach(range(1, min(5, $logs->lastPage())) as $page)
                    @if($page == $logs->currentPage())
                    <span style="padding: 8px 12px; background: var(--primary); color: white; border-radius: 6px; font-size: 0.875rem;">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $logs->url($page) }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach
                
                @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
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
                <button onclick="clearOldLogs()" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s; border: none; background: transparent; width: 100%; cursor: pointer;">
                    <div style="width: 36px; height: 36px; background: #fee2e2; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--danger);">
                        <i class="fas fa-trash"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Clear Old Logs</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Remove logs older than 30 days</div>
                    </div>
                </button>
                <a href="#" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #fce7f3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #db2777;">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Export Logs</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Download as CSV</div>
                    </div>
                </a>
                <a href="#" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Advanced Search</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Filter with multiple criteria</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Log Statistics</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Most Active User</span>
                        <span style="font-weight: 600;">
                            @php
                                try {
                                    // Simple query without withCount for now
                                    $mostActiveUser = \App\Models\User::orderBy('id', 'desc')->first();
                                    echo $mostActiveUser ? $mostActiveUser->f_name : '-';
                                } catch (\Exception $e) {
                                    echo '-';
                                }
                            @endphp
                        </span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Most Common Action</span>
                        <span style="font-weight: 600;">
                            @php
                                try {
                                    $commonAction = \App\Models\AuditLog::select('action')
                                        ->selectRaw('COUNT(*) as count')
                                        ->groupBy('action')
                                        ->orderBy('count', 'desc')
                                        ->first();
                                    echo $commonAction ? ucfirst($commonAction->action) : '-';
                                } catch (\Exception $e) {
                                    echo '-';
                                }
                            @endphp
                        </span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Logs This Week</span>
                        <span style="font-weight: 600;">
                            @php
                                try {
                                    echo \App\Models\AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                                } catch (\Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </span>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Average Daily Logs</span>
                        <span style="font-weight: 600;">
                            @php
                                try {
                                    $firstLog = \App\Models\AuditLog::oldest()->first();
                                    if ($firstLog && $totalLogs > 0) {
                                        $days = max(1, $firstLog->created_at->diffInDays(now()));
                                        $avg = $totalLogs / $days;
                                        echo round($avg, 1);
                                    } else {
                                        echo '0';
                                    }
                                } catch (\Exception $e) {
                                    echo '0';
                                }
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
    // Filter functionality
    document.getElementById('actionFilter')?.addEventListener('change', function() {
        const action = this.value;
        const userId = document.getElementById('userFilter')?.value;
        let url = new URL(window.location.href);
        
        if (action) {
            url.searchParams.set('action', action);
        } else {
            url.searchParams.delete('action');
        }
        
        if (userId) {
            url.searchParams.set('user_id', userId);
        }
        
        window.location.href = url.toString();
    });
    
    document.getElementById('userFilter')?.addEventListener('change', function() {
        const userId = this.value;
        const action = document.getElementById('actionFilter')?.value;
        let url = new URL(window.location.href);
        
        if (userId) {
            url.searchParams.set('user_id', userId);
        } else {
            url.searchParams.delete('user_id');
        }
        
        if (action) {
            url.searchParams.set('action', action);
        }
        
        window.location.href = url.toString();
    });
    
    // Clear old logs confirmation
    function clearOldLogs() {
        if (confirm('Are you sure you want to clear logs older than 30 days? This action cannot be undone.')) {
            // Use direct URL instead of named route
            fetch('/admin/audit-logs/clear-old', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Old logs cleared successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error clearing logs: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                showNotification('Network error. Please try again.', 'error');
            });
        }
    }
    
    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        `;
        
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Add animation keyframes
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
@endsection