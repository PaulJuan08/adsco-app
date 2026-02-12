@extends('layouts.student')

@section('title', 'Available Quizzes')

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
                    <h1 class="welcome-title">Available Quizzes</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-clipboard-list"></i> Browse and attempt available quizzes
                        @if($quizzes->count() > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $quizzes->count() }} available</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Using stats-grid-compact from quiz-index.css -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Quizzes</div>
                    <div class="stat-number">{{ $quizzes->count() }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Quizzes Passed</div>
                    @php
                        $passedCount = $attempts->where('passed', 1)->count();
                    @endphp
                    <div class="stat-number">{{ $passedCount }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">In Progress</div>
                    @php
                        $inProgress = $attempts->where('completed_at', null)->count();
                    @endphp
                    <div class="stat-number">{{ $inProgress }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Completion Rate</div>
                    @php
                        $passedCount = $attempts->where('passed', 1)->count();
                        $totalQuizzes = $quizzes->count();
                        $completionRate = $totalQuizzes > 0 ? round(($passedCount / $totalQuizzes) * 100) : 0;
                    @endphp
                    <div class="stat-number">{{ $completionRate }}%</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column - Quizzes List -->
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-clipboard-list"></i>
                        All Quizzes
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search quizzes..." id="search-quizzes">
                        </div>
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

                    @if($quizzes->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h3 class="empty-title">No quizzes available</h3>
                            <p class="empty-text">Check back later for new quizzes to test your knowledge.</p>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                New quizzes are added regularly to help you learn
                            </div>
                        </div>
                    @else
                        <!-- Quizzes Table -->
                        <div class="table-responsive">
                            <table class="quiz-table">
                                <thead>
                                    <tr>
                                        <th>Quiz Title</th>
                                        <th class="hide-on-mobile">Questions</th>
                                        <th class="hide-on-tablet">Passing Score</th>
                                        <th>Status</th>
                                        <th class="hide-on-tablet">Your Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizzes as $quiz)
                                    @php
                                        $attempt = $attempts[$quiz->id] ?? null;
                                        $now = now();
                                        $isAvailable = true;
                                        
                                        if ($quiz->available_from && $quiz->available_from > $now) {
                                            $isAvailable = false;
                                        }
                                        
                                        if ($quiz->available_until && $quiz->available_until < $now) {
                                            $isAvailable = false;
                                        }
                                        
                                        $statusClass = 'badge-info';
                                        $statusIcon = 'fa-play-circle';
                                        $statusText = 'Ready';
                                        
                                        if ($attempt) {
                                            if ($attempt->passed) {
                                                $statusClass = 'badge-success';
                                                $statusIcon = 'fa-check-circle';
                                                $statusText = 'Passed';
                                            } elseif ($attempt->completed_at) {
                                                $statusClass = 'badge-warning';
                                                $statusIcon = 'fa-times-circle';
                                                $statusText = 'Failed';
                                            } else {
                                                $statusClass = 'badge-primary';
                                                $statusIcon = 'fa-clock';
                                                $statusText = 'In Progress';
                                            }
                                        } elseif (!$isAvailable) {
                                            $statusClass = 'badge-secondary';
                                            $statusIcon = 'fa-calendar-times';
                                            $statusText = 'Not Available';
                                        }
                                    @endphp
                                    <tr class="clickable-row" 
                                        data-href="{{ route('student.quizzes.show', Crypt::encrypt($quiz->id)) }}"
                                        data-title="{{ strtolower($quiz->title) }}">
                                        <td>
                                            <div class="quiz-info-cell">
                                                <div class="quiz-icon">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                                <div class="quiz-details">
                                                    <div class="quiz-title">{{ $quiz->title }}</div>
                                                    <div class="quiz-desc">{{ Str::limit($quiz->description, 60) }}</div>
                                                    @if($quiz->available_from && $quiz->available_until)
                                                    <div class="quiz-mobile-info">
                                                        <div class="quiz-meta">
                                                            <i class="fas fa-question-circle"></i> {{ $quiz->questions_count }} Questions
                                                        </div>
                                                        <div class="quiz-meta">
                                                            <i class="fas fa-chart-line"></i> Pass: {{ $quiz->passing_score ?? 70 }}%
                                                        </div>
                                                        <div class="quiz-date">
                                                            <i class="far fa-calendar-alt"></i>
                                                            {{ $quiz->available_from->format('M d') }} - {{ $quiz->available_until->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hide-on-mobile">
                                            <span class="item-badge badge-info">{{ $quiz->questions_count }} Qs</span>
                                        </td>
                                        <td class="hide-on-tablet">
                                            <span class="item-badge badge-info">{{ $quiz->passing_score ?? 70 }}%</span>
                                        </td>
                                        <td>
                                            <span class="item-badge {{ $statusClass }}">
                                                <i class="fas {{ $statusIcon }}"></i> {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="hide-on-tablet">
                                            @if($attempt)
                                                <div class="students-count">
                                                    <div class="count-number" style="color: {{ $attempt->passed ? 'var(--success)' : ($attempt->completed_at ? 'var(--danger)' : 'var(--primary)') }};">
                                                        {{ $attempt->percentage ?? 0 }}%
                                                    </div>
                                                    @if($attempt->completed_at)
                                                        <div class="count-label">{{ $attempt->score }}/{{ $attempt->total_points }}</div>
                                                    @else
                                                        <div class="count-label">In progress</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="no-attempt">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($quizzes instanceof \Illuminate\Pagination\AbstractPaginator && $quizzes->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $quizzes->firstItem() }} to {{ $quizzes->lastItem() }} of {{ $quizzes->total() }} quizzes
                    </div>
                    <div class="pagination-links">
                        {{ $quizzes->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Sidebar Stats -->
        <div class="right-column">
            <!-- Progress Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Quiz Overview
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Quizzes</div>
                            </div>
                            <div class="stat-number">{{ $quizzes->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Quizzes Passed</div>
                            </div>
                            <div class="stat-number">{{ $attempts->where('passed', 1)->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">In Progress</div>
                            </div>
                            <div class="stat-number">{{ $attempts->where('completed_at', null)->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--danger-light), var(--danger));">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Failed Quizzes</div>
                            </div>
                            <div class="stat-number">{{ $attempts->where('passed', 0)->whereNotNull('completed_at')->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-percent"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Average Score</div>
                            </div>
                            <div class="stat-number">
                                @php
                                    $completedAttempts = $attempts->whereNotNull('completed_at');
                                    $avgScore = $completedAttempts->count() > 0 ? round($completedAttempts->avg('percentage')) : 0;
                                @endphp
                                {{ $avgScore }}%
                            </div>
                        </div>
                    </div>

                    @php
                        $passedQuizzes = $attempts->where('passed', 1)->count();
                        $totalQuizzes = $quizzes->count();
                        $progress = $totalQuizzes > 0 ? round(($passedQuizzes / $totalQuizzes) * 100) : 0;
                    @endphp
                    
                    <div class="progress-section" style="margin-top: 1.5rem;">
                        <div class="progress-header">
                            <span style="font-size: var(--font-size-sm); color: var(--gray-600);">Overall Completion</span>
                            <strong style="color: var(--gray-900);">{{ $progress }}%</strong>
                        </div>
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attempts Card -->
            @if($attempts->whereNotNull('completed_at')->isNotEmpty())
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-history"></i>
                        Recent Attempts
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        @foreach($attempts->whereNotNull('completed_at')->sortByDesc('completed_at')->take(5) as $attempt)
                        @php
                            $quiz = $quizzes->where('id', $attempt->quiz_id)->first();
                            if (!$quiz) continue;
                        @endphp
                        <a href="{{ route('student.quizzes.show', Crypt::encrypt($quiz->id)) }}" class="list-item clickable-item">
                            <div class="item-avatar" style="background: {{ $attempt->passed ? 'var(--success-light)' : 'var(--danger-light)' }}; color: {{ $attempt->passed ? 'var(--success)' : 'var(--danger)' }};">
                                <i class="fas {{ $attempt->passed ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ Str::limit($quiz->title, 30) }}</div>
                                <div class="item-meta">
                                    <i class="far fa-clock"></i> {{ $attempt->completed_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="stat-number" style="font-size: 1rem; color: {{ $attempt->passed ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $attempt->percentage }}%
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Tips Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-lightbulb" style="color: var(--warning);"></i>
                        Quiz Tips
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-infinity"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Unlimited Retakes</div>
                                <div class="item-meta">Retake quizzes anytime to improve</div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Track Progress</div>
                                <div class="item-meta">Monitor your improvement over time</div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Passing Score</div>
                                <div class="item-meta">Need {{ $quizzes->first()?->passing_score ?? 70 }}% to pass</div>
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
        <p class="footer-note">
            Student Portal • Unlimited Retakes • Last accessed {{ now()->format('M d, Y h:i A') }}
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
        const searchInput = document.getElementById('search-quizzes');
        const quizRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && quizRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                quizRows.forEach(row => {
                    const quizTitle = row.dataset.title || '';
                    if (searchTerm === '' || quizTitle.includes(searchTerm)) {
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
                if (alert) alert.remove();
            }, 5000);
        });
    });
</script>
@endpush