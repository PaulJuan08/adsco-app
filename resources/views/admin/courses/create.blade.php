@extends('layouts.admin')

@section('title', 'Create New Course - Admin Dashboard')

@push('styles')
<style>
    /* Modern Form Container - Matching Edit Course */
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
    }

    /* Two Column Layout for Form and Sidebar */
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

    /* Course Preview - For Create Page */
    .course-preview {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .course-preview-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 0.75rem;
        border: 4px solid white;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .course-preview-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.125rem;
    }
    
    .course-preview-code {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.5rem;
    }
    
    .course-preview-status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.6875rem;
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
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
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
    
    .form-label .required {
        color: #f56565;
        margin-left: 0.25rem;
    }
    
    .form-input,
    .form-textarea,
    .form-select {
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
    
    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .form-input.error,
    .form-textarea.error,
    .form-select.error {
        border-color: #f56565;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23f56565' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23f56565' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.875rem center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        padding-right: calc(1.5em + 0.875rem);
    }
    
    .form-error {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #f56565;
        font-weight: 500;
    }
    
    .form-hint {
        font-size: 0.6875rem;
        color: #718096;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .input-with-unit {
        position: relative;
    }
    
    .input-with-unit .form-input {
        padding-right: 4rem;
    }
    
    .input-unit {
        position: absolute;
        right: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #718096;
        font-size: 0.8125rem;
        font-weight: 500;
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

    /* Guidelines List */
    .guidelines-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .guideline-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #edf2f7;
        transition: all 0.2s ease;
    }

    .guideline-item:hover {
        background: white;
        border-color: #e2e8f0;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    }
    
    .guideline-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    
    .guideline-content {
        flex: 1;
        min-width: 0;
    }
    
    .guideline-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8125rem;
        margin-bottom: 0.125rem;
    }
    
    .guideline-text {
        font-size: 0.6875rem;
        color: #718096;
        line-height: 1.4;
    }

    /* Quick Actions - Redesigned */
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

    /* Alerts */
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
    
    .alert-error ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }
    
    .alert-error li {
        margin-bottom: 0.25rem;
        font-size: 0.75rem;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    
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

    /* Status Notice */
    .status-notice {
        background: linear-gradient(135deg, #f0f9ff 0%, #e6f7ff 100%);
        border: 1px solid #bae6fd;
        color: #075985;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .status-notice i {
        color: #0284c7;
        font-size: 1rem;
        margin-top: 0.125rem;
    }

    .status-notice-content {
        flex: 1;
    }

    .status-notice-title {
        font-weight: 700;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }

    .status-notice-text {
        font-size: 0.8125rem;
        opacity: 0.9;
        line-height: 1.5;
    }

    /* Toast Notifications */
    .custom-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        background: white;
        color: #2d3748;
        z-index: 10000;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        max-width: 400px;
        font-size: 0.875rem;
        font-weight: 500;
        border-left: 4px solid;
    }

    .custom-toast.success {
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        color: #276749;
        border-left-color: #48bb78;
    }

    .custom-toast.error {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        color: #c53030;
        border-left-color: #f56565;
    }

    .custom-toast.info {
        background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
        color: #2c5282;
        border-left-color: #4299e1;
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

    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
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

        .two-column-layout {
            grid-template-columns: 1fr;
        }

        .form-section {
            padding: 1rem 1.25rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .custom-toast {
            max-width: 90%;
            left: 5%;
            right: 5%;
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

        .course-preview-avatar {
            width: 70px;
            height: 70px;
            font-size: 1.75rem;
        }

        .action-card {
            padding: 0.875rem;
        }

        .action-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Create Course Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-book-medical card-icon"></i>
                <h2 class="card-title">Create New Course</h2>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>
        
        <div class="card-body">
            <!-- Course Preview - Live Preview -->
            <div class="course-preview">
                <div class="course-preview-avatar" id="previewAvatar">
                    <span id="avatarLetter">📚</span>
                </div>
                <div class="course-preview-title" id="previewTitle">New Course</div>
                <div class="course-preview-code" id="previewCode">---</div>
                <div class="course-preview-status">
                    <i class="fas fa-check-circle"></i> Published
                </div>
            </div>

            <!-- Display validation errors -->
            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            <!-- Two Column Layout - Form and Sidebar Inline -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <form action="{{ route('admin.courses.store') }}" method="POST" id="courseForm">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Course Information
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading"></i> Course Title
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           required
                                           placeholder="e.g., Introduction to Programming"
                                           class="form-input @error('title') error @enderror">
                                    @error('title')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="course_code" class="form-label">
                                        <i class="fas fa-code"></i> Course Code
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="course_code" 
                                           name="course_code" 
                                           value="{{ old('course_code') }}" 
                                           required
                                           placeholder="e.g., CS101"
                                           class="form-input @error('course_code') error @enderror">
                                    <div class="form-hint">
                                        <i class="fas fa-lightbulb"></i> Will auto-generate based on title
                                    </div>
                                    @error('course_code')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Course Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Enter a detailed description of the course..."
                                          class="form-textarea @error('description') error @enderror">{{ old('description') }}</textarea>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Describe what students will learn
                                </div>
                                @error('description')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Course Details Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-cog"></i> Course Details
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="teacher_id" class="form-label">
                                        <i class="fas fa-chalkboard-teacher"></i> Assign Teacher
                                    </label>
                                    <select id="teacher_id" 
                                            name="teacher_id"
                                            class="form-select @error('teacher_id') error @enderror">
                                        <option value="">-- Select Teacher (Optional) --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->f_name }} {{ $teacher->l_name }} 
                                                @if($teacher->employee_id)
                                                    ({{ $teacher->employee_id }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint">
                                        <i class="fas fa-user-tie"></i> Can be assigned later
                                    </div>
                                    @error('teacher_id')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="credits" class="form-label">
                                        <i class="fas fa-cubes"></i> Credits
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-with-unit">
                                        <input type="number" 
                                               id="credits" 
                                               name="credits" 
                                               value="{{ old('credits', 3) }}" 
                                               min="0.5" 
                                               max="10"
                                               step="0.5"
                                               required
                                               class="form-input @error('credits') error @enderror">
                                        <span class="input-unit">credits</span>
                                    </div>
                                    @error('credits')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Notice -->
                        <div class="status-notice">
                            <i class="fas fa-info-circle"></i>
                            <div class="status-notice-content">
                                <div class="status-notice-title">Course Status</div>
                                <div class="status-notice-text">
                                    New courses are automatically created as <strong>Published</strong> and <strong>Active</strong>. 
                                    They will be visible to enrolled students immediately.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="status" value="active">
                        <input type="hidden" name="is_published" value="1">
                    </form>
                </div>
                
                <!-- Right Column - Guidelines Sidebar -->
                <div class="sidebar-column">
                    <!-- Guidelines Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i> Guidelines
                        </div>
                        
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-asterisk"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Required Fields</div>
                                    <div class="guideline-text">Fields marked with * are mandatory</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-code"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Course Code</div>
                                    <div class="guideline-text">Use standard format like CS101, MATH201</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Teacher Assignment</div>
                                    <div class="guideline-text">Can be assigned now or later</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-cubes"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Credits</div>
                                    <div class="guideline-text">Enter between 0.5 and 10 credits</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <div class="quick-actions-grid">
                            <a href="{{ route('admin.courses.index') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View All Courses</div>
                                    <div class="action-subtitle">Browse existing courses</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                            
                            <button type="button" onclick="resetForm()" class="action-card" style="width: 100%; border: none; background: #f8fafc; text-align: left; cursor: pointer;">
                                <div class="action-icon">
                                    <i class="fas fa-eraser"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Clear Form</div>
                                    <div class="action-subtitle">Reset all fields</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </button>
                            
                            <a href="{{ route('admin.courses.create') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Refresh Data</div>
                                    <div class="action-subtitle">Reload teacher list</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer-modern">
            <div class="form-actions">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" form="courseForm" class="btn btn-primary" id="submitButton">
                    <i class="fas fa-save"></i> Create Course
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const codeInput = document.getElementById('course_code');
        const previewTitle = document.getElementById('previewTitle');
        const previewCode = document.getElementById('previewCode');
        const avatarLetter = document.getElementById('avatarLetter');
        const submitButton = document.getElementById('submitButton');
        
        // Live preview update
        function updatePreview() {
            // Update title
            const title = titleInput.value.trim();
            previewTitle.textContent = title || 'New Course';
            
            // Update code
            const code = codeInput.value.trim();
            previewCode.textContent = code || '---';
            
            // Update avatar
            if (code) {
                avatarLetter.textContent = code.charAt(0).toUpperCase();
            } else if (title) {
                avatarLetter.textContent = title.charAt(0).toUpperCase();
            } else {
                avatarLetter.textContent = '📚';
            }
        }
        
        titleInput.addEventListener('input', updatePreview);
        codeInput.addEventListener('input', updatePreview);
        
        // Auto-generate course code suggestion
        titleInput.addEventListener('input', function() {
            const title = this.value.trim();
            
            if (title && !codeInput.value) {
                const words = title.toUpperCase().split(' ');
                let code = '';
                
                if (words.length >= 2) {
                    if (words[0].length >= 3) {
                        code = words[0].substring(0, 3);
                    } else {
                        code = words[0].substring(0, 2) + words[1].charAt(0);
                    }
                    
                    if (code) {
                        const randomNum = Math.floor(Math.random() * 900) + 100;
                        const suggestedCode = code + randomNum;
                        codeInput.value = suggestedCode;
                        updatePreview();
                        
                        // Show hint
                        const hintDiv = codeInput.nextElementSibling;
                        if (hintDiv && hintDiv.classList.contains('form-hint')) {
                            hintDiv.innerHTML = `<i class="fas fa-check-circle"></i> Suggested: ${suggestedCode}`;
                        }
                    }
                }
            }
        });
        
        // Form validation and submission
        const courseForm = document.getElementById('courseForm');
        if (courseForm) {
            courseForm.addEventListener('submit', function(e) {
                const title = titleInput.value.trim();
                const code = codeInput.value.trim();
                const credits = document.getElementById('credits').value;
                
                let isValid = true;
                
                if (!title) {
                    titleInput.classList.add('error');
                    isValid = false;
                } else {
                    titleInput.classList.remove('error');
                }
                
                if (!code) {
                    codeInput.classList.add('error');
                    isValid = false;
                } else {
                    codeInput.classList.remove('error');
                }
                
                if (!credits || parseFloat(credits) < 0.5 || parseFloat(credits) > 10) {
                    document.getElementById('credits').classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById('credits').classList.remove('error');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    showToast('Please fill in all required fields correctly.', 'error');
                    return;
                }
                
                // Show loading state
                if (submitButton) {
                    const originalHTML = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                    submitButton.disabled = true;
                    
                    // Revert after 5 seconds (in case submission fails)
                    setTimeout(() => {
                        submitButton.innerHTML = originalHTML;
                        submitButton.disabled = false;
                    }, 5000);
                }
            });
        }
        
        // Toast notification function
        window.showToast = function(message, type = 'info') {
            // Remove existing toast if any
            const existingToast = document.querySelector('.custom-toast');
            if (existingToast) {
                existingToast.remove();
            }
            
            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}`;
            
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            
            toast.innerHTML = `
                <i class="fas fa-${icon}" style="font-size: 1.25rem;"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
        
        // Show validation errors if any
        @if($errors->any())
            showToast('Please fix the errors in the form.', 'error');
        @endif
        
        // Show success message if redirected with success
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
    });
    
    // Reset form function
    function resetForm() {
        document.getElementById('courseForm').reset();
        document.getElementById('previewTitle').textContent = 'New Course';
        document.getElementById('previewCode').textContent = '---';
        document.getElementById('avatarLetter').textContent = '📚';
        
        // Clear error states
        document.querySelectorAll('.form-input, .form-textarea, .form-select').forEach(el => {
            el.classList.remove('error');
        });
        
        showToast('Form has been cleared.', 'info');
    }
</script>
@endpush