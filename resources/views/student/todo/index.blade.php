@extends('layouts.student')

@section('title', 'My To-Do')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/todo-index.css') }}">
<style>
    .assignment-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .assignment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #f59e0b;
    }
    
    .assignment-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }
    
    .assignment-card:hover::before {
        transform: scaleX(1);
    }
    
    .assignment-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .assignment-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    .assignment-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 0.25rem;
    }
    
    .assignment-course {
        font-size: 0.75rem;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin: 1rem 0;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 10px;
    }
    
    .meta-item {
        text-align: center;
    }
    
    .meta-label {
        font-size: 0.625rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.125rem;
    }
    
    .meta-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending {
        background: #fff3e0;
        color: #c05621;
    }
    
    .status-submitted {
        background: #e6fffa;
        color: #2c7a7b;
    }
    
    .status-graded {
        background: #f0fff4;
        color: #22543d;
    }
    
    .status-late {
        background: #fff5f5;
        color: #c53030;
    }
    
    .btn-assignment {
        display: block;
        width: 100%;
        padding: 0.75rem;
        text-align: center;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-assignment:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-secondary {
        background: #6b7280;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }
    
    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        background: white;
        padding: 0.5rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .filter-tab {
        flex: 1;
        padding: 0.625rem;
        text-align: center;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        color: #718096;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .filter-tab.active {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .filter-tab i {
        margin-right: 0.375rem;
    }
    
    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #2d3748;
        margin: 2rem 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-title i {
        color: #f59e0b;
    }
    
    .section-title .count {
        margin-left: auto;
        font-size: 0.875rem;
        font-weight: 600;
        color: #718096;
        background: #f7fafc;
        padding: 0.25rem 1rem;
        border-radius: 20px;
    }
    
    .grade-display {
        background: #f0fff4;
        border-radius: 8px;
        padding: 0.75rem;
        margin: 1rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .grade-score {
        font-size: 1.25rem;
        font-weight: 700;
        color: #22543d;
    }
    
    .grade-feedback {
        font-size: 0.75rem;
        color: #2c7a7b;
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Quiz Results Notification --}}
    @if(session('quiz_results'))
        @php $qr = session('quiz_results'); @endphp
        <div class="alert alert-success">
            <i class="fas {{ $qr['passed'] ? 'fa-trophy' : 'fa-check-circle' }}"></i>
            <div>
                <strong>{{ $qr['passed'] ? '✓ You passed!' : 'Quiz submitted' }} - {{ $qr['quiz'] }}</strong>
                <p style="margin-top: 0.25rem; margin-bottom: 0;">
                    Score: {{ $qr['score'] }}/{{ $qr['total'] }} ({{ $qr['percentage'] }}%)
                </p>
            </div>
        </div>
    @endif

    {{-- Assignment Success Notification --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="filter-tabs">
        <a href="{{ route('student.todo.index', ['type' => 'all']) }}" 
           class="filter-tab {{ $type === 'all' ? 'active' : '' }}">
            <i class="fas fa-list"></i> All
        </a>
        <a href="{{ route('student.todo.index', ['type' => 'quiz']) }}" 
           class="filter-tab {{ $type === 'quiz' ? 'active' : '' }}">
            <i class="fas fa-brain"></i> Quizzes
        </a>
        <a href="{{ route('student.todo.index', ['type' => 'assignment']) }}" 
           class="filter-tab {{ $type === 'assignment' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Assignments
        </a>
    </div>

    {{-- Quizzes Section --}}
    @if($quizzes->isNotEmpty() && ($type === 'all' || $type === 'quiz'))
        <div class="section-title">
            <i class="fas fa-brain"></i>
            Available Quizzes
            <span class="count">{{ $quizzes->count() }}</span>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            @foreach($quizzes as $quiz)
                <div class="assignment-card" style="border-left: 4px solid #667eea;">
                    <div class="assignment-header">
                        <div class="assignment-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div style="flex: 1;">
                            <div class="assignment-title">{{ $quiz->title }}</div>
                            <div class="assignment-course">
                                <i class="fas fa-question-circle"></i> {{ $quiz->questions_count }} Questions
                                <span style="margin-left: 0.5rem;">
                                    <i class="fas fa-trophy"></i> Pass: {{ $quiz->passing_score }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($quiz->latest_attempt)
                        <div class="grade-display" style="background: {{ $quiz->latest_attempt->passed ? '#f0fff4' : '#fff5f5' }};">
                            <div>
                                <span style="font-size: 0.75rem; color: #718096;">Your last attempt</span>
                                <div class="grade-score" style="color: {{ $quiz->latest_attempt->passed ? '#22543d' : '#c53030' }};">
                                    {{ $quiz->latest_attempt->percentage }}%
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 0.75rem; color: #718096;">Score</span>
                                <div style="font-weight: 600;">{{ $quiz->latest_attempt->score }}/{{ $quiz->latest_attempt->total_points }}</div>
                            </div>
                        </div>
                    @endif

                    <div style="margin-top: 1rem;">
                        @if($quiz->latest_attempt)
                            <a href="{{ route('student.todo.quiz.take', Crypt::encrypt($quiz->id)) }}" 
                               class="btn-assignment" 
                               style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                                <i class="fas fa-redo-alt"></i> Retake Quiz
                            </a>
                        @else
                            <a href="{{ route('student.todo.quiz.take', Crypt::encrypt($quiz->id)) }}" 
                               class="btn-assignment" 
                               style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-play"></i> Take Quiz
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Assignments Section --}}
    @if($assignments->isNotEmpty() && ($type === 'all' || $type === 'assignment'))
        <div class="section-title">
            <i class="fas fa-file-alt"></i>
            Available Assignments
            <span class="count">{{ $assignments->count() }}</span>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1rem;">
            @foreach($assignments as $assignment)
                @php
                    $submission = $assignment->my_submission;
                    $status = 'pending';
                    $statusText = 'Pending';
                    $statusIcon = 'fa-clock';
                    $statusClass = 'status-pending';
                    $btnText = 'Start Assignment';
                    
                    if ($submission) {
                        if ($submission->status == 'graded') {
                            $status = 'graded';
                            $statusText = 'Graded';
                            $statusIcon = 'fa-check-circle';
                            $statusClass = 'status-graded';
                            $btnText = 'View Grade';
                        } elseif ($submission->status == 'late') {
                            $status = 'late';
                            $statusText = 'Late Submission';
                            $statusIcon = 'fa-exclamation-circle';
                            $statusClass = 'status-late';
                            $btnText = 'Resubmit';
                        } else {
                            $status = 'submitted';
                            $statusText = 'Submitted';
                            $statusIcon = 'fa-paper-plane';
                            $statusClass = 'status-submitted';
                            $btnText = 'View/Resubmit';
                        }
                    }
                    
                    $isOverdue = $assignment->due_date && $assignment->due_date->isPast() && !$submission;
                @endphp
                
                <div class="assignment-card" style="border-left: 4px solid {{ $isOverdue ? '#f56565' : '#f59e0b' }};">
                    <div class="assignment-header">
                        <div class="assignment-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div style="flex: 1;">
                            <div class="assignment-title">{{ $assignment->title }}</div>
                            <div class="assignment-course">
                                <i class="fas fa-book"></i> {{ $assignment->course->course_name ?? 'No Course' }}
                            </div>
                        </div>
                        <span class="status-badge {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }}"></i> {{ $statusText }}
                        </span>
                    </div>

                    <div class="meta-grid">
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-star"></i> Points</div>
                            <div class="meta-value">{{ $assignment->points }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-calendar-alt"></i> Due</div>
                            <div class="meta-value">
                                @if($assignment->due_date)
                                    {{ $assignment->due_date->format('M d') }}
                                @else
                                    No due
                                @endif
                            </div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-clock"></i> Status</div>
                            <div class="meta-value">
                                @if($isOverdue)
                                    <span style="color: #f56565;">Overdue</span>
                                @elseif($assignment->available_from && $assignment->available_from->isFuture())
                                    <span style="color: #718096;">Not yet</span>
                                @else
                                    <span style="color: #48bb78;">Open</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($submission && $submission->status == 'graded')
                        <div class="grade-display">
                            <div>
                                <span style="font-size: 0.75rem; color: #718096;">Your grade</span>
                                <div class="grade-score">{{ $submission->score }}/{{ $assignment->points }}</div>
                                @if($submission->feedback)
                                    <div class="grade-feedback">
                                        <i class="fas fa-comment"></i> {{ Str::limit($submission->feedback, 50) }}
                                    </div>
                                @endif
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 0.75rem; color: #718096;">Percentage</span>
                                <div style="font-weight: 700; color: #22543d;">
                                    {{ round(($submission->score / $assignment->points) * 100) }}%
                                </div>
                            </div>
                        </div>
                    @elseif($submission)
                        <div style="background: #f8fafc; border-radius: 8px; padding: 0.75rem; margin: 1rem 0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                                <span style="font-size: 0.875rem;">
                                    Submitted {{ $submission->submitted_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <div style="margin-top: 1rem;">
                        <a href="{{ route('student.todo.assignment.view', Crypt::encrypt($assignment->id)) }}" 
                           class="btn-assignment {{ $submission ? 'btn-secondary' : '' }}">
                            <i class="fas fa-{{ $submission ? 'eye' : 'pencil-alt' }}"></i> 
                            {{ $btnText }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Empty State --}}
    @if($quizzes->isEmpty() && $assignments->isEmpty())
        <div class="empty-state" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 4rem; color: #cbd5e0; margin-bottom: 1.5rem;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 style="font-size: 1.25rem; color: #4a5568; margin-bottom: 0.5rem;">All Caught Up!</h3>
            <p style="color: #718096; max-width: 400px; margin: 0 auto;">
                You don't have any pending quizzes or assignments at the moment. Check back later for new tasks.
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        });
    });
</script>
@endpush