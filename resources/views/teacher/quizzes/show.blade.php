@extends('layouts.teacher')

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

<!-- Results Modal -->
@if(session('results'))
<div id="resultsModal" style="display: block; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow-y: auto;">
    <div style="background-color: white; margin: 5% auto; padding: 0; width: 90%; max-width: 600px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); position: relative;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; background: white; z-index: 10;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--dark); margin: 0;">Quiz Results</h2>
        </div>
        
        <div style="padding: 1.5rem; max-height: calc(80vh - 120px); overflow-y: auto;">
            @php
                $results = session('results') ?? [];
                $score = session('score') ?? 0;
                $totalPoints = session('totalPoints') ?? 0;
                $percentage = session('percentage') ?? 0;
                $passed = session('passed') ?? false;
            @endphp
            
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="display: inline-block; padding: 1.5rem; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, #5b21b6 100%); width: 120px; height: 120px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <div style="font-size: 2rem; font-weight: 700; color: white;">{{ $percentage }}%</div>
                    <div style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.9);">Your Score</div>
                </div>
                
                <div style="margin-top: 1rem;">
                    @if($passed)
                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #dcfce7; color: #166534; border-radius: 6px; font-weight: 500;">
                            <i class="fas fa-check-circle"></i>
                            <span>PASSED! ({{ $quiz->passing_score }}% required)</span>
                        </div>
                    @else
                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #fee2e2; color: #991b1b; border-radius: 6px; font-weight: 500;">
                            <i class="fas fa-times-circle"></i>
                            <span>FAILED ({{ $quiz->passing_score }}% required)</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Detailed Results -->
            <div style="margin-top: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem; position: sticky; top: 0; background: white; padding: 8px 0;">Detailed Results</h3>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.25rem; font-weight: 600; color: var(--primary);">{{ $score }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">Points Earned</div>
                    </div>
                    <div style="padding: 0.75rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.25rem; font-weight: 600; color: #10b981;">{{ $totalPoints }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">Total Points</div>
                    </div>
                </div>
                
                <!-- Question Review -->
                @if(count($results) > 0)
                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem;">
                    @foreach($results as $index => $result)
                    <div style="margin-bottom: 1rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 8px; border-left: 4px solid {{ $result['is_correct'] ? '#10b981' : '#ef4444' }}; background: white;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <div style="font-size: 0.875rem; font-weight: 600; color: var(--dark);">
                                Question {{ $index + 1 }}
                            </div>
                            <div style="font-size: 0.75rem; font-weight: 600; color: {{ $result['is_correct'] ? '#10b981' : '#ef4444' }};">
                                {{ $result['points'] ?? 0 }} / {{ $result['question']->points ?? 1 }} points
                            </div>
                        </div>
                        <div style="font-size: 0.75rem; color: #6b7280;">
                            @if($result['is_correct'] ?? false)
                                <i class="fas fa-check" style="color: #10b981; margin-right: 4px;"></i> Correct
                            @else
                                <i class="fas fa-times" style="color: #ef4444; margin-right: 4px;"></i> Incorrect
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="padding: 2rem; text-align: center; background: #f8fafc; border-radius: 8px;">
                    <i class="fas fa-info-circle" style="font-size: 2rem; color: #6b7280; margin-bottom: 1rem;"></i>
                    <p style="color: #6b7280;">No results available.</p>
                </div>
                @endif
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button onclick="closeModal()" 
                        style="padding: 8px 16px; background: transparent; color: #6b7280; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 500; cursor: pointer;">
                    Close
                </button>
                <button onclick="retakeQuiz()" 
                        style="padding: 8px 16px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-redo" style="margin-right: 4px;"></i> Retake Quiz
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add this CSS to prevent body scrolling when modal is open -->
<style>
    body.modal-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
    }
</style>
@endif

<!-- Main Quiz Content -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ $quiz->title }}</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('teacher.quizzes.edit', Crypt::encrypt($quiz->id)) }}" 
               style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--primary); color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('teacher.quizzes.index') }}" 
               style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div style="padding: 2rem;">
        <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
            <div style="text-align: center;">
                <div style="font-size: 0.875rem; color: #6b7280;">Questions</div>
                <div style="font-weight: 600; font-size: 1rem; color: var(--dark);">{{ $quiz->questions->count() }}</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 0.875rem; color: #6b7280;">Passing Score</div>
                <div style="font-weight: 600; font-size: 1rem; color: var(--dark);">{{ $quiz->passing_score }}%</div>
            </div>
        </div>
        
        <form action="{{ route('teacher.quizzes.submit', Crypt::encrypt($quiz->id)) }}" method="POST" id="quiz-form">
            @csrf
            
            @foreach($quiz->questions as $index => $question)
            <div style="margin-bottom: 2rem; padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem;">
                            Question {{ $index + 1 }}
                            <span style="font-size: 0.875rem; font-weight: 500; color: var(--primary); margin-left: 0.5rem;">
                                ({{ $question->points ?? 1 }} point{{ $question->points != 1 ? 's' : '' }})
                            </span>
                        </h3>
                        @if($question->type == 'single')
                            <span style="display: inline-block; padding: 2px 8px; background: #e0e7ff; color: var(--primary); border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                Single Answer
                            </span>
                        @else
                            <span style="display: inline-block; padding: 2px 8px; background: #fef3c7; color: #92400e; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                Multiple Answers
                            </span>
                        @endif
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 1rem; font-weight: 500; color: var(--dark); line-height: 1.6;">
                        {{ $question->question }}
                    </div>
                </div>
                
                <!-- Options -->
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin-bottom: 0.75rem;">
                        Select {{ $question->type == 'single' ? 'one' : 'all correct' }} answer(s):
                    </div>
                    
                    <div style="display: grid; gap: 0.75rem;">
                        @foreach($question->options as $option)
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: white; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer;"
                             onclick="toggleOption(this, {{ $question->id }}, {{ $option->id }}, '{{ $question->type }}')">
                            @if($question->type == 'single')
                                <input type="radio" 
                                       name="question_{{ $question->id }}"
                                       value="{{ $option->id }}"
                                       id="option_{{ $option->id }}"
                                       style="width: 18px; height: 18px;">
                            @else
                                <input type="checkbox" 
                                       name="question_{{ $question->id }}[]"
                                       value="{{ $option->id }}"
                                       id="option_{{ $option->id }}"
                                       style="width: 18px; height: 18px;">
                            @endif
                            <label for="option_{{ $option->id }}" style="flex: 1; font-size: 0.875rem; color: var(--dark); cursor: pointer; margin: 0;">
                                {{ $option->option_text }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
            
            <div style="display: flex; justify-content: center; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" 
                        style="padding: 12px 32px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-paper-plane"></i> Submit Quiz
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    }
    
    /* Modal animation */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #resultsModal > div {
        animation: modalFadeIn 0.3s ease-out;
    }
    
    /* Prevent body scroll when modal is open */
    body.modal-open {
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
    }
</style>

<script>
    // Function to toggle option selection with visual feedback
    function toggleOption(element, questionId, optionId, questionType) {
        const input = element.querySelector('input');
        
        if (questionType === 'single') {
            // For single answer questions, uncheck all other options in this question
            const allOptions = document.querySelectorAll(`div[onclick*="toggleOption.*${questionId}"]`);
            allOptions.forEach(opt => {
                opt.style.background = 'white';
                opt.querySelector('input').checked = false;
            });
            
            // Check this option
            input.checked = true;
            element.style.background = '#f3f4f6';
        } else {
            // For multiple answer questions, just toggle this option
            input.checked = !input.checked;
            if (input.checked) {
                element.style.background = '#f3f4f6';
            } else {
                element.style.background = 'white';
            }
        }
    }
    
    // Form validation
    document.getElementById('quiz-form').addEventListener('submit', function(e) {
        const questionCount = {{ $quiz->questions->count() }};
        let answeredQuestions = 0;
        
        for (let i = 1; i <= questionCount; i++) {
            const questionInputs = document.querySelectorAll(`input[name^="question_"]`);
            let questionAnswered = false;
            
            questionInputs.forEach(input => {
                if (input.checked) {
                    questionAnswered = true;
                }
            });
            
            if (questionAnswered) {
                answeredQuestions++;
            }
        }
        
        if (answeredQuestions < questionCount) {
            e.preventDefault();
            const confirmSubmit = confirm(`You have answered ${answeredQuestions} out of ${questionCount} questions. Are you sure you want to submit?`);
            if (!confirmSubmit) {
                return false;
            }
        }
    });
    
    // Modal functions
    function closeModal() {
        const modal = document.getElementById('resultsModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            
            // Reload the page to clear session and show fresh quiz form
            window.location.href = "{{ route('teacher.quizzes.show', Crypt::encrypt($quiz->id)) }}";
        }
    }

    function retakeQuiz() {
        // Reload the page to clear session and show fresh quiz form
        window.location.href = "{{ route('teacher.quizzes.show', Crypt::encrypt($quiz->id)) }}";
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('resultsModal');
        if (modal && event.target == modal) {
            closeModal();
        }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('resultsModal');
            if (modal && modal.style.display === 'block') {
                closeModal();
            }
        }
    });
    
    // Initialize modal state on page load
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('resultsModal');
        if (modal && modal.style.display === 'block') {
            // Prevent body scrolling when modal is open
            document.body.classList.add('modal-open');
            
            // Add event listener to modal close button
            document.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }
        
        // Also add modal-open class immediately if modal exists
        if (modal) {
            document.body.classList.add('modal-open');
        }
    });
    
    // Clean up when leaving the page
    window.addEventListener('beforeunload', function() {
        document.body.classList.remove('modal-open');
    });
</script>
@endsection