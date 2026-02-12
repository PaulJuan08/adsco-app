@extends('layouts.admin')

@section('title', 'Topic Details - ' . $topic->title)

@push('styles')
<style>
    /* Modern Form Container - Matching Edit/Create Course */
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
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    
    .topic-preview-meta {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    .topic-preview-badge {
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
    
    .topic-preview-badge.published {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }
    
    .topic-preview-badge.draft {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
    }

    .topic-preview-id {
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

    /* Resource Cards */
    .resource-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    
    .resource-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
        border-color: #cbd5e0;
    }
    
    .resource-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #edf2f7;
        background: #f8fafc;
    }
    
    .resource-content {
        padding: 1rem 1.5rem;
    }
    
    .resource-title {
        font-weight: 700;
        color: #2d3748;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .resource-description {
        color: #718096;
        font-size: 0.8125rem;
        line-height: 1.6;
    }

    /* File Icon Styles */
    .file-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .file-pdf {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }
    
    .file-video {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    
    .file-word {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
    }
    
    .file-excel {
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
    }
    
    .file-powerpoint {
        background: rgba(217, 119, 6, 0.1);
        color: #d97706;
    }
    
    .file-image {
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
    }
    
    .file-zip {
        background: rgba(75, 85, 99, 0.1);
        color: #4b5563;
    }
    
    .file-generic {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    /* Course Tags */
    .course-tag {
        display: inline-block;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-top: 0.25rem;
        border: 1px solid rgba(102, 126, 234, 0.2);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .course-tag:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    /* Resource Action Buttons */
    .resource-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }

    .resource-action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .resource-action-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }

    .resource-action-btn.secondary {
        background: white;
        color: #4a5568;
        border: 1.5px solid #e2e8f0;
    }

    .resource-action-btn.secondary:hover {
        background: #f7fafc;
        border-color: #a0aec0;
        transform: translateY(-2px);
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

    /* Modal Styles */
    .pdf-modal-overlay,
    .video-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .pdf-modal-overlay.active,
    .video-modal-overlay.active {
        display: flex;
    }
    
    .pdf-modal-container,
    .video-modal-container {
        width: 90%;
        max-width: 1000px;
        height: 90%;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease;
    }
    
    .video-modal-container {
        background: #000;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-header {
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .video-player-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        background: #000;
    }
    
    .video-player {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }
    
    .video-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
    }
    
    .video-loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255,255,255,0.3);
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Loading Spinner */
    .loading-spinner {
        animation: spin 1s linear infinite;
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

        .resource-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .topic-preview-avatar {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }

        .topic-preview-title {
            font-size: 1.25rem;
        }

        .pdf-modal-container,
        .video-modal-container {
            width: 95%;
            height: 80%;
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

        .topic-preview-avatar {
            width: 60px;
            height: 60px;
            font-size: 1.75rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Topic Details Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-file-alt card-icon"></i>
                <h2 class="card-title">Topic Details</h2>
            </div>
            <div class="top-actions">
                <!-- Edit Button -->
                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                <!-- Delete Button -->
                <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="top-action-btn delete-btn" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                
                <!-- Back Button -->
                <a href="{{ route('admin.topics.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Topic Preview -->
            <div class="topic-preview">
                <div class="topic-preview-avatar">
                    {{ strtoupper(substr($topic->title, 0, 1)) }}
                </div>
                <div class="topic-preview-title">{{ $topic->title }}</div>
                <div class="topic-preview-meta">
                    <div class="topic-preview-badge {{ $topic->is_published ? 'published' : 'draft' }}">
                        <i class="fas {{ $topic->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $topic->is_published ? 'Published' : 'Draft' }}
                    </div>
                    <span class="topic-preview-id">
                        <i class="fas fa-hashtag"></i> ID: {{ $topic->id }}
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
                    <!-- Topic Description -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-align-left"></i> Topic Description
                        </div>
                        
                        <div class="description-box">
                            {{ $topic->description ?: 'No description provided for this topic.' }}
                        </div>
                    </div>

                    <!-- Resources Section -->
                    @if($topic->pdf_file || $topic->video_link || $topic->attachment)
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-paperclip"></i> Resources & Attachments
                        </div>
                        
                        <!-- PDF Document -->
                        @if($topic->pdf_file)
                        <div class="resource-card">
                            <div class="resource-header">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="file-icon file-pdf">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">PDF Document</div>
                                        <div style="font-size: 0.75rem; color: #718096;">
                                            <i class="fas fa-file"></i>
                                            {{ basename($topic->pdf_file) }}
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button onclick="openPdfModal('{{ asset($topic->pdf_file) }}')" 
                                            class="resource-action-btn primary">
                                        <i class="fas fa-eye"></i> View PDF
                                    </button>
                                    <a href="{{ asset($topic->pdf_file) }}" download 
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    PDF document associated with this topic.
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Video Link -->
                        @if($topic->video_link)
                        <div class="resource-card">
                            <div class="resource-header">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="file-icon file-video">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">Video Content</div>
                                        <div style="font-size: 0.75rem; color: #718096;">
                                            <i class="fas fa-link"></i>
                                            @php
                                                $host = parse_url($topic->video_link, PHP_URL_HOST);
                                                echo $host ? str_replace('www.', '', $host) : 'Video Link';
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button onclick="openVideoModal('{{ $topic->video_link }}')" 
                                            class="resource-action-btn primary">
                                        <i class="fas fa-play"></i> Play Video
                                    </button>
                                    <a href="{{ $topic->video_link }}" target="_blank" 
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-external-link-alt"></i> Open Link
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    <div style="color: #4a5568; margin-bottom: 0.5rem;">Video URL:</div>
                                    <div style="word-break: break-all; font-family: monospace; font-size: 0.8125rem; background: #f8fafc; padding: 0.625rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        {{ $topic->video_link }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Attachment -->
                        @if($topic->attachment)
                        <div class="resource-card">
                            <div class="resource-header">
                                @php
                                    $fileType = \App\Http\Controllers\Admin\TopicController::getFileType($topic->attachment);
                                    $icon = \App\Http\Controllers\Admin\TopicController::getFileIcon($topic->attachment);
                                    $colorClass = 'file-generic';
                                    
                                    if (str_contains($fileType, 'pdf')) $colorClass = 'file-pdf';
                                    elseif (str_contains($fileType, 'video')) $colorClass = 'file-video';
                                    elseif (str_contains($fileType, 'word')) $colorClass = 'file-word';
                                    elseif (str_contains($fileType, 'excel')) $colorClass = 'file-excel';
                                    elseif (str_contains($fileType, 'powerpoint')) $colorClass = 'file-powerpoint';
                                    elseif (str_contains($fileType, 'image')) $colorClass = 'file-image';
                                    elseif (str_contains($fileType, 'zip')) $colorClass = 'file-zip';
                                @endphp
                                
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="file-icon {{ $colorClass }}">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">Additional Attachment</div>
                                        <div style="font-size: 0.75rem; color: #718096;">
                                            <i class="fas fa-paperclip"></i>
                                            {{ $fileType }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ $topic->attachment }}" target="_blank" 
                                       class="resource-action-btn primary">
                                        <i class="fas fa-external-link-alt"></i> Open File
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    <a href="{{ $topic->attachment }}" target="_blank" style="color: #667eea; text-decoration: none; word-break: break-all;">
                                        {{ basename($topic->attachment) }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Publish Button for Draft Topics -->
                    @if(!$topic->is_published)
                    <div class="publish-section">
                        <form action="{{ route('admin.topics.publish', Crypt::encrypt($topic->id)) }}" method="POST" id="publishForm" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="publish-btn" id="publishButton">
                                <i class="fas fa-upload"></i> Publish Topic
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Right Column - Sidebar -->
                <div class="sidebar-column">
                    <!-- Topic Information Card -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-info-circle"></i> Topic Information
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hashtag"></i> Topic ID</span>
                            <span class="info-value">#{{ $topic->id }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Created</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $topic->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $topic->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="info-value">
                                @if($topic->is_published)
                                    <span style="color: #48bb78;">Published</span>
                                @else
                                    <span style="color: #ed8936;">Draft</span>
                                @endif
                            </span>
                        </div>
                        
                        @php
                            $resources = 0;
                            if($topic->pdf_file) $resources++;
                            if($topic->video_link) $resources++;
                            if($topic->attachment) $resources++;
                        @endphp
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-paperclip"></i> Resources</span>
                            <span class="info-value">{{ $resources }} file(s)</span>
                        </div>
                    </div>
                    
                    <!-- Courses Card -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-book"></i> Assigned Courses
                        </div>
                        
                        @if($topic->courses && $topic->courses->count() > 0)
                            <div style="margin-bottom: 0.75rem;">
                                <span class="info-label"><i class="fas fa-layer-group"></i> Total Courses</span>
                                <span class="info-value" style="display: block; margin-top: 0.25rem; font-size: 1.25rem;">{{ $topic->courses->count() }}</span>
                            </div>
                            <div style="margin-top: 0.5rem;">
                                @foreach($topic->courses as $course)
                                    <a href="{{ route('admin.courses.show', Crypt::encrypt($course->id)) }}" 
                                       class="course-tag">
                                        {{ $course->course_code }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state" style="padding: 1.5rem 0.5rem;">
                                <i class="fas fa-book-open" style="font-size: 2rem;"></i>
                                <h3 style="margin-top: 0.5rem;">No Courses Assigned</h3>
                                <p style="font-size: 0.75rem;">This topic is not used in any courses yet.</p>
                                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" 
                                   style="display: inline-block; margin-top: 0.75rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-plus" style="margin-right: 0.375rem;"></i> Assign to Course
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div class="pdf-modal-overlay" id="pdfModal">
        <div class="pdf-modal-container">
            <div class="modal-header">
                <h3 style="margin: 0; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-file-pdf"></i> PDF Preview
                </h3>
                <button class="modal-close" onclick="closePdfModal()">&times;</button>
            </div>
            <div style="flex: 1; position: relative; background: #f8fafc;">
                <iframe id="pdfIframe" style="width: 100%; height: 100%; border: none;"></iframe>
                <div id="pdfLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; display: none;">
                    <div style="width: 50px; height: 50px; border: 4px solid #e2e8f0; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                    <p style="color: #4a5568;">Loading PDF...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Player Modal -->
    <div class="video-modal-overlay" id="videoModal">
        <div class="video-modal-container">
            <div class="modal-header">
                <h3 style="margin: 0; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-video"></i> Video Player
                </h3>
                <button class="modal-close" onclick="closeVideoModal()">&times;</button>
            </div>
            <div class="video-player-container">
                <iframe id="videoPlayer" class="video-player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <div id="videoLoading" class="video-loading" style="display: none;">
                    <div class="video-loading-spinner"></div>
                    <p>Loading video...</p>
                </div>
            </div>
            <div style="padding: 1rem; background: #1a202c; color: white; font-size: 0.8125rem; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <div id="videoUrlDisplay" style="word-break: break-all; opacity: 0.8;"></div>
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
                    title: 'Publish Topic?',
                    text: 'Once published, this topic will be visible to students.',
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
                    title: 'Delete Topic?',
                    text: 'This action cannot be undone. All topic data will be permanently removed.',
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
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
        
        // Setup modal event listeners
        setupModals();
    });
    
    // Toast Notification Function
    function showNotification(message, type = 'info') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            icon: type,
            title: message,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    // PDF Modal Functions
    function openPdfModal(pdfUrl) {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');
        const loading = document.getElementById('pdfLoading');
        
        modal.style.display = 'flex';
        loading.style.display = 'block';
        
        iframe.src = pdfUrl;
        
        iframe.onload = () => {
            loading.style.display = 'none';
        };
        
        iframe.onerror = () => {
            loading.style.display = 'none';
            showNotification('Failed to load PDF. Please try downloading the file instead.', 'error');
            closePdfModal();
        };
    }

    function closePdfModal() {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');
        const loading = document.getElementById('pdfLoading');
        
        modal.style.display = 'none';
        iframe.src = '';
        loading.style.display = 'none';
    }

    // Video Modal Functions
    function openVideoModal(videoUrl) {
        const modal = document.getElementById('videoModal');
        const player = document.getElementById('videoPlayer');
        const loading = document.getElementById('videoLoading');
        const urlDisplay = document.getElementById('videoUrlDisplay');
        
        modal.style.display = 'flex';
        loading.style.display = 'block';
        
        const embedUrl = getEmbedUrl(videoUrl);
        
        if (embedUrl) {
            player.src = embedUrl;
            player.style.display = 'block';
            urlDisplay.textContent = 'Source: ' + videoUrl;
        } else {
            loading.innerHTML = `
                <div style="color: white; text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: #ed8936;"></i>
                    <p>This video cannot be embedded directly.</p>
                    <a href="${videoUrl}" target="_blank" style="color: #667eea; text-decoration: underline; display: inline-block; margin-top: 0.5rem;">
                        Open video in new tab
                    </a>
                </div>
            `;
            player.style.display = 'none';
            urlDisplay.textContent = 'Source: ' + videoUrl;
        }
        
        player.onload = () => {
            loading.style.display = 'none';
        };
        
        player.onerror = () => {
            loading.innerHTML = `
                <div style="color: white; text-align: center;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; margin-bottom: 1rem; color: #f56565;"></i>
                    <p>Failed to load video.</p>
                    <a href="${videoUrl}" target="_blank" style="color: #667eea; text-decoration: underline; display: inline-block; margin-top: 0.5rem;">
                        Try opening in new tab
                    </a>
                </div>
            `;
            player.style.display = 'none';
        };
    }

    function getEmbedUrl(url) {
        // YouTube
        const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
        const youtubeMatch = url.match(youtubeRegex);
        if (youtubeMatch) {
            return `https://www.youtube.com/embed/${youtubeMatch[1]}?autoplay=1&rel=0`;
        }
        
        // Vimeo
        const vimeoRegex = /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/i;
        const vimeoMatch = url.match(vimeoRegex);
        if (vimeoMatch) {
            return `https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`;
        }
        
        // Direct video file (mp4, webm, etc.)
        if (url.match(/\.(mp4|webm|ogg|mov|avi|wmv|flv|mkv)(\?.*)?$/i)) {
            return url;
        }
        
        return null;
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const player = document.getElementById('videoPlayer');
        const loading = document.getElementById('videoLoading');
        
        modal.style.display = 'none';
        player.src = '';
        loading.style.display = 'none';
        loading.innerHTML = '<div class="video-loading-spinner"></div><p>Loading video...</p>';
    }

    function setupModals() {
        // Close PDF modal when clicking outside
        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePdfModal();
            }
        });

        // Close video modal when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePdfModal();
                closeVideoModal();
            }
        });
    }
</script>
@endpush