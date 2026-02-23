@extends('layouts.student')

@section('title', 'My Assignments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-index.css') }}">
<style>
    :root {
        --primary: #f59e0b;
        --primary-dark: #d97706;
        --primary-light: rgba(245, 158, 11, 0.1);
    }
    
    .assignment-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending {
        background: #fff3e0;
        color: #c05621;
    }
    
    .status-submitted {
        background: #e6fffa;
        color: #2c7a7b;
    }
    
    .status-graded {
        background: #f0fff4;
        color: #22543d;
    }
    
    .status-late {
        background: #fff5f5;
        color: #c53030;
    }
    
    .grade-chip {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--primary-light);
        color: var(--primary-dark);
    }
    
    .due-date {
        font-size: 0.75rem;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .due-date.overdue {
        color: #f56565;
        font-weight: 600;
    }
    
    .points-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        background: #f7fafc;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4a5568;
    }
    
    .points-badge i {
        color: var(--primary);
    }
    
    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        background: white;
        padding: 0.5rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }
    
    .filter-tab {
        flex: 1;
        min-width: 100px;
        padding: 0.625rem;
        text-align: center;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        color: #718096;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .filter-tab.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
    }
    
    .filter-tab i {
        margin-right: 0.375rem;
    }
    
    .assignment-stats {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
        margin: 1rem 0;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        text-align: center;
    }
    
    .stat-item {
        padding: 0.5rem;
    }
    
    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        line-height: 1.2;
    }
    
    .stat-label {
        font-size: 0.6875rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .mobile-info {
        display: none;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px dashed #e2e8f0;
    }
    
    @media (max-width: 768px) {
        .filter-tabs {
            flex-direction: column;
        }
        
        .filter-tab {
            width: 100%;
        }
        
        .mobile-info {
            display: block;
        }
        
        .assignment-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Dashboard Header --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">My Assignments</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-tasks" style="color: var(--primary);"></i> Track and submit your assignments
                        @if($assignments->count() > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $assignments->count() }} available</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    @php
        $totalAssignments = $assignments->count();
        $submittedCount = $assignments->filter(function($assignment) {
            return $assignment->my_submission && $assignment->my_submission->status != 'pending';
        })->count();
        $gradedCount = $assignments->filter(function($assignment) {
            return $assignment->my_submission && $assignment->my_submission->status == 'graded';
        })->count();
        $pendingCount = $assignments->filter(function($assignment) {
            return !$assignment->my_submission || $assignment->my_submission->status == 'pending';
        })->count();
    @endphp

    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Assignments</div>
                    <div class="stat-number">{{ $totalAssignments }}</div>
                </div>
                <div class="stat-icon" style="color: var(--primary);">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Submitted</div>
                    <div class="stat-number">{{ $submittedCount }}</div>
                </div>
                <div class="stat-icon" style="color: #48bb78;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Graded</div>
                    <div class="stat-number">{{ $gradedCount }}</div>
                </div>
                <div class="stat-icon" style="color: #667eea;">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-number">{{ $pendingCount }}</div>
                </div>
                <div class="stat-icon" style="color: #ed8936;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="content-grid">
        {{-- Left Column - Assignments List --}}
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-tasks" style="color: var(--primary);"></i>
                        All Assignments
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search assignments..." id="search-assignments">
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Success/Error Messages --}}
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

                    {{-- Filter Tabs --}}
                    <div class="filter-tabs">
                        <a href="{{ route('student.assignments.index') }}" class="filter-tab {{ !request('filter') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> All
                        </a>
                        <a href="{{ route('student.assignments.index', ['filter' => 'pending']) }}" class="filter-tab {{ request('filter') == 'pending' ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Pending
                        </a>
                        <a href="{{ route('student.assignments.index', ['filter' => 'submitted']) }}" class="filter-tab {{ request('filter') == 'submitted' ? 'active' : '' }}">
                            <i class="fas fa-paper-plane"></i> Submitted
                        </a>
                        <a href="{{ route('student.assignments.index', ['filter' => 'graded']) }}" class="filter-tab {{ request('filter') == 'graded' ? 'active' : '' }}">
                            <i class="fas fa-star"></i> Graded
                        </a>
                    </div>

                    @if($assignments->isEmpty())
                        {{-- Empty State --}}
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h3 class="empty-title">No assignments available</h3>
                            <p class="empty-text">You don't have any assignments at the moment.</p>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                Check back later for new assignments
                            </div>
                        </div>
                    @else
                        {{-- Assignments Table --}}
                        <div class="table-responsive">
                            <table class="quiz-table">
                                <thead>
                                    <tr>
                                        <th>Assignment</th>
                                        <th class="hide-on-mobile">Course</th>
                                        <th class="hide-on-tablet">Due Date</th>
                                        <th>Status</th>
                                        <th class="hide-on-tablet">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        @php
                                            $submission = $assignment->my_submission;
                                            $isOverdue = $assignment->due_date && $assignment->due_date->isPast() && (!$submission || $submission->status == 'pending');
                                            
                                            if ($submission) {
                                                if ($submission->status == 'graded') {
                                                    $statusClass = 'status-graded';
                                                    $statusIcon = 'fa-check-circle';
                                                    $statusText = 'Graded';
                                                } elseif ($submission->status == 'late') {
                                                    $statusClass = 'status-late';
                                                    $statusIcon = 'fa-exclamation-circle';
                                                    $statusText = 'Late';
                                                } else {
                                                    $statusClass = 'status-submitted';
                                                    $statusIcon = 'fa-paper-plane';
                                                    $statusText = 'Submitted';
                                                }
                                            } else {
                                                if ($isOverdue) {
                                                    $statusClass = 'status-late';
                                                    $statusIcon = 'fa-exclamation-triangle';
                                                    $statusText = 'Overdue';
                                                } else {
                                                    $statusClass = 'status-pending';
                                                    $statusIcon = 'fa-clock';
                                                    $statusText = 'Pending';
                                                }
                                            }
                                        @endphp
                                        <tr class="clickable-row" 
                                            data-href="{{ route('student.assignments.show', Crypt::encrypt($assignment->id)) }}"
                                            data-title="{{ strtolower($assignment->title) }}">
                                            <td>
                                                <div class="quiz-info-cell">
                                                    <div class="quiz-icon assignment-icon">
                                                        <i class="fas fa-file-alt"></i>
                                                    </div>
                                                    <div class="quiz-details">
                                                        <div class="quiz-title">{{ $assignment->title }}</div>
                                                        <div class="quiz-desc">{{ Str::limit($assignment->description, 60) }}</div>
                                                        
                                                        {{-- Mobile Info --}}
                                                        <div class="mobile-info">
                                                            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.5rem;">
                                                                <span class="points-badge">
                                                                    <i class="fas fa-star"></i> {{ $assignment->points }} pts
                                                                </span>
                                                                @if($assignment->due_date)
                                                                    <span class="due-date {{ $isOverdue ? 'overdue' : '' }}">
                                                                        <i class="fas fa-calendar-alt"></i>
                                                                        {{ $assignment->due_date->format('M d, Y') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="hide-on-mobile">
                                                <span class="item-badge badge-info">
                                                    <i class="fas fa-book"></i> 
                                                    {{ $assignment->course->course_name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="hide-on-tablet">
                                                @if($assignment->due_date)
                                                    <div class="due-date {{ $isOverdue ? 'overdue' : '' }}">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        {{ $assignment->due_date->format('M d, Y') }}
                                                        @if($isOverdue)
                                                            <span style="color: #f56565; margin-left: 0.25rem;">(Overdue)</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span style="color: #a0aec0;">No due date</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="status-badge {{ $statusClass }}">
                                                    <i class="fas {{ $statusIcon }}"></i> {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="hide-on-tablet">
                                                @if($submission && $submission->status == 'graded')
                                                    <div class="students-count">
                                                        <div class="count-number" style="color: var(--primary);">
                                                            {{ $submission->score }}/{{ $assignment->points }}
                                                        </div>
                                                        <div class="count-label">
                                                            {{ round(($submission->score / $assignment->points) * 100) }}%
                                                        </div>
                                                    </div>
                                                @elseif($submission)
                                                    <span class="grade-chip">
                                                        <i class="fas fa-clock"></i> Awaiting grade
                                                    </span>
                                                @else
                                                    <span class="points-badge">
                                                        <i class="fas fa-star"></i> {{ $assignment->points }} pts
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                @if($assignments instanceof \Illuminate\Pagination\AbstractPaginator && $assignments->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $assignments->firstItem() }} to {{ $assignments->lastItem() }} of {{ $assignments->total() }} assignments
                    </div>
                    <div class="pagination-links">
                        {{ $assignments->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Column - Sidebar Stats --}}
        <div class="right-column">
            {{-- Progress Card --}}
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary);"></i>
                        Assignment Overview
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--primary-light); color: var(--primary);">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Assignments</div>
                            </div>
                            <div class="stat-number">{{ $totalAssignments }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: #e6fffa; color: #2c7a7b;">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Submitted</div>
                            </div>
                            <div class="stat-number">{{ $submittedCount }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: #f0fff4; color: #22543d;">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Graded</div>
                            </div>
                            <div class="stat-number">{{ $gradedCount }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: #fff3e0; color: #c05621;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Pending</div>
                            </div>
                            <div class="stat-number">{{ $pendingCount }}</div>
                        </div>
                    </div>

                    @php
                        $completionRate = $totalAssignments > 0 ? round(($gradedCount / $totalAssignments) * 100) : 0;
                    @endphp
                    
                    <div class="progress-section" style="margin-top: 1.5rem;">
                        <div class="progress-header">
                            <span style="font-size: var(--font-size-sm); color: var(--gray-600);">Completion Rate</span>
                            <strong style="color: var(--gray-900);">{{ $completionRate }}%</strong>
                        </div>
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill" style="width: {{ $completionRate }}%; background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Assignments Card --}}
            @if($assignments->isNotEmpty())
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-history" style="color: var(--primary);"></i>
                        Recent Assignments
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        @foreach($assignments->take(5) as $assignment)
                            @php
                                $submission = $assignment->my_submission;
                                $statusColor = '#718096';
                                $statusBg = '#f7fafc';
                                
                                if ($submission) {
                                    if ($submission->status == 'graded') {
                                        $statusColor = '#22543d';
                                        $statusBg = '#f0fff4';
                                    } elseif ($submission->status == 'late') {
                                        $statusColor = '#c53030';
                                        $statusBg = '#fff5f5';
                                    } else {
                                        $statusColor = '#2c7a7b';
                                        $statusBg = '#e6fffa';
                                    }
                                }
                            @endphp
                            <a href="{{ route('student.assignments.show', Crypt::encrypt($assignment->id)) }}" class="list-item clickable-item">
                                <div class="item-avatar" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white;">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ Str::limit($assignment->title, 25) }}</div>
                                    <div class="item-meta">
                                        <i class="fas fa-calendar-alt"></i> 
                                        @if($assignment->due_date)
                                            Due {{ $assignment->due_date->format('M d') }}
                                        @else
                                            No due date
                                        @endif
                                    </div>
                                </div>
                                @if($submission && $submission->status == 'graded')
                                    <div class="stat-number" style="font-size: 0.875rem; color: var(--primary);">
                                        {{ $submission->score }}/{{ $assignment->points }}
                                    </div>
                                @else
                                    <div class="stat-number" style="font-size: 0.875rem; color: {{ $statusColor }};">
                                        <span style="background: {{ $statusBg }}; padding: 0.25rem 0.5rem; border-radius: 12px;">
                                            {{ $submission ? ucfirst($submission->status) : 'Pending' }}
                                        </span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Quick Tips Card --}}
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-lightbulb" style="color: var(--warning);"></i>
                        Assignment Tips
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Submit Early</div>
                                <div class="item-meta">Avoid last-minute technical issues</div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Follow Instructions</div>
                                <div class="item-meta">Read all instructions carefully</div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-paperclip"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Check File Format</div>
                                <div class="item-meta">Ensure files are in accepted formats</div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-save"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Save Locally</div>
                                <div class="item-meta">Keep backups of your work</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p class="footer-note">
            Student Portal • {{ $totalAssignments }} Assignments • Last accessed {{ now()->format('M d, Y h:i A') }}
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make rows clickable
        const clickableRows = document.querySelectorAll('.clickable-row');
        
        clickableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                    e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById('search-assignments');
        const assignmentRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && assignmentRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                assignmentRows.forEach(row => {
                    const assignmentTitle = row.dataset.title || '';
                    if (searchTerm === '' || assignmentTitle.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Auto-close alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        });
    });
</script>
@endpush