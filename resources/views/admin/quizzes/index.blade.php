@extends('layouts.admin')

@section('title', 'Quizzes - Admin Dashboard')

@section('content')
    <!-- Header with Dashboard Style -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar-large">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Quizzes</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-question-circle"></i> Manage and organize assessment quizzes
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards with Dashboard Style -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Quizzes</div>
                    <div class="stat-number">{{ number_format($quizzes->total()) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-question-circle"></i> All assessment quizzes
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-question-circle"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.quizzes.index') }}" class="stat-link">
                    View all quizzes <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Published Quizzes</div>
                    <div class="stat-number">{{ number_format($quizzes->where('is_published', true)->count()) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-eye"></i> Available to students
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.quizzes.index') }}?status=published" class="stat-link">
                    View published <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Private Quizzes</div>
                    <div class="stat-number">{{ number_format($quizzes->where('is_published', false)->count()) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-lock"></i> Draft mode
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.quizzes.index') }}?status=draft" class="stat-link">
                    View drafts <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Quiz List Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-question-circle card-icon"></i>
                        <h2 class="card-title-modern">Quiz List</h2>
                    </div>
                    <div class="header-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search quizzes..." id="search-input">
                        </div>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Quiz
                        </a>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    @if($quizzes->isEmpty())
                    <!-- Empty State -->
                    <div class="empty-state-modern">
                        <div class="empty-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h3 class="empty-title">No quizzes yet</h3>
                        <p class="empty-text">Get started by creating your first quiz</p>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Create Quiz
                        </a>
                    </div>
                    @else
                    <!-- Quiz List -->
                    <div class="table-container">
                        <table class="quiz-table">
                            <thead>
                                <tr>
                                    <th>Quiz Title</th>
                                    <th>Questions</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quizzes as $quiz)
                                <tr class="quiz-row" data-search="{{ strtolower($quiz->title . ' ' . $quiz->description) }}">
                                    <td>
                                        <div class="quiz-info">
                                            <div class="quiz-icon">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <div>
                                                <div class="quiz-title">{{ $quiz->title }}</div>
                                                <div class="quiz-desc">{{ Str::limit($quiz->description, 60) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge">{{ $quiz->total_questions ?? 0 }} Qs</span>
                                    </td>
                                    <td>
                                        @if($quiz->is_published)
                                            <span class="status published">
                                                <i class="fas fa-circle"></i> Public
                                            </span>
                                        @else
                                            <span class="status draft">
                                                <i class="fas fa-circle"></i> Private
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="date">{{ $quiz->created_at->format('M d, Y') }}</div>
                                        <div class="time">{{ $quiz->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                               class="btn-icon view" title="View Quiz">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) }}" 
                                               class="btn-icon edit" title="Edit Quiz">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon delete" title="Delete Quiz">
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

                @if($quizzes->hasPages())
                <div class="card-footer-modern">
                    <div class="pagination-info">
                        Showing {{ $quizzes->firstItem() }}-{{ $quizzes->lastItem() }} of {{ $quizzes->total() }}
                    </div>
                    <div class="pagination-links">
                        {{ $quizzes->links() }}
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
                        <a href="{{ route('admin.quizzes.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create New Quiz</div>
                                <div class="action-subtitle">Add assessment quiz</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.topics.index') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Manage Topics</div>
                                <div class="action-subtitle">View learning materials</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.assignments.index') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">View Assignments</div>
                                <div class="action-subtitle">Check assignments list</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quiz Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-chart-pie card-icon"></i>
                        <h2 class="card-title-modern">Quiz Statistics</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="stats-list">
                        <div class="stat-item">
                            <span class="stat-label">Quizzes This Month</span>
                            <span class="stat-value">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Published Quizzes</span>
                            <span class="stat-value">{{ $quizzes->where('is_published', true)->count() }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Draft Quizzes</span>
                            <span class="stat-value">{{ $quizzes->where('is_published', false)->count() }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Avg Questions</span>
                            <span class="stat-value">0</span>
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
                <span><i class="fas fa-question-circle"></i> Quiz Management</span>
                <span class="separator">•</span>
                <span><i class="fas fa-calendar"></i> {{ now()->format('M d, Y') }}</span>
            </p>
        </div>
    </footer>
@endsection

@push('styles')
<style>
    /* Apply all the dashboard CSS variables and styles */
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

    /* Dashboard Header */
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

    /* Stats Grid */
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
    
    .stat-warning .stat-icon-wrapper {
        background: var(--warning-light);
        color: var(--warning);
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

    /* Content Grid */
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

    /* Dashboard Cards */
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

    /* Header Actions - KEEP ORIGINAL STRUCTURE */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    /* Search Box - KEEP ORIGINAL STRUCTURE */
    .search-box {
        position: relative;
        min-width: 250px;
    }
    
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-500);
    }
    
    .search-box input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    /* Buttons - KEEP ORIGINAL STRUCTURE */
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

    /* Empty State - Update classes */
    .empty-state-modern {
        padding: 3rem 2rem;
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
    }

    /* Table - KEEP ORIGINAL STRUCTURE */
    .table-container {
        overflow-x: auto;
    }
    
    .quiz-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .quiz-table thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }
    
    .quiz-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--gray-600);
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    .quiz-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    
    .quiz-table tbody tr:hover {
        background: var(--gray-50);
    }

    /* Quiz Info - KEEP ORIGINAL STRUCTURE */
    .quiz-info {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .quiz-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    
    .quiz-title {
        font-weight: 500;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }
    
    .quiz-desc {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    /* Badges - KEEP ORIGINAL STRUCTURE with updated colors */
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: var(--primary-light);
        color: var(--primary-dark);
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Status - KEEP ORIGINAL STRUCTURE with updated colors */
    .status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status i {
        font-size: 0.5rem;
    }
    
    .status.published {
        color: var(--success-dark);
    }
    
    .status.draft {
        color: var(--danger-dark);
    }

    /* Date - KEEP ORIGINAL STRUCTURE */
    .date {
        font-weight: 500;
        color: var(--gray-800);
        font-size: 0.875rem;
    }
    
    .time {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.125rem;
    }

    /* Action Buttons - KEEP ORIGINAL STRUCTURE */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
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
    
    .btn-icon.view:hover {
        background: #dbeafe;
        transform: translateY(-1px);
    }
    
    .btn-icon.edit:hover {
        background: #fef3c7;
        transform: translateY(-1px);
    }
    
    .btn-icon.delete:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }

    /* Pagination - KEEP ORIGINAL STRUCTURE with updated classes */
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

    /* Quick Actions */
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

    /* Stats List - KEEP ORIGINAL STRUCTURE */
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

    /* Footer */
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
    
    .separator {
        opacity: 0.5;
    }

    /* Responsive Design */
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
        
        .card-header-modern {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .search-box {
            width: 100%;
            min-width: unset;
        }
        
        .action-buttons {
            flex-wrap: wrap;
        }
        
        .quiz-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .quiz-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple search functionality - EXACTLY AS IN ORIGINAL
        const searchInput = document.getElementById('search-input');
        const quizRows = document.querySelectorAll('.quiz-row');
        
        if (searchInput && quizRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                quizRows.forEach(row => {
                    const searchData = row.dataset.search || '';
                    
                    if (searchTerm === '' || searchData.includes(searchTerm)) {
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