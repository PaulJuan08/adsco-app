@extends('layouts.student')

@section('title', $quiz->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-show.css') }}">
@endpush

@section('content')
<!-- Quiz Details Card -->
<div class="form-container">
    <div class="card-header">
        <div class="card-title-group">
            <i class="fas fa-file-alt card-icon"></i>
            <h2 class="card-title">{{ $quiz->title }}</h2>
        </div>
        <div class="top-actions">
            <!-- Back Button -->
            <a href="{{ route('student.quizzes.index') }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back to Quizzes
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Quiz Preview -->
        <div class="quiz-preview">
            <div class="quiz-preview-avatar">
                {{ strtoupper(substr($quiz->title, 0, 1)) }}
            </div>
            <div class="quiz-preview-title">{{ $quiz->title }}</div>
            <div class="quiz-preview-meta">
                <div class="quiz-preview-badge published">
                    <i class="fas fa-check-circle"></i>
                    {{ $attempt->completed_at ? 'Completed' : 'In Progress' }}
                </div>
                <span class="quiz-preview-id">
                    <i class="fas fa-hashtag"></i> Attempt #{{ $attempt->id }}
                </span>
            </div>
        </div>

        <!-- Status Alerts -->
        @if($completedAttempt)
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Quiz Already Completed</strong>
                    <p class="mt-1 mb-0">
                        Your score: <strong class="{{ $completedAttempt->passed ? 'text-success' : 'text-danger' }}">{{ $completedAttempt->percentage }}%</strong> 
                        ({{ $completedAttempt->score }}/{{ $completedAttempt->total_points }} points)
                    </p>
                    <p class="unlimited-retake mt-2 mb-0">
                        <i class="fas fa-infinity"></i> 
                        You can retake this quiz unlimited times to improve your score
                    </p>
                </div>
            </div>
        @endif
        
        @if(!$attempt->completed_at && !$completedAttempt)
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>Quiz in Progress</strong>
                    <p class="mt-1 mb-0">
                        You have an incomplete attempt. Continue below to complete your quiz.
                    </p>
                </div>
            </div>
        @endif

        <!-- Display success/error messages -->
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

        <!-- Two Column Layout -->
        <div class="two-column-layout">
            <!-- Left Column - Main Content -->
            <div class="form-column">
                @if(!$attempt->completed_at)
                    <form action="{{ route('student.quizzes.submit', Crypt::encrypt($quiz->id)) }}" method="POST" id="quizForm">
                        @csrf
                        
                        <!-- Stats Grid -->
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="stat-value">{{ $quiz->questions->count() }}</div>
                                <div class="stat-label">Questions</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="stat-value">{{ $quiz->passing_score ?? 70 }}%</div>
                                <div class="stat-label">Passing Score</div>
                            </div>
                            
                            @php
                                $totalPoints = $quiz->questions->sum('points');
                            @endphp
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-value">{{ $totalPoints }}</div>
                                <div class="stat-label">Total Points</div>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <div class="detail-section">
                            <div class="detail-section-title">
                                <i class="fas fa-list"></i> Questions
                                <span class="progress-indicator" id="progressText">
                                    Question 1 of {{ $quiz->questions->count() }}
                                </span>
                            </div>
                            
                            <div class="questions-body">
                                @foreach($quiz->questions as $index => $question)
                                    <div class="question-wrapper {{ $index === 0 ? 'active' : '' }}" 
                                         id="question-{{ $question->id }}" 
                                         data-question-index="{{ $index }}">
                                        
                                        <div class="question-header-row">
                                            <div class="question-number-badge">{{ $index + 1 }}</div>
                                            <div class="question-content-wrapper">
                                                <div class="question-text-student">
                                                    {!! nl2br(e($question->question)) !!}
                                                </div>
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
                                                    <div class="option-text-student">{{ $option->option_text }}</div>
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
                            <h3 class="detail-section-title">Attempt Details</h3>
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

            <!-- Right Column - Sidebar -->
            <div class="sidebar-column">
                <!-- Quiz Information Card -->
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-info-circle"></i> Quiz Summary
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-hashtag"></i> Quiz ID</span>
                        <span class="info-value">#{{ $quiz->id }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-check-circle"></i> Status</span>
                        <span class="info-value">
                            @if($attempt->completed_at)
                                <span class="text-success">Completed</span>
                            @else
                                <span class="text-warning">In Progress</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-trophy"></i> Passing Score</span>
                        <span class="info-value">{{ $quiz->passing_score ?? 70 }}%</span>
                    </div>
                    
                    @if($quiz->duration)
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-clock"></i> Duration</span>
                        <span class="info-value">{{ $quiz->duration }} minutes</span>
                    </div>
                    @endif
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-star"></i> Total Points</span>
                        <span class="info-value">{{ $totalPoints }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-question-circle"></i> Questions</span>
                        <span class="info-value">{{ $quiz->questions->count() }}</span>
                    </div>
                    
                    @if($attempt->started_at)
                    <div class="info-row">
                        <span class="info-label"><i class="far fa-clock"></i> Started At</span>
                        <span class="info-value">{{ $attempt->started_at->format('M d, h:i A') }}</span>
                    </div>
                    @endif
                    
                    @if($attempt->completed_at)
                    <div class="info-row">
                        <span class="info-label"><i class="far fa-check-circle"></i> Completed At</span>
                        <span class="info-value">{{ $attempt->completed_at->format('M d, h:i A') }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Navigation Card (only show if quiz is in progress) -->
                @if(!$attempt->completed_at)
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-compass"></i> Navigation
                    </div>
                    
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
                @endif
                
                <!-- Quick Tips Card -->
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-lightbulb"></i> Quick Tips
                    </div>
                    
                    <div class="tips-grid">
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Select One Answer</div>
                                <div class="tip-description">Choose the correct answer for each question</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-arrow-left"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Navigate Questions</div>
                                <div class="tip-description">Use Previous/Next buttons or number grid</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Track Progress</div>
                                <div class="tip-description">Watch your progress bar fill as you answer</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-redo"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Unlimited Attempts</div>
                                <div class="tip-description">Retake anytime to improve your score</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                        onclick="toggleAccordion({{ $index }})"
                                        type="button">
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
                                                        <span class="badge-correct">Correct Answer</span>
                                                    @endif
                                                    @if($option['is_user_selected'])
                                                        <span class="badge-your-choice">Your Answer</span>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        let currentQuestionIndex = 0;
        const questions = document.querySelectorAll('.question-wrapper');
        const navButtons = document.querySelectorAll('.question-nav-btn');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const answeredCountElement = document.getElementById('answeredCount');
        const totalQuestions = {{ $quiz->questions->count() }};
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        // Only initialize quiz functionality if quiz is in progress
        @if(!$attempt->completed_at)
            // Show question function
            window.showQuestion = function(index) {
                questions.forEach(q => q.classList.remove('active'));
                navButtons.forEach(btn => btn.classList.remove('active'));
                
                if (questions[index]) {
                    questions[index].classList.add('active');
                }
                if (navButtons[index]) {
                    navButtons[index].classList.add('active');
                }
                
                if (prevBtn) prevBtn.disabled = index === 0;
                if (nextBtn) nextBtn.disabled = index === questions.length - 1;
                
                if (progressText) {
                    progressText.textContent = `Question ${index + 1} of ${questions.length}`;
                }
                
                currentQuestionIndex = index;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            // Navigation functions
            window.nextQuestion = function() {
                if (currentQuestionIndex < questions.length - 1) {
                    window.showQuestion(currentQuestionIndex + 1);
                }
            };

            window.prevQuestion = function() {
                if (currentQuestionIndex > 0) {
                    window.showQuestion(currentQuestionIndex - 1);
                }
            };

            window.goToQuestion = function(index) {
                window.showQuestion(index);
            };

            // Update progress function
            window.updateProgress = function() {
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
            };

            // Clear answers function - FIXED
            window.clearAnswers = function() {
                Swal.fire({
                    title: 'Clear All Answers?',
                    text: 'Are you sure you want to clear all your answers? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Clear All',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Clear all radio inputs
                        document.querySelectorAll('input[type="radio"]').forEach(input => {
                            input.checked = false;
                        });
                        
                        // Update progress
                        window.updateProgress();
                        
                        // Remove answered class from nav buttons
                        navButtons.forEach(btn => {
                            btn.classList.remove('answered');
                        });
                        
                        Swal.fire({
                            title: 'Cleared!',
                            text: 'All answers have been cleared.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            };

            // Initialize
            if (questions.length > 0) {
                window.showQuestion(0);
                window.updateProgress();
                
                document.querySelectorAll('input[type="radio"]').forEach(input => {
                    input.addEventListener('change', window.updateProgress);
                });
            }

            // Form submission validation
            const quizForm = document.getElementById('quizForm');
            if (quizForm) {
                quizForm.addEventListener('submit', function(e) {
                    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
                    const unanswered = totalQuestions - answeredQuestions;
                    
                    if (unanswered > 0) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Unanswered Questions',
                            text: `You have ${unanswered} unanswered question${unanswered > 1 ? 's' : ''}. Are you sure you want to submit?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#48bb78',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, Submit',
                            cancelButtonText: 'Review Answers'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const submitBtn = e.target.querySelector('.btn-submit');
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                                submitBtn.disabled = true;
                                e.target.submit();
                            }
                        });
                    }
                });
            }
        @endif

        // Modal functions (used in results view)
        window.closeModal = function() {
            const modal = document.getElementById('resultsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        };

        window.toggleAccordion = function(index) {
            const content = document.getElementById(`accordion-${index}`);
            if (content) {
                const header = content.previousElementSibling;
                content.classList.toggle('open');
                header.classList.toggle('open');
            }
        };
        
        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Show notifications from session
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'success',
                title: '{{ session('success') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: '{{ session('error') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
</script>
@endpush