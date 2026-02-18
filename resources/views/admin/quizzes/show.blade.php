@extends('layouts.admin')

@section('title', 'Quiz Details - ' . $quiz->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-show.css') }}">
@endpush

@section('content')
    <!-- Quiz Details Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-brain card-icon"></i>
                <h2 class="card-title">Quiz Details</h2>
            </div>
            <div class="top-actions">
                <!-- Edit Button -->
                <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                <!-- Delete Button -->
                <form action="{{ route('admin.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="top-action-btn delete-btn" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                
                <!-- Back Button -->
                <a href="{{ route('admin.quizzes.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
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
                    <div class="quiz-preview-badge {{ $quiz->is_published ? 'published' : 'draft' }}">
                        <i class="fas {{ $quiz->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $quiz->is_published ? 'Published' : 'Draft' }}
                    </div>
                    <span class="quiz-preview-id">
                        <i class="fas fa-hashtag"></i> ID: {{ $quiz->id }}
                    </span>
                </div>
            </div>

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
                            <div class="stat-value">{{ $quiz->passing_score }}%</div>
                            <div class="stat-label">Passing Score</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value">{{ $quiz->duration }}</div>
                            <div class="stat-label">Minutes</div>
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

                    <!-- Quiz Information -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-info-circle"></i> Quiz Information
                        </div>
                        
                        <div class="description-box" style="margin-bottom: 1rem;">
                            {{ $quiz->description ?: 'No description provided for this quiz.' }}
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Created</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $quiz->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $quiz->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $quiz->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $quiz->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Questions Section -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-list"></i> Questions
                            <span style="font-size: 0.75rem; color: #718096; font-weight: 400; margin-left: 0.5rem;">({{ $quiz->questions->count() }})</span>
                        </div>
                        
                        @forelse($quiz->questions as $index => $question)
                        <div class="question-card">
                            <div class="question-header">
                                <div>
                                    <div class="question-title">Question #{{ $index + 1 }}</div>
                                    <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: #718096;">
                                        <span style="color: #667eea; font-weight: 600;">
                                            <i class="fas fa-star"></i> {{ $question->points }} points
                                        </span>
                                        <span>
                                            <i class="fas fa-sort"></i> Order: {{ $question->order }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="question-content">
                                <div class="question-text">
                                    {!! nl2br(e($question->question)) !!}
                                </div>
                                
                                <!-- Options -->
                                @if($question->options->count() > 0)
                                <div style="margin-bottom: 1rem;">
                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #2d3748; margin-bottom: 0.75rem;">
                                        Options:
                                    </div>
                                    
                                    <div style="display: grid; gap: 0.5rem;">
                                        @foreach($question->options as $option)
                                        <div class="option-item {{ $option->is_correct ? 'correct' : 'incorrect' }}">
                                            <div class="option-icon">
                                                @if($option->is_correct)
                                                <i class="fas fa-check"></i>
                                                @else
                                                <i class="fas fa-times"></i>
                                                @endif
                                            </div>
                                            <div class="option-text">
                                                {{ $option->option_text }}
                                            </div>
                                            <div class="option-badge">
                                                {{ $option->is_correct ? 'Correct' : 'Incorrect' }}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                @if($question->explanation)
                                <div class="explanation-box">
                                    <div class="explanation-title">
                                        <i class="fas fa-info-circle"></i> Explanation
                                    </div>
                                    <div style="font-size: 0.8125rem; color: #4a5568;">
                                        {!! nl2br(e($question->explanation)) !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h3>No Questions Yet</h3>
                            <p>This quiz doesn't have any questions yet.</p>
                            <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) . '#questions' }}" 
                               style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.25rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                                <i class="fas fa-plus" style="margin-right: 0.375rem;"></i> Add Questions
                            </a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Publish Button for Draft Quizzes -->
                    @if(!$quiz->is_published)
                    <div class="publish-section">
                        <form action="{{ route('admin.quizzes.publish', Crypt::encrypt($quiz->id)) }}" method="POST" id="publishForm" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="publish-btn" id="publishButton">
                                <i class="fas fa-upload"></i> Publish Quiz
                            </button>
                        </form>
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
                                @if($quiz->is_published)
                                    <span style="color: #48bb78;">Published</span>
                                @else
                                    <span style="color: #ed8936;">Draft</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-trophy"></i> Passing Score</span>
                            <span class="info-value">{{ $quiz->passing_score }}%</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Duration</span>
                            <span class="info-value">{{ $quiz->duration }} minutes</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-star"></i> Total Points</span>
                            <span class="info-value">{{ $totalPoints }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-question-circle"></i> Questions</span>
                            <span class="info-value">{{ $quiz->questions->count() }}</span>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <div class="quick-actions-grid">
                            <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Edit Quiz</div>
                                    <div class="action-subtitle">Update quiz settings</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) . '#questions' }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Add Questions</div>
                                    <div class="action-subtitle">Create new questions</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.quizzes.index') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">All Quizzes</div>
                                    <div class="action-subtitle">View all system quizzes</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quiz Stats Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-chart-pie"></i> Statistics
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; color: #718096;">Time Limit</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">{{ $quiz->duration }} minutes</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px solid #edf2f7;">
                                <span style="font-size: 0.75rem; color: #718096;">Passing Score</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">{{ $quiz->passing_score }}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px solid #edf2f7;">
                                <span style="font-size: 0.75rem; color: #718096;">Avg. Points/Question</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">
                                    @if($quiz->questions->count() > 0)
                                        {{ round($totalPoints / $quiz->questions->count(), 1) }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle publish button click with SweetAlert2
        const publishButton = document.getElementById('publishButton');
        if (publishButton) {
            publishButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Publish Quiz?',
                    text: 'Once published, this quiz will be visible to students.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#48bb78',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Publish',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        publishButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';
                        publishButton.disabled = true;
                        document.getElementById('publishForm').submit();
                    }
                });
            });
        }
        
        // Handle delete button click with SweetAlert2
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Quiz?',
                    text: 'This action cannot be undone. All quiz data and questions will be permanently removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        deleteButton.disabled = true;
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
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