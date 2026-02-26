@extends('layouts.student')

@section('title', 'Take Quiz — ' . $quiz->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-show.css') }}">
@endpush

@section('content')
<div class="quiz-container">
    <div class="form-container">
        <!-- Header -->
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
                <div class="quiz-preview-content">
                    <div class="quiz-preview-title">{{ $quiz->title }}</div>
                    <div class="quiz-preview-meta">
                        <span><i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} Questions</span>
                        <span><i class="fas fa-trophy"></i> Passing: {{ $quiz->passing_score }}%</span>
                        <span><i class="fas fa-infinity"></i> Unlimited attempts</span>
                    </div>
                </div>
                <div class="quiz-preview-badge published">
                    <i class="fas fa-play-circle"></i>
                    Taking Quiz
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
                                    <div class="question-wrapper" id="question-{{ $question->id }}" data-question-index="{{ $index }}">
                                        <div class="question-header">
                                            <div class="question-header-content">
                                                <div class="question-number">{{ $index + 1 }}</div>
                                                <div class="question-text">{!! nl2br(e($question->question)) !!}</div>
                                                <div class="question-points">1 point</div>
                                            </div>
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
                                                    <div class="option-text">{{ $option->option_text }}</div>
                                                    <div class="option-check">
                                                        <i class="fas fa-check-circle"></i>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
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
                                    <div class="progress-bar-fill" id="progressBar"></div>
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
                            
                            <div class="info-row-sm">
                                <span class="lbl"><i class="fas fa-question-circle"></i> Questions</span>
                                <span class="val">{{ $quiz->questions->count() }}</span>
                            </div>
                            
                            <div class="info-row-sm">
                                <span class="lbl"><i class="fas fa-trophy"></i> Passing Score</span>
                                <span class="val highlight">{{ $quiz->passing_score }}%</span>
                            </div>
                            
                            <div class="info-row-sm">
                                <span class="lbl"><i class="fas fa-infinity"></i> Attempts</span>
                                <span class="val">Unlimited</span>
                            </div>
                        </div>

                        <!-- Progress Summary Card -->
                        <div class="sidebar-card">
                            <div class="sidebar-card-title">
                                <i class="fas fa-chart-pie"></i> Progress Summary
                            </div>
                            
                            <div class="info-row-sm">
                                <span class="lbl"><i class="fas fa-check-circle" style="color: var(--success);"></i> Answered</span>
                                <span class="val"><span id="answeredCount">0</span>/{{ $quiz->questions->count() }}</span>
                            </div>
                            
                            <div class="info-row-sm">
                                <span class="lbl"><i class="fas fa-hourglass-half" style="color: var(--warning);"></i> Remaining</span>
                                <span class="val"><span id="remainingCount">{{ $quiz->questions->count() }}</span></span>
                            </div>
                        </div>

                        <!-- Tips Card -->
                        <div class="sidebar-card help-card">
                            <div class="sidebar-card-title">
                                <i class="fas fa-lightbulb"></i> Tips
                            </div>
                            
                            <div class="help-text">
                                <p><i class="fas fa-check-circle" style="color: var(--success);"></i> <strong>Select One Answer</strong> — Choose the correct answer for each question</p>
                                <p><i class="fas fa-arrow-down" style="color: var(--primary);"></i> <strong>Scroll Down</strong> — All questions are on one page</p>
                                <p><i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i> <strong>All Questions Required</strong> — You must answer all questions before submitting</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="submit-container">
                    <button type="submit" class="btn-sm btn-sm-success" id="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Quiz
                    </button>
                    <div id="validationMessage" class="validation-message" style="display: none;"></div>
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
        const questions = document.querySelectorAll('.question-wrapper');
        const navButtons = document.querySelectorAll('.question-nav-btn');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const answeredCountElement = document.getElementById('answeredCount');
        const remainingCountElement = document.getElementById('remainingCount');
        const totalQuestions = {{ $quiz->questions->count() }};
        const quizForm = document.getElementById('quiz-form');
        const submitBtn = document.getElementById('submit-btn');
        const validationMessage = document.getElementById('validationMessage');

        // Scroll to question function
        window.goToQuestion = function(index) {
            if (questions[index]) {
                questions[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
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
            
            if (remainingCountElement) {
                remainingCountElement.textContent = totalQuestions - answeredQuestions;
            }
            
            if (progressText) {
                progressText.textContent = `${answeredQuestions} of ${totalQuestions} answered`;
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
            
            // Update option card selected state
            document.querySelectorAll('.option-card').forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
        };

        // Initialize
        if (questions.length > 0) {
            updateProgress();
            
            // Add change event listeners to all radio buttons
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', updateProgress);
            });
        }

        // Form submission validation - require all questions answered
        if (quizForm) {
            quizForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
                const unanswered = totalQuestions - answeredQuestions;
                
                if (unanswered > 0) {
                    // Show validation message
                    validationMessage.style.display = 'block';
                    validationMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i> Please answer all ${unanswered} remaining question(s) before submitting.`;
                    
                    // Scroll to first unanswered question
                    for (let i = 0; i < questions.length; i++) {
                        const questionId = questions[i]?.id.replace('question-', '');
                        const hasAnswer = document.querySelector(`input[name="question_${questionId}"]:checked`);
                        if (!hasAnswer) {
                            questions[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
                            break;
                        }
                    }
                    
                    Swal.fire({
                        title: 'Incomplete Quiz',
                        html: `You have <strong>${unanswered}</strong> unanswered question${unanswered > 1 ? 's' : ''}.<br>Please answer all questions before submitting.`,
                        icon: 'warning',
                        confirmButtonColor: '#667eea',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // All questions answered - confirm submission
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

        // Hide validation message when user starts answering
        document.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', function() {
                validationMessage.style.display = 'none';
            });
        });
    });
</script>
@endpush