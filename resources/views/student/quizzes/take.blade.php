@extends('layouts.student')

@section('title', 'Take Quiz - ' . $quiz->title)

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>{{ $quiz->title }}</h1>
        <p>Answer all questions to complete the quiz</p>
    </div>
    <div class="user-info">
        <div class="timer" id="quizTimer" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">
            @if($quiz->duration)
                {{ $quiz->duration }}:00
            @else
                No Time Limit
            @endif
        </div>
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Quiz Questions</h2>
        <div class="header-actions">
            <div class="progress-indicator">
                <span id="currentQuestion">1</span> of <span id="totalQuestions">{{ count($questions) }}</span>
            </div>
        </div>
    </div>
    
    <form id="quizForm" action="{{ route('student.quizzes.submit', Crypt::encrypt($quiz->id)) }}" method="POST">
        @csrf
        <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
        
        <div style="padding: 2rem;">
            <!-- Questions Navigation -->
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @foreach($questions as $index => $question)
                    <button type="button" 
                            class="question-nav-btn {{ $loop->first ? 'active' : '' }}" 
                            data-question="{{ $index + 1 }}"
                            onclick="showQuestion({{ $index + 1 }})">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>
            </div>
            
            <!-- Questions Container -->
            <div id="questionsContainer">
                @foreach($questions as $index => $question)
                <div class="question-container {{ $loop->first ? 'active' : '' }}" id="question-{{ $index + 1 }}">
                    <!-- Question Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem;">
                                Question #{{ $index + 1 }}
                                <span style="font-size: 0.875rem; color: #6b7280; font-weight: 400;">
                                    ({{ $question->points }} point{{ $question->points > 1 ? 's' : '' }})
                                </span>
                            </h3>
                            @if($question->explanation)
                            <div style="font-size: 0.875rem; color: #6b7280;">
                                <i class="fas fa-info-circle"></i> {{ $question->explanation }}
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Question Text -->
                    <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid var(--primary);">
                        <div style="font-size: 1rem; color: var(--dark); line-height: 1.6;">
                            {!! nl2br(e($question->question)) !!}
                        </div>
                    </div>
                    
                    <!-- Options -->
                    <div style="margin-bottom: 2rem;">
                        <h4 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem;">
                            Select your answer:
                        </h4>
                        
                        <div style="display: grid; gap: 0.75rem;">
                            @foreach($question->shuffled_options as $option)
                            <div class="option-item">
                                <input type="radio" 
                                       id="question-{{ $question->id }}-option-{{ $option->id }}"
                                       name="answers[{{ $question->id }}]"
                                       value="{{ $option->id }}"
                                       class="option-input"
                                       {{ isset($answers[$question->id]) && $answers[$question->id] == $option->id ? 'checked' : '' }}>
                                <label for="question-{{ $question->id }}-option-{{ $option->id }}" class="option-label">
                                    <span class="option-letter">{{ chr(65 + $loop->index) }}</span>
                                    <span class="option-text">{{ $option->option_text }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div style="display: flex; justify-content: space-between; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                        <button type="button" 
                                class="btn btn-secondary"
                                onclick="previousQuestion()"
                                {{ $loop->first ? 'disabled' : '' }}>
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="button" 
                                    class="btn btn-outline-primary"
                                    onclick="markForReview()">
                                <i class="fas fa-flag"></i> Mark for Review
                            </button>
                            
                            @if(!$loop->last)
                            <button type="button" 
                                    class="btn btn-primary"
                                    onclick="nextQuestion()">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                            @else
                            <button type="button" 
                                    class="btn btn-success"
                                    onclick="submitQuiz()">
                                <i class="fas fa-check-circle"></i> Submit Quiz
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Quiz Summary -->
            <div style="margin-top: 3rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem;">Quiz Summary</h3>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--secondary);">Questions Answered</div>
                        <div style="font-size: 1.25rem; font-weight: 600; color: var(--dark);" id="answeredCount">0</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--secondary);">Marked for Review</div>
                        <div style="font-size: 1.25rem; font-weight: 600; color: #f59e0b;" id="reviewCount">0</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--secondary);">Remaining</div>
                        <div style="font-size: 1.25rem; font-weight: 600; color: #ef4444;" id="remainingCount">{{ count($questions) }}</div>
                    </div>
                    <button type="button" 
                            class="btn btn-success btn-lg"
                            onclick="submitQuiz()">
                        <i class="fas fa-paper-plane"></i> Submit Quiz
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Quiz</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="modal-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h4>Are you sure you want to submit?</h4>
                    <p class="text-muted">You have answered <span id="modalAnsweredCount">0</span> out of {{ count($questions) }} questions.</p>
                    <p class="text-muted">Once submitted, you cannot change your answers.</p>
                </div>
                <div style="display: grid; gap: 0.75rem;">
                    <div class="d-flex justify-content-between">
                        <span>Answered Questions:</span>
                        <span id="modalAnswered">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Unanswered Questions:</span>
                        <span id="modalUnanswered">{{ count($questions) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Marked for Review:</span>
                        <span id="modalReview">0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">Submit Quiz</button>
            </div>
        </div>
    </div>
</div>

<style>
    .timer {
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
    }
    
    .progress-indicator {
        font-weight: 600;
        color: var(--dark);
    }
    
    .question-nav-btn {
        width: 36px;
        height: 36px;
        border: 2px solid var(--border);
        border-radius: 6px;
        background: white;
        color: var(--dark);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .question-nav-btn:hover {
        border-color: var(--primary);
        background: #e0e7ff;
    }
    
    .question-nav-btn.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    
    .question-container {
        display: none;
    }
    
    .question-container.active {
        display: block;
    }
    
    .option-item {
        position: relative;
    }
    
    .option-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    
    .option-label {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: white;
        border: 2px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .option-input:checked + .option-label {
        border-color: var(--primary);
        background: #e0e7ff;
    }
    
    .option-label:hover {
        border-color: var(--primary);
    }
    
    .option-letter {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        border-radius: 6px;
        font-weight: 600;
        color: var(--dark);
        flex-shrink: 0;
    }
    
    .option-input:checked + .option-label .option-letter {
        background: var(--primary);
        color: white;
    }
    
    .option-text {
        flex: 1;
    }
    
    .modal-icon {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .top-header .user-info {
            flex-direction: column-reverse;
            gap: 0.5rem;
        }
        
        .question-nav-btn {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }
        
        .option-label {
            padding: 0.75rem 1rem;
        }
    }
</style>

<script>
    let currentQuestion = 1;
    const totalQuestions = {{ count($questions) }};
    let timeRemaining = {{ $quiz->duration ? $quiz->duration * 60 : 0 }};
    let timerInterval;
    let answeredQuestions = new Set();
    let reviewQuestions = new Set();
    
    document.addEventListener('DOMContentLoaded', function() {
        updateProgress();
        
        // Start timer if quiz has time limit
        if (timeRemaining > 0) {
            startTimer();
        }
        
        // Load saved answers from localStorage
        loadSavedAnswers();
        
        // Update answer count when options are clicked
        document.querySelectorAll('.option-input').forEach(input => {
            input.addEventListener('change', function() {
                const questionId = this.name.match(/\[(\d+)\]/)[1];
                answeredQuestions.add(parseInt(questionId));
                saveAnswerToLocalStorage(questionId, this.value);
                updateProgress();
            });
        });
    });
    
    function startTimer() {
        timerInterval = setInterval(function() {
            timeRemaining--;
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                submitQuiz();
                return;
            }
            
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            document.getElementById('quizTimer').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
            // Change color when less than 5 minutes
            if (timeRemaining < 300) {
                document.getElementById('quizTimer').style.background = '#ef4444';
            }
        }, 1000);
    }
    
    function showQuestion(questionNumber) {
        // Hide current question
        document.querySelector('.question-container.active').classList.remove('active');
        document.querySelector(`.question-nav-btn[data-question="${currentQuestion}"]`).classList.remove('active');
        
        // Show selected question
        currentQuestion = questionNumber;
        document.getElementById(`question-${questionNumber}`).classList.add('active');
        document.querySelector(`.question-nav-btn[data-question="${questionNumber}"]`).classList.add('active');
        document.getElementById('currentQuestion').textContent = questionNumber;
    }
    
    function nextQuestion() {
        if (currentQuestion < totalQuestions) {
            showQuestion(currentQuestion + 1);
        }
    }
    
    function previousQuestion() {
        if (currentQuestion > 1) {
            showQuestion(currentQuestion - 1);
        }
    }
    
    function markForReview() {
        const questionId = {{ $questions[currentQuestion - 1]->id }};
        if (reviewQuestions.has(questionId)) {
            reviewQuestions.delete(questionId);
        } else {
            reviewQuestions.add(questionId);
        }
        updateProgress();
    }
    
    function updateProgress() {
        // Update counts
        document.getElementById('answeredCount').textContent = answeredQuestions.size;
        document.getElementById('reviewCount').textContent = reviewQuestions.size;
        document.getElementById('remainingCount').textContent = totalQuestions - answeredQuestions.size;
        
        // Update modal counts
        document.getElementById('modalAnsweredCount').textContent = answeredQuestions.size;
        document.getElementById('modalAnswered').textContent = answeredQuestions.size;
        document.getElementById('modalUnanswered').textContent = totalQuestions - answeredQuestions.size;
        document.getElementById('modalReview').textContent = reviewQuestions.size;
        
        // Update navigation buttons
        document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
            const questionId = {{ $questions[index]->id }};
            if (answeredQuestions.has(questionId)) {
                btn.style.borderColor = '#10b981';
            }
            if (reviewQuestions.has(questionId)) {
                btn.innerHTML = `${index + 1} <i class="fas fa-flag" style="font-size: 8px;"></i>`;
            }
        });
    }
    
    function submitQuiz() {
        // Stop timer
        if (timerInterval) {
            clearInterval(timerInterval);
        }
        
        // Show confirmation modal
        const modal = new bootstrap.Modal(document.getElementById('submitModal'));
        modal.show();
    }
    
    function confirmSubmit() {
        // Submit the form
        document.getElementById('quizForm').submit();
    }
    
    function saveAnswerToLocalStorage(questionId, answer) {
        const quizId = {{ $quiz->id }};
        const key = `quiz_${quizId}_answers`;
        let answers = JSON.parse(localStorage.getItem(key) || '{}');
        answers[questionId] = answer;
        localStorage.setItem(key, JSON.stringify(answers));
    }
    
    function loadSavedAnswers() {
        const quizId = {{ $quiz->id }};
        const key = `quiz_${quizId}_answers`;
        const savedAnswers = JSON.parse(localStorage.getItem(key) || '{}');
        
        Object.keys(savedAnswers).forEach(questionId => {
            const answer = savedAnswers[questionId];
            const input = document.querySelector(`input[name="answers[${questionId}]"][value="${answer}"]`);
            if (input) {
                input.checked = true;
                answeredQuestions.add(parseInt(questionId));
            }
        });
        
        updateProgress();
    }
    
    // Warn before leaving page
    window.addEventListener('beforeunload', function(e) {
        if (answeredQuestions.size > 0) {
            e.preventDefault();
            e.returnValue = 'You have unsaved quiz answers. Are you sure you want to leave?';
        }
    });
</script>

<!-- Include Bootstrap JS for modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection