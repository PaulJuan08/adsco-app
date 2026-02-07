@extends('layouts.student')

@section('title', 'Available Quizzes')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="header-content">
        <div>
            <h1 class="page-title">
                <i class="fas fa-clipboard-list"></i>
                Available Quizzes
            </h1>
            <p class="page-subtitle">Browse and attempt available quizzes. Unlimited retakes to improve your score.</p>
        </div>
        <div class="header-badge">
            <i class="fas fa-user-graduate"></i>
            <span>Student Portal</span>
        </div>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="alert-message alert-success">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="alert-content">
            <strong>Success!</strong>
            <p>{{ session('success') }}</p>
        </div>
        <button class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert-message alert-error">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-content">
            <strong>Error!</strong>
            <p>{{ session('error') }}</p>
        </div>
        <button class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if($quizzes->isEmpty())
    <div class="empty-state-card">
        <i class="fas fa-clipboard-list"></i>
        <h3>No Quizzes Available</h3>
        <p>Check back later for new quizzes to test your knowledge.</p>
    </div>
@else
    <!-- Statistics Cards -->
    <div class="quiz-stats-grid">
        <div class="quiz-stat-card stat-primary">
            <div class="stat-content">
                <div class="stat-label">Total Quizzes</div>
                <div class="stat-value">{{ $quizzes->count() }}</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>

        <div class="quiz-stat-card stat-success">
            <div class="stat-content">
                <div class="stat-label">Quizzes Completed</div>
                @php
                    $passedCount = $attempts->where('passed', 1)->count();
                @endphp
                <div class="stat-value">{{ $passedCount }}</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="quiz-stat-card stat-warning">
            <div class="stat-content">
                <div class="stat-label">Quizzes Passed</div>
                @php
                    $passedCount = $attempts->where('passed', 1)->count();
                @endphp
                <div class="stat-value">{{ $passedCount }}</div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>

    <div class="quiz-layout">
        <!-- Main Quizzes List -->
        <div class="quiz-main">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-list-alt"></i>
                        All Quizzes
                    </h2>
                </div>
                
                <div class="quiz-list">
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
                            
                            $statusClass = 'status-ready';
                            $statusIcon = 'fa-play-circle';
                            $statusText = 'Ready';
                            $buttonText = 'Start Quiz';
                            $buttonClass = 'btn-primary';
                            
                            if ($attempt) {
                                if ($attempt->passed) {
                                    $statusClass = 'status-passed';
                                    $statusIcon = 'fa-check-circle';
                                    $statusText = 'Passed';
                                    $buttonText = 'View Results';
                                    $buttonClass = 'btn-success';
                                } else {
                                    $statusClass = 'status-failed';
                                    $statusIcon = 'fa-times-circle';
                                    $statusText = 'Failed';
                                    $buttonText = 'Retake Quiz';
                                    $buttonClass = 'btn-warning';
                                }
                            } elseif (!$isAvailable) {
                                $statusClass = 'status-unavailable';
                                $statusIcon = 'fa-clock';
                                $statusText = 'Not Available';
                                $buttonText = 'View Details';
                                $buttonClass = 'btn-secondary';
                            }
                        @endphp

                        <div class="quiz-card">
                            <div class="quiz-card-header">
                                <div class="quiz-title-section">
                                    <h3 class="quiz-title">{{ $quiz->title }}</h3>
                                    <span class="quiz-status {{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }}"></i>
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="quiz-description">{{ Str::limit($quiz->description, 150) }}</p>
                            
                            <div class="quiz-meta">
                                <div class="meta-item">
                                    <i class="fas fa-question-circle"></i>
                                    <span><strong>{{ $quiz->questions_count }}</strong> Questions</span>
                                </div>
                                @if($quiz->passing_score)
                                    <div class="meta-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Passing: <strong>{{ $quiz->passing_score }}%</strong></span>
                                    </div>
                                @endif
                                <div class="meta-item">
                                    <i class="fas fa-infinity"></i>
                                    <span><strong>Unlimited</strong> Attempts</span>
                                </div>
                            </div>
                            
                            @if($quiz->available_from && $quiz->available_until)
                                <div class="quiz-availability">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ $quiz->available_from->format('M d, Y') }} - {{ $quiz->available_until->format('M d, Y') }}</span>
                                </div>
                            @endif
                            
                            @if($attempt)
                                <div class="quiz-score-card">
                                    <div class="score-info">
                                        <div class="score-label">Best Score</div>
                                        <div class="score-value {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                                            {{ $attempt->percentage }}%
                                        </div>
                                    </div>
                                    <div class="score-details">
                                        <div>{{ $attempt->score }}/{{ $attempt->total_points }} points</div>
                                        <div class="score-date">
                                            <i class="far fa-clock"></i>
                                            {{ $attempt->completed_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($isAvailable)
                                <div class="quiz-card-footer">
                                    @if($attempt && !$attempt->passed && $buttonText === 'Retake Quiz')
                                        <form action="{{ route('student.quizzes.retake', Crypt::encrypt($quiz->id)) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="quiz-btn {{ $buttonClass }}">
                                                <i class="fas fa-redo"></i>
                                                {{ $buttonText }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('student.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                           class="quiz-btn {{ $buttonClass }}">
                                            <i class="fas fa-arrow-right"></i>
                                            {{ $buttonText }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="quiz-sidebar">
            <!-- Progress Card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h3 class="sidebar-title">
                        <i class="fas fa-chart-pie"></i>
                        Your Progress
                    </h3>
                </div>
                <div class="sidebar-card-body">
                    @php
                        $passedQuizzes = $attempts->where('passed', 1)->count();
                        $totalQuizzes = $quizzes->count();
                        $progress = $totalQuizzes > 0 ? round(($passedQuizzes / $totalQuizzes) * 100) : 0;
                    @endphp
                    
                    <div class="progress-section">
                        <div class="progress-header">
                            <span>Quizzes Passed</span>
                            <strong>{{ $progress }}%</strong>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    
                    <div class="stats-breakdown">
                        <div class="breakdown-item item-success">
                            <i class="fas fa-check-circle"></i>
                            <span>Passed Quizzes</span>
                            <strong>{{ $attempts->where('passed', 1)->count() }}</strong>
                        </div>
                        
                        <div class="breakdown-item item-danger">
                            <i class="fas fa-times-circle"></i>
                            <span>Failed Quizzes</span>
                            <strong>{{ $attempts->where('passed', 0)->count() }}</strong>
                        </div>
                        
                        <div class="breakdown-item item-info">
                            <i class="fas fa-infinity"></i>
                            <span>Retakes Allowed</span>
                            <strong>Unlimited</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Attempts -->
            @if($attempts->isNotEmpty())
                <div class="sidebar-card">
                    <div class="sidebar-card-header">
                        <h3 class="sidebar-title">
                            <i class="fas fa-history"></i>
                            Recent Attempts
                        </h3>
                    </div>
                    <div class="sidebar-card-body">
                        <div class="recent-attempts">
                            @foreach($attempts->take(5) as $attempt)
                                @php
                                    $quiz = $quizzes->where('id', $attempt->quiz_id)->first();
                                    if (!$quiz) continue;
                                @endphp
                                <div class="attempt-item">
                                    <div class="attempt-info">
                                        <div class="attempt-title">{{ Str::limit($quiz->title, 35) }}</div>
                                        <div class="attempt-date">
                                            <i class="far fa-clock"></i>
                                            {{ $attempt->completed_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="attempt-score {{ $attempt->passed ? 'score-passed' : 'score-failed' }}">
                                        <div class="score-percentage">{{ $attempt->percentage }}%</div>
                                        <div class="score-badge">{{ $attempt->passed ? 'Passed' : 'Failed' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }
    
    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .page-title i {
        color: #10b981;
        font-size: 1.75rem;
    }
    
    .page-subtitle {
        color: #6b7280;
        font-size: 1rem;
        margin: 0;
    }
    
    .header-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        white-space: nowrap;
    }
    
    /* Alert Messages */
    .alert-message {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .alert-success {
        background: #d1fae5;
        border: 1px solid #10b981;
    }
    
    .alert-error {
        background: #fee2e2;
        border: 1px solid #ef4444;
    }
    
    .alert-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .alert-success .alert-icon {
        color: #10b981;
    }
    
    .alert-error .alert-icon {
        color: #ef4444;
    }
    
    .alert-content {
        flex: 1;
    }
    
    .alert-content strong {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .alert-content p {
        margin: 0;
        font-size: 0.875rem;
        opacity: 0.9;
    }
    
    .alert-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        opacity: 0.5;
        transition: opacity 0.2s;
    }
    
    .alert-close:hover {
        opacity: 1;
    }
    
    /* Quiz Stats Grid */
    .quiz-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .quiz-stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .quiz-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: currentColor;
        opacity: 0.05;
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    
    .quiz-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }
    
    .stat-primary { color: #3b82f6; }
    .stat-success { color: #10b981; }
    .stat-warning { color: #f59e0b; }
    
    .stat-content {
        z-index: 1;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111827;
        line-height: 1;
    }
    
    .stat-icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        background: currentColor;
        color: white;
        opacity: 0.9;
    }
    
    /* Quiz Layout */
    .quiz-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 2rem;
    }
    
    /* Section Card */
    .section-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .section-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .section-title i {
        color: #10b981;
    }
    
    /* Quiz List */
    .quiz-list {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    /* Quiz Card */
    .quiz-card {
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.75rem;
        transition: all 0.3s ease;
    }
    
    .quiz-card:hover {
        border-color: #10b981;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.1);
        transform: translateY(-2px);
    }
    
    .quiz-card-header {
        margin-bottom: 1rem;
    }
    
    .quiz-title-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .quiz-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        flex: 1;
    }
    
    .quiz-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .status-ready {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .status-passed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-failed {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .status-unavailable {
        background: #f3f4f6;
        color: #4b5563;
    }
    
    .quiz-description {
        color: #6b7280;
        font-size: 0.9375rem;
        line-height: 1.6;
        margin: 0 0 1.25rem 0;
    }
    
    .quiz-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .meta-item i {
        color: #10b981;
        font-size: 1rem;
    }
    
    .quiz-availability {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: #f9fafb;
        border-radius: 8px;
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    
    .quiz-availability i {
        color: #10b981;
    }
    
    .quiz-score-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #86efac;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .score-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .score-label {
        font-size: 0.75rem;
        color: #065f46;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .score-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .text-success {
        color: #10b981;
    }
    
    .text-danger {
        color: #ef4444;
    }
    
    .score-details {
        text-align: right;
        font-size: 0.875rem;
        color: #065f46;
    }
    
    .score-date {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.25rem;
        opacity: 0.8;
    }
    
    .quiz-card-footer {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .quiz-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9375rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
    }
    
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
    }
    
    /* Form button styling */
    form button.quiz-btn,
    form button.action-btn {
        font-family: 'Inter', sans-serif;
    }
    
    /* Sidebar */
    .quiz-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .sidebar-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .sidebar-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
    }
    
    .sidebar-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .sidebar-title i {
        color: #10b981;
    }
    
    .sidebar-card-body {
        padding: 1.5rem;
    }
    
    .progress-section {
        margin-bottom: 1.5rem;
    }
    
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
    }
    
    .progress-header span {
        color: #6b7280;
        font-weight: 500;
    }
    
    .progress-header strong {
        color: #10b981;
        font-size: 1.125rem;
    }
    
    .progress-bar-container {
        height: 12px;
        background: #e5e7eb;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 20px;
        transition: width 1s ease;
    }
    
    .stats-breakdown {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .breakdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .breakdown-item:hover {
        transform: translateX(4px);
    }
    
    .item-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    }
    
    .item-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    }
    
    .item-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    }
    
    .breakdown-item i {
        font-size: 1.25rem;
    }
    
    .item-success i { color: #10b981; }
    .item-danger i { color: #ef4444; }
    .item-info i { color: #3b82f6; }
    
    .breakdown-item span {
        flex: 1;
        font-size: 0.875rem;
        color: #374151;
        font-weight: 500;
    }
    
    .breakdown-item strong {
        font-size: 1rem;
        color: #111827;
    }
    
    /* Recent Attempts */
    .recent-attempts {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .attempt-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    
    .attempt-item:hover {
        background: white;
        border-color: #10b981;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
    }
    
    .attempt-info {
        flex: 1;
    }
    
    .attempt-title {
        font-weight: 600;
        color: #111827;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .attempt-date {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .attempt-score {
        text-align: right;
    }
    
    .score-percentage {
        font-size: 1.125rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .score-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
        display: inline-block;
    }
    
    .score-passed .score-percentage {
        color: #10b981;
    }
    
    .score-passed .score-badge {
        background: #d1fae5;
        color: #065f46;
    }
    
    .score-failed .score-percentage {
        color: #ef4444;
    }
    
    .score-failed .score-badge {
        background: #fee2e2;
        color: #991b1b;
    }
    
    /* Empty State */
    .empty-state-card {
        background: white;
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
        border: 2px dashed #e5e7eb;
    }
    
    .empty-state-card i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1.5rem;
    }
    
    .empty-state-card h3 {
        font-size: 1.5rem;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .empty-state-card p {
        color: #6b7280;
        font-size: 1rem;
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .quiz-layout {
            grid-template-columns: 1fr;
        }
        
        .quiz-sidebar {
            order: -1;
        }
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .header-content {
            flex-direction: column;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
        
        .quiz-stats-grid {
            grid-template-columns: 1fr;
        }
        
        .quiz-card {
            padding: 1.25rem;
        }
        
        .quiz-list {
            padding: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
        }
    }
</style>
@endpush