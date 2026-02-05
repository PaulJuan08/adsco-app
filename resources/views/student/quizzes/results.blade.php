@extends('layouts.student')

@section('title', 'Quiz Results - ' . $quiz->title)

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Quiz Results: {{ $quiz->title }}</h1>
        <p>Your performance on this quiz</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Results Summary -->
<div class="card mb-4">
    <div class="card-header">
        <h2 class="card-title">Results Summary</h2>
        <div class="header-actions">
            <a href="{{ route('student.quizzes.show', Crypt::encrypt($quiz->id)) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Quiz
            </a>
        </div>
    </div>
    
    <div style="padding: 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="info-card">
                <div class="info-icon" style="background: {{ $attempt->passed ? '#dcfce7' : '#fee2e2' }}; color: {{ $attempt->passed ? '#10b981' : '#ef4444' }};">
                    <i class="fas {{ $attempt->passed ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Status</div>
                    <div class="info-value {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                        {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon" style="background: #e0e7ff; color: #4f46e5;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Your Score</div>
                    <div class="info-value">{{ $attempt->score }}/{{ $attempt->total_points }}</div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Percentage</div>
                    <div class="info-value">{{ $attempt->percentage }}%</div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon" style="background: #dbeafe; color: #3b82f6;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Passing Score</div>
                    <div class="info-value">{{ $quiz->passing_score }}%</div>
                </div>
            </div>
        </div>
        
        @if($attempt->time_taken)
        <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-clock" style="color: #6b7280;"></i>
                <div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Time Taken</div>
                    <div style="font-weight: 600; color: var(--dark);">
                        @if($attempt->time_taken < 60)
                            {{ $attempt->time_taken }} seconds
                        @else
                            {{ floor($attempt->time_taken / 60) }} minutes {{ $attempt->time_taken % 60 }} seconds
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Question-by-Question Results -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Detailed Results</h2>
        <div class="header-actions">
            <div class="text-muted">
                @php
                    $correctCount = count(array_filter($questions, function($q) {
                        return $q['is_correct'];
                    }));
                @endphp
                {{ $correctCount }} of {{ count($questions) }} correct
            </div>
        </div>
    </div>
    
    <div style="padding: 2rem;">
        @foreach($questions as $index => $q)
        <div class="question-result" style="margin-bottom: 2rem; padding: 1.5rem; border: 1px solid {{ $q['is_correct'] ? '#10b981' : '#ef4444' }}; border-radius: 8px; background: {{ $q['is_correct'] ? '#f0fdf4' : '#fef2f2' }};">
            <!-- Question Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <h4 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem;">
                        Question #{{ $index + 1 }}
                        <span class="badge {{ $q['is_correct'] ? 'badge-success' : 'badge-danger' }}">
                            {{ $q['is_correct'] ? 'Correct' : 'Incorrect' }}
                        </span>
                    </h4>
                </div>
            </div>
            
            <!-- Question Text -->
            <div style="padding: 1rem; background: white; border-radius: 6px; margin-bottom: 1.5rem; border-left: 4px solid {{ $q['is_correct'] ? '#10b981' : '#ef4444' }};">
                <div style="font-size: 1rem; color: var(--dark); line-height: 1.6;">
                    {!! nl2br(e($q['question']->question)) !!}
                </div>
            </div>
            
            <!-- Options with Results -->
            <div>
                <h5 style="font-size: 0.9375rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem;">
                    Your Answer:
                </h5>
                
                <div style="display: grid; gap: 0.75rem;">
                    @foreach($q['all_options'] as $option)
                    <div class="option-result" style="padding: 1rem; border: 2px solid {{ $option->is_correct ? '#10b981' : ($option->id == $q['user_answer'] ? '#ef4444' : '#e5e7eb') }}; border-radius: 8px; background: {{ $option->is_correct ? '#f0fdf4' : ($option->id == $q['user_answer'] ? '#fef2f2' : 'white') }};">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                @if($option->is_correct)
                                    <i class="fas fa-check" style="color: #10b981;"></i>
                                @elseif($option->id == $q['user_answer'])
                                    <i class="fas fa-times" style="color: #ef4444;"></i>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                {{ $option->option_text }}
                            </div>
                            <div>
                                @if($option->is_correct)
                                    <span class="badge badge-success">Correct Answer</span>
                                @endif
                                @if($option->id == $q['user_answer'])
                                    <span class="badge badge-info">Your Choice</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Result Summary -->
                <div style="margin-top: 1rem; padding: 1rem; background: white; border-radius: 6px;">
                    @if($q['is_correct'])
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #10b981;">
                            <i class="fas fa-check-circle"></i>
                            <span>Well done! You selected the correct answer.</span>
                        </div>
                    @else
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #ef4444;">
                            <i class="fas fa-times-circle"></i>
                            <span>Your answer was incorrect.</span>
                        </div>
                        @if(isset($q['correct_option']) && $q['correct_option'])
                            <div style="margin-top: 0.5rem; padding: 0.75rem; background: #f0fdf4; border-radius: 6px; border-left: 4px solid #10b981;">
                                <div style="font-size: 0.875rem; color: #065f46; font-weight: 600;">
                                    <i class="fas fa-lightbulb mr-1"></i> Correct Answer: 
                                    {{ $q['correct_option']->option_text }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Action Buttons -->
<div style="display: flex; gap: 1rem; margin-top: 2rem;">
    <a href="{{ route('student.quizzes.index') }}" class="btn btn-secondary">
        <i class="fas fa-list"></i> Back to Quizzes
    </a>
    
    @php
        $completedAttempts = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->count();
    @endphp
    
    @if($quiz->max_attempts == 0 || $completedAttempts < $quiz->max_attempts)
        <a href="{{ route('student.quizzes.retake', Crypt::encrypt($quiz->id)) }}" 
           class="btn btn-primary"
           onclick="return confirm('Are you sure you want to retake this quiz? Your previous attempt will be saved.');">
            <i class="fas fa-redo"></i> Retake Quiz
        </a>
    @endif
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
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .badge-success {
        background: #10b981;
        color: white;
    }
    
    .badge-danger {
        background: #ef4444;
        color: white;
    }
    
    .badge-info {
        background: #0ea5e9;
        color: white;
    }
    
    .question-result {
        transition: transform 0.2s;
    }
    
    .question-result:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
</style>
@endsection