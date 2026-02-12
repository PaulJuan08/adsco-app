@extends('layouts.admin')

@section('title', 'Quiz Details - ' . $quiz->title)

@push('styles')
<style>
    /* Modern Form Container - Matching Other Pages */
    .form-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin: 1.5rem auto;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        max-width: 1200px;
        width: 95%;
    }

    .form-container:hover {
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Action Buttons Container - At the Top */
    .top-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .top-action-btn {
        color: white;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .top-action-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        gap: 0.75rem;
    }

    .top-action-btn.delete-btn {
        background: rgba(245, 101, 101, 0.3);
    }

    .top-action-btn.delete-btn:hover {
        background: rgba(245, 101, 101, 0.5);
    }

    .top-action-btn i {
        font-size: 0.875rem;
    }

    .card-body {
        padding: 1.5rem 1.75rem;
    }

    .card-footer-modern {
        padding: 1.25rem 1.75rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    /* Two Column Layout */
    .two-column-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .two-column-layout {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* Form Column */
    .form-column {
        min-width: 0;
    }

    /* Sidebar Column */
    .sidebar-column {
        min-width: 0;
    }

    /* Quiz Preview */
    .quiz-preview {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .quiz-preview-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 auto 1rem;
        border: 4px solid white;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .quiz-preview-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    
    .quiz-preview-meta {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    .quiz-preview-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.8125rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .quiz-preview-badge.published {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }
    
    .quiz-preview-badge.draft {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
    }

    .quiz-preview-id {
        font-size: 0.875rem;
        color: #718096;
        background: rgba(255, 255, 255, 0.8);
        padding: 0.375rem 1rem;
        border-radius: 20px;
        display: inline-block;
    }

    /* Modern Form Sections */
    .detail-section {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .detail-section:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }

    .detail-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px 3px 0 0;
    }
    
    .detail-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
    }
    
    .detail-section-title i {
        color: #667eea;
        font-size: 1.125rem;
        width: 20px;
        text-align: center;
    }

    /* Info Rows */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .info-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
    }

    .info-subvalue {
        font-size: 0.625rem;
        color: #a0aec0;
        margin-top: 0.125rem;
    }

    .detail-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        font-size: 1rem;
        color: #1a202c;
        font-weight: 600;
        line-height: 1.5;
        margin-bottom: 1rem;
    }
    
    .detail-subvalue {
        font-size: 0.75rem;
        color: #718096;
        margin-top: -0.75rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    /* Description Box */
    .description-box {
        padding: 1rem 1.25rem;
        background: white;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.9375rem;
        line-height: 1.6;
        color: #2d3748;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        margin: 0 auto 0.75rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.125rem;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Question Cards */
    .question-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    
    .question-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
        border-color: #cbd5e0;
    }
    
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #edf2f7;
        background: #f8fafc;
    }
    
    .question-content {
        padding: 1.25rem 1.5rem;
    }
    
    .question-title {
        font-weight: 700;
        color: #2d3748;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .question-text {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 10px;
        border-left: 4px solid #667eea;
        margin-bottom: 1rem;
        font-size: 0.9375rem;
        color: #2d3748;
        line-height: 1.6;
    }
    
    /* Option Styles */
    .option-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left-width: 4px;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .option-item.correct {
        background: rgba(72, 187, 120, 0.1);
        border-left-color: #48bb78;
    }
    
    .option-item.incorrect {
        background: rgba(245, 101, 101, 0.1);
        border-left-color: #f56565;
    }
    
    .option-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }
    
    .option-item.correct .option-icon {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }
    
    .option-item.incorrect .option-icon {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }
    
    .option-text {
        flex: 1;
        font-size: 0.875rem;
        color: #2d3748;
    }
    
    .option-badge {
        font-size: 0.6875rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .option-item.correct .option-badge {
        background: rgba(72, 187, 120, 0.2);
        color: #276749;
    }
    
    .option-item.incorrect .option-badge {
        background: rgba(245, 101, 101, 0.2);
        color: #c53030;
    }
    
    .explanation-box {
        padding: 1rem;
        background: rgba(102, 126, 234, 0.1);
        border-radius: 10px;
        border-left: 4px solid #667eea;
        margin-top: 1rem;
    }
    
    .explanation-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: #a0aec0;
    }
    
    .empty-state i {
        font-size: 2.5rem;
        color: #cbd5e0;
        margin-bottom: 0.75rem;
    }
    
    .empty-state h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #718096;
        margin-bottom: 0.25rem;
    }
    
    .empty-state p {
        font-size: 0.8125rem;
        color: #a0aec0;
    }

    /* Sidebar Card */
    .sidebar-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .sidebar-card:last-child {
        margin-bottom: 0;
    }

    .sidebar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px 3px 0 0;
    }

    .sidebar-card-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
    }

    .sidebar-card-title i {
        color: #667eea;
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    /* Quick Actions */
    .quick-actions-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border-radius: 12px;
        text-decoration: none;
        color: #2d3748;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        width: 100%;
        text-align: left;
        border: none;
        background: #f8fafc;
    }

    .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .action-card:hover::before {
        transform: scaleX(1);
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e0;
        background: white;
    }

    .action-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        color: white;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .action-card:hover .action-icon {
        transform: rotate(8deg) scale(1.08);
    }

    .action-content {
        flex: 1;
        min-width: 0;
    }

    .action-title {
        font-weight: 700;
        color: #2d3748;
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }

    .action-subtitle {
        font-size: 0.6875rem;
        color: #718096;
        line-height: 1.3;
    }

    .action-arrow {
        color: #a0aec0;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .action-card:hover .action-arrow {
        color: #667eea;
        transform: translateX(4px);
    }

    /* Alert Messages */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 10px;
        font-size: 0.8125rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        border-left-width: 4px;
        border-left-style: solid;
        animation: slideIn 0.3s ease;
    }
    
    .alert i {
        font-size: 1rem;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        color: #276749;
        border-left-color: #48bb78;
    }
    
    .alert-error {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        color: #c53030;
        border-left-color: #f56565;
    }

    /* Publish Button - Centered */
    .publish-section {
        margin-top: 1.5rem;
        text-align: center;
    }

    .publish-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }

    .publish-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(72, 187, 120, 0.4);
    }

    /* Loading Spinner */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-container {
            width: 98%;
            margin: 1rem auto;
        }
        
        .card-header {
            padding: 1rem 1.25rem;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .top-actions {
            align-self: stretch;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .top-action-btn {
            flex: 1;
            justify-content: center;
        }

        .card-body {
            padding: 1.25rem;
        }

        .two-column-layout {
            grid-template-columns: 1fr;
        }

        .detail-section {
            padding: 1rem 1.25rem;
        }

        .question-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .quiz-preview-avatar {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }

        .quiz-preview-title {
            font-size: 1.25rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .form-container {
            width: 100%;
            margin: 0.5rem auto;
            border-radius: 16px;
        }
        
        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.125rem;
        }

        .detail-section-title {
            font-size: 0.9375rem;
        }

        .quiz-preview-avatar {
            width: 60px;
            height: 60px;
            font-size: 1.75rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .option-item {
            flex-wrap: wrap;
        }

        .option-badge {
            margin-left: auto;
        }
    }
</style>
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