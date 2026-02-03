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

<!-- Main Quiz Details -->
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
        <!-- Quiz Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Total Questions</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $quiz->questions->count() }}</div>
            </div>
            
            <div style="padding: 1.5rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Status</div>
                <div>
                    @if($quiz->is_published)
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 20px; font-weight: 500; font-size: 0.875rem;">
                        <i class="fas fa-check-circle"></i> Published
                    </span>
                    @else
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 20px; font-weight: 500; font-size: 0.875rem;">
                        <i class="fas fa-clock"></i> Draft
                    </span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quiz Info -->
        <div style="padding: 1.5rem; background: white; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 2rem;">
            <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem;">Quiz Information</h3>
            <div>
                <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Description</div>
                <div style="color: var(--dark);">{{ $quiz->description }}</div>
            </div>
        </div>
        
        <!-- Questions Section -->
        <div style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--dark);">
                    Questions
                    <span style="font-size: 0.875rem; color: #6b7280; font-weight: 400;">({{ $quiz->questions->count() }})</span>
                </h2>
            </div>
            
            @forelse($quiz->questions as $index => $question)
            <div style="margin-bottom: 1.5rem; padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 8px; background: white;">
                <!-- Question Header -->
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 0.25rem;">
                            Question #{{ $index + 1 }}
                        </h3>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.875rem; color: #6b7280;">
                                Points: <strong style="color: var(--primary);">1</strong>
                            </span>
                            <span style="color: #d1d5db;">•</span>
                            <span style="font-size: 0.875rem; color: #6b7280;">
                                Order: <strong>{{ $question->order }}</strong>
                            </span>
                            <span style="color: #d1d5db;">•</span>
                            <span style="font-size: 0.875rem; color: #6b7280;">
                                Options: <strong>{{ $question->options ? $question->options->count() : 0 }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Question Content -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="padding: 1rem; background: #f8fafc; border-radius: 6px; border-left: 4px solid var(--primary);">
                        <div style="font-size: 0.9375rem; color: var(--dark); line-height: 1.6;">
                            {!! nl2br(e($question->question)) !!}
                        </div>
                    </div>
                    
                    @if($question->explanation)
                    <div style="margin-top: 1rem; padding: 1rem; background: #f0f9ff; border-radius: 6px; border-left: 4px solid #0ea5e9;">
                        <div style="font-size: 0.875rem; color: #0369a1; font-weight: 600; margin-bottom: 0.25rem;">
                            <i class="fas fa-info-circle"></i> Explanation
                        </div>
                        <div style="font-size: 0.875rem; color: #0c4a6e;">
                            {!! nl2br(e($question->explanation)) !!}
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Options -->
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin-bottom: 0.75rem;">
                        Options ({{ $question->options->count() }} total):
                    </div>
                    
                    @if($question->options->count() > 0)
                    <div style="display: grid; gap: 0.5rem;">
                        @foreach($question->options as $optionIndex => $option)
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; 
                                background: {{ $option->is_correct ? '#dcfce7' : '#f9fafb' }}; 
                                border: 1px solid {{ $option->is_correct ? '#10b981' : '#e5e7eb' }};
                                border-left: 4px solid {{ $option->is_correct ? '#10b981' : '#d1d5db' }};
                                border-radius: 6px;">
                            <div style="display: flex; align-items: center; justify-content: center; width: 20px; height: 20px;">
                                @if($option->is_correct)
                                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                @else
                                <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                                @endif
                            </div>
                            <div style="flex: 1; font-size: 0.875rem; color: var(--dark);">
                                {{ $option->option_text }}
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="font-size: 0.75rem; color: #6b7280; padding: 2px 8px; background: white; border-radius: 4px;">
                                    Order: {{ $option->order }}
                                </div>
                                <div style="font-size: 0.75rem; color: {{ $option->is_correct ? '#166534' : '#dc2626' }}; padding: 2px 8px; background: white; border-radius: 4px; font-weight: 500;">
                                    {{ $option->is_correct ? 'Correct' : 'Incorrect' }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Statistics --}}
                    @php
                        $correctCount = $question->options->where('is_correct', true)->count();
                        $incorrectCount = $question->options->where('is_correct', false)->count();
                    @endphp
                    
                    <div style="margin-top: 1rem; display: flex; gap: 1rem; font-size: 0.875rem;">
                        <div style="color: #166534; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ $correctCount }} correct</span>
                        </div>
                        <div style="color: #dc2626; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-times-circle"></i>
                            <span>{{ $incorrectCount }} incorrect</span>
                        </div>
                    </div>
                    @else
                    <div style="padding: 1rem; background: #fef3c7; border: 1px solid #fbbf24; border-radius: 6px; text-align: center;">
                        <i class="fas fa-exclamation-triangle" style="color: #92400e; margin-right: 8px;"></i>
                        <span style="color: #92400e;">No options found for this question.</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem; background: #f8fafc; border-radius: 8px;">
                <div style="width: 64px; height: 64px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <i class="fas fa-question" style="font-size: 1.5rem; color: #9ca3af;"></i>
                </div>
                <h3 style="font-size: 1rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No Questions Yet</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 1rem;">This quiz doesn't have any questions yet.</p>
                <a href="{{ route('teacher.quizzes.edit', Crypt::encrypt($quiz->id)) }}" 
                style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--primary); color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add Questions
                </a>
            </div>
            @endforelse
        </div>
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
    
    /* Responsive grid */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .card-header > div {
            width: 100%;
            justify-content: flex-start;
        }
        
        .card-header h2 {
            width: 100%;
        }
    }
</style>
@endsection