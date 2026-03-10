@extends('layouts.student')

@section('title', 'My To-Do')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/todo-index.css') }}">
<link rel="stylesheet" href="{{ asset('css/student-todo.css') }}">
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
                                <i class="fas fa-book"></i> {{ $assignment->course->title ?? 'No Course' }}
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