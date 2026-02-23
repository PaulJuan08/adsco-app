@extends('layouts.student')

@section('title', 'Take Quiz — ' . $quiz->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-show.css') }}">
<style>
    .progress-indicator {
        margin-left: auto;
        font-size: 0.8125rem;
        color: #667eea;
        font-weight: 600;
        background: #ebf4ff;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
    }

    .btn-take-submit {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }

    .btn-take-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
    }

    .answered-count {
        font-size: 0.875rem;
        color: #4a5568;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .answered-count i {
        color: #48bb78;
    }
</style>
@endpush

@section('content')
<div class="quiz-container">
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-pencil-alt card-icon"></i>
                <h2 class="card-title">{{ $quiz->title }}</h2>
            </div>
            <div class="top-actions">
                <a href="{{ route('student.todo.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back to To-Do
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Quiz Info Bar -->
            <div class="quiz-preview">
                <div class="quiz-preview-avatar">
                    {{ strtoupper(substr($quiz->title, 0, 1)) }}
                </div>
                <div class="quiz-preview-title">{{ $quiz->title }}</div>
                <div class="quiz-preview-meta">
                    <div class="quiz-preview-badge published">
                        <i class="fas fa-play-circle"></i>
                        Taking Quiz
                    </div>
                </div>
                <div class="quiz-preview-id">
                    <i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} Questions
                    · <i class="fas fa-trophy"></i> Passing: {{ $quiz->passing_score }}%
                    · <i class="fas fa-infinity" style="color: #f59e0b;"></i> Unlimited attempts
                </div>
            </div>

            <!-- Quiz Form -->
            <form action="{{ route('student.todo.quiz.submit', $encryptedId) }}" method="POST" id="quiz-form">
                @csrf
                
                <div class="two-column-layout">
                    <!-- Left Column - Questions -->
                    <div class="form-column">
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
                                            </div>
                                            <div class="points-badge">1 point</div>
                                        </div>
                                        
                                        <div class="options-wrapper">
                                            @foreach($question->options as $optionIndex => $option)
                                                <label class="option-card" for="option-{{ $option->id }}">
                                                    <input type="radio" 
                                                           class="option-radio" 
                                                           name="question_{{ $question->id }}" 
                                                           id="option-{{ $option->id }}" 
                                                           value="{{ $option->id }}"
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
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Navigation Controls -->
                            <div class="nav-controls">
                                <button type="button" class="nav-btn btn-prev" id="prevBtn" onclick="prevQuestion()" disabled>
                                    <i class="fas fa-arrow-left"></i> Previous
                                </button>
                                
                                <div class="center-actions">
                                    <div class="answered-count">
                                        <i class="fas fa-check-circle"></i>
                                        <span><span id="answeredCount">0</span> of {{ $quiz->questions->count() }} answered</span>
                                    </div>
                                </div>
                                
                                <button type="button" class="nav-btn btn-next" id="nextBtn" onclick="nextQuestion()">
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Sidebar -->
                    <div class="sidebar-column">
                        <!-- Navigation Card -->
                        <div class="sidebar-card">
                            <div class="sidebar-card-title">
                                <i class="fas fa-compass"></i> Navigation
                            </div>
                            
                            <div class="question-grid">
                                @foreach($quiz->questions as $index => $question)
                                    <button type="button" 
                                            class="question-nav-btn" 
                                            onclick="goToQuestion({{ $index }})"
                                            data-question-id="{{ $question->id }}">
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>
                            
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar-track">
                                    <div class="progress-bar-fill" id="progressBar" style="width: 0%;"></div>
                                </div>
                            </div>
                            
                            <div class="progress-text">
                                <i class="fas fa-chart-line"></i>
                                <span>Your Progress</span>
                            </div>
                        </div>

                        <!-- Quiz Info Card -->
                        <div class="sidebar-card">
                            <div class="sidebar-card-title">
                                <i class="fas fa-info-circle"></i> Quiz Information
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-question-circle"></i> Questions</span>
                                <span class="info-value">{{ $quiz->questions->count() }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-trophy"></i> Passing Score</span>
                                <span class="info-value">{{ $quiz->passing_score }}%</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-infinity" style="color: #f59e0b;"></i> Attempts</span>
                                <span class="info-value">Unlimited</span>
                            </div>
                        </div>

                        <!-- Tips Card -->
                        <div class="sidebar-card">
                            <div class="sidebar-card-title">
                                <i class="fas fa-lightbulb"></i> Tips
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
                                        <i class="fas fa-redo-alt"></i>
                                    </div>
                                    <div class="tip-content">
                                        <div class="tip-title">Unlimited Retakes</div>
                                        <div class="tip-description">You can retake this quiz anytime</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="margin-top: 1.5rem; text-align: center;">
                    <button type="submit" class="action-btn btn-take-submit" id="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
        const quizForm = document.getElementById('quiz-form');
        const submitBtn = document.getElementById('submit-btn');

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
            
            prevBtn.disabled = index === 0;
            nextBtn.disabled = index === questions.length - 1;
            
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
            
            // Update navigation buttons
            navButtons.forEach((btn, index) => {
                const questionId = questions[index]?.id.replace('question-', '');
                if (questionId) {
                    const hasAnswer = document.querySelector(`input[name="question_${questionId}"]:checked`);
                    btn.classList.remove('answered');
                    if (hasAnswer) {
                        btn.classList.add('answered');
                    }
                }
            });
        };

        // Initialize
        if (questions.length > 0) {
            window.showQuestion(0);
            window.updateProgress();
            
            // Add change event listeners to all radio buttons
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', window.updateProgress);
            });
        }

        // Form submission validation
        if (quizForm) {
            quizForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
                const unanswered = totalQuestions - answeredQuestions;
                
                if (unanswered > 0) {
                    Swal.fire({
                        title: 'Unanswered Questions',
                        html: `You have <strong>${unanswered}</strong> unanswered question${unanswered > 1 ? 's' : ''}.<br>Are you sure you want to submit?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#48bb78',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Submit',
                        cancelButtonText: 'Review Answers'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                            submitBtn.disabled = true;
                            quizForm.submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Submit Quiz?',
                        text: 'Are you ready to submit your answers?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#48bb78',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Submit',
                        cancelButtonText: 'Review'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                            submitBtn.disabled = true;
                            quizForm.submit();
                        }
                    });
                }
            });
        }

        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>
@endpush