@extends('layouts.student')

@section('title', 'Available Quizzes')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Available Quizzes</h1>
        <p>Take quizzes and test your knowledge</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Quiz Stats -->
<div class="quick-stats">
    <div class="stat-item">
        <div class="stat-number">{{ $quizzes->count() }}</div>
        <div class="stat-label">Available Quizzes</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">{{ $attempts->count() }}</div>
        <div class="stat-label">Attempts Made</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">{{ $attempts->where('passed', true)->count() }}</div>
        <div class="stat-label">Quizzes Passed</div>
    </div>
</div>

<!-- Quiz List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Quiz List</h2>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search quizzes..." id="search-input">
            </div>
        </div>
    </div>

    @if($quizzes->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-question-circle"></i>
        </div>
        <h3>No quizzes available</h3>
        <p>There are no quizzes available at the moment.</p>
    </div>
    @else
    <div class="table-container">
        <table class="quiz-table">
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Questions</th>
                    <th>Duration</th>
                    <th>Due Date</th>
                    <th>Your Score</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizzes as $quiz)
                @php
                    $attempt = $attempts[$quiz->id] ?? null;
                    $completedAttempts = $attempts->where('quiz_id', $quiz->id)->count();
                    $canRetake = !$attempt || ($quiz->max_attempts == 0 || $completedAttempts < $quiz->max_attempts);
                @endphp
                <tr class="quiz-row" data-search="{{ strtolower($quiz->title . ' ' . $quiz->description) }}">
                    <td>
                        <div class="quiz-info">
                            <div class="quiz-icon" style="background: linear-gradient(135deg, {{ $attempt && $attempt->passed ? '#10b981' : '#3b82f6' }} 0%, {{ $attempt && $attempt->passed ? '#059669' : '#2563eb' }} 100%);">
                                <i class="fas {{ $attempt ? 'fa-check-circle' : 'fa-question-circle' }}"></i>
                            </div>
                            <div>
                                <div class="quiz-title">{{ $quiz->title }}</div>
                                <div class="quiz-desc">{{ Str::limit($quiz->description, 60) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge">{{ $quiz->questions_count ?? 0 }} Qs</span>
                    </td>
                    <td>
                        @if($quiz->duration)
                            <div class="duration">{{ $quiz->duration }} min</div>
                        @else
                            <div class="duration text-muted">Unlimited</div>
                        @endif
                    </td>
                    <td>
                        @if($quiz->available_until)
                            <div class="date">{{ \Carbon\Carbon::parse($quiz->available_until)->format('M d, Y') }}</div>
                            <div class="time {{ \Carbon\Carbon::parse($quiz->available_until)->isPast() ? 'text-danger' : 'text-muted' }}">
                                {{ \Carbon\Carbon::parse($quiz->available_until)->diffForHumans() }}
                            </div>
                        @else
                            <div class="text-muted">No due date</div>
                        @endif
                    </td>
                    <td>
                        @if($attempt && $attempt->completed_at)
                            <div class="score-display">
                                <div class="score-number {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                                    {{ $attempt->score }}/{{ $attempt->total_points }}
                                </div>
                                <div class="score-percentage">
                                    ({{ $attempt->percentage }}%)
                                </div>
                            </div>
                        @else
                            <div class="text-muted">Not attempted</div>
                        @endif
                    </td>
                    <td>
                        @if($attempt && $attempt->completed_at)
                            @if($attempt->passed)
                                <span class="status passed">
                                    <i class="fas fa-check-circle"></i> Passed
                                </span>
                            @else
                                <span class="status failed">
                                    <i class="fas fa-times-circle"></i> Failed
                                </span>
                            @endif
                        @else
                            <span class="status available">
                                <i class="fas fa-clock"></i> Available
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if($attempt && $attempt->completed_at)
                                <!-- Already completed - can retake or view -->
                                @if($canRetake)
                                    <a href="{{ route('student.quizzes.retake', Crypt::encrypt($quiz->id)) }}" 
                                       class="btn-icon retake" title="Retake Quiz"
                                       onclick="return confirm('Are you sure you want to retake this quiz?');">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                @endif
                                <a href="{{ route('student.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                   class="btn-icon view" title="View Quiz">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @else
                                <!-- Not attempted or incomplete - start/continue -->
                                <a href="{{ route('student.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                   class="btn-icon start" title="{{ $attempt ? 'Continue Quiz' : 'Start Quiz' }}">
                                    <i class="fas {{ $attempt ? 'fa-play-circle' : 'fa-play' }}"></i>
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

<style>
    /* Quick Stats */
    .quick-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-item {
        flex: 1;
        background: white;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    /* Table Styles */
    .table-container {
        overflow-x: auto;
    }
    
    .quiz-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .quiz-table thead {
        background: #f8fafc;
        border-bottom: 2px solid var(--border);
    }
    
    .quiz-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.875rem;
    }
    
    .quiz-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .quiz-table tbody tr:hover {
        background: #f9fafb;
    }
    
    /* Quiz Info */
    .quiz-info {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .quiz-icon {
        width: 40px;
        height: 40px;
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
        color: var(--dark);
        margin-bottom: 0.25rem;
    }
    
    .quiz-desc {
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    /* Badges & Status */
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #e0e7ff;
        color: var(--primary);
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
    }
    
    .status.passed {
        background: #dcfce7;
        color: #166534;
    }
    
    .status.failed {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .status.available {
        background: #dbeafe;
        color: #1e40af;
    }
    
    /* Score Display */
    .score-display {
        display: flex;
        align-items: baseline;
        gap: 0.25rem;
    }
    
    .score-number {
        font-weight: 600;
        font-size: 1rem;
    }
    
    .score-percentage {
        font-size: 0.75rem;
        color: var(--secondary);
    }
    
    /* Action Buttons */
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
    }
    
    .btn-icon.view {
        color: #3b82f6;
    }
    
    .btn-icon.view:hover {
        background: #dbeafe;
    }
    
    .btn-icon.start {
        color: #10b981;
    }
    
    .btn-icon.start:hover {
        background: #dcfce7;
    }
    
    .btn-icon.retake {
        color: #f59e0b;
    }
    
    .btn-icon.retake:hover {
        background: #fef3c7;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .quick-stats {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .btn-icon {
            width: 28px;
            height: 28px;
        }
    }
</style>

<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
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
@endsection