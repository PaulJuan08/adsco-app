@extends('layouts.student')

@section('title', $quiz->title)

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="header-content">
        <div class="header-main">
            <h1 class="page-title">
                <i class="fas fa-file-alt"></i>
                {{ $quiz->title }}
            </h1>
            <p class="page-subtitle">{{ $quiz->description }}</p>
        </div>
        <div class="header-badge">
            <i class="fas fa-user-clock"></i>
            <span>Quiz Attempt</span>
        </div>
    </div>
</div>

<div class="quiz-container">
    <div class="quiz-main-content">
        <!-- Status Alerts -->
        @if($completedAttempt)
            <div class="status-alert alert-info-custom">
                <div class="alert-icon-large">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="alert-text">
                    <h3>Quiz Already Completed</h3>
                    <p>Your score: <strong class="{{ $completedAttempt->passed ? 'text-success' : 'text-danger' }}">{{ $completedAttempt->percentage }}%</strong> 
                        ({{ $completedAttempt->score }}/{{ $completedAttempt->total_points }} points)</p>
                    <p class="unlimited-retake">
                        <i class="fas fa-infinity"></i> 
                        You can retake this quiz unlimited times to improve your score
                    </p>
                </div>
            </div>
        @endif
        
        @if(!$attempt->completed_at && !$completedAttempt)
            <div class="status-alert alert-warning-custom">
                <div class="alert-icon-large">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="alert-text">
                    <h3>Quiz in Progress</h3>
                    <p>You have an incomplete attempt. Continue below to complete your quiz.</p>
                </div>
            </div>
        @endif

        <!-- Quiz Form or Completed View -->
        @if(!$attempt->completed_at)
            <form action="{{ route('student.quizzes.submit', Crypt::encrypt($quiz->id)) }}" method="POST" id="quizForm">
                @csrf
                
                <div class="questions-card">
                    <div class="questions-header">
                        <h2 class="questions-title">
                            <i class="fas fa-pencil-alt"></i>
                            Questions
                        </h2>
                        <span class="progress-indicator" id="progressText">
                            Question 1 of {{ $quiz->questions->count() }}
                        </span>
                    </div>
                    
                    <div class="questions-body">
                        @foreach($quiz->questions as $index => $question)
                            <div class="question-wrapper {{ $index === 0 ? 'active' : '' }}" id="question-{{ $question->id }}" data-question-index="{{ $index }}">
                                <div class="question-header-row">
                                    <div class="question-number-badge">{{ $index + 1 }}</div>
                                    <div class="question-content-wrapper">
                                        <h3 class="question-text">{{ $question->question }}</h3>
                                        @if($question->description)
                                            <div class="question-hint">
                                                <i class="fas fa-info-circle"></i>
                                                {{ $question->description }}
                                            </div>
                                        @endif
                                    </div>
                                    @if($question->points)
                                        <div class="points-badge">{{ $question->points }} {{ $question->points == 1 ? 'pt' : 'pts' }}</div>
                                    @endif
                                </div>
                                
                                <div class="options-wrapper">
                                    @foreach($question->options as $optionIndex => $option)
                                        <label class="option-card" for="option-{{ $option->id }}">
                                            <input type="radio" 
                                                   class="option-radio" 
                                                   name="answers[{{ $question->id }}]" 
                                                   id="option-{{ $option->id }}" 
                                                   value="{{ $option->id }}"
                                                   {{ isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $option->id ? 'checked' : '' }}
                                                   onchange="updateProgress()">
                                            <div class="option-indicator">
                                                <span class="option-letter">{{ chr(65 + $optionIndex) }}</span>
                                            </div>
                                            <div class="option-text">{{ $option->option_text }}</div>
                                            <div class="option-checkmark">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                
                                @if($question->explanation && isset($userAnswers[$question->id]))
                                    <div class="explanation-box">
                                        <div class="explanation-header">
                                            <i class="fas fa-lightbulb"></i>
                                            <strong>Explanation</strong>
                                        </div>
                                        <p>{{ $question->explanation }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Navigation Controls -->
                    <div class="nav-controls">
                        <button type="button" class="nav-btn btn-prev" id="prevBtn" onclick="prevQuestion()" disabled>
                            <i class="fas fa-arrow-left"></i>
                            <span>Previous</span>
                        </button>
                        
                        <div class="center-actions">
                            <button type="button" class="action-btn btn-clear" onclick="clearAnswers()">
                                <i class="fas fa-eraser"></i>
                                Clear All
                            </button>
                            
                            <button type="submit" class="action-btn btn-submit">
                                <i class="fas fa-paper-plane"></i>
                                Submit Quiz
                            </button>
                        </div>
                        
                        <button type="button" class="nav-btn btn-next" id="nextBtn" onclick="nextQuestion()">
                            <span>Next</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </form>
        @else
            <!-- Completed Quiz View -->
            <div class="completed-card">
                <div class="completed-icon-section">
                    @if($completedAttempt->passed)
                        <div class="trophy-animation">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h2 class="completed-title success">Congratulations!</h2>
                        <p class="completed-subtitle">You passed the quiz!</p>
                    @else
                        <div class="fail-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h2 class="completed-title fail">Quiz Completed</h2>
                        <p class="completed-subtitle">Keep trying, you'll get it!</p>
                    @endif
                </div>
                
                <div class="score-display-large">
                    <div class="score-circle {{ $completedAttempt->passed ? 'passed' : 'failed' }}">
                        <span class="score-number">{{ $completedAttempt->percentage }}%</span>
                    </div>
                    <div class="score-details-large">
                        <p>Your Score: <strong>{{ $completedAttempt->score }}/{{ $completedAttempt->total_points }} points</strong></p>
                        <p>Passing Score: <strong>{{ $quiz->passing_score }}%</strong></p>
                    </div>
                </div>
                
                <div class="unlimited-notice">
                    <i class="fas fa-infinity"></i>
                    You can retake this quiz unlimited times to improve your score.
                </div>
                
                <div class="attempt-details-card">
                    <h3>Attempt Details</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="far fa-calendar-check"></i>
                                Started
                            </div>
                            <div class="detail-value">{{ $completedAttempt->started_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="far fa-calendar-check"></i>
                                Completed
                            </div>
                            <div class="detail-value">{{ $completedAttempt->completed_at->format('M d, Y h:i A') }}</div>
                        </div>
                        @if($completedAttempt->time_taken)
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="far fa-clock"></i>
                                    Time Taken
                                </div>
                                <div class="detail-value">{{ gmdate('H:i:s', $completedAttempt->time_taken) }}</div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="completed-actions">
                    <a href="{{ route('student.quizzes.index') }}" class="action-btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Quizzes
                    </a>
                    
                    <form action="{{ route('student.quizzes.retake', Crypt::encrypt($quiz->id)) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="action-btn btn-primary">
                            <i class="fas fa-redo"></i>
                            Retake Quiz
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Sidebar -->
    <div class="quiz-sidebar-content">
        <!-- Quiz Details Card -->
        <div class="sidebar-info-card">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Quiz Details
                </h3>
            </div>
            <div class="sidebar-body">
                <div class="info-item">
                    <div class="info-icon icon-primary">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Questions</div>
                        <div class="info-value">{{ $quiz->questions->count() }}</div>
                    </div>
                </div>
                
                @if($quiz->passing_score)
                    <div class="info-item">
                        <div class="info-icon icon-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Passing Score</div>
                            <div class="info-value">{{ $quiz->passing_score }}%</div>
                        </div>
                    </div>
                @endif
                
                <div class="info-item">
                    <div class="info-icon icon-info">
                        <i class="fas fa-infinity"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Attempts</div>
                        <div class="info-value">Unlimited</div>
                    </div>
                </div>
                
                @if($attempt->started_at)
                    <div class="info-item">
                        <div class="info-icon icon-warning">
                            <i class="far fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Started At</div>
                            <div class="info-value small">{{ $attempt->started_at->format('M d, h:i A') }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Navigation Card (only show if quiz is in progress) -->
        @if(!$attempt->completed_at)
            <div class="sidebar-nav-card">
                <div class="sidebar-header">
                    <h3>
                        <i class="fas fa-compass"></i>
                        Navigation
                    </h3>
                </div>
                <div class="sidebar-body">
                    <div class="question-grid">
                        @foreach($quiz->questions as $index => $question)
                            <button type="button" 
                                    class="question-nav-btn {{ $index === 0 ? 'active' : '' }}" 
                                    onclick="goToQuestion({{ $index }})"
                                    data-question-id="{{ $question->id }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                    
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill" id="progressBar"></div>
                        </div>
                    </div>
                    
                    <div class="progress-text">
                        <i class="fas fa-check-circle"></i>
                        <span><span id="answeredCount">0</span> of {{ $quiz->questions->count() }} answered</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Results Modal -->
@if(session('quiz_results'))
    @php
        $results = session('quiz_results');
    @endphp
    <div class="modal-overlay" id="resultsModal" onclick="closeModal()">
        <div class="modal-container" onclick="event.stopPropagation()">
            <div class="modal-header {{ $results['passed'] ? 'header-success' : 'header-danger' }}">
                <h2>
                    <i class="fas fa-{{ $results['passed'] ? 'trophy' : 'times-circle' }}"></i>
                    Quiz Results
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="results-summary">
                    <div class="results-score {{ $results['passed'] ? 'score-success' : 'score-danger' }}">
                        {{ $results['percentage'] }}%
                    </div>
                    <h3>{{ $results['passed'] ? 'Congratulations! You Passed!' : 'You Did Not Pass' }}</h3>
                    <p>Score: {{ $results['score'] }}/{{ $results['total_points'] }} points</p>
                    <p class="passing-score">Passing Score: {{ $results['passing_score'] }}%</p>
                    <div class="unlimited-notice">
                        <i class="fas fa-infinity"></i>
                        You can retake this quiz unlimited times to improve your score.
                    </div>
                </div>
                
                <div class="results-review">
                    <h3 class="review-title">
                        <i class="fas fa-list-check"></i>
                        Question Review
                    </h3>
                    
                    <div class="accordion-questions">
                        @foreach($results['questions'] as $index => $question)
                            <div class="accordion-item">
                                <button class="accordion-header {{ $question['is_correct'] ? 'correct' : 'incorrect' }}" 
                                        onclick="toggleAccordion({{ $index }})">
                                    <span class="accordion-icon">
                                        @if($question['is_correct'])
                                            <i class="fas fa-check-circle"></i>
                                        @else
                                            <i class="fas fa-times-circle"></i>
                                        @endif
                                    </span>
                                    <span class="accordion-title">Question {{ $index + 1 }}</span>
                                    <span class="accordion-toggle">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </button>
                                <div class="accordion-content" id="accordion-{{ $index }}">
                                    <p class="question-text-review">{{ $question['question'] }}</p>
                                    <div class="options-review">
                                        @foreach($question['options'] as $option)
                                            <div class="option-review {{ $option['is_correct'] ? 'correct' : ($option['is_user_selected'] ? 'incorrect' : '') }}">
                                                <div class="option-indicator-review">
                                                    @if($option['is_correct'])
                                                        <i class="fas fa-check"></i>
                                                    @elseif($option['is_user_selected'])
                                                        <i class="fas fa-times"></i>
                                                    @endif
                                                </div>
                                                <div class="option-text-review">{{ $option['text'] }}</div>
                                                <div class="option-badges">
                                                    @if($option['is_correct'])
                                                        <span class="badge-correct">Correct</span>
                                                    @endif
                                                    @if($option['is_user_selected'])
                                                        <span class="badge-your-choice">Your Choice</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <a href="{{ route('student.quizzes.index') }}" class="action-btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Quizzes
                </a>
                <form action="{{ route('student.quizzes.retake', Crypt::encrypt($quiz->id)) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="action-btn btn-primary">
                        <i class="fas fa-redo"></i>
                        Retake Quiz
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid #e5e7eb;
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }
    
    .header-main {
        flex: 1;
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
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        white-space: nowrap;
    }
    
    /* Quiz Container */
    .quiz-container {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 2rem;
        align-items: start;
    }
    
    /* Status Alerts */
    .status-alert {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        border: 2px solid;
    }
    
    .alert-info-custom {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-color: #3b82f6;
    }
    
    .alert-warning-custom {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-color: #f59e0b;
    }
    
    .alert-icon-large {
        font-size: 3rem;
        flex-shrink: 0;
        opacity: 0.9;
    }
    
    .alert-info-custom .alert-icon-large {
        color: #3b82f6;
    }
    
    .alert-warning-custom .alert-icon-large {
        color: #f59e0b;
    }
    
    .alert-text h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.75rem;
    }
    
    .alert-text p {
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .unlimited-retake {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #059669 !important;
        font-weight: 600;
    }
    
    .unlimited-retake i {
        color: #10b981;
    }
    
    /* Questions Card */
    .questions-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .questions-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .questions-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .questions-title i {
        color: #10b981;
    }
    
    .progress-indicator {
        padding: 0.5rem 1rem;
        background: #f3f4f6;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
    }
    
    .questions-body {
        padding: 2rem;
        min-height: 400px;
    }
    
    /* Question Wrapper */
    .question-wrapper {
        display: none;
        animation: fadeInUp 0.4s ease;
    }
    
    .question-wrapper.active {
        display: block;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .question-header-row {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
        align-items: flex-start;
    }
    
    .question-number-badge {
        min-width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 16px;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .question-content-wrapper {
        flex: 1;
    }
    
    .question-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin: 0 0 0.75rem 0;
        line-height: 1.6;
    }
    
    .question-hint {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 12px;
        color: #065f46;
        font-size: 0.9375rem;
    }
    
    .question-hint i {
        color: #10b981;
    }
    
    .points-badge {
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    /* Options */
    .options-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .option-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .option-card:hover {
        border-color: #10b981;
        background: #f9fafb;
        transform: translateX(4px);
    }
    
    .option-radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    
    .option-radio:checked + .option-indicator + .option-text ~ .option-checkmark,
    .option-card:has(.option-radio:checked) .option-checkmark {
        opacity: 1;
    }
    
    .option-radio:checked ~ * {
        color: #065f46;
    }
    
    .option-card:has(.option-radio:checked) {
        border-color: #10b981;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    
    .option-indicator {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: 700;
        color: #6b7280;
        transition: all 0.3s ease;
    }
    
    .option-card:has(.option-radio:checked) .option-indicator {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .option-letter {
        font-size: 1.125rem;
    }
    
    .option-text {
        flex: 1;
        font-size: 1rem;
        color: #374151;
        font-weight: 500;
    }
    
    .option-checkmark {
        font-size: 1.5rem;
        color: #10b981;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    /* Explanation Box */
    .explanation-box {
        margin-top: 1.5rem;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #93c5fd;
        border-radius: 12px;
    }
    
    .explanation-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        color: #1e40af;
        font-weight: 600;
    }
    
    .explanation-header i {
        color: #3b82f6;
    }
    
    .explanation-box p {
        color: #1e3a8a;
        margin: 0;
        line-height: 1.6;
    }
    
    /* Navigation Controls */
    .nav-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        gap: 1rem;
    }
    
    .nav-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .nav-btn:hover:not(:disabled) {
        border-color: #10b981;
        background: #f9fafb;
    }
    
    .nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .center-actions {
        display: flex;
        gap: 1rem;
    }
    
    .action-btn {
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
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }
    
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
    }
    
    .btn-clear {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .btn-clear:hover {
        background: #fecaca;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }
    
    /* Ensure form buttons have consistent styling */
    form button.action-btn,
    form .action-btn {
        font-family: 'Inter', sans-serif;
    }
    
    /* Sidebar */
    .quiz-sidebar-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        position: sticky;
        top: 2rem;
    }
    
    .sidebar-info-card, .sidebar-nav-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .sidebar-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
    }
    
    .sidebar-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .sidebar-header h3 i {
        color: #10b981;
    }
    
    .sidebar-body {
        padding: 1.5rem;
    }
    
    /* Info Items */
    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: #f9fafb;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .info-item:last-child {
        margin-bottom: 0;
    }
    
    .info-item:hover {
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .info-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    
    .icon-primary {
        background: #dbeafe;
        color: #3b82f6;
    }
    
    .icon-success {
        background: #d1fae5;
        color: #10b981;
    }
    
    .icon-info {
        background: #e0e7ff;
        color: #6366f1;
    }
    
    .icon-warning {
        background: #fef3c7;
        color: #f59e0b;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }
    
    .info-value.small {
        font-size: 0.875rem;
    }
    
    /* Question Grid Navigation */
    .question-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .question-nav-btn {
        aspect-ratio: 1;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 10px;
        font-weight: 700;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9375rem;
    }
    
    .question-nav-btn:hover {
        border-color: #10b981;
        background: #f9fafb;
    }
    
    .question-nav-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: #3b82f6;
    }
    
    .question-nav-btn.answered {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-color: #10b981;
    }
    
    /* Progress Bar */
    .progress-bar-wrapper {
        margin-bottom: 1rem;
    }
    
    .progress-bar-track {
        height: 10px;
        background: #e5e7eb;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 20px;
        transition: width 0.5s ease;
        width: 0;
    }
    
    .progress-text {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .progress-text i {
        color: #10b981;
    }
    
    /* Completed Card */
    .completed-card {
        background: white;
        border-radius: 16px;
        padding: 3rem 2rem;
        border: 1px solid #e5e7eb;
        text-align: center;
    }
    
    .completed-icon-section {
        margin-bottom: 2rem;
    }
    
    .trophy-animation {
        font-size: 5rem;
        color: #f59e0b;
        margin-bottom: 1rem;
        animation: bounce 1s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .fail-icon {
        font-size: 5rem;
        color: #ef4444;
        margin-bottom: 1rem;
    }
    
    .completed-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .completed-title.success {
        color: #10b981;
    }
    
    .completed-title.fail {
        color: #ef4444;
    }
    
    .completed-subtitle {
        color: #6b7280;
        font-size: 1.125rem;
    }
    
    .score-display-large {
        margin-bottom: 2rem;
    }
    
    .score-circle {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        border: 8px solid;
    }
    
    .score-circle.passed {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-color: #10b981;
    }
    
    .score-circle.failed {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-color: #ef4444;
    }
    
    .score-number {
        font-size: 3rem;
        font-weight: 700;
    }
    
    .score-circle.passed .score-number {
        color: #10b981;
    }
    
    .score-circle.failed .score-number {
        color: #ef4444;
    }
    
    .score-details-large {
        color: #6b7280;
    }
    
    .unlimited-notice {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #3b82f6;
        border-radius: 12px;
        color: #1e40af;
        font-weight: 600;
        margin-bottom: 2rem;
    }
    
    .unlimited-notice i {
        color: #3b82f6;
        font-size: 1.25rem;
    }
    
    .attempt-details-card {
        background: #f9fafb;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .attempt-details-card h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .detail-item {
        text-align: left;
    }
    
    .detail-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .detail-value {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #111827;
    }
    
    .completed-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.75);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal-container {
        background: white;
        border-radius: 20px;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s ease;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-header {
        padding: 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .header-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .header-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        color: white;
        font-size: 1.125rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    .modal-body {
        padding: 2rem;
        overflow-y: auto;
        flex: 1;
    }
    
    .results-summary {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .results-score {
        font-size: 4rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .score-success {
        color: #10b981;
    }
    
    .score-danger {
        color: #ef4444;
    }
    
    .results-summary h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .results-summary p {
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .passing-score {
        font-weight: 600;
    }
    
    .review-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .review-title i {
        color: #10b981;
    }
    
    .accordion-questions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .accordion-item {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .accordion-header {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: white;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
        font-weight: 600;
        text-align: left;
    }
    
    .accordion-header.correct {
        background: #f0fdf4;
        color: #065f46;
    }
    
    .accordion-header.incorrect {
        background: #fef2f2;
        color: #991b1b;
    }
    
    .accordion-header:hover {
        background: #f9fafb;
    }
    
    .accordion-icon {
        font-size: 1.25rem;
    }
    
    .accordion-header.correct .accordion-icon {
        color: #10b981;
    }
    
    .accordion-header.incorrect .accordion-icon {
        color: #ef4444;
    }
    
    .accordion-title {
        flex: 1;
    }
    
    .accordion-toggle {
        transition: transform 0.3s;
    }
    
    .accordion-header.open .accordion-toggle {
        transform: rotate(180deg);
    }
    
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: white;
    }
    
    .accordion-content.open {
        max-height: 1000px;
        padding: 1.25rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .question-text-review {
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    .options-review {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .option-review {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
    }
    
    .option-review.correct {
        background: #f0fdf4;
        border-color: #86efac;
    }
    
    .option-review.incorrect {
        background: #fef2f2;
        border-color: #fca5a5;
    }
    
    .option-indicator-review {
        width: 24px;
        text-align: center;
        font-size: 1.125rem;
    }
    
    .option-review.correct .option-indicator-review {
        color: #10b981;
    }
    
    .option-review.incorrect .option-indicator-review {
        color: #ef4444;
    }
    
    .option-text-review {
        flex: 1;
        color: #374151;
        font-weight: 500;
    }
    
    .option-badges {
        display: flex;
        gap: 0.5rem;
    }
    
    .badge-correct, .badge-your-choice {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-correct {
        background: #10b981;
        color: white;
    }
    
    .badge-your-choice {
        background: #ef4444;
        color: white;
    }
    
    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .quiz-container {
            grid-template-columns: 1fr;
        }
        
        .quiz-sidebar-content {
            position: static;
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
        
        .questions-body {
            padding: 1.5rem;
        }
        
        .nav-controls {
            flex-direction: column;
            gap: 1rem;
        }
        
        .center-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .center-actions .action-btn {
            width: 100%;
            justify-content: center;
        }
        
        .question-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .modal-container {
            margin: 1rem;
        }
        
        .modal-footer {
            flex-direction: column;
        }
        
        .modal-footer .action-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let currentQuestionIndex = 0;
    const questions = document.querySelectorAll('.question-wrapper');
    const navButtons = document.querySelectorAll('.question-nav-btn');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const answeredCountElement = document.getElementById('answeredCount');
    const totalQuestions = {{ $quiz->questions->count() }};

    function showQuestion(index) {
        questions.forEach(q => q.classList.remove('active'));
        navButtons.forEach(btn => btn.classList.remove('active'));
        
        questions[index].classList.add('active');
        navButtons[index].classList.add('active');
        
        document.getElementById('prevBtn').disabled = index === 0;
        document.getElementById('nextBtn').disabled = index === questions.length - 1;
        
        progressText.textContent = `Question ${index + 1} of ${questions.length}`;
        
        currentQuestionIndex = index;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextQuestion() {
        if (currentQuestionIndex < questions.length - 1) {
            showQuestion(currentQuestionIndex + 1);
        }
    }

    function prevQuestion() {
        if (currentQuestionIndex > 0) {
            showQuestion(currentQuestionIndex - 1);
        }
    }

    function goToQuestion(index) {
        showQuestion(index);
    }

    function updateProgress() {
        const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
        const progress = (answeredQuestions / totalQuestions) * 100;
        
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }
        
        if (answeredCountElement) {
            answeredCountElement.textContent = answeredQuestions;
        }
        
        navButtons.forEach((btn, index) => {
            const questionId = btn.getAttribute('data-question-id');
            const hasAnswer = document.querySelector(`input[name="answers[${questionId}]"]:checked`);
            
            btn.classList.remove('answered');
            if (hasAnswer && !btn.classList.contains('active')) {
                btn.classList.add('answered');
            }
        });
    }

    function clearAnswers() {
        if (confirm('Are you sure you want to clear all answers? This action cannot be undone.')) {
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.checked = false;
            });
            updateProgress();
        }
    }

    function closeModal() {
        window.location.href = "{{ route('student.quizzes.show', Crypt::encrypt($quiz->id)) }}";
    }

    function toggleAccordion(index) {
        const content = document.getElementById(`accordion-${index}`);
        const header = content.previousElementSibling;
        
        content.classList.toggle('open');
        header.classList.toggle('open');
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (questions.length > 0) {
            showQuestion(0);
            updateProgress();
            
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', updateProgress);
            });
        }
        
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });

    const quizForm = document.getElementById('quizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
            const unanswered = totalQuestions - answeredQuestions;
            
            if (unanswered > 0) {
                if (!confirm(`You have ${unanswered} unanswered question${unanswered > 1 ? 's' : ''}. Are you sure you want to submit?`)) {
                    e.preventDefault();
                }
            }
        });
    }
</script>
@endpush
@endsection