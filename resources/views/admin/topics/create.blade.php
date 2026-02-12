@extends('layouts.admin')

@section('title', isset($topic) ? 'Edit Topic - ' . $topic->title : 'Create New Topic')

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

    /* Topic Preview */
    .topic-preview {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .topic-preview-avatar {
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
    
    .topic-preview-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .topic-preview-meta {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .topic-preview-badge {
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
    
    .form-input.error, .form-textarea.error {
        border-color: #f56565;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23f56565' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23f56565' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.875rem center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        padding-right: calc(1.5em + 0.875rem);
    }
    
    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .form-error {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #f56565;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .form-help {
        font-size: 0.6875rem;
        color: #718096;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* File Input */
    .form-file {
        display: block;
        width: 100%;
        padding: 0.625rem;
        font-size: 0.875rem;
        color: #1a202c;
        background-color: white;
        border: 1.5px dashed #e2e8f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .form-file:hover {
        border-color: #667eea;
        background-color: rgba(102, 126, 234, 0.05);
    }
    
    .form-file:focus {
        outline: none;
        border-color: #667eea;
        border-style: solid;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    /* Current File Display */
    .current-file {
        margin-top: 0.75rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    
    .current-file-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .current-file-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .current-file-info {
        flex: 1;
        min-width: 0;
    }
    
    .current-file-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8125rem;
        margin-bottom: 0.125rem;
        word-break: break-word;
    }
    
    .current-file-type {
        font-size: 0.6875rem;
        color: #718096;
    }
    
    .current-file-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .current-file-warning {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
        font-size: 0.6875rem;
        color: #ed8936;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.8125rem;
        font-weight: 600;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }

    /* Info Row */
    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .info-icon {
        color: #667eea;
        font-size: 0.875rem;
        margin-top: 0.125rem;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.6875rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.125rem;
    }

    .info-text {
        font-size: 0.8125rem;
        color: #2d3748;
        font-weight: 500;
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

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
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
    
    .alert-error ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }
    
    .alert-error li {
        margin-bottom: 0.25rem;
    }

    /* Auto-publish Notice */
    .auto-publish-notice {
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

    .auto-publish-notice i {
        color: #0284c7;
        font-size: 1rem;
        margin-top: 0.125rem;
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

        .current-file-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .current-file-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .topic-preview-avatar {
            width: 70px;
            height: 70px;
            font-size: 2rem;
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

        .topic-preview-avatar {
            width: 60px;
            height: 60px;
            font-size: 1.75rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Topic Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas {{ isset($topic) ? 'fa-edit' : 'fa-plus' }} card-icon"></i>
                <h2 class="card-title">{{ isset($topic) ? 'Edit Topic' : 'Create New Topic' }}</h2>
            </div>
            <a href="{{ route('admin.topics.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Topics
            </a>
        </div>
        
        <div class="card-body">
            <!-- Topic Preview - Live Preview -->
            <div class="topic-preview">
                <div class="topic-preview-avatar" id="previewAvatar">
                    {{ isset($topic) ? strtoupper(substr($topic->title, 0, 1)) : '📚' }}
                </div>
                <div class="topic-preview-title" id="previewTitle">
                    {{ isset($topic) ? $topic->title : 'New Topic' }}
                </div>
                <div class="topic-preview-meta">
                    <span class="topic-preview-badge">
                        <i class="fas fa-check-circle"></i> 
                        {{ isset($topic) && $topic->is_published ? 'Published' : 'Will be published' }}
                    </span>
                </div>
            </div>

            <!-- Error Display -->
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
            
            <!-- Auto-publish Notice for New Topics -->
            @if(!isset($topic))
            <div class="auto-publish-notice">
                <i class="fas fa-rocket"></i>
                <div>
                    <strong>Auto-published:</strong> Topics are automatically published and will be immediately visible to students.
                </div>
            </div>
            @endif

            <!-- Two Column Layout -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <form action="{{ isset($topic) ? route('admin.topics.update', Crypt::encrypt($topic->id)) : route('admin.topics.store') }}" method="POST" enctype="multipart/form-data" id="topicForm">
                        @csrf
                        @if(isset($topic))
                            @method('PUT')
                        @endif
                        
                        <!-- Hidden field to set topic as published -->
                        <input type="hidden" name="is_published" value="1">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </div>
                            
                            <!-- Title -->
                            <div class="form-group">
                                <label for="title" class="form-label required">
                                    <i class="fas fa-heading"></i> Topic Title
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $topic->title ?? '') }}"
                                       required
                                       placeholder="e.g., Introduction to Arrays"
                                       class="form-input @error('title') error @enderror">
                                @error('title')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Enter a descriptive title for your topic
                                </span>
                            </div>
                            
                            <!-- Description -->
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    placeholder="Describe what this topic covers..."
                                    class="form-textarea @error('description') error @enderror">{{ old('description', $topic->description ?? '') }}</textarea>
                                @error('description')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Optional: Add a brief description of the topic content
                                </span>
                            </div>
                        </div>
                        
                        <!-- Resources Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-paperclip"></i> Resources & Attachments
                            </div>
                            
                            <!-- PDF File Upload -->
                            <div class="form-group">
                                <label for="pdf_file" class="form-label">
                                    <i class="fas fa-file-pdf"></i> PDF Document
                                </label>
                                <input type="file" 
                                       id="pdf_file" 
                                       name="pdf_file" 
                                       accept=".pdf"
                                       class="form-file @error('pdf_file') error @enderror">
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Maximum file size: 10MB. PDF files only.
                                </span>
                                
                                <!-- Show current PDF if exists -->
                                @if(isset($topic) && $topic->pdf_file)
                                <div class="current-file">
                                    <div class="current-file-header">
                                        <div class="current-file-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="current-file-info">
                                            <div class="current-file-name">{{ basename($topic->pdf_file) }}</div>
                                            <div class="current-file-type">PDF Document</div>
                                        </div>
                                        <div class="current-file-actions">
                                            <a href="{{ asset($topic->pdf_file) }}" target="_blank" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ asset($topic->pdf_file) }}" download 
                                               class="btn btn-secondary btn-sm">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                    <div class="current-file-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Uploading a new PDF will replace the current one.</span>
                                    </div>
                                </div>
                                @endif
                                
                                @error('pdf_file')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <!-- Video Link -->
                            <div class="form-group">
                                <label for="video_link" class="form-label">
                                    <i class="fas fa-video"></i> Video Link
                                </label>
                                <input type="url" 
                                       id="video_link" 
                                       name="video_link" 
                                       value="{{ old('video_link', $topic->video_link ?? '') }}"
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       class="form-input @error('video_link') error @enderror">
                                @error('video_link')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Enter YouTube, Vimeo, or direct video URL
                                </span>
                            </div>
                            
                            <!-- Attachment Link -->
                            <div class="form-group">
                                <label for="attachment" class="form-label">
                                    <i class="fas fa-link"></i> Attachment Link
                                </label>
                                <input type="url" 
                                       id="attachment" 
                                       name="attachment" 
                                       value="{{ old('attachment', $topic->attachment ?? '') }}"
                                       placeholder="https://drive.google.com/file/..."
                                       class="form-input @error('attachment') error @enderror">
                                @error('attachment')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Enter Google Drive, Dropbox, or direct file URL
                                </span>
                            </div>
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
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Auto-published</div>
                                    <div class="tip-description">Topics publish immediately and are visible to students</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">PDF Files</div>
                                    <div class="tip-description">Max 10MB, PDF format only. Replaces existing file</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Video Links</div>
                                    <div class="tip-description">YouTube, Vimeo, or direct video URLs supported</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Attachment Links</div>
                                    <div class="tip-description">Google Drive, Dropbox, or direct file URLs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Guidelines Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i> Guidelines
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">Title should be clear and descriptive</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">Description helps students understand the topic</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">Resources are optional but enhance learning</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">All topics are published automatically</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Topic Summary Card (for Edit mode) -->
                    @if(isset($topic))
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-chart-simple"></i> Topic Summary
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0;">
                                <span style="font-size: 0.75rem; color: #718096;">Topic ID</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">#{{ $topic->id }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                                <span style="font-size: 0.75rem; color: #718096;">Last Updated</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">{{ $topic->updated_at->format('M d, Y') }}</span>
                            </div>
                            @php
                                $resources = 0;
                                if($topic->pdf_file) $resources++;
                                if($topic->video_link) $resources++;
                                if($topic->attachment) $resources++;
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                                <span style="font-size: 0.75rem; color: #718096;">Resources</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">{{ $resources }} file(s)</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                                <span style="font-size: 0.75rem; color: #718096;">Courses</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">{{ $topic->courses ? $topic->courses->count() : 0 }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                                <span style="font-size: 0.75rem; color: #718096;">Status</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">
                                    @if($topic->is_published)
                                        <span style="color: #48bb78;">Published</span>
                                    @else
                                        <span style="color: #ed8936;">Draft (Will be published on update)</span>
                                    @endif
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                                <span style="font-size: 0.75rem; color: #718096;">Visibility</span>
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">
                                    @if($topic->is_published)
                                        Public - Visible to all students
                                    @else
                                        Private - Only visible to admins and instructors
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-footer-modern">
            <a href="{{ route('admin.topics.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" form="topicForm" class="btn btn-primary" id="submitButton">
                <i class="fas {{ isset($topic) ? 'fa-save' : 'fa-plus-circle' }}"></i>
                {{ isset($topic) ? 'Update Topic' : 'Create Topic' }}
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const previewTitle = document.getElementById('previewTitle');
        const previewAvatar = document.getElementById('previewAvatar');
        const submitButton = document.getElementById('submitButton');
        
        // Live preview update
        function updatePreview() {
            const title = titleInput.value.trim();
            previewTitle.textContent = title || 'New Topic';
            
            if (title) {
                previewAvatar.textContent = title.charAt(0).toUpperCase();
            } else {
                previewAvatar.textContent = '📚';
            }
        }
        
        if (titleInput) {
            titleInput.addEventListener('input', updatePreview);
        }
        
        // File size validation for PDF upload
        const pdfFileInput = document.getElementById('pdf_file');
        if (pdfFileInput) {
            pdfFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const fileSize = file.size / 1024 / 1024;
                    const maxSize = 10;
                    
                    if (fileSize > maxSize) {
                        Swal.fire({
                            title: 'File Too Large',
                            text: `File size (${fileSize.toFixed(2)}MB) exceeds maximum allowed size (${maxSize}MB).`,
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                        this.value = '';
                    }
                    
                    if (!file.name.toLowerCase().endsWith('.pdf')) {
                        Swal.fire({
                            title: 'Invalid File Type',
                            text: 'Only PDF files are allowed.',
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                        this.value = '';
                    }
                }
            });
        }
        
        // URL validation for video link
        const videoLinkInput = document.getElementById('video_link');
        if (videoLinkInput) {
            videoLinkInput.addEventListener('blur', function() {
                if (this.value && !this.value.match(/^https?:\/\//i)) {
                    this.value = 'https://' + this.value;
                }
            });
        }
        
        // URL validation for attachment link
        const attachmentInput = document.getElementById('attachment');
        if (attachmentInput) {
            attachmentInput.addEventListener('blur', function() {
                if (this.value && !this.value.match(/^https?:\/\//i)) {
                    this.value = 'https://' + this.value;
                }
            });
        }
        
        // Form validation and submission with SweetAlert2
        const form = document.getElementById('topicForm');
        
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const titleInput = document.getElementById('title');
                const title = titleInput.value.trim();
                
                if (!title) {
                    titleInput.classList.add('error');
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please enter a topic title.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    titleInput.focus();
                    return false;
                } else {
                    titleInput.classList.remove('error');
                }
                
                @if(isset($topic))
                // Edit mode confirmation
                Swal.fire({
                    title: 'Update Topic?',
                    text: 'This topic will remain published and visible to students.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                        submitButton.disabled = true;
                        form.submit();
                    }
                });
                @else
                // Create mode confirmation
                Swal.fire({
                    title: 'Create Topic?',
                    text: 'This topic will be automatically published and visible to students.',
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
                @endif
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