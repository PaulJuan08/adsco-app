@extends('layouts.student')

@section('title', $quiz->title)

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>{{ $quiz->title }}</h1>
        <p>{{ $quiz->description }}</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Quiz Details -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ $quiz->title }}</h2>
        <div class="header-actions">
            <a href="{{ route('student.quizzes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div style="padding: 2rem;">
        <!-- Quiz Information Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="info-card">
                <div class="info-icon" style="background: #e0e7ff; color: #4f46e5;">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Total Questions</div>
                    <div class="info-value">{{ $quiz->questions->count() }}</div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon" style="background: #dcfce7; color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Passing Score</div>
                    <div class="info-value">{{ $quiz->passing_score }}%</div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Max Attempts</div>
                    <div class="info-value">{{ $quiz->max_attempts ?? 'Unlimited' }}</div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon" style="background: #ede9fe; color: #8b5cf6;">
                    <i class="fas fa-redo"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Your Attempts</div>
                    <div class="info-value">{{ $attemptCount }}</div>
                </div>
            </div>
        </div>
        
        <!-- Quiz Description -->
        <div style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem;">Quiz Description</h3>
            <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; line-height: 1.6;">
                {{ $quiz->description }}
            </div>
        </div>
        
        <!-- Instructions -->
        <div style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem;">Instructions</h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 0.75rem;">
                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fas fa-check-circle" style="color: #10b981; margin-top: 0.25rem;"></i>
                    <span>Read each question carefully before answering.</span>
                </li>
                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fas fa-check-circle" style="color: #10b981; margin-top: 0.25rem;"></i>
                    <span>You must score at least {{ $quiz->passing_score }}% to pass.</span>
                </li>
                @if($quiz->max_attempts)
                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fas fa-check-circle" style="color: #10b981; margin-top: 0.25rem;"></i>
                    <span>Maximum attempts: {{ $quiz->max_attempts }}</span>
                </li>
                @endif
                @if($attempt && $attempt->completed_at)
                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-top: 0.25rem;"></i>
                    <span>You have already taken this quiz. Score: {{ $attempt->score }}/{{ $attempt->total_points }} ({{ $attempt->percentage }}%)</span>
                </li>
                @endif
            </ul>
        </div>
        
        <!-- Availability Warning -->
        @if(!$isAvailable)
        <div style="margin-bottom: 2rem;">
            <div style="padding: 1rem; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #d97706;"></i>
                    <div>
                        <div style="font-weight: 600; color: #92400e;">Quiz Not Available</div>
                        <div style="color: #92400e;">
                            @if($quiz->available_from && $quiz->available_from > now())
                                This quiz will be available from {{ \Carbon\Carbon::parse($quiz->available_from)->format('F j, Y g:i A') }}
                            @elseif($quiz->available_until && $quiz->available_until < now())
                                This quiz expired on {{ \Carbon\Carbon::parse($quiz->available_until)->format('F j, Y g:i A') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem; margin-bottom: 2rem;">
            @if(!$isAvailable)
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-lock"></i> Quiz Not Available
                </button>
            @elseif($attempt && !$attempt->completed_at)
                <!-- Continue incomplete quiz -->
                <button type="button" class="btn btn-primary btn-lg" onclick="startQuiz()">
                    <i class="fas fa-play"></i> Continue Quiz
                </button>
            @elseif(!$attempt || $canRetake)
                <!-- Start new quiz or retake -->
                <button type="button" class="btn btn-primary btn-lg" onclick="startQuiz()">
                    <i class="fas fa-play"></i> {{ $attempt ? 'Retake Quiz' : 'Start Quiz' }}
                </button>
            @endif
            
            @if($attempt && $attempt->completed_at)
                <!-- View results -->
                <a href="{{ route('student.quizzes.results', Crypt::encrypt($quiz->id)) }}" 
                   class="btn btn-info btn-lg">
                    <i class="fas fa-chart-bar"></i> View Detailed Results
                </a>
            @endif
        </div>
        
        <!-- Quiz Questions Form (Initially Hidden) -->
        @if($showQuiz)
        <div id="quizQuestions" style="display: block;">
            <form id="quizForm" action="{{ route('student.quizzes.submit', Crypt::encrypt($quiz->id)) }}" method="POST">
                @csrf
                
                <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--dark); margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--border);">
                    Quiz Questions
                </h3>
                
                @foreach($quiz->questions as $index => $question)
                <div class="question-card" style="margin-bottom: 2rem; padding: 1.5rem; border: 1px solid var(--border); border-radius: 8px; background: white;">
                    <!-- Question Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem;">
                                Question #{{ $index + 1 }}
                                <span style="font-size: 0.875rem; color: #6b7280; font-weight: 400;">
                                    ({{ $question->points ?? 1 }} point{{ ($question->points ?? 1) > 1 ? 's' : '' }})
                                </span>
                            </h4>
                            @if($question->explanation)
                            <div style="font-size: 0.875rem; color: #6b7280;">
                                <i class="fas fa-info-circle"></i> {{ $question->explanation }}
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Question Text -->
                    <div style="padding: 1rem; background: #f8fafc; border-radius: 6px; margin-bottom: 1.5rem; border-left: 4px solid var(--primary);">
                        <div style="font-size: 1rem; color: var(--dark); line-height: 1.6;">
                            {!! nl2br(e($question->question)) !!}
                        </div>
                    </div>
                    
                    <!-- Options -->
                    <div>
                        <h5 style="font-size: 0.9375rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem;">
                            Select your answer:
                        </h5>
                        
                        <div style="display: grid; gap: 0.75rem;">
                            @php
                                $options = $question->options ?? collect();
                            @endphp
                            
                            @if($options->count() > 0)
                                @foreach($options as $optionIndex => $option)
                                <div class="option-item">
                                    <input type="radio" 
                                        id="question-{{ $question->id }}-option-{{ $option->id }}"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $option->id }}"
                                        class="option-input"
                                        {{ (isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $option->id) ? 'checked' : '' }}>
                                    <label for="question-{{ $question->id }}-option-{{ $option->id }}" class="option-label">
                                        <span class="option-letter">{{ chr(65 + $optionIndex) }}</span>
                                        <span class="option-text">{{ $option->option_text }}</span>
                                    </label>
                                </div>
                                @endforeach
                            @else
                                <div style="padding: 1rem; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px;">
                                    <i class="fas fa-exclamation-triangle mr-2" style="color: #d97706;"></i>
                                    No options available for this question.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Progress and Submit -->
                <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border); margin-top: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--secondary);">Questions Answered</div>
                            <div style="font-size: 1.25rem; font-weight: 600; color: var(--dark);" id="answeredCount">0</div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--secondary);">Total Questions</div>
                            <div style="font-size: 1.25rem; font-weight: 600; color: var(--dark);">{{ $quiz->questions->count() }}</div>
                        </div>
                        <button type="button" 
                                class="btn btn-success btn-lg"
                                onclick="validateAndSubmit()">
                            <i class="fas fa-paper-plane"></i> Submit Quiz
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @else
        <div id="quizQuestions" style="display: none;"></div>
        @endif
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade" id="resultsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quiz Results</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="resultsContent">
                    <!-- Results will be loaded here via AJAX or from session -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="{{ route('student.quizzes.results', Crypt::encrypt($quiz->id)) }}" 
                   class="btn btn-primary" id="viewDetailedBtn" style="display: none;">
                    View Detailed Results
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .info-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .info-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: var(--secondary);
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: #4f46e5;
    }
    
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
    }
    
    .btn-success {
        background: #10b981;
        color: white;
    }
    
    .btn-success:hover {
        background: #059669;
    }
    
    .btn-info {
        background: #0ea5e9;
        color: white;
    }
    
    .btn-info:hover {
        background: #0284c7;
    }
    
    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1rem;
    }
    
    /* Option Styling */
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
    
    .question-card {
        transition: transform 0.2s;
    }
    
    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        .info-card {
            flex-direction: column;
            text-align: center;
            gap: 0.75rem;
        }
        
        .btn-lg {
            width: 100%;
            justify-content: center;
        }
        
        #quizQuestions .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let answeredQuestions = new Set();
    
    function startQuiz() {
        // Show quiz questions section
        document.getElementById('quizQuestions').style.display = 'block';
        
        // Scroll to questions
        document.getElementById('quizQuestions').scrollIntoView({ behavior: 'smooth' });
        
        // Update answered count
        updateAnswerCount();
    }
    
    function updateAnswerCount() {
        const totalQuestions = {{ $quiz->questions->count() }};
        const answered = document.querySelectorAll('.option-input:checked').length;
        document.getElementById('answeredCount').textContent = answered;
        
        // Store answered questions
        answeredQuestions.clear();
        document.querySelectorAll('.option-input:checked').forEach(input => {
            const questionId = input.name.match(/\[(\d+)\]/)[1];
            answeredQuestions.add(questionId);
        });
    }
    
    function validateAndSubmit() {
        const totalQuestions = {{ $quiz->questions->count() }};
        
        if (answeredQuestions.size < totalQuestions) {
            if (confirm(`You have answered ${answeredQuestions.size} out of ${totalQuestions} questions. Are you sure you want to submit?`)) {
                submitQuiz();
            }
        } else {
            submitQuiz();
        }
    }
    
    function submitQuiz() {
        document.getElementById('quizForm').submit();
    }
    
    // Update answer count when options change
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.option-input').forEach(input => {
            input.addEventListener('change', updateAnswerCount);
        });
        
        // Show results modal if results exist in session
        @if(session('quiz_results'))
            showResultsModal(@json(session('quiz_results')));
        @endif
        
        // Auto-show quiz if started or retake
        @if(session('started') || session('retake'))
            startQuiz();
        @endif
    });
    
    function showResultsModal(results) {
        let html = `
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fas ${results.passed ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}" 
                       style="font-size: 4rem;"></i>
                </div>
                <h4 class="${results.passed ? 'text-success' : 'text-danger'}">
                    ${results.passed ? 'Congratulations!' : 'Try Again!'}
                </h4>
                <p class="text-muted">
                    ${results.passed ? 'You passed the quiz!' : 'You need more practice.'}
                </p>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-center border-right">
                            <div class="text-muted small">Your Score</div>
                            <div class="h3 font-weight-bold ${results.passed ? 'text-success' : 'text-danger'}">
                                ${results.score}/${results.total_points}
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="text-muted small">Percentage</div>
                            <div class="h3 font-weight-bold ${results.passed ? 'text-success' : 'text-danger'}">
                                ${results.percentage}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert ${results.passed ? 'alert-success' : 'alert-danger'}">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Passing Score: ${results.passing_score}%</span>
                    <span class="badge ${results.passed ? 'badge-success' : 'badge-danger'}">
                        ${results.passed ? 'PASSED' : 'FAILED'}
                    </span>
                </div>
            </div>
        `;
        
        // Add question-by-question results if available
        if (results.questions && results.questions.length > 0) {
            html += `
                <div class="mt-4">
                    <h6 class="font-weight-bold text-dark mb-3">Question Results:</h6>
                    <div class="accordion" id="resultsAccordion">
            `;
            
            results.questions.forEach((q, index) => {
                html += `
                    <div class="card mb-2">
                        <div class="card-header" id="heading${index}">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-dark w-100 text-left" type="button" data-toggle="collapse" data-target="#collapse${index}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Question ${index + 1}: ${q.question.substring(0, 50)}${q.question.length > 50 ? '...' : ''}</span>
                                        <span class="badge ${q.is_correct ? 'badge-success' : 'badge-danger'}">
                                            ${q.is_correct ? 'Correct' : 'Incorrect'}
                                        </span>
                                    </div>
                                </button>
                            </h6>
                        </div>
                        <div id="collapse${index}" class="collapse" data-parent="#resultsAccordion">
                            <div class="card-body">
                                <p><strong>Question:</strong> ${q.question}</p>
                                <div class="mt-3">
                                    <strong>Your Answer:</strong>
                                    <div class="mt-2">
                `;
                
                q.options.forEach(option => {
                    html += `
                        <div class="option-result p-2 mb-1 rounded ${option.is_user_selected ? 'bg-light border' : ''} ${option.is_correct ? 'border-success' : option.is_user_selected ? 'border-danger' : ''}">
                            <div class="d-flex align-items-center">
                                <div class="mr-2">
                                    ${option.is_correct ? '<i class="fas fa-check text-success"></i>' : ''}
                                    ${option.is_user_selected && !option.is_correct ? '<i class="fas fa-times text-danger"></i>' : ''}
                                </div>
                                <div>${option.text}</div>
                                ${option.is_user_selected ? '<div class="ml-auto"><span class="badge badge-info">Your Choice</span></div>' : ''}
                                ${option.is_correct ? '<div class="ml-auto"><span class="badge badge-success">Correct Answer</span></div>' : ''}
                            </div>
                        </div>
                    `;
                });
                
                html += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
            
            // Show detailed results button
            document.getElementById('viewDetailedBtn').style.display = 'inline-block';
        }
        
        document.getElementById('resultsContent').innerHTML = html;
        
        // Initialize accordion
        $('.collapse').collapse();
        
        // Show modal
        $('#resultsModal').modal('show');
    }
    
    // Clear results from session after showing
    @if(session('quiz_results'))
        setTimeout(() => {
            fetch('/clear-quiz-results', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        }, 1000);
    @endif
</script>

@if(session('quiz_results'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showResultsModal(@json(session('quiz_results')));
    });
</script>
@endif
@endsection