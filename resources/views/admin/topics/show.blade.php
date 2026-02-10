@extends('layouts.admin')

@section('title', 'Topic Details - ' . $topic->title)

@push('styles')
{{-- Consolidated and optimized styles --}}
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
        --info: #3b82f6;
        --info-light: #dbeafe;
        --info-dark: #1d4ed8;
        --radius: 0.5rem;
        --radius-sm: 0.25rem;
        --radius-lg: 0.75rem;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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
    
    /* Resource Cards */
    .resource-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }
    
    .resource-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    
    .resource-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        background: var(--gray-50);
    }
    
    .resource-content {
        padding: 1rem 1.5rem;
    }
    
    .resource-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
    }
    
    .resource-description {
        color: var(--gray-600);
        font-size: 0.875rem;
        line-height: 1.6;
    }
    
    .action-btn-small {
        padding: 0.5rem;
        color: var(--gray-600);
        border: none;
        background: none;
        cursor: pointer;
        border-radius: var(--radius-sm);
        transition: all 0.2s;
    }
    
    .action-btn-small:hover {
        background: var(--gray-100);
        color: var(--danger);
    }
    
    /* File Icon Styles */
    .file-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .file-pdf {
        background: rgba(220, 38, 38, 0.1);
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
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--gray-500);
    }
    
    .empty-state i {
        font-size: 3rem;
        color: var(--gray-300);
        margin-bottom: 1rem;
    }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal-container {
        background: white;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 600px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        animation: modalSlideIn 0.3s ease;
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
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--gray-50);
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }
    
    .modal-close {
        background: none;
        border: none;
        color: var(--gray-600);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        transition: all 0.2s;
    }
    
    .modal-close:hover {
        background: var(--gray-100);
    }
    
    .modal-body {
        padding: 1.5rem;
        max-height: calc(80vh - 120px);
        overflow-y: auto;
    }
    
    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: var(--gray-50);
    }
    
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
    }
    
    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }
    
    .btn-secondary:hover {
        background: var(--gray-200);
    }
    
    /* Action buttons grid */
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
    
    /* Notification Styles */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        z-index: 1001;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .notification.success {
        background: var(--success);
        color: white;
    }
    
    .notification.error {
        background: var(--danger);
        color: white;
    }
    
    .notification i {
        font-size: 1.25rem;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* PDF Modal Styles */
    .pdf-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .pdf-modal-container {
        width: 90%;
        max-width: 1000px;
        height: 90%;
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Video Modal Styles */
    .video-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .video-modal-container {
        width: 90%;
        max-width: 1000px;
        height: auto;
        max-height: 90%;
        background: transparent;
        border-radius: var(--radius);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .video-modal-header {
        padding: 1rem 1.5rem;
        background: rgba(0,0,0,0.8);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: var(--radius);
        border-top-right-radius: var(--radius);
    }
    
    .video-player-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
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
        border-top: 4px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .detail-section {
            padding: 1rem;
        }
        
        .detail-value {
            font-size: 1rem;
        }
        
        .action-buttons-grid {
            grid-template-columns: 1fr;
        }
        
        .resource-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .resource-content {
            padding: 1rem;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .video-modal-container {
            width: 95%;
            max-height: 70%;
        }
    }
</style>
@endpush

@section('content')
    <!-- Topic Profile Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-file-alt card-icon"></i>
                <h2 class="card-title">Topic Details: {{ $topic->title }}</h2>
            </div>
            <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" class="view-all-link">
                Edit Topic <i class="fas fa-edit"></i>
            </a>
        </div>
        
        <div class="card-body">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700; margin: 0 auto 1.5rem;">
                    {{ strtoupper(substr($topic->title, 0, 1)) }}
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.5rem;">
                    {{ $topic->title }}
                </h3>
                
                <div class="status-badge {{ $topic->is_published ? 'status-published' : 'status-draft' }}">
                    <i class="fas {{ $topic->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $topic->is_published ? 'Topic Published' : 'Draft Mode' }}
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i>
                        Topic Information
                    </div>
                    
                    <div>
                        <div class="detail-label">Topic Title</div>
                        <div class="detail-value">{{ $topic->title }}</div>
                        
                        <div class="detail-label">Topic ID</div>
                        <div class="detail-value">#{{ $topic->id }}</div>
                        
                        <div class="detail-label">Created</div>
                        <div class="detail-value">{{ $topic->created_at->format('F d, Y') }}</div>
                        <div class="detail-subvalue">
                            <i class="fas fa-clock"></i> {{ $topic->created_at->diffForHumans() }}
                        </div>
                        
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">{{ $topic->updated_at->format('F d, Y') }}</div>
                        <div class="detail-subvalue">
                            <i class="fas fa-clock"></i> {{ $topic->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-chart-bar"></i>
                        Content Information
                    </div>
                    
                    <div>
                        @php
                            $resources = 0;
                            if($topic->pdf_file) $resources++;
                            if($topic->video_link) $resources++;
                            if($topic->attachment) $resources++;
                        @endphp
                        
                        <div class="detail-label">Total Resources</div>
                        <div class="detail-value">{{ $resources }} file(s)</div>
                        
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            @if($topic->is_published)
                                <span style="color: var(--success); font-weight: 600;">Published</span>
                            @else
                                <span style="color: var(--warning); font-weight: 600;">Draft</span>
                            @endif
                        </div>
                        
                        @if($topic->courses && $topic->courses->count() > 0)
                        <div class="detail-label">Used in Courses</div>
                        <div class="detail-value">{{ $topic->courses->count() }} course(s)</div>
                        <div class="detail-subvalue">
                            @foreach($topic->courses->take(3) as $course)
                                <span style="display: inline-block; background: var(--primary-light); color: var(--primary-dark); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.75rem; margin-right: 0.5rem; margin-top: 0.25rem;">
                                    {{ $course->title }}
                                </span>
                            @endforeach
                            @if($topic->courses->count() > 3)
                                <span style="font-size: 0.75rem; color: var(--gray-500);">
                                    +{{ $topic->courses->count() - 3 }} more
                                </span>
                            @endif
                        </div>
                        @else
                        <div class="detail-label">Used in Courses</div>
                        <div class="detail-value" style="color: var(--gray-500);">
                            Not assigned to any courses
                        </div>
                        @endif
                    </div>
                </div>
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
                            <div style="font-size: 0.75rem; color: var(--gray-500);">
                                <i class="fas fa-file" style="margin-right: 0.25rem;"></i>
                                {{ basename($topic->pdf_file) }}
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="openPdfModal('{{ asset($topic->pdf_file) }}')" 
                                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border-radius: var(--radius-sm); border: none; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                            <i class="fas fa-eye"></i> View PDF
                        </button>
                        <a href="{{ asset($topic->pdf_file) }}" download 
                           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--gray-100); color: var(--gray-700); border-radius: var(--radius-sm); border: 1px solid var(--gray-300); text-decoration: none; font-weight: 500; font-size: 0.875rem;">
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
                            <div style="font-size: 0.75rem; color: var(--gray-500);">
                                <i class="fas fa-link" style="margin-right: 0.25rem;"></i>
                                @php
                                    $host = parse_url($topic->video_link, PHP_URL_HOST);
                                    echo $host ?: 'Video Link';
                                @endphp
                            </div>
                        </div>
                    </div>
                    <div>
                        <button onclick="openVideoModal('{{ $topic->video_link }}')" 
                                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border-radius: var(--radius-sm); border: none; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                            <i class="fas fa-play"></i> Play Video
                        </button>
                        <a href="{{ $topic->video_link }}" target="_blank" 
                           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--gray-100); color: var(--gray-700); border-radius: var(--radius-sm); border: 1px solid var(--gray-300); text-decoration: none; font-weight: 500; font-size: 0.875rem;">
                            <i class="fas fa-external-link-alt"></i> Open Link
                        </a>
                    </div>
                </div>
                <div class="resource-content">
                    <div class="resource-description">
                        <div style="color: var(--gray-600); margin-bottom: 0.5rem;">Video URL:</div>
                        <div style="word-break: break-all; font-family: monospace; font-size: 0.875rem; background: var(--gray-100); padding: 0.5rem; border-radius: var(--radius-sm);">
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
                            <div style="font-size: 0.75rem; color: var(--gray-500);">
                                <i class="fas fa-paperclip" style="margin-right: 0.25rem;"></i>
                                {{ $fileType }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ $topic->attachment }}" target="_blank" 
                           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border-radius: var(--radius-sm); text-decoration: none; font-weight: 500; font-size: 0.875rem;">
                            <i class="fas fa-external-link-alt"></i> Open File
                        </a>
                    </div>
                </div>
                <div class="resource-content">
                    <div class="resource-description">
                        <a href="{{ $topic->attachment }}" target="_blank" style="color: var(--primary); text-decoration: none; word-break: break-all;">
                            {{ basename($topic->attachment) }}
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Description -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-align-left"></i>
                    Topic Description
                </div>
                
                <div style="padding: 1rem; background: white; border-radius: var(--radius-sm); border: 1px solid var(--gray-200);">
                    {{ $topic->description ?: 'No description provided for this topic.' }}
                </div>
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
                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" class="action-btn btn-edit">
                    <i class="fas fa-edit"></i>
                    Edit Topic
                </a>
                
                @if(!$topic->is_published)
                <form action="{{ route('admin.topics.publish', Crypt::encrypt($topic->id)) }}" method="POST" id="publishForm">
                    @csrf
                    <button type="submit" class="action-btn btn-success" id="publishButton">
                        <i class="fas fa-upload"></i>
                        Publish Topic
                    </button>
                </form>
                @endif
                
                <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-delete" id="deleteButton">
                        <i class="fas fa-trash"></i>
                        Delete Topic
                    </button>
                </form>
                
                <a href="{{ route('admin.topics.index') }}" class="action-btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Topics
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
                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); border: 1px solid var(--primary); text-decoration: none; color: var(--primary-dark); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Edit Topic</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Update topic information</div>
                    </div>
                </a>
                
                <a href="{{ route('admin.topics.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--gray-100); border-radius: var(--radius-sm); border: 1px solid var(--gray-300); text-decoration: none; color: var(--gray-700); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--gray-300); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: var(--gray-700);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">All Topics</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">View all system topics</div>
                    </div>
                </a>
                
                @if($topic->pdf_file)
                <a href="#" onclick="openPdfModal('{{ asset($topic->pdf_file) }}')" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--info-light); border-radius: var(--radius-sm); border: 1px solid var(--info); text-decoration: none; color: var(--info-dark); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--info); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">View PDF</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Open PDF document</div>
                    </div>
                </a>
                @endif
                
                @if($topic->video_link)
                <a href="#" onclick="openVideoModal('{{ $topic->video_link }}')" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--danger-light); border-radius: var(--radius-sm); border: 1px solid var(--danger); text-decoration: none; color: var(--danger-dark); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--danger); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Play Video</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Watch topic video</div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div class="pdf-modal-overlay" id="pdfModal">
        <div class="pdf-modal-container">
            <div style="padding: 1rem 1.5rem; background: var(--primary); color: white; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-weight: 600;">PDF Preview</h3>
                <button id="closePdfModal" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>
            <div style="flex: 1; position: relative;">
                <iframe id="pdfIframe" style="width: 100%; height: 100%; border: none;"></iframe>
                <div id="pdfLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; display: none;">
                    <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                    <p>Loading PDF...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Player Modal -->
    <div class="video-modal-overlay" id="videoModal">
        <div class="video-modal-container">
            <div class="video-modal-header">
                <h3 id="videoModalTitle" style="margin: 0; font-weight: 600; font-size: 1.125rem;">Video Player</h3>
                <button id="closeVideoModal" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>
            <div class="video-player-container">
                <iframe id="videoPlayer" class="video-player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <div id="videoLoading" class="video-loading" style="display: none;">
                    <div class="video-loading-spinner"></div>
                    <p>Loading video...</p>
                </div>
            </div>
            <div style="padding: 1rem; background: rgba(0,0,0,0.8); color: white; font-size: 0.875rem; border-bottom-left-radius: var(--radius); border-bottom-right-radius: var(--radius);">
                <div id="videoUrlDisplay" style="word-break: break-all; opacity: 0.8; font-size: 0.75rem;"></div>
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
                
                if (confirm('Are you sure you want to publish this topic?\n\nOnce published, the topic will be visible to students.')) {
                    // Show loading state
                    const originalHTML = publishButton.innerHTML;
                    publishButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Publishing...';
                    publishButton.disabled = true;
                    
                    // Submit the form
                    document.getElementById('publishForm').submit();
                }
            });
        }
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('WARNING: Are you sure you want to delete this topic?\n\nThis action cannot be undone. All topic data will be permanently removed.')) {
                    // Show loading state
                    const originalHTML = deleteButton.innerHTML;
                    deleteButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Deleting...';
                    deleteButton.disabled = true;
                    
                    // Submit the form
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
        
        // Setup video modal event listeners
        setupVideoModal();
    });
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--warning)'};
            color: white;
            z-index: 9999;
            box-shadow: var(--shadow-lg);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            max-width: 400px;
        `;
        
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon}" style="font-size: 1.25rem;"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Add CSS animations if not present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }

    // PDF Modal Functions
    function openPdfModal(pdfUrl) {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');
        const loading = document.getElementById('pdfLoading');
        
        // Show modal and loading indicator
        modal.style.display = 'flex';
        loading.style.display = 'block';
        
        // Set iframe source
        iframe.src = pdfUrl;
        
        // Hide loading when iframe loads
        iframe.onload = () => {
            loading.style.display = 'none';
        };
        
        iframe.onerror = () => {
            loading.style.display = 'none';
            alert('Failed to load PDF. Please try downloading the file instead.');
            closePdfModal();
        };
    }

    // Video Modal Functions
    function openVideoModal(videoUrl) {
        const modal = document.getElementById('videoModal');
        const player = document.getElementById('videoPlayer');
        const loading = document.getElementById('videoLoading');
        const urlDisplay = document.getElementById('videoUrlDisplay');
        
        // Show modal and loading indicator
        modal.style.display = 'flex';
        loading.style.display = 'block';
        
        // Embed video based on platform
        const embedUrl = getEmbedUrl(videoUrl);
        
        if (embedUrl) {
            player.src = embedUrl;
            player.style.display = 'block';
            urlDisplay.textContent = 'Source: ' + videoUrl;
        } else {
            // If we can't embed, show message
            loading.innerHTML = `
                <div style="color: white; text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
                    <p>This video cannot be embedded directly.</p>
                    <a href="${videoUrl}" target="_blank" style="color: var(--primary); text-decoration: underline; display: inline-block; margin-top: 0.5rem;">
                        Open video in new tab
                    </a>
                </div>
            `;
            player.style.display = 'none';
        }
        
        // Hide loading when video loads
        player.onload = () => {
            loading.style.display = 'none';
        };
        
        player.onerror = () => {
            loading.innerHTML = `
                <div style="color: white; text-align: center;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--danger);"></i>
                    <p>Failed to load video.</p>
                    <a href="${videoUrl}" target="_blank" style="color: var(--primary); text-decoration: underline; display: inline-block; margin-top: 0.5rem;">
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

    function setupVideoModal() {
        // Close modal functionality
        document.getElementById('closeVideoModal').addEventListener('click', closeVideoModal);

        // Close modal when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target.id === 'videoModal') {
                closeVideoModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('videoModal');
                if (modal.style.display === 'flex') {
                    closeVideoModal();
                }
            }
        });
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const player = document.getElementById('videoPlayer');
        const loading = document.getElementById('videoLoading');
        
        modal.style.display = 'none';
        player.src = ''; // Stop video playback
        loading.style.display = 'none';
        loading.innerHTML = '<div class="video-loading-spinner"></div><p>Loading video...</p>';
    }

    // PDF Modal close functionality
    document.getElementById('closePdfModal').addEventListener('click', closePdfModal);

    // Close PDF modal when clicking outside
    document.getElementById('pdfModal').addEventListener('click', function(e) {
        if (e.target.id === 'pdfModal') {
            closePdfModal();
        }
    });

    // Close PDF modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('pdfModal');
            if (modal.style.display === 'flex') {
                closePdfModal();
            }
        }
    });

    function closePdfModal() {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');
        const loading = document.getElementById('pdfLoading');
        
        modal.style.display = 'none';
        iframe.src = ''; // Clear iframe source
        loading.style.display = 'none'; // Reset loading
    }
</script>
@endpush