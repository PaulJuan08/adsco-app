@extends('layouts.admin')

@section('title', 'Quiz Details - ' . $quiz->title)

@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #e0e7ff;
        --primary-dark: #3730a3;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
        --success: #10b981;
        --success-light: #d1fae5;
        --success-dark: #047857;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --danger-dark: #b91c1c;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --warning-dark: #d97706;
        --radius: 0.5rem;
        --radius-sm: 0.25rem;
        --radius-lg: 0.75rem;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    /* Form Container */
    .form-container {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.125rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }

    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer-modern {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-published {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    .status-draft {
        background: var(--warning-light);
        color: var(--warning-dark);
    }
    
    /* Detail Sections */
    .detail-section {
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
    }
    
    .detail-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .detail-section-title i {
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    .detail-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-value {
        font-size: 1.125rem;
        color: var(--gray-900);
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .detail-subvalue {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin-top: -0.75rem;
        margin-bottom: 1rem;
    }
    
    /* Question Cards */
    .question-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }
    
    .question-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        background: var(--gray-50);
    }
    
    .question-content {
        padding: 1rem 1.5rem;
    }
    
    .question-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
    }
    
    .question-description {
        color: var(--gray-600);
        font-size: 0.875rem;
        line-height: 1.6;
    }
    
    /* Option Styles */
    .option-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-left: 4px solid var(--gray-300);
        border-radius: var(--radius-sm);
        margin-bottom: 0.5rem;
    }
    
    .option-item.correct {
        background: var(--success-light);
        border-color: var(--success);
        border-left-color: var(--success);
    }
    
    .option-item.incorrect {
        background: var(--danger-light);
        border-color: var(--danger);
        border-left-color: var(--danger);
    }
    
    .option-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: white;
        color: var(--gray-600);
        font-size: 0.75rem;
    }
    
    .option-item.correct .option-icon {
        background: var(--success);
        color: white;
    }
    
    .option-item.incorrect .option-icon {
        background: var(--danger);
        color: white;
    }
    
    .option-text {
        flex: 1;
        font-size: 0.875rem;
        color: var(--gray-900);
    }
    
    .option-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        background: white;
        font-weight: 500;
    }
    
    .option-item.correct .option-badge {
        color: var(--success-dark);
        background: var(--success-light);
    }
    
    .option-item.incorrect .option-badge {
        color: var(--danger-dark);
        background: var(--danger-light);
    }
    
    .explanation-box {
        padding: 1rem;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        border-left: 4px solid var(--primary);
        margin-top: 1rem;
    }
    
    .explanation-title {
        font-size: 0.875rem;
        color: var(--primary-dark);
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Action Buttons */
    .action-buttons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-edit {
        background: var(--primary-light);
        color: var(--primary-dark);
    }
    
    .btn-edit:hover {
        background: var(--primary);
        color: white;
    }
    
    .btn-delete {
        background: var(--danger-light);
        color: var(--danger-dark);
    }
    
    .btn-delete:hover {
        background: var(--danger);
        color: white;
    }
    
    .btn-back {
        background: var(--gray-100);
        color: var(--gray-700);
    }
    
    .btn-back:hover {
        background: var(--gray-200);
        color: var(--gray-900);
    }
    
    .btn-success {
        background: var(--success);
        color: white;
    }
    
    .btn-success:hover {
        background: var(--success-dark);
    }
    
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--radius);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .stat-card.stat-primary {
        border-top: 3px solid var(--primary);
    }
    
    .stat-card.stat-success {
        border-top: 3px solid var(--success);
    }
    
    .stat-card.stat-warning {
        border-top: 3px solid var(--warning);
    }
    
    .stat-card.stat-info {
        border-top: 3px solid var(--primary);
    }
    
    .stat-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
    }
    
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--primary);
    }
    
    .stat-success .stat-icon-wrapper {
        background: var(--success-light);
        color: var(--success);
    }
    
    .stat-warning .stat-icon-wrapper {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .stat-info .stat-icon-wrapper {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    /* Quick Actions */
    .action-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
    }
    
    .action-card:hover {
        background: white;
        transform: translateY(-1px);
    }
    
    .action-card.action-primary {
        border-left: 3px solid var(--primary);
    }
    
    .action-card.action-success {
        border-left: 3px solid var(--success);
    }
    
    .action-card.action-warning {
        border-left: 3px solid var(--warning);
    }
    
    .action-card.action-info {
        border-left: 3px solid var(--primary);
    }
    
    .action-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--primary);
    }
    
    .action-success .action-icon {
        background: var(--success-light);
        color: var(--success);
    }
    
    .action-warning .action-icon {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .action-info .action-icon {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .action-content {
        flex: 1;
    }
    
    .action-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }
    
    .action-subtitle {
        font-size: 0.625rem;
        color: var(--gray-600);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons-grid {
            grid-template-columns: 1fr;
        }
        
        .question-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .question-content {
            padding: 1rem;
        }
        
        .detail-section {
            padding: 1rem;
        }
        
        .detail-value {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Quiz Profile Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-brain card-icon"></i>
                <h2 class="card-title">Quiz Details: {{ $quiz->title }}</h2>
            </div>
            <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) }}" class="view-all-link">
                Edit Quiz <i class="fas fa-edit"></i>
            </a>
        </div>
        
        <div class="card-body">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700; margin: 0 auto 1.5rem;">
                    {{ strtoupper(substr($quiz->title, 0, 1)) }}
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.5rem;">
                    {{ $quiz->title }}
                </h3>
                
                <div class="status-badge {{ $quiz->is_published ? 'status-published' : 'status-draft' }}">
                    <i class="fas {{ $quiz->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $quiz->is_published ? 'Quiz Published' : 'Draft Mode' }}
                </div>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card stat-primary">
                    <div class="stat-content">
                        <div>
                            <div class="stat-label">Status</div>
                            <div class="stat-number">
                                @if($quiz->is_published)
                                    <span style="color: var(--success);">Published</span>
                                @else
                                    <span style="color: var(--warning);">Draft</span>
                                @endif
                            </div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-{{ $quiz->is_published ? 'check-circle' : 'clock' }}"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card stat-success">
                    <div class="stat-content">
                        <div>
                            <div class="stat-label">Questions</div>
                            <div class="stat-number">{{ $quiz->questions->count() }}</div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-question"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card stat-warning">
                    <div class="stat-content">
                        <div>
                            <div class="stat-label">Passing Score</div>
                            <div class="stat-number">{{ $quiz->passing_score }}%</div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i>
                        Quiz Information
                    </div>
                    
                    <div>
                        <div class="detail-label">Quiz Title</div>
                        <div class="detail-value">{{ $quiz->title }}</div>
                        
                        <div class="detail-label">Description</div>
                        <div class="detail-value">{{ $quiz->description }}</div>
                        
                        <div class="detail-label">Quiz ID</div>
                        <div class="detail-value">#{{ $quiz->id }}</div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-chart-bar"></i>
                        Quiz Settings
                    </div>
                    
                    <div>
                        <div class="detail-label">Passing Score</div>
                        <div class="detail-value">{{ $quiz->passing_score }}%</div>
                        
                        <div class="detail-label">Duration</div>
                        <div class="detail-value">{{ $quiz->duration }} minutes</div>
                        
                        @php
                            $totalPoints = $quiz->questions->sum('points');
                        @endphp
                        <div class="detail-label">Total Points</div>
                        <div class="detail-value">{{ $totalPoints }} points</div>
                    </div>
                </div>
            </div>
            
            <!-- Questions Section -->
            <div style="margin-top: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--gray-900);">
                        Questions
                        <span style="font-size: 0.875rem; color: var(--gray-500); font-weight: 400;">({{ $quiz->questions->count() }})</span>
                    </h2>
                </div>
                
                @forelse($quiz->questions as $index => $question)
                <div class="question-card">
                    <div class="question-header">
                        <div>
                            <div class="question-title">Question #{{ $index + 1 }}</div>
                            <div style="display: flex; gap: 1rem; font-size: 0.875rem; color: var(--gray-500);">
                                <span style="color: var(--primary); font-weight: 500;">
                                    <i class="fas fa-star"></i> {{ $question->points }} points
                                </span>
                                <span>Order: {{ $question->order }}</span>
                                <span>Type: {{ $question->question_type ?? 'Multiple Choice' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="question-content">
                        <div style="padding: 1rem; background: var(--gray-50); border-radius: var(--radius-sm); border-left: 4px solid var(--primary); margin-bottom: 1rem;">
                            <div style="font-size: 0.9375rem; color: var(--gray-900); line-height: 1.6;">
                                {!! nl2br(e($question->question)) !!}
                            </div>
                        </div>
                        
                        <!-- Options -->
                        @if($question->options->count() > 0)
                        <div style="margin-bottom: 1rem;">
                            <div style="font-size: 0.875rem; font-weight: 600; color: var(--gray-900); margin-bottom: 0.75rem;">
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
                            <div style="font-size: 0.875rem; color: var(--primary-dark);">
                                {!! nl2br(e($question->explanation)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 3rem; background: var(--gray-50); border-radius: var(--radius);">
                    <div style="width: 64px; height: 64px; background: var(--gray-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-question" style="font-size: 1.5rem; color: var(--gray-400);"></i>
                    </div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: var(--gray-600); margin-bottom: 0.5rem;">No Questions Yet</h3>
                    <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: 1rem;">This quiz doesn't have any questions yet.</p>
                    <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) . '#questions' }}" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--primary); color: white; text-decoration: none; border-radius: var(--radius); font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-plus"></i> Add Questions
                    </a>
                </div>
                @endforelse
            </div>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div style="margin-top: 1.5rem; padding: 1rem; background: var(--success-light); color: var(--success-dark); border-radius: var(--radius-sm); border-left: 4px solid var(--success);">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div style="margin-top: 1.5rem; padding: 1rem; background: var(--danger-light); color: var(--danger-dark); border-radius: var(--radius-sm); border-left: 4px solid var(--danger);">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
        
        <div class="card-footer-modern">
            <div class="action-buttons-grid">
                <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) }}" class="action-btn btn-edit">
                    <i class="fas fa-edit"></i>
                    Edit Quiz
                </a>
                
                @if(!$quiz->is_published)
                <form action="{{ route('admin.quizzes.publish', Crypt::encrypt($quiz->id)) }}" method="POST" id="publishForm">
                    @csrf
                    <button type="submit" class="action-btn btn-success" id="publishButton">
                        <i class="fas fa-upload"></i>
                        Publish Quiz
                    </button>
                </form>
                @endif
                
                <form action="{{ route('admin.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-delete" id="deleteButton">
                        <i class="fas fa-trash"></i>
                        Delete Quiz
                    </button>
                </form>
                
                <a href="{{ route('admin.quizzes.index') }}" class="action-btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Quizzes
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-bolt card-icon"></i>
                <h2 class="card-title">Quick Actions</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); border: 1px solid var(--primary); text-decoration: none; color: var(--primary-dark); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Edit Quiz</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Update quiz information</div>
                    </div>
                </a>
                
                <a href="{{ route('admin.quizzes.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--gray-100); border-radius: var(--radius-sm); border: 1px solid var(--gray-300); text-decoration: none; color: var(--gray-700); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--gray-300); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: var(--gray-700);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">All Quizzes</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">View all system quizzes</div>
                    </div>
                </a>
                
                <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) . '#questions' }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--success-light); border-radius: var(--radius-sm); border: 1px solid var(--success); text-decoration: none; color: var(--success-dark); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Add Questions</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Add questions to this quiz</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle publish button click
        const publishButton = document.getElementById('publishButton');
        if (publishButton) {
            publishButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to publish this quiz?\n\nOnce published, the quiz will be visible to students.')) {
                    const originalHTML = publishButton.innerHTML;
                    publishButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Publishing...';
                    publishButton.disabled = true;
                    document.getElementById('publishForm').submit();
                }
            });
        }
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('WARNING: Are you sure you want to delete this quiz?\n\nThis action cannot be undone. All quiz data will be permanently removed.')) {
                    const originalHTML = deleteButton.innerHTML;
                    deleteButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Deleting...';
                    deleteButton.disabled = true;
                    document.getElementById('deleteForm').submit();
                }
            });
        }
        
        // Show success message from session
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
    });
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            z-index: 9999;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            font-size: 0.875rem;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
</script>
@endpush