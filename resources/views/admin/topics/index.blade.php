@extends('layouts.admin')

@section('title', 'Topics - Admin Dashboard')

@section('content')
    <!-- Header with Dashboard Style -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar-large">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Topics</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-chalkboard"></i> Manage and organize learning topics
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

    <!-- Stats Cards with Dashboard Style -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($topics->total() ?? $topics->count()) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-chalkboard"></i> All learning topics
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.topics.index') }}" class="stat-link">
                    View all topics <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Published Topics</div>
                    <div class="stat-number">{{ number_format($publishedTopics ?? 0) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-eye"></i> Visible to students
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.topics.index') }}?status=published" class="stat-link">
                    View published <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Topics with Video</div>
                    <div class="stat-number">{{ number_format($topicsWithVideo ?? 0) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-video"></i> Video content available
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-video"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.topics.index') }}?has_video=true" class="stat-link">
                    View with video <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Topics List Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-chalkboard card-icon"></i>
                        <h2 class="card-title-modern">All Topics</h2>
                    </div>
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
                
                <div class="card-body-modern">
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
                    <div class="empty-state-modern">
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
                    @endif
                </div>

                <!-- Pagination -->
                @if($topics instanceof \Illuminate\Pagination\AbstractPaginator && $topics->hasPages())
                <div class="card-footer-modern">
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
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-bolt card-icon"></i>
                        <h2 class="card-title-modern">Quick Actions</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
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
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-chart-pie card-icon"></i>
                        <h2 class="card-title-modern">Topic Statistics</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
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
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-content">
            <p class="footer-text">© {{ date('Y') }} School Management System. All rights reserved.</p>
            <p class="footer-meta">
                <span><i class="fas fa-chalkboard"></i> Topic Management</span>
                <span class="separator">•</span>
                <span><i class="fas fa-calendar"></i> {{ now()->format('M d, Y') }}</span>
            </p>
        </div>
    </footer>
@endsection

@push('styles')
<style>
    /* Color Variables - Same as dashboard */
    :root {
        --primary: #4f46e5;
        --primary-light: #eef2ff;
        --primary-dark: #3730a3;
        
        --success: #10b981;
        --success-light: #d1fae5;
        --success-dark: #059669;
        
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --warning-dark: #d97706;
        
        --info: #06b6d4;
        --info-light: #cffafe;
        --info-dark: #0891b2;
        
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --danger-dark: #dc2626;
        
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        
        --radius: 12px;
        --radius-sm: 8px;
        --radius-lg: 16px;
    }

    /* Dashboard Header - Same as dashboard */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }
    
    .user-greeting {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    
    .user-avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        border: 3px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }
    
    .greeting-text {
        color: white;
    }
    
    .welcome-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: white;
    }
    
    .welcome-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .separator {
        opacity: 0.5;
    }
    
    .pending-notice {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .header-alert {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .alert-badge {
        position: relative;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }
    
    .badge-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--danger);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        border: 2px solid var(--primary);
    }
    
    .alert-text {
        color: white;
    }
    
    .alert-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .alert-subtitle {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* Stats Grid - Same as dashboard */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-200);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-content {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-meta {
        font-size: 0.875rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .stat-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }
    
    .stat-primary .stat-icon-wrapper {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .stat-success .stat-icon-wrapper {
        background: var(--success-light);
        color: var(--success);
    }
    
    .stat-info .stat-icon-wrapper {
        background: var(--info-light);
        color: var(--info);
    }
    
    .stat-footer {
        background: var(--gray-50);
        padding: 0.75rem 1.5rem;
        border-top: 1px solid var(--gray-200);
    }
    
    .stat-link {
        color: var(--gray-700);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .stat-link:hover {
        color: var(--primary);
        gap: 0.75rem;
    }

    /* Content Grid - Same as dashboard */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Dashboard Cards - Same as dashboard */
    .dashboard-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header-modern {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .card-icon {
        font-size: 1.25rem;
        color: var(--primary);
    }
    
    .card-title-modern {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }
    
    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }
    
    .card-body-modern {
        padding: 1.5rem;
    }
    
    .card-footer-modern {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Header Actions - Kept from original */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .search-container {
        position: relative;
        min-width: 200px;
    }

    .search-container i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-500);
    }

    .search-container input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.2s;
        background: white;
    }

    .search-container input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    /* Buttons - Kept from original */
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
        background: var(--primary-dark);
    }

    /* Alerts - Kept from original */
    .alert {
        margin: 0 0 1.5rem 0;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-success {
        background: var(--success-light);
        color: var(--success-dark);
        border: 1px solid var(--success);
    }

    .alert-error {
        background: var(--danger-light);
        color: var(--danger-dark);
        border: 1px solid var(--danger);
    }

    .alert i {
        font-size: 1rem;
    }

    /* Table Styles - Kept from original */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .topics-table {
        width: 100%;
        border-collapse: collapse;
    }

    .topics-table thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .topics-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--gray-600);
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .topics-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .topics-table tbody tr:hover {
        background: var(--gray-50);
    }

    /* Topic Info Cell - Kept from original */
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
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }

    .topic-2 {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
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
        color: var(--gray-900);
        margin-bottom: 0.25rem;
        font-size: 0.9375rem;
    }

    .topic-video-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: var(--danger-dark);
        background: var(--danger-light);
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
        
        .card-header-modern {
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

    /* Status Badges - Kept from original */
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
        background: var(--success-light);
        color: var(--success-dark);
    }

    .status-draft {
        background: var(--warning-light);
        color: var(--warning-dark);
    }

    /* Created Date - Kept from original */
    .created-date {
        font-weight: 500;
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .created-ago {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.125rem;
    }

    /* Action Buttons - Kept from original */
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

    /* Empty State - Kept from original */
    .empty-state-modern {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--gray-400);
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    .empty-state-modern h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .empty-state-modern p {
        color: var(--gray-600);
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.5;
    }

    .empty-hint {
        margin-top: 1rem;
        color: var(--gray-500);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Pagination - Kept from original */
    .pagination-info {
        font-size: 0.875rem;
        color: var(--gray-600);
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
        background: var(--gray-100);
        color: var(--gray-700);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    
    .pagination-btn:hover:not(.disabled):not(.active) {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .pagination-btn.active {
        background: var(--primary);
        color: white;
    }
    
    .pagination-btn.disabled {
        background: var(--gray-200);
        color: var(--gray-400);
        cursor: not-allowed;
    }

    /* Quick Actions - Same as dashboard */
    .quick-actions-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: all 0.2s ease;
        border: 2px solid;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }
    
    .action-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }
    
    .action-primary {
        background: var(--primary-light);
        border-color: var(--primary);
    }
    
    .action-primary:hover {
        background: var(--primary);
    }
    
    .action-primary:hover .action-title,
    .action-primary:hover .action-subtitle,
    .action-primary:hover .action-icon,
    .action-primary:hover .action-arrow {
        color: white;
    }
    
    .action-success {
        background: var(--success-light);
        border-color: var(--success);
    }
    
    .action-success:hover {
        background: var(--success);
    }
    
    .action-success:hover .action-title,
    .action-success:hover .action-subtitle,
    .action-success:hover .action-icon,
    .action-success:hover .action-arrow {
        color: white;
    }
    
    .action-warning {
        background: var(--warning-light);
        border-color: var(--warning);
    }
    
    .action-warning:hover {
        background: var(--warning);
    }
    
    .action-warning:hover .action-title,
    .action-warning:hover .action-subtitle,
    .action-warning:hover .action-icon,
    .action-warning:hover .action-arrow {
        color: white;
    }
    
    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-icon {
        color: var(--primary);
    }
    
    .action-success .action-icon {
        color: var(--success);
    }
    
    .action-warning .action-icon {
        color: var(--warning);
    }
    
    .action-content {
        flex: 1;
    }
    
    .action-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-title {
        color: var(--primary-dark);
    }
    
    .action-success .action-title {
        color: var(--success-dark);
    }
    
    .action-warning .action-title {
        color: var(--warning-dark);
    }
    
    .action-subtitle {
        font-size: 0.875rem;
        color: var(--gray-600);
        transition: all 0.2s ease;
    }
    
    .action-arrow {
        font-size: 1.125rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-arrow {
        color: var(--primary);
    }
    
    .action-success .action-arrow {
        color: var(--success);
    }
    
    .action-warning .action-arrow {
        color: var(--warning);
    }

    /* Stats List - Kept from original */
    .stats-list {
        padding: 0.5rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid var(--gray-200);
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .stat-value {
        font-weight: 600;
        color: var(--gray-900);
    }

    /* Footer - Same as dashboard */
    .dashboard-footer {
        background: white;
        border-top: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: var(--shadow-sm);
    }
    
    .footer-content {
        text-align: center;
    }
    
    .footer-text {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0 0 0.5rem 0;
    }
    
    .footer-meta {
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin: 0;
    }
    
    /* Responsive Design - Same as dashboard */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-avatar-large {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 1.875rem;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .footer-meta {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .separator {
            display: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality - EXACTLY AS IN ORIGINAL
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