@extends('layouts.admin')

@section('title', 'Topics - Admin Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Topics</h1>
        <p>Manage and organize learning topics</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $topics->total() ?? $topics->count() }}</div>
                <div class="stat-label">Total Topics</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $publishedTopics ?? 0 }}</div>
                <div class="stat-label">Published Topics</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-eye"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $topicsWithVideo ?? 0 }}</div>
                <div class="stat-label">Topics with Video</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-video"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Topics List Card -->
    <div class="card main-card">
        <div class="card-header">
            <div class="card-title">All Topics</div>
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                </div>
                <a href="{{ route('admin.topics.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Add Topic
                </a>
            </div>
        </div>
        
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
            <h3>No topics yet</h3>
            <p>You haven't created any topics. Start building your content by adding the first topic.</p>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                    <tr class="topic-row" data-title="{{ strtolower($topic->title) }}">
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
                                            <span class="status-badge status-published">
                                                <i class="fas fa-check-circle"></i>
                                                Published
                                            </span>
                                            @else
                                            <span class="status-badge status-draft">
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
                                <span class="status-badge status-published">
                                    <i class="fas fa-check-circle"></i>
                                    Published
                                </span>
                            @else
                                <span class="status-badge status-draft">
                                    <i class="fas fa-clock"></i>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="hide-on-tablet">
                            <div class="created-date">{{ $topic->created_at->format('M d, Y') }}</div>
                            <div class="created-ago">{{ $topic->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" 
                                   class="btn-icon view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" 
                                   class="btn-icon edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this topic?')">
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
        @if($topics instanceof \Illuminate\Pagination\AbstractPaginator && $topics->hasPages())
        <div class="pagination-container">
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
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div class="sidebar-container">
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="quick-actions">
                <a href="{{ route('admin.topics.create') }}" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Add New Topic</div>
                        <div class="quick-action-subtitle">Create a new learning topic</div>
                    </div>
                </a>
                <a href="{{ route('admin.assignments.create') }}" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Create Assignment</div>
                        <div class="quick-action-subtitle">Add assignment to topic</div>
                    </div>
                </a>
                <a href="{{ route('admin.quizzes.create') }}" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Create Quiz</div>
                        <div class="quick-action-subtitle">Add quiz to topic</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Topic Statistics</div>
            </div>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="stat-label">Topics This Month</span>
                    <span class="stat-value">{{ $topicsThisMonth ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Published Topics</span>
                    <span class="stat-value">{{ $publishedTopics ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Draft Topics</span>
                    <span class="stat-value">{{ $draftTopics ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Topics with Video</span>
                    <span class="stat-value">{{ $topicsWithVideo ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Responsive CSS Variables */
    :root {
        --primary: #4361ee;
        --primary-light: #e0e7ff;
        --secondary: #6c757d;
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --light: #f8f9fa;
        --dark: #343a40;
        --border: #e9ecef;
    }

    /* Responsive Grid Layouts */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .sidebar-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .content-grid {
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .card-title {
            text-align: center;
        }
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
    }

    /* Search Container */
    .search-container {
        position: relative;
        min-width: 200px;
    }

    .search-container i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--secondary);
    }

    .search-container input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .search-container input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    @media (max-width: 768px) {
        .search-container {
            min-width: unset;
            width: 100%;
        }
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
        white-space: nowrap;
    }

    .btn:hover {
        background: #4f46e5;
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .topics-table {
        width: 100%;
        border-collapse: collapse;
    }

    .topics-table thead {
        background: #f9fafb;
        border-bottom: 2px solid var(--border);
    }

    .topics-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--secondary);
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .topics-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .topics-table tbody tr:hover {
        background: #f9fafb;
    }

    /* Topic Info Cell */
    .topic-info-cell {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .topic-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .topic-1 {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .topic-2 {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .topic-3 {
        background: linear-gradient(135deg, #db2777 0%, #ec4899 100%);
    }

    .topic-details {
        flex: 1;
        min-width: 0;
    }

    .topic-name {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
        font-size: 0.9375rem;
    }

    .topic-video-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: #dc2626;
        background: #fee2e2;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        margin-top: 0.25rem;
    }

    .topic-video-indicator i {
        font-size: 0.625rem;
    }

    .topic-mobile-info {
        display: none;
        margin-top: 0.5rem;
    }

    /* Hide/Show Columns for Responsive */
    .hide-on-mobile {
        display: table-cell;
    }

    .hide-on-tablet {
        display: table-cell;
    }

    @media (max-width: 768px) {
        .hide-on-tablet {
            display: none;
        }
        
        .topic-mobile-info {
            display: block;
        }
    }

    @media (max-width: 576px) {
        .hide-on-mobile {
            display: none;
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
            font-size: 0.875rem;
        }
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 0.625rem;
    }

    .status-published {
        background: #dcfce7;
        color: #166534;
    }

    .status-draft {
        background: #fef3c7;
        color: #92400e;
    }

    /* Created Date */
    .created-date {
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .created-ago {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.125rem;
    }

    /* Action Buttons - Consistent with other pages */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
        font-size: 0.875rem;
    }

    .btn-icon.view {
        color: #3b82f6;
    }

    .btn-icon.edit {
        color: #f59e0b;
    }

    .btn-icon.delete {
        color: #ef4444;
    }

    .btn-icon:hover {
        background: rgba(0,0,0,0.05);
        transform: translateY(-1px);
    }

    .inline-form {
        display: inline;
    }

    @media (max-width: 576px) {
        .btn-icon {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--secondary);
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--secondary);
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.5;
    }

    .empty-hint {
        margin-top: 1rem;
        color: var(--secondary);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Alerts */
    .alert {
        margin: 0 1.5rem 1.5rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert i {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .alert {
            margin: 0 1rem 1rem;
        }
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
    }

    @media (min-width: 768px) {
        .pagination-container {
            flex-direction: row;
        }
    }

    .pagination-info {
        font-size: 0.875rem;
        color: var(--secondary);
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
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .pagination-btn:hover:not(.disabled):not(.active) {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.active {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.disabled {
        background: #f3f4f6;
        color: var(--secondary);
        cursor: not-allowed;
    }

    /* Sidebar */
    .sidebar-card {
        margin-bottom: 1.5rem;
    }

    .sidebar-card:last-child {
        margin-bottom: 0;
    }

    .quick-actions {
        padding: 0.5rem;
    }

    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--dark);
        transition: background 0.2s;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }

    .quick-action-item:hover {
        background: #f9fafb;
    }

    .quick-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quick-action-icon:first-child {
        background: #e0e7ff;
        color: var(--primary);
    }

    .quick-action-icon:nth-child(2) {
        background: #fce7f3;
        color: #db2777;
    }

    .quick-action-icon:last-child {
        background: #dcfce7;
        color: var(--success);
    }

    .quick-action-content {
        text-align: left;
        flex: 1;
        min-width: 0;
    }

    .quick-action-title {
        font-weight: 500;
        margin-bottom: 0.125rem;
        font-size: 0.875rem;
    }

    .quick-action-subtitle {
        font-size: 0.75rem;
        color: var(--secondary);
        line-height: 1.4;
    }

    /* Stats List */
    .stats-list {
        padding: 0.5rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .stat-value {
        font-weight: 600;
        color: var(--dark);
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .top-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }

    .greeting h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .greeting p {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .icon-courses {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #4f46e5;
    }

    .icon-users {
        background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        color: #db2777;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-topics');
        const topicRows = document.querySelectorAll('.topic-row');
        
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
@endsection