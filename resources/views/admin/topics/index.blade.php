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
<link rel="stylesheet" href="{{ asset('css/topic-index.css') }}">
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