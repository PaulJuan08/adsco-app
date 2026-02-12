@extends('layouts.admin')

@section('title', 'Topics - Admin Dashboard')

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
                    <h1 class="welcome-title">Topic Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-chalkboard"></i> Manage all learning topics
                        @if(($draftTopics ?? 0) > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $draftTopics ?? 0 }} draft{{ ($draftTopics ?? 0) > 1 ? 's' : '' }} pending</span>
                        @endif
                    </p>
                </div>
            </div>
            @if(($draftTopics ?? 0) > 0)
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-edit"></i>
                    <span class="badge-count">{{ $draftTopics ?? 0 }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Draft Topics</div>
                    <div class="alert-subtitle">{{ $draftTopics ?? 0 }} topic{{ ($draftTopics ?? 0) > 1 ? 's' : '' }} in draft status</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.topics.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($topics->total() ?? $topics->count()) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-link">
                View all topics <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.topics.index') }}?status=published" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published Topics</div>
                    <div class="stat-number">{{ number_format($publishedTopics ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="stat-link">
                View published <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.topics.index') }}?has_video=true" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Topics with Video</div>
                    <div class="stat-number">{{ number_format($topicsWithVideo ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-video"></i>
                </div>
            </div>
            <div class="stat-link">
                View with video <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Topics List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chalkboard" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        All Topics
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                        </div>
                        <a href="{{ route('admin.topics.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add Topic
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

                    @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    @endif

                    @if($topics->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h3 class="empty-title">No topics yet</h3>
                            <p class="empty-text">You haven't created any topics. Start building your content by adding the first topic.</p>
                            <a href="{{ route('admin.topics.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i>
                                Create Your First Topic
                            </a>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                Topics organize content and can contain videos and learning materials
                            </div>
                        </div>
                    @else
                        <!-- Topics List -->
                        <div class="table-responsive">
                            <table class="topics-table" id="topics-table">
                                <thead>
                                    <tr>
                                        <th>Topic Title</th>
                                        <th class="hide-on-mobile">Status</th>
                                        <th class="hide-on-tablet">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topics as $topic)
                                    <tr class="clickable-row" 
                                        data-href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}"
                                        data-title="{{ strtolower($topic->title) }}">
                                        <td>
                                            <div class="topic-info-cell">
                                                <div class="topic-icon topic-{{ ($loop->index % 3) + 1 }}">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <div class="topic-details">
                                                    <div class="topic-name">{{ $topic->title }}</div>
                                                    @if($topic->video_link)
                                                    <div class="topic-video-indicator">
                                                        <i class="fas fa-video"></i>
                                                        Has video content
                                                    </div>
                                                    @endif
                                                    <div class="topic-mobile-info">
                                                        <div class="status-mobile">
                                                            @if($topic->is_published)
                                                            <span class="item-badge badge-success">
                                                                <i class="fas fa-check-circle"></i>
                                                                Published
                                                            </span>
                                                            @else
                                                            <span class="item-badge badge-warning">
                                                                <i class="fas fa-clock"></i>
                                                                Draft
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hide-on-mobile">
                                            @if($topic->is_published)
                                                <span class="item-badge badge-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Published
                                                </span>
                                            @else
                                                <span class="item-badge badge-warning">
                                                    <i class="fas fa-clock"></i>
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="hide-on-tablet">
                                            <div class="created-date">{{ $topic->created_at->format('M d, Y') }}</div>
                                            <div class="created-ago">{{ $topic->created_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($topics instanceof \Illuminate\Pagination\AbstractPaginator && $topics->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $topics->firstItem() }} to {{ $topics->lastItem() }} of {{ $topics->total() }} entries
                    </div>
                    <div class="pagination-links">
                        @if($topics->onFirstPage())
                        <span class="pagination-btn disabled">Previous</span>
                        @else
                        <a href="{{ $topics->previousPageUrl() }}" class="pagination-btn">Previous</a>
                        @endif
                        
                        @foreach(range(1, min(5, $topics->lastPage())) as $page)
                            @if($page == $topics->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                            @else
                            <a href="{{ $topics->url($page) }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($topics->hasMorePages())
                        <a href="{{ $topics->nextPageUrl() }}" class="pagination-btn">Next</a>
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
                        <a href="{{ route('admin.topics.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add New Topic</div>
                                <div class="action-subtitle">Create a new learning topic</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.assignments.create') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create Assignment</div>
                                <div class="action-subtitle">Add assignment to topic</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.quizzes.create') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create Quiz</div>
                                <div class="action-subtitle">Add quiz to topic</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Topic Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Topic Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <a href="{{ route('admin.topics.index') }}?month={{ now()->format('Y-m') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Topics This Month</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $topicsThisMonth ?? 0 }}</div>
                        </a>
                        
                        <a href="{{ route('admin.topics.index') }}?status=published" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Published Topics</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $publishedTopics ?? 0 }}</div>
                        </a>
                        
                        <a href="{{ route('admin.topics.index') }}?status=draft" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Draft Topics</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $draftTopics ?? 0 }}</div>
                        </a>
                        
                        <a href="{{ route('admin.topics.index') }}?has_video=true" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Topics with Video</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $topicsWithVideo ?? 0 }}</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Topic Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>
@endsection

@push('styles')
<style>
    /* Additional styles specific to topics index */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .search-container {
        position: relative;
        min-width: 250px;
    }
    
    .search-container i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
        z-index: 1;
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-lg);
        font-size: var(--font-size-sm);
        transition: all var(--transition-base);
        background: var(--white);
        box-shadow: var(--shadow-sm);
    }
    
    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }
    
    /* Compact Stats Cards */
    .stats-grid-compact {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    
    .stats-grid-compact .stat-card {
        padding: 1rem;
        min-height: auto;
    }
    
    .stats-grid-compact .stat-header {
        margin-bottom: 0.75rem;
    }
    
    .stats-grid-compact .stat-label {
        font-size: var(--font-size-sm);
        margin-bottom: 0.25rem;
    }
    
    .stats-grid-compact .stat-number {
        font-size: 1.75rem;
        font-weight: var(--font-bold);
    }
    
    .stats-grid-compact .stat-icon {
        font-size: 1.5rem;
        opacity: 0.8;
    }
    
    .stats-grid-compact .stat-link {
        padding: 0.5rem;
        font-size: var(--font-size-xs);
    }
    
    /* Clickable Cards */
    .clickable-card {
        display: block;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .clickable-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .clickable-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: currentColor;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .clickable-card:hover::after {
        opacity: 0.05;
    }
    
    /* Clickable items in lists */
    .clickable-item {
        display: block;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .clickable-item:hover {
        background: var(--gray-50);
        transform: translateX(4px);
        border-color: var(--primary);
    }
    
    /* Ensure links within clickable cards don't interfere */
    .clickable-card a {
        position: relative;
        z-index: 1;
    }
    
    /* Make stat cards look interactive */
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: var(--radius);
        border: 1px solid var(--gray-200);
    }
    
    .topics-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--white);
    }
    
    .topics-table thead {
        background: var(--gray-50);
    }
    
    .topics-table th {
        padding: 1rem;
        text-align: left;
        font-weight: var(--font-semibold);
        color: var(--gray-700);
        font-size: var(--font-size-sm);
        border-bottom: 2px solid var(--gray-300);
        white-space: nowrap;
    }
    
    .topics-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    
    /* Clickable row styling */
    .clickable-row {
        cursor: pointer;
        transition: all var(--transition-base);
    }
    
    .clickable-row:hover {
        background: var(--gray-50);
        transform: translateX(4px);
        box-shadow: -4px 0 0 var(--primary-light);
    }
    
    .topic-info-cell {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .topic-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--white);
        flex-shrink: 0;
        box-shadow: var(--shadow-sm);
    }
    
    .topic-1 {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    
    .topic-2 {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
    }
    
    .topic-3 {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    
    .topic-details {
        flex: 1;
        min-width: 0;
    }
    
    .topic-name {
        font-weight: var(--font-semibold);
        color: var(--gray-900);
        margin-bottom: 0.25rem;
        font-size: var(--font-size-base);
    }
    
    .topic-video-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: var(--font-size-xs);
        color: var(--danger-dark);
        background: var(--danger-light);
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-sm);
        margin-top: 0.25rem;
        border: 1px solid var(--danger);
        font-weight: var(--font-medium);
    }
    
    .topic-video-indicator i {
        font-size: 0.75rem;
    }
    
    .topic-mobile-info {
        display: none;
        margin-top: 0.5rem;
    }
    
    .created-date {
        font-weight: var(--font-medium);
        color: var(--gray-800);
        font-size: var(--font-size-sm);
        margin-bottom: 0.125rem;
    }
    
    .created-ago {
        font-size: var(--font-size-xs);
        color: var(--gray-600);
    }
    
    .alert {
        margin: 0 0 1.5rem 0;
        padding: 0.875rem 1rem;
        border-radius: var(--radius);
        font-size: var(--font-size-sm);
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        border: 1px solid;
    }
    
    .alert-success {
        background: var(--success-light);
        color: var(--success-dark);
        border-color: var(--success);
    }
    
    .alert-error {
        background: var(--danger-light);
        color: var(--danger-dark);
        border-color: var(--danger);
    }
    
    .alert i {
        font-size: 1rem;
        margin-top: 0.125rem;
    }
    
    .empty-hint {
        margin-top: 1rem;
        color: var(--gray-500);
        font-size: var(--font-size-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .empty-hint i {
        color: var(--warning);
    }
    
    .action-content {
        flex: 1;
    }
    
    .action-title {
        font-weight: var(--font-semibold);
        font-size: var(--font-size-base);
        margin-bottom: 0.25rem;
    }
    
    .action-subtitle {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
    }
    
    .action-arrow {
        font-size: var(--font-size-lg);
        opacity: 0.8;
        transition: transform var(--transition-fast);
    }
    
    .action-card:hover .action-arrow {
        transform: translateX(4px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .hide-on-tablet {
            display: none !important;
        }
        
        .topic-mobile-info {
            display: block;
        }
        
        .card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .search-container {
            min-width: unset;
            width: 100%;
        }
        
        .stats-grid-compact {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .stats-grid-compact .stat-card {
            padding: 0.75rem;
        }
        
        .clickable-row:hover {
            transform: translateX(2px);
            box-shadow: -2px 0 0 var(--primary-light);
        }
        
        .clickable-card:hover {
            transform: translateY(-2px);
        }
    }
    
    @media (max-width: 576px) {
        .hide-on-mobile {
            display: none !important;
        }
        
        .topics-table th,
        .topics-table td {
            padding: 0.75rem;
        }
        
        .topic-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .topic-name {
            font-size: var(--font-size-sm);
        }
        
        .topic-video-indicator {
            padding: 0.125rem 0.5rem;
            font-size: var(--font-size-xs);
        }
        
        .stats-grid-compact {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make rows clickable
        const clickableRows = document.querySelectorAll('.clickable-row');
        
        clickableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't redirect if user clicked on a link or button
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
            
            // Add hover effect
            row.style.cursor = 'pointer';
        });

        // Search functionality
        const searchInput = document.getElementById('search-topics');
        const topicRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && topicRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                topicRows.forEach(row => {
                    const topicTitle = row.dataset.title || '';
                    
                    if (searchTerm === '' || topicTitle.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush