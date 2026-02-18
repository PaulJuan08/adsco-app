@extends('layouts.admin')

@section('title', 'Quizzes - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-index.css') }}">
@endpush

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
                    <h1 class="welcome-title">Quiz Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-question-circle"></i> Manage all assessment quizzes
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.quizzes.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Quizzes</div>
                    <div class="stat-number">{{ number_format($quizzes->total()) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
            </div>
            <div class="stat-link">
                View all quizzes <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.quizzes.index') }}?status=published" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published Quizzes</div>
                    <div class="stat-number">{{ number_format($quizzes->where('is_published', true)->count()) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="stat-link">
                View published <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Quiz List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-question-circle" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quiz List
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search quizzes..." id="search-input">
                        </div>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Quiz
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($quizzes->isEmpty())
                    <!-- Empty State -->
                    <div class="empty-state">
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
                    <div class="table-responsive">
                        <table class="quiz-table" id="quiz-table">
                            <thead>
                                <tr>
                                    <th>Quiz Title</th>
                                    <th>Questions</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quizzes as $quiz)
                                <tr class="clickable-row" 
                                    data-href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}"
                                    data-search="{{ strtolower($quiz->title . ' ' . $quiz->description) }}">
                                    <td>
                                        <div class="quiz-info-cell">
                                            <div class="quiz-icon">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <div class="quiz-details">
                                                <div class="quiz-title">{{ $quiz->title }}</div>
                                                <div class="quiz-desc">{{ Str::limit($quiz->description, 60) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="item-badge badge-info">{{ $quiz->total_questions ?? 0 }} Qs</span>
                                    </td>
                                    <td>
                                        @if($quiz->is_published)
                                            <span class="item-badge badge-success">
                                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.25rem;"></i> Public
                                            </span>
                                        @else
                                            <span class="item-badge badge-warning">
                                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.25rem;"></i> Private
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="created-date">{{ $quiz->created_at->format('M d, Y') }}</div>
                                        <div class="created-ago">{{ $quiz->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                @if($quizzes->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $quizzes->firstItem() }} to {{ $quizzes->lastItem() }} of {{ $quizzes->total() }} entries
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
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
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
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quiz Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <a href="{{ route('admin.quizzes.index') }}?month={{ now()->format('Y-m') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Quizzes This Month</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">
                                {{ $quizzes->where('created_at', '>=', now()->startOfMonth())->count() }}
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.quizzes.index') }}?status=published" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Published Quizzes</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $quizzes->where('is_published', true)->count() }}</div>
                        </a>
                        
                        <a href="{{ route('admin.quizzes.index') }}?status=draft" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Private Quizzes</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $quizzes->where('is_published', false)->count() }}</div>
                        </a>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Avg Questions</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">
                                {{ $quizzes->count() > 0 ? round($quizzes->sum('total_questions') / $quizzes->count()) : 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Quiz Management • Updated {{ now()->format('M d, Y') }}
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
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
            
            row.style.cursor = 'pointer';
        });

        // Search functionality
        const searchInput = document.getElementById('search-input');
        const quizRows = document.querySelectorAll('.clickable-row');
        
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