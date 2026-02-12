@extends('layouts.admin')

@section('title', 'Create New Quiz')

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

    .view-all-link {
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
        position: relative;
        z-index: 1;
    }

    .view-all-link:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        gap: 0.75rem;
    }

    .card-body {
        padding: 1.5rem 1.75rem;
    }

    .card-footer-modern {
        padding: 1.25rem 1.75rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
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
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .quiz-preview-meta {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .quiz-preview-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    /* Modern Form Sections */
    .form-section {
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

    .form-section:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px 3px 0 0;
    }
    
    .form-section-title {
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
    
    .form-section-title i {
        color: #667eea;
        font-size: 1.125rem;
        width: 20px;
        text-align: center;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 1.25rem;
        position: relative;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.375rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8125rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .form-label.required::after {
        content: " *";
        color: #f56565;
    }
    
    .form-input, .form-textarea {
        display: block;
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #1a202c;
        background-color: white;
        background-clip: padding-box;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    
    .form-input:focus, .form-textarea:focus {
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .form-textarea {
        min-height: 100px;
        resize: vertical;
        font-family: inherit;
    }

    .form-help {
        font-size: 0.6875rem;
        color: #718096;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Question Cards */
    .question-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        margin-bottom: 1.25rem;
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
    
    .question-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .question-content {
        padding: 1.25rem 1.5rem;
    }

    /* Option Items */
    .option-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .option-item:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }
    
    .option-radio {
        width: 18px;
        height: 18px;
        accent-color: #667eea;
        margin: 0;
        cursor: pointer;
    }
    
    .option-input {
        flex: 1;
        padding: 0.5rem 0.75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.8125rem;
        transition: all 0.2s ease;
    }
    
    .option-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 600;
        font-size: 0.8125rem;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1.5px solid transparent;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.3px;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.7s ease;
    }

    .btn:hover::before {
        left: 100%;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4);
    }
    
    .btn-secondary {
        background: white;
        border-color: #cbd5e0;
        color: #4a5568;
    }
    
    .btn-secondary:hover {
        background: #f7fafc;
        border-color: #a0aec0;
        color: #2d3748;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
        border-color: transparent;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(245, 101, 101, 0.4);
    }
    
    .btn-add {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        border: 1.5px dashed #667eea;
        width: 100%;
        padding: 0.75rem;
        margin-top: 0.5rem;
    }
    
    .btn-add:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-style: solid;
        transform: translateY(-2px);
    }
    
    .btn-add-option {
        background: rgba(72, 187, 120, 0.1);
        color: #38a169;
        border: 1.5px dashed #48bb78;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .btn-add-option:hover {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        border-style: solid;
    }

    .remove-option-btn {
        background: rgba(245, 101, 101, 0.1);
        color: #e53e3e;
        border: none;
        padding: 0.375rem 0.75rem;
    }
    
    .remove-option-btn:hover {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
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

    /* Tips Grid */
    .tips-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .tip-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #edf2f7;
        transition: all 0.2s ease;
    }

    .tip-item:hover {
        background: white;
        border-color: #e2e8f0;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    }

    .tip-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .tip-content {
        flex: 1;
        min-width: 0;
    }

    .tip-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8125rem;
        margin-bottom: 0.125rem;
    }

    .tip-description {
        font-size: 0.6875rem;
        color: #718096;
        line-height: 1.4;
    }

    /* Guidelines List */
    .guidelines-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .guideline-item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.75rem;
        color: #4a5568;
    }

    .guideline-item i {
        color: #48bb78;
        font-size: 0.875rem;
        margin-top: 0.125rem;
    }

    /* Error Alert */
    .error-alert {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        color: #c53030;
        border: 1px solid #fc8181;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        font-size: 0.8125rem;
        box-shadow: 0 2px 8px rgba(245, 101, 101, 0.1);
        animation: slideIn 0.3s ease;
    }
    
    .error-alert-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .error-list {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }
    
    .error-list li {
        margin-bottom: 0.25rem;
        font-size: 0.75rem;
    }

    /* Loading Spinner */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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

        .view-all-link {
            align-self: stretch;
            justify-content: center;
        }

        .card-body {
            padding: 1.25rem;
        }

        .card-footer-modern {
            padding: 1rem 1.25rem;
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .two-column-layout {
            grid-template-columns: 1fr;
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

        .option-item {
            flex-wrap: wrap;
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

        .form-section-title {
            font-size: 0.9375rem;
        }

        .quiz-preview-avatar {
            width: 60px;
            height: 60px;
            font-size: 1.75rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Create Quiz Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-plus-circle card-icon"></i>
                <h2 class="card-title">Create New Quiz</h2>
            </div>
            <a href="{{ route('admin.quizzes.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Quizzes
            </a>
        </div>
        
        <div class="card-body">
            <!-- Quiz Preview - Live Preview -->
            <div class="quiz-preview">
                <div class="quiz-preview-avatar" id="previewAvatar">
                    📝
                </div>
                <div class="quiz-preview-title" id="previewTitle">
                    New Quiz
                </div>
                <div class="quiz-preview-meta">
                    <span class="quiz-preview-badge">
                        <i class="fas fa-check-circle"></i> 
                        Draft
                    </span>
                </div>
            </div>

            <!-- Error Display -->
            @if($errors->any())
            <div class="error-alert">
                <div class="error-alert-header">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Please fix the following errors:</span>
                </div>
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Two Column Layout -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <form action="{{ route('admin.quizzes.store') }}" method="POST" id="quiz-form">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </div>
                            
                            <!-- Quiz Title -->
                            <div class="form-group">
                                <label for="title" class="form-label required">
                                    <i class="fas fa-heading"></i> Quiz Title
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}" 
                                       required
                                       placeholder="e.g., JavaScript Fundamentals Quiz"
                                       class="form-input">
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Enter a descriptive title for your quiz
                                </span>
                            </div>
                            
                            <!-- Quiz Description -->
                            <div class="form-group">
                                <label for="description" class="form-label required">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="3"
                                          required
                                          placeholder="Describe what this quiz covers..."
                                          class="form-textarea">{{ old('description') }}</textarea>
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Provide a clear description of the quiz content
                                </span>
                            </div>
                            
                            <!-- Quiz Settings -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label for="passing_score" class="form-label required">
                                        <i class="fas fa-trophy"></i> Passing Score (%)
                                    </label>
                                    <input type="number" 
                                           id="passing_score" 
                                           name="passing_score" 
                                           value="{{ old('passing_score', 70) }}" 
                                           min="0"
                                           max="100"
                                           required
                                           class="form-input">
                                </div>
                                
                                <div class="form-group">
                                    <label for="duration" class="form-label required">
                                        <i class="fas fa-clock"></i> Duration (minutes)
                                    </label>
                                    <input type="number" 
                                           id="duration" 
                                           name="duration" 
                                           value="{{ old('duration', 30) }}" 
                                           min="1"
                                           max="180"
                                           required
                                           class="form-input">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Questions Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-question-circle"></i> Questions & Options
                            </div>
                            
                            <div id="questions-list">
                                <!-- Questions will be added here dynamically -->
                            </div>
                            
                            <button type="button" 
                                    id="add-question-btn"
                                    class="btn btn-add">
                                <i class="fas fa-plus-circle"></i> Add Question
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Right Column - Sidebar -->
                <div class="sidebar-column">
                    <!-- Quick Tips Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-lightbulb"></i> Quick Tips
                        </div>
                        
                        <div class="tips-grid">
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-question"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Clear Questions</div>
                                    <div class="tip-description">Write clear, concise questions</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">One Correct Answer</div>
                                    <div class="tip-description">Select one correct answer per question</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-list-ol"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Max 4 Options</div>
                                    <div class="tip-description">Maximum 4 options per question</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Set Passing Score</div>
                                    <div class="tip-description">Define minimum score to pass</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Guidelines Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i> Guidelines
                        </div>
                        
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Title should be clear and descriptive</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Description helps students understand the quiz</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Each question must have 2-4 options</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Select one correct answer per question</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Quiz must have at least 1 question</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>All questions require text and options</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer-modern">
            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" form="quiz-form" class="btn btn-primary" id="submitButton">
                <i class="fas fa-save"></i> Create Quiz
            </button>
        </div>
    </div>

    <!-- Templates -->
    <template id="question-template">
        <div class="question-card">
            <div class="question-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="question-number">#1</span>
                    <span style="font-weight: 600; color: #2d3748;">Question</span>
                </div>
                <button type="button" class="btn btn-danger remove-question-btn">
                    <i class="fas fa-trash-alt"></i> Remove
                </button>
            </div>
            
            <div class="question-content">
                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-question-circle"></i> Question Text
                    </label>
                    <textarea name="questions[0][question]" 
                              class="question-text form-textarea"
                              rows="3"
                              required
                              placeholder="Enter the question..."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-list"></i> Options (Select one correct answer)
                    </label>
                    <div class="options-list">
                        <!-- Options will be added here dynamically -->
                    </div>
                    
                    <button type="button" 
                            class="btn btn-add-option add-option-btn"
                            style="margin-top: 0.75rem;">
                        <i class="fas fa-plus"></i> Add Option
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template id="option-template">
        <div class="option-item">
            <input type="radio" 
                   class="option-radio is-correct-checkbox"
                   name="questions[0][correct_answer]"
                   value="0">
            <input type="text" 
                   class="option-input option-text"
                   name="questions[0][options][0][option_text]"
                   placeholder="Enter option text"
                   required>
            <button type="button" 
                    class="btn remove-option-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const previewTitle = document.getElementById('previewTitle');
        const previewAvatar = document.getElementById('previewAvatar');
        
        // Live preview update
        function updatePreview() {
            const title = titleInput.value.trim();
            previewTitle.textContent = title || 'New Quiz';
            
            if (title) {
                previewAvatar.textContent = title.charAt(0).toUpperCase();
            } else {
                previewAvatar.textContent = '📝';
            }
        }
        
        if (titleInput) {
            titleInput.addEventListener('input', updatePreview);
        }

        const questionsContainer = document.getElementById('questions-list');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const questionTemplate = document.getElementById('question-template');
        const optionTemplate = document.getElementById('option-template');
        const submitButton = document.getElementById('submitButton');
        
        let questionCount = 0;
        const MAX_OPTIONS_PER_QUESTION = 4;
        
        // Add first question by default
        addQuestion();
        
        // Add question button click event
        addQuestionBtn.addEventListener('click', addQuestion);
        
        function addQuestion() {
            const questionClone = questionTemplate.content.cloneNode(true);
            const questionCard = questionClone.querySelector('.question-card');
            
            // Update question number display
            questionCard.querySelector('.question-number').textContent = `#${questionCount + 1}`;
            
            // Update all input names with current question count
            updateQuestionNames(questionCard, questionCount);
            
            // Add remove question event
            const removeBtn = questionCard.querySelector('.remove-question-btn');
            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.question-card').length > 1) {
                    questionCard.remove();
                    updateQuestionNumbers();
                } else {
                    Swal.fire({
                        title: 'Cannot Remove',
                        text: 'Quiz must have at least one question.',
                        icon: 'warning',
                        confirmButtonColor: '#667eea'
                    });
                }
            });
            
            // Add option button event
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            addOptionBtn.addEventListener('click', function() {
                addOption(questionCard, questionCount);
            });
            
            // Add 2 default options
            for (let i = 0; i < 2; i++) {
                addOption(questionCard, questionCount);
            }
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            questionCount++;
            
            // Update add option button visibility
            updateAddOptionButton(questionCard);
        }
        
        function updateQuestionNames(questionCard, questionIndex) {
            // Update question textarea
            const questionText = questionCard.querySelector('.question-text');
            if (questionText) {
                questionText.name = `questions[${questionIndex}][question]`;
                questionText.value = '';
            }
            
            // Update options in this question
            updateOptionNames(questionCard, questionIndex);
        }
        
        function addOption(questionCard, questionIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const currentOptionCount = optionsList.children.length;
            
            // Check if we can add more options
            if (currentOptionCount >= MAX_OPTIONS_PER_QUESTION) {
                Swal.fire({
                    title: 'Maximum Options Reached',
                    text: `Maximum ${MAX_OPTIONS_PER_QUESTION} options allowed per question.`,
                    icon: 'warning',
                    confirmButtonColor: '#667eea'
                });
                return;
            }
            
            const optionClone = optionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            
            // Create radio input
            const radioInput = optionItem.querySelector('.is-correct-checkbox');
            radioInput.name = `questions[${questionIndex}][correct_answer]`;
            radioInput.value = currentOptionCount;
            
            // Create option text input
            const optionTextInput = optionItem.querySelector('.option-text');
            optionTextInput.name = `questions[${questionIndex}][options][${currentOptionCount}][option_text]`;
            optionTextInput.value = '';
            
            // Add remove option event
            const removeBtn = optionItem.querySelector('.remove-option-btn');
            removeBtn.addEventListener('click', function() {
                if (optionsList.children.length > 2) {
                    optionItem.remove();
                    updateOptionNames(questionCard, questionIndex);
                    updateAddOptionButton(questionCard);
                } else {
                    Swal.fire({
                        title: 'Cannot Remove',
                        text: 'Each question must have at least 2 options.',
                        icon: 'warning',
                        confirmButtonColor: '#667eea'
                    });
                }
            });
            
            // Set first option as checked by default
            if (currentOptionCount === 0) {
                radioInput.checked = true;
            }
            
            // Append to options list
            optionsList.appendChild(optionItem);
            
            // Update add option button visibility
            updateAddOptionButton(questionCard);
        }
        
        function updateOptionNames(questionCard, questionIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const options = optionsList.querySelectorAll('.option-item');
            
            options.forEach((optionItem, index) => {
                const radio = optionItem.querySelector('.is-correct-checkbox');
                if (radio) {
                    radio.name = `questions[${questionIndex}][correct_answer]`;
                    radio.value = index;
                }
                
                const optionText = optionItem.querySelector('.option-text');
                if (optionText) {
                    optionText.name = `questions[${questionIndex}][options][${index}][option_text]`;
                }
            });
        }
        
        function updateQuestionNumbers() {
            const questionCards = document.querySelectorAll('.question-card');
            questionCount = questionCards.length;
            
            questionCards.forEach((card, qIndex) => {
                // Update display number
                const questionNumberSpan = card.querySelector('.question-number');
                if (questionNumberSpan) {
                    questionNumberSpan.textContent = `#${qIndex + 1}`;
                }
                
                // Update question text name
                const questionText = card.querySelector('.question-text');
                if (questionText) {
                    questionText.name = `questions[${qIndex}][question]`;
                }
                
                // Update options
                updateOptionNames(card, qIndex);
                
                // Update add option button visibility
                updateAddOptionButton(card);
            });
        }
        
        function updateAddOptionButton(questionCard) {
            const optionsList = questionCard.querySelector('.options-list');
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            const currentOptionCount = optionsList.children.length;
            
            // Show/hide add option button based on option count
            if (addOptionBtn) {
                if (currentOptionCount >= MAX_OPTIONS_PER_QUESTION) {
                    addOptionBtn.style.display = 'none';
                } else {
                    addOptionBtn.style.display = 'inline-flex';
                }
            }
        }
        
        // Form validation with SweetAlert2
        const form = document.getElementById('quiz-form');
        
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const title = document.getElementById('title').value.trim();
                const description = document.getElementById('description').value.trim();
                const passingScore = document.getElementById('passing_score').value;
                const duration = document.getElementById('duration').value;
                const questionCards = document.querySelectorAll('.question-card');
                
                let isValid = true;
                const errorMessages = [];
                
                // Validate basic info
                if (!title) {
                    isValid = false;
                    errorMessages.push('Quiz title is required.');
                }
                
                if (!description) {
                    isValid = false;
                    errorMessages.push('Quiz description is required.');
                }
                
                if (!passingScore || passingScore < 0 || passingScore > 100) {
                    isValid = false;
                    errorMessages.push('Passing score must be between 0 and 100.');
                }
                
                if (!duration || duration < 1 || duration > 180) {
                    isValid = false;
                    errorMessages.push('Duration must be between 1 and 180 minutes.');
                }
                
                // Validate questions
                if (questionCards.length === 0) {
                    isValid = false;
                    errorMessages.push('Please add at least one question.');
                }
                
                questionCards.forEach((card, index) => {
                    const questionText = card.querySelector('.question-text');
                    if (!questionText || !questionText.value.trim()) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} text is required.`);
                    }
                    
                    const options = card.querySelectorAll('.option-item');
                    if (options.length < 2) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} must have at least 2 options.`);
                    }
                    
                    // Check if all options have text
                    options.forEach((option, optIndex) => {
                        const optionText = option.querySelector('.option-text');
                        if (!optionText || !optionText.value.trim()) {
                            isValid = false;
                            errorMessages.push(`Question ${index + 1}, Option ${optIndex + 1} text is required.`);
                        }
                    });
                    
                    // Check one correct answer is selected
                    const checkedRadio = card.querySelector('input[type="radio"]:checked');
                    if (!checkedRadio) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} must have one correct answer selected.`);
                    }
                });
                
                if (!isValid) {
                    Swal.fire({
                        title: 'Validation Error',
                        html: errorMessages.join('<br>'),
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }
                
                // Show confirmation
                Swal.fire({
                    title: 'Create Quiz?',
                    text: `You are about to create a quiz with ${questionCards.length} question(s).`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Create',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                        submitButton.disabled = true;
                        form.submit();
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