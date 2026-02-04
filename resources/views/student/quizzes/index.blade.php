@extends('layouts.student')

@section('title', 'Quizzes - Student Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Quizzes</h1>
        <p>Test your knowledge and track your progress</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
        <span class="badge badge-student">
            <i class="fas fa-user-graduate"></i> Student
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $availableQuizzes->count() }}</div>
                <div class="stat-label">Available Quizzes</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-question-circle"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-clock text-info"></i> Ready to take
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $completedQuizzes->count() }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-trophy text-warning"></i> Quizzes finished
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $passedAttempts->count() }}</div>
                <div class="stat-label">Passed</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-award"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-star text-success"></i> Success rate
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $averageScore }}%</div>
                <div class="stat-label">Average Score</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-percentage text-primary"></i> Overall performance
        </div>
    </div>
</div>

<!-- Quiz Tabs -->
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" data-tab="available">Available Quizzes</button>
        <button class="tab-btn" data-tab="attempts">My Attempts</button>
    </div>
    
    <!-- Available Quizzes Tab -->
    <div class="tab-content active" id="available-tab">
        @if($availableQuizzes->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>No quizzes available</h3>
            <p>There are no quizzes available for you to take at the moment.</p>
            <p class="text-sm">Check back later or contact your teacher for quiz availability.</p>
        </div>
        @else
        <div class="quiz-grid">
            @foreach($availableQuizzes as $quiz)
            @php
                $hasAttempted = $completedAttempts->contains('quiz_id', $quiz->id);
                $quizAttempt = $hasAttempted ? $completedAttempts->firstWhere('quiz_id', $quiz->id) : null;
                $encryptedId = Crypt::encrypt($quiz->id);
            @endphp
            <div class="quiz-card">
                <div class="quiz-card-header">
                    <div class="quiz-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="quiz-status">
                        @if($hasAttempted)
                            <span class="badge badge-success">Completed</span>
                        @elseif($quiz->available_from && $quiz->available_from > now())
                            <span class="badge badge-warning">Upcoming</span>
                        @else
                            <span class="badge badge-primary">Available</span>
                        @endif
                    </div>
                </div>
                <div class="quiz-card-body">
                    <h3 class="quiz-title">{{ $quiz->title }}</h3>
                    <p class="quiz-description">{{ Str::limit($quiz->description, 100) }}</p>
                    
                    <div class="quiz-meta">
                        <div class="meta-item">
                            <i class="fas fa-list-ol"></i>
                            <span>{{ $quiz->questions->count() ?? 0 }} questions</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $quiz->duration ?? 'Unlimited' }} minutes</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-percentage"></i>
                            <span>{{ $quiz->passing_score ?? 70 }}% to pass</span>
                        </div>
                    </div>
                    
                    @if($quiz->available_from && $quiz->available_from > now())
                    <div class="upcoming-notice">
                        <i class="fas fa-calendar-alt"></i>
                        Available from: {{ $quiz->available_from->format('M d, Y H:i') }}
                    </div>
                    @endif
                    
                    @if($quiz->available_until && $quiz->available_until < now())
                    <div class="expired-notice">
                        <i class="fas fa-exclamation-triangle"></i>
                        This quiz has expired
                    </div>
                    @endif
                    
                    @if($hasAttempted && $quizAttempt)
                    <div class="attempt-result">
                        <div class="result-header">
                            <span>Your Score</span>
                            <span class="result-score {{ $quizAttempt->passed ? 'passed' : 'failed' }}">
                                {{ $quizAttempt->percentage }}%
                            </span>
                        </div>
                        <div class="result-details">
                            <div class="result-item">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ $quizAttempt->score }}/{{ $quizAttempt->total_questions }} correct</span>
                            </div>
                            <div class="result-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $quizAttempt->time_taken }} minutes</span>
                            </div>
                            <div class="result-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $quizAttempt->completed_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="quiz-card-footer">
                    @if($hasAttempted)
                        <a href="{{ route('student.quizzes.results', $encryptedId) }}" class="btn btn-success">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
                        <button class="btn btn-outline btn-sm" 
                                data-quiz-id="{{ $attempt->quiz->encrypted_id ?? '' }}"
                                onclick="retakeQuiz('{{ $attempt->quiz->encrypted_id ?? '' }}')">
                            Retake
                        </button>
                    @elseif($quiz->available_from && $quiz->available_from > now())
                        <button class="btn btn-outline" disabled>
                            <i class="fas fa-clock"></i> Not Available Yet
                        </button>
                    @elseif($quiz->available_until && $quiz->available_until < now())
                        <button class="btn btn-outline" disabled>
                            <i class="fas fa-times-circle"></i> Quiz Expired
                        </button>
                    @else
                        <a href="{{ route('student.quizzes.take', $encryptedId) }}" class="btn btn-primary">
                            <i class="fas fa-play"></i> Start Quiz
                        </a>
                        <a href="{{ route('student.quizzes.instructions', $encryptedId) }}" class="btn btn-outline">
                            <i class="fas fa-info-circle"></i> Instructions
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    
    <!-- My Attempts Tab -->
    <div class="tab-content" id="attempts-tab">
        @if($completedAttempts->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-history"></i>
            </div>
            <h3>No quiz attempts yet</h3>
            <p>You haven't taken any quizzes yet.</p>
            <button class="btn btn-primary switch-tab" data-tab="available">
                <i class="fas fa-question-circle"></i>
                Browse Available Quizzes
            </button>
        </div>
        @else
        <div class="table-container">
            <table class="attempts-table">
                <thead>
                    <tr>
                        <th>Quiz</th>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedAttempts as $attempt)
                    @php
                        $encryptedQuizId = Crypt::encrypt($attempt->quiz_id);
                    @endphp
                    <tr>
                        <td>
                            <div class="attempt-quiz">
                                <div class="quiz-icon small">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div>
                                    <div class="quiz-name">{{ $attempt->quiz->title }}</div>
                                    <div class="quiz-questions">{{ $attempt->total_questions }} questions</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="attempt-date">
                                <div class="date">{{ $attempt->completed_at->format('M d, Y') }}</div>
                                <div class="time">{{ $attempt->completed_at->format('h:i A') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="score-display">
                                <div class="score-percent {{ $attempt->passed ? 'passed' : 'failed' }}">
                                    {{ $attempt->percentage }}%
                                </div>
                                <div class="score-detail">{{ $attempt->score }}/{{ $attempt->total_questions }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="time-taken">
                                <i class="fas fa-clock"></i>
                                {{ $attempt->time_taken }} mins
                            </div>
                        </td>
                        <td>
                            @if($attempt->passed)
                                <span class="status-badge success">
                                    <i class="fas fa-check-circle"></i> Passed
                                </span>
                            @else
                                <span class="status-badge failed">
                                    <i class="fas fa-times-circle"></i> Failed
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('student.quizzes.results', $encryptedQuizId) }}" 
                                   class="btn-icon view" title="View Results">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($attempt->quiz->isAvailable())
                                <a href="{{ route('student.quizzes.take', $encryptedQuizId) }}" 
                                   class="btn-icon retake" title="Retake Quiz">
                                    <i class="fas fa-redo"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal" id="retakeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Retake Quiz</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to retake this quiz?</p>
            <div class="modal-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Your previous attempt will be replaced with the new score.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline modal-close">Cancel</button>
            <form id="retakeForm" method="GET" style="display: none;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Yes, Retake Quiz
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Responsive Design */
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
    }

    .stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--secondary);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .text-sm {
        font-size: 0.75rem;
    }

    .text-secondary {
        color: var(--secondary);
    }

    /* Tabs */
    .tabs-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .tabs-header {
        display: flex;
        border-bottom: 1px solid var(--border);
        background: #f9fafb;
    }

    .tab-btn {
        padding: 1rem 2rem;
        background: none;
        border: none;
        font-size: 0.9375rem;
        font-weight: 500;
        color: var(--secondary);
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
    }

    .tab-btn:hover {
        color: var(--primary);
        background: rgba(67, 97, 238, 0.05);
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background: white;
    }

    @media (max-width: 576px) {
        .tabs-header {
            flex-direction: column;
        }
        
        .tab-btn {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 2rem;
    }

    .tab-content.active {
        display: block;
    }

    @media (max-width: 768px) {
        .tab-content {
            padding: 1rem;
        }
    }

    /* Quiz Grid */
    .quiz-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .quiz-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .quiz-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }

    @media (max-width: 576px) {
        .quiz-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Quiz Card */
    .quiz-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .quiz-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .quiz-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.5rem 1.5rem 0;
    }

    .quiz-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
    }

    .quiz-icon.small {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
        border-radius: 10px;
    }

    .quiz-card-body {
        padding: 1rem 1.5rem;
    }

    .quiz-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .quiz-description {
        font-size: 0.875rem;
        color: var(--secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .quiz-meta {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 480px) {
        .quiz-meta {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 0.5rem;
        background: #f9fafb;
        border-radius: 6px;
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .meta-item i {
        font-size: 0.875rem;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }

    .upcoming-notice {
        padding: 0.75rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 8px;
        margin: 1rem 0;
        border: 1px solid #fbbf24;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #92400e;
    }

    .expired-notice {
        padding: 0.75rem;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 8px;
        margin: 1rem 0;
        border: 1px solid #ef4444;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #991b1b;
    }

    .attempt-result {
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-top: 1rem;
        border: 1px solid var(--border);
    }

    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        font-weight: 500;
        color: var(--dark);
    }

    .result-score {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .result-score.passed {
        color: #10b981;
    }

    .result-score.failed {
        color: #ef4444;
    }

    .result-details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }

    @media (max-width: 480px) {
        .result-details {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .result-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .result-item i {
        font-size: 0.625rem;
    }

    .quiz-card-footer {
        padding: 1rem 1.5rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #4f46e5;
        transform: translateY(-1px);
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #0da271;
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary-light);
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-student {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-primary {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .badge-success {
        background: #dcfce7;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: #d1d5db;
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
        line-height: 1.5;
    }

    .switch-tab {
        margin: 0 auto;
    }

    /* Table */
    .table-container {
        overflow-x: auto;
    }

    .attempts-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attempts-table thead {
        background: #f8fafc;
        border-bottom: 2px solid var(--border);
    }

    .attempts-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .attempts-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .attempts-table tbody tr:hover {
        background: #f9fafb;
    }

    .attempt-quiz {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .quiz-name {
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .quiz-questions {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .attempt-date {
        display: flex;
        flex-direction: column;
    }

    .date {
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .time {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.125rem;
    }

    .score-display {
        text-align: center;
    }

    .score-percent {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.125rem;
    }

    .score-percent.passed {
        color: #10b981;
    }

    .score-percent.failed {
        color: #ef4444;
    }

    .score-detail {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .time-taken {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.875rem;
        color: var(--dark);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge.success {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.failed {
        background: #fee2e2;
        color: #991b1b;
    }

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

    .btn-icon.retake {
        color: #f59e0b;
    }

    .btn-icon.view:hover {
        background: #dbeafe;
    }

    .btn-icon.retake:hover {
        background: #fef3c7;
    }

    @media (max-width: 576px) {
        .attempts-table th,
        .attempts-table td {
            padding: 0.75rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 400px;
        animation: modalSlide 0.3s ease;
    }

    @keyframes modalSlide {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .modal-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--secondary);
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .modal-close:hover {
        background: var(--light);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-body p {
        color: var(--dark);
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .modal-warning {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.75rem;
        background: #fef3c7;
        border-radius: 6px;
        font-size: 0.875rem;
        color: #92400e;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
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
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                
                // Update active tab button
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update active tab content
                tabContents.forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });

        // Switch tab from empty state
        document.querySelectorAll('.switch-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                const targetTab = document.querySelector(`[data-tab="${tabId}"]`);
                if (targetTab) {
                    targetTab.click();
                }
            });
        });

        // Retake quiz confirmation modal
        const modal = document.getElementById('retakeModal');
        const retakeBtns = document.querySelectorAll('.retake-btn');
        const closeModalBtns = document.querySelectorAll('.modal-close');
        const retakeForm = document.getElementById('retakeForm');
        
        retakeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const quizId = this.dataset.quizId;
                
                // Set form action
                retakeForm.action = "{{ route('student.quizzes.take', 'placeholder') }}".replace('placeholder', encodeURIComponent(btoa(quizId)));
                
                // Show modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        // Close modal
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        // Close modal on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Handle success/error messages
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif

        function showNotification(message, type) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
                <button class="alert-close">&times;</button>
            `;
            
            alert.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem;
                border-radius: 8px;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                z-index: 9999;
                max-width: 400px;
                animation: slideIn 0.3s ease;
            `;
            
            if (type === 'success') {
                alert.style.backgroundColor = '#dcfce7';
                alert.style.color = '#065f46';
                alert.style.border = '1px solid #bbf7d0';
            } else {
                alert.style.backgroundColor = '#fee2e2';
                alert.style.color = '#991b1b';
                alert.style.border = '1px solid #fecaca';
            }
            
            document.body.appendChild(alert);
            
            // Close button
            alert.querySelector('.alert-close').addEventListener('click', () => {
                alert.remove();
            });
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }
    });

    // Add slide in animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
@endsection