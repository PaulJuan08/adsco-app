@extends('layouts.teacher')

@section('title', 'Topics')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Topics</h1>
        <p>Manage learning materials for your courses</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Left Column - Topics List -->
    <div class="content-grid-left">
        <div class="card">
            <div class="card-header">
                <div class="card-header-content">
                    <h2 class="card-title">All Topics</h2>
                    <a href="{{ route('teacher.topics.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Create Topic
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
                @endif
                
                @if($topics->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="table-header">Title</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Video</th>
                                <th class="table-header">Attachment</th>
                                <th class="table-header">Created</th>
                                <th class="table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topics as $topic)
                            <tr class="table-row">
                                <td class="table-cell">
                                    <div class="table-cell-title">{{ $topic->title }}</div>
                                    @if($topic->learning_outcomes)
                                    <div class="table-cell-subtitle">
                                        {{ Str::limit($topic->learning_outcomes, 60) }}
                                    </div>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    @if($topic->is_published)
                                    <span class="status-badge status-published">
                                        Published
                                    </span>
                                    @else
                                    <span class="status-badge status-draft">
                                        Draft
                                    </span>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    @if($topic->video_link)
                                    <span class="status-icon status-yes">
                                        <i class="fas fa-video"></i> Yes
                                    </span>
                                    @else
                                    <span class="status-icon status-no">
                                        <i class="fas fa-video-slash"></i> No
                                    </span>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    @if($topic->attachment)
                                    <span class="status-icon status-yes">
                                        <i class="fas fa-paperclip"></i> Yes
                                    </span>
                                    @else
                                    <span class="status-icon status-no">
                                        <i class="fas fa-times"></i> No
                                    </span>
                                    @endif
                                </td>
                                <td class="table-cell table-cell-date">
                                    {{ $topic->created_at->format('M d, Y') }}
                                </td>
                                <td class="table-cell">
                                    <div class="action-buttons">
                                        <a href="{{ route('teacher.topics.show', Crypt::encrypt($topic->id)) }}" 
                                           class="action-btn action-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('teacher.topics.edit', Crypt::encrypt($topic->id)) }}" 
                                           class="action-btn action-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-container">
                    {{ $topics->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-folder-open empty-state-icon"></i>
                    <div class="empty-state-title">No Topics Yet</div>
                    <div class="empty-state-description">Start by creating your first topic</div>
                    <a href="{{ route('teacher.topics.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Create First Topic
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column - Statistics -->
    <div class="content-grid-right">
        <!-- Stats Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Topic Statistics</h2>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div class="stat-card stat-primary">
                        <div class="stat-value">{{ $publishedTopics }}</div>
                        <div class="stat-label">Published</div>
                    </div>
                    
                    <div class="stat-card stat-warning">
                        <div class="stat-value">{{ $draftTopics }}</div>
                        <div class="stat-label">Draft</div>
                    </div>
                    
                    <div class="stat-card stat-success">
                        <div class="stat-value">{{ $topicsThisMonth }}</div>
                        <div class="stat-label">This Month</div>
                    </div>
                    
                    <div class="stat-card stat-info">
                        <div class="stat-value">{{ $topicsWithVideo }}</div>
                        <div class="stat-label">With Video</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="{{ route('teacher.topics.create') }}" class="quick-action-link">
                        <div class="quick-action-icon bg-primary">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">Create Topic</div>
                            <div class="quick-action-subtitle">Add new learning material</div>
                        </div>
                        <i class="fas fa-chevron-right quick-action-arrow"></i>
                    </a>
                    
                    <a href="{{ route('teacher.courses.index') }}" class="quick-action-link">
                        <div class="quick-action-icon bg-success">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">My Courses</div>
                            <div class="quick-action-subtitle">Manage your courses</div>
                        </div>
                        <i class="fas fa-chevron-right quick-action-arrow"></i>
                    </a>
                </div>
            </div>
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
    min-width: 0; /* Prevent overflow */
}

.content-grid-right {
    min-width: 0;
}

/* Card Header Alignment */
.card-header-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
}

@media (min-width: 640px) {
    .card-header-content {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

/* Table Responsiveness */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px; /* Minimum width for small screens */
}

@media (max-width: 1024px) {
    .data-table {
        min-width: 100%;
    }
}

.table-header {
    padding: 0.75rem;
    text-align: left;
    font-weight: 500;
    color: var(--secondary);
    font-size: 0.875rem;
    background: #f9fafb;
    border-bottom: 1px solid var(--border);
}

.table-row {
    border-bottom: 1px solid var(--border);
    transition: background-color 0.2s;
}

.table-row:hover {
    background-color: #f9fafb;
}

.table-cell {
    padding: 0.75rem;
    vertical-align: top;
}

.table-cell-title {
    font-weight: 500;
    color: var(--dark);
    word-wrap: break-word;
}

.table-cell-subtitle {
    font-size: 0.75rem;
    color: var(--secondary);
    margin-top: 0.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-wrap: break-word;
}

.table-cell-date {
    font-size: 0.875rem;
    color: var(--secondary);
    white-space: nowrap;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    text-align: center;
    white-space: nowrap;
}

.status-published {
    background: #dcfce7;
    color: #065f46;
}

.status-draft {
    background: #fef3c7;
    color: #92400e;
}

.status-icon {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.status-yes {
    color: var(--success);
}

.status-no {
    color: var(--secondary);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.2s;
    white-space: nowrap;
}

.action-view {
    background: #f3f4f6;
    color: var(--secondary);
}

.action-view:hover {
    background: #e5e7eb;
}

.action-edit {
    background: #e0e7ff;
    color: var(--primary);
}

.action-edit:hover {
    background: #c7d2fe;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (max-width: 640px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}

.stat-card {
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 500;
}

.stat-primary {
    background: #f0f9ff;
}
.stat-primary .stat-value { color: #0369a1; }
.stat-primary .stat-label { color: #0c4a6e; }

.stat-warning {
    background: #fef3c7;
}
.stat-warning .stat-value { color: #b45309; }
.stat-warning .stat-label { color: #92400e; }

.stat-success {
    background: #f0fdf4;
}
.stat-success .stat-value { color: #15803d; }
.stat-success .stat-label { color: #166534; }

.stat-info {
    background: #e0e7ff;
}
.stat-info .stat-value { color: #4f46e5; }
.stat-info .stat-label { color: #3730a3; }

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
    border-radius: 6px;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s;
}

.quick-action-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
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

.quick-action-content {
    flex: 1;
    min-width: 0; /* Prevent overflow */
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

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--secondary);
}

.empty-state-icon {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state-title {
    font-size: 1rem;
    font-weight: 500;
    color: var(--secondary);
    margin-bottom: 0.5rem;
}

.empty-state-description {
    font-size: 0.875rem;
    color: #9ca3af;
    margin-bottom: 1.5rem;
}

/* Buttons */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--primary);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.2s;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

/* Alerts */
.alert {
    margin-bottom: 1.5rem;
    padding: 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    border-left: 4px solid transparent;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.alert-success {
    background: #dcfce7;
    color: #065f46;
    border-left-color: #10b981;
}

.alert i {
    flex-shrink: 0;
}

/* Pagination */
.pagination-container {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

/* Ensure pagination is responsive */
.pagination-container ul {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.25rem;
}

/* Mobile Optimization */
@media (max-width: 640px) {
    .card-body {
        padding: 1rem;
    }
    
    .table-cell {
        padding: 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .btn-primary {
        width: 100%;
        justify-content: center;
    }
    
    .card-header-content .btn-primary {
        width: auto;
    }
}
</style>
@endsection