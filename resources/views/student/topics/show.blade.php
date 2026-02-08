@extends('layouts.student')

@section('title', $topic->title . ' - Student Dashboard')

@section('content')

<!-- PDF Preview Modal -->
<div id="pdfModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 999999; align-items: center; justify-content: center;">
    <div style="width: 90%; max-width: 1200px; height: 90%; background: white; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="padding: 1rem 1.5rem; background: #dc2626; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-weight: 600;">PDF Preview</h3>
            <button id="closePdfModal" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>
        <div style="flex: 1; position: relative;">
            <iframe id="pdfIframe" style="width: 100%; height: 100%; border: none;"></iframe>
            <div id="pdfLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; display: none;">
                <div class="spinner" style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #dc2626; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: #6b7280;">Loading PDF...</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="topic-container">
    <!-- Progress & Actions Section -->
    <div class="progress-section">
        <div class="progress-card">
            <div class="progress-status">
                @php
                    $isCompleted = Auth::user()->completedTopics()
                        ->where('topic_id', $topic->id)
                        ->exists();
                @endphp
                
                <div class="status-indicator {{ $isCompleted ? 'completed' : 'in-progress' }}">
                    <div class="indicator-icon">
                        @if($isCompleted)
                        <i class="fas fa-check-circle"></i>
                        @else
                        <i class="fas fa-spinner"></i>
                        @endif
                    </div>
                    <div class="indicator-content">
                        <h3 class="status-title">
                            @if($isCompleted)
                            Topic Completed
                            @else
                            Topic In Progress
                            @endif
                        </h3>
                        <p class="status-subtitle">
                            @if($isCompleted)
                            Completed on {{ $completionDate->format('M d, Y') }}
                            @else
                            Start learning to complete this topic
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="status-actions">
                    @if(!$isCompleted)
                    <button class="btn btn-success btn-complete" data-topic-id="{{ $encryptedId }}">
                        <i class="fas fa-check-circle"></i>
                        <span>Mark as Complete</span>
                    </button>
                    @else
                    <button class="btn btn-outline-secondary btn-incomplete" data-topic-id="{{ $encryptedId }}">
                        <i class="fas fa-undo"></i>
                        <span>Mark as Incomplete</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Live Progress Update Section -->
    <div class="progress-update-section" style="display: none;">
        <div class="live-progress-card">
            <div class="progress-header">
                <h4><i class="fas fa-sync-alt"></i> Progress Updated</h4>
                <button class="close-progress-update">&times;</button>
            </div>
            <div class="progress-body">
                <div class="progress-info">
                    <div class="progress-item">
                        <span class="progress-label">Completed Topics:</span>
                        <span class="progress-value completed-count">{{ $completedTopics }}</span>
                    </div>
                    <div class="progress-item">
                        <span class="progress-label">Total Topics:</span>
                        <span class="progress-value total-count">{{ $totalTopics }}</span>
                    </div>
                </div>
                <div class="progress-visual">
                    <div class="progress-text">
                        <span class="progress-percent">
                            @if($totalTopics > 0)
                                {{ round(($completedTopics / $totalTopics) * 100) }}%
                            @else
                                0%
                            @endif
                        </span>
                        <span>Complete</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 
                            @if($totalTopics > 0)
                                {{ round(($completedTopics / $totalTopics) * 100) }}%
                            @else
                                0%
                            @endif
                        "></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Lesson Section -->
    @if($topic->video_link)
    <section class="video-section">
        <div class="section-header">
            <div class="header-content">
                <div class="section-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h2>Video Lesson</h2>
            </div>
        </div>
        
        <!-- PDF Materials Section -->
        @if($topic->pdf_file)
        <section class="pdf-section">
            <div class="section-header">
                <div class="header-content">
                    <div class="section-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h2>PDF Materials</h2>
                </div>
            </div>
            
            <div class="pdf-container">
                <div class="pdf-card">
                    <div class="pdf-header" style="border-color: #dc2626;">
                        <div class="pdf-icon" style="color: #dc2626;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="pdf-info">
                            <h4>PDF Document</h4>
                            <p class="pdf-desc">{{ basename($topic->pdf_file) }}</p>
                        </div>
                    </div>
                    <div class="pdf-body">
                        <div class="file-info">
                            <div class="info-item">
                                <i class="fas fa-file-pdf"></i>
                                <span>PDF Document</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>Ready to View</span>
                            </div>
                        </div>
                        <div class="pdf-actions">
                            <button onclick="openPdfModal('{{ asset($topic->pdf_file) }}')" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Open
                            </button>
                            <!-- <a href="{{ asset($topic->pdf_file) }}" download class="btn btn-outline-primary">
                                <i class="fas fa-download"></i> Download
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="video-container-wrapper">
            @php
                $embedUrl = null;
                
                if (str_contains($topic->video_link, 'youtube.com/watch?v=')) {
                    $videoId = substr($topic->video_link, strpos($topic->video_link, 'v=') + 2);
                    $videoId = strtok($videoId, '&');
                    $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
                } 
                elseif (str_contains($topic->video_link, 'youtu.be/')) {
                    $videoId = substr($topic->video_link, strrpos($topic->video_link, '/') + 1);
                    $videoId = strtok($videoId, '?');
                    $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
                }
                elseif (str_contains($topic->video_link, 'vimeo.com/')) {
                    $videoId = substr($topic->video_link, strrpos($topic->video_link, '/') + 1);
                    $videoId = strtok($videoId, '?');
                    $embedUrl = $videoId ? "https://player.vimeo.com/video/{$videoId}" : null;
                }
            @endphp
            
            @if($embedUrl)
            <div class="video-player">
                <div class="video-embed">
                    <iframe 
                        src="{{ $embedUrl }}" 
                        title="Video Lesson"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            @else
            <div class="video-alt-card">
                <div class="alt-icon">
                    <i class="fas fa-external-link-alt"></i>
                </div>
                <div class="alt-content">
                    <h4>External Video Link</h4>
                    <p>This video cannot be embedded. Click the button below to watch on the original platform.</p>
                    <a href="{{ $topic->video_link }}" target="_blank" class="btn btn-primary btn-video">
                        <i class="fas fa-play-circle"></i>
                        Watch Video
                    </a>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Learning Materials -->
    @if($topic->attachment)
    <section class="materials-section">
        <div class="section-header">
            <div class="header-content">
                <div class="section-icon">
                    <i class="fas fa-paperclip"></i>
                </div>
                <h2>Learning Materials</h2>
            </div>
        </div>
        
        <div class="materials-container">
            @php
                $icon = 'fas fa-file';
                $color = '#6b7280';
                
                if (str_contains($topic->attachment, '.pdf')) {
                    $icon = 'fas fa-file-pdf';
                    $color = '#dc2626';
                } elseif (str_contains($topic->attachment, '.doc') || str_contains($topic->attachment, '.docx')) {
                    $icon = 'fas fa-file-word';
                    $color = '#2563eb';
                } elseif (str_contains($topic->attachment, '.xls') || str_contains($topic->attachment, '.xlsx')) {
                    $icon = 'fas fa-file-excel';
                    $color = '#059669';
                } elseif (str_contains($topic->attachment, '.ppt') || str_contains($topic->attachment, '.pptx')) {
                    $icon = 'fas fa-file-powerpoint';
                    $color = '#d97706';
                } elseif (str_contains($topic->attachment, '.jpg') || str_contains($topic->attachment, '.jpeg') || 
                          str_contains($topic->attachment, '.png') || str_contains($topic->attachment, '.gif')) {
                    $icon = 'fas fa-file-image';
                    $color = '#7c3aed';
                }
            @endphp
            
            <div class="material-card">
                <div class="material-header" style="border-color: {{ $color }};">
                    <div class="material-icon" style="color: {{ $color }};">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div class="material-info">
                        <h4>Topic Materials</h4>
                        <p class="material-desc">Supplementary resources for this topic</p>
                    </div>
                </div>
                <div class="material-body">
                    <div class="file-info">
                        <div class="info-item">
                            <i class="fas fa-file"></i>
                            <span>{{ strtoupper(pathinfo($topic->attachment, PATHINFO_EXTENSION)) }} File</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-download"></i>
                            <span>Download Available</span>
                        </div>
                    </div>
                    <div class="material-actions">
                        <a href="{{ $topic->attachment }}" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        <a href="{{ $topic->attachment }}" download class="btn btn-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</div>

<style>
    /* CSS Variables */
    :root {
        --primary: #4361ee;
        --primary-light: #eef2ff;
        --secondary: #6b7280;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --dark: #1f2937;
        --light: #f9fafb;
        --border: #e5e7eb;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
    }

    /* Main Container */
    .topic-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Progress Section */
    .progress-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        padding: 1.5rem;
    }

    .progress-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .status-indicator {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .status-indicator.completed .indicator-icon {
        color: var(--success);
    }

    .status-indicator.in-progress .indicator-icon {
        color: var(--primary);
    }

    .indicator-icon {
        font-size: 2.5rem;
    }

    .indicator-content {
        flex: 1;
    }

    .status-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0 0 0.25rem 0;
    }

    .status-subtitle {
        color: var(--secondary);
        font-size: 0.875rem;
        margin: 0;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #0da271;
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        background: transparent;
        color: var(--secondary);
        border: 1px solid var(--border);
    }

    .btn-outline-secondary:hover {
        background: var(--light);
    }

    /* Section Styles */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-icon {
        width: 48px;
        height: 48px;
        background: var(--primary-light);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.25rem;
    }

    .section-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    /* Video Section */
    .video-container-wrapper {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .video-player {
        background: #000;
    }

    .video-embed {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        height: 0;
    }

    .video-embed iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    .video-alt-card {
        text-align: center;
        padding: 3rem;
        background: var(--light);
        border-radius: var(--radius-md);
    }

    .alt-icon {
        font-size: 3rem;
        color: var(--secondary);
        margin-bottom: 1rem;
    }

    .alt-content h4 {
        font-size: 1.25rem;
        color: var(--dark);
        margin: 0 0 0.5rem 0;
    }

    .alt-content p {
        color: var(--secondary);
        margin-bottom: 1.5rem;
    }

    .btn-video {
        padding: 0.75rem 2rem;
        font-size: 1rem;
    }

    /* Materials Section */
    .materials-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .material-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .material-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 3px solid;
    }

    .material-icon {
        font-size: 2.5rem;
    }

    .material-info h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0 0 0.25rem 0;
    }

    .material-desc {
        color: var(--secondary);
        font-size: 0.875rem;
        margin: 0;
    }

    .material-body {
        padding: 1.5rem;
    }

    .file-info {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .file-info .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .material-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #3a56d4;
    }

    .btn-outline-primary {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline-primary:hover {
        background: var(--primary-light);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .progress-status {
            flex-direction: column;
            align-items: stretch;
        }
        
        .status-actions {
            width: 100%;
        }
        
        .btn-complete, .btn-incomplete {
            width: 100%;
        }
        
        .material-actions {
            flex-direction: column;
        }
        
        .material-actions .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .topic-container {
            padding: 1rem;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .file-info {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    /* Progress Update Section */
    .progress-update-section {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        animation: slideInUp 0.3s ease;
    }

    @keyframes slideInUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .live-progress-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border: 1px solid var(--border);
        width: 300px;
        overflow: hidden;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
        color: white;
    }

    .progress-header h4 {
        margin: 0;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .close-progress-update {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .close-progress-update:hover {
        background: rgba(255,255,255,0.2);
    }

    .progress-body {
        padding: 1rem;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .progress-item {
        display: flex;
        flex-direction: column;
    }

    .progress-label {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-bottom: 0.25rem;
    }

    .progress-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    .completed-count {
        color: var(--success);
    }

    .total-count {
        color: var(--primary);
    }

    .progress-visual {
        text-align: center;
    }

    .progress-text {
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .progress-percent {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .progress-text span:last-child {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .progress-bar {
        height: 8px;
        background: #f3f4f6;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary) 0%, #7c3aed 100%);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

        /* PDF Section */
    .pdf-section {
        margin-top: 2rem;
    }

    .pdf-container {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .pdf-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .pdf-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 3px solid;
        background: #fef2f2;
    }

    .pdf-icon {
        font-size: 2.5rem;
    }

    .pdf-info h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0 0 0.25rem 0;
    }

    .pdf-desc {
        color: var(--secondary);
        font-size: 0.875rem;
        margin: 0;
        word-break: break-all;
    }

    .pdf-body {
        padding: 1.5rem;
    }

    .pdf-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    /* PDF Modal Styles */
    #pdfModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 999999;
        align-items: center;
        justify-content: center;
    }

    #pdfIframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* Spinner animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .pdf-actions {
            flex-direction: column;
        }
        
        .pdf-actions .btn {
            width: 100%;
        }
        
        #pdfModal > div {
            width: 95% !important;
            height: 95% !important;
        }
    }
</style>


@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ========== GLOBAL PDF MODAL FUNCTIONS ==========
function openPdfModal(pdfUrl) {
    console.log('openPdfModal called with URL:', pdfUrl);
    
    const modal = document.getElementById('pdfModal');
    const iframe = document.getElementById('pdfIframe');
    const loading = document.getElementById('pdfLoading');
    
    if (!modal || !iframe) {
        console.error('Modal or iframe not found');
        alert('PDF preview is not available. Please try downloading the file.');
        return;
    }
    
    // Show modal and loading indicator
    modal.style.display = 'flex';
    if (loading) {
        loading.style.display = 'block';
    }
    
    // Set iframe source
    console.log('Setting iframe src to PDF');
    iframe.src = pdfUrl;
    
    // Hide loading when iframe loads
    iframe.onload = () => {
        console.log('PDF loaded successfully');
        if (loading) {
            loading.style.display = 'none';
        }
    };
    
    iframe.onerror = () => {
        console.error('Failed to load PDF');
        if (loading) {
            loading.style.display = 'none';
        }
        alert('Failed to load PDF. Please try downloading the file instead.');
        closePdfModal();
    };
}

function closePdfModal() {
    console.log('Closing PDF modal');
    
    const modal = document.getElementById('pdfModal');
    const iframe = document.getElementById('pdfIframe');
    const loading = document.getElementById('pdfLoading');
    
    if (modal) {
        modal.style.display = 'none';
    }
    
    if (iframe) {
        iframe.src = '';
    }
    
    if (loading) {
        loading.style.display = 'none';
    }
}

// ========== DOM CONTENT LOADED ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    // Initialize progress stats
    const isCurrentlyCompleted = {{ $isCompleted ? 'true' : 'false' }};
    const totalTopics = {{ $totalTopics ?? 0 }};
    const initialCompletedTopics = {{ $completedTopics ?? 0 }};
    
    function initProgressStats() {
        const stats = {
            totalTopics: totalTopics,
            completedTopics: initialCompletedTopics,
            progressPercentage: totalTopics > 0 ? Math.round((initialCompletedTopics / totalTopics) * 100) : 0
        };
        
        localStorage.setItem('topicProgress', JSON.stringify(stats));
        console.log('Initial stats:', stats);
    }
    
    initProgressStats();
    
    // PDF Modal Event Listeners
    const closeBtn = document.getElementById('closePdfModal');
    if (closeBtn) {
        closeBtn.addEventListener('click', closePdfModal);
        console.log('Close button listener added');
    }
    
    const modal = document.getElementById('pdfModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePdfModal();
            }
        });
        console.log('Modal outside click listener added');
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('pdfModal');
            if (modal && modal.style.display === 'flex') {
                closePdfModal();
            }
        }
    });
    console.log('Escape key listener added');
    
    // Mark topic as complete functionality
    const markCompleteBtn = document.querySelector('.btn-complete');
    const markIncompleteBtn = document.querySelector('.btn-incomplete');
    
    if (markCompleteBtn) {
        markCompleteBtn.addEventListener('click', function() {
            const topicId = '{{ $encryptedId }}';
            markTopicComplete(topicId, true);
        });
    }
    
    if (markIncompleteBtn) {
        markIncompleteBtn.addEventListener('click', function() {
            const topicId = '{{ $encryptedId }}';
            markTopicComplete(topicId, false);
        });
    }
    
    function markTopicComplete(topicId, complete) {
        const url = complete 
            ? `/student/topics/${topicId}/complete`
            : `/student/topics/${topicId}/incomplete`;
        
        const actionText = complete ? 'marking as complete' : 'marking as incomplete';
        
        // Show loading state
        Swal.fire({
            title: 'Please wait...',
            text: `Topic is being ${actionText}`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Update UI immediately
                    updateTopicStatusUI(complete);
                    
                    // Update localStorage with new stats
                    if (data.stats) {
                        localStorage.setItem('topicProgress', JSON.stringify(data.stats));
                        triggerStorageUpdate();
                    }
                    
                    // Reload the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'An error occurred. Please try again.'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Unable to connect. Please check your internet connection and try again.'
            });
        });
    }
    
    function updateTopicStatusUI(isCompleted) {
        const statusIndicator = document.querySelector('.status-indicator');
        const statusTitle = document.querySelector('.status-title');
        const statusSubtitle = document.querySelector('.status-subtitle');
        const statusActions = document.querySelector('.status-actions');
        
        if (isCompleted) {
            // Update to completed state
            statusIndicator.className = 'status-indicator completed';
            statusIndicator.querySelector('.indicator-icon').innerHTML = '<i class="fas fa-check-circle"></i>';
            statusTitle.textContent = 'Topic Completed';
            statusSubtitle.textContent = 'Completed just now';
            
            // Update button
            statusActions.innerHTML = `
                <button class="btn btn-outline-secondary btn-incomplete" data-topic-id="{{ $encryptedId }}">
                    <i class="fas fa-undo"></i>
                    <span>Mark as Incomplete</span>
                </button>
            `;
            
            // Add event listener to new button
            document.querySelector('.btn-incomplete').addEventListener('click', function() {
                const topicId = '{{ $encryptedId }}';
                markTopicComplete(topicId, false);
            });
        } else {
            // Update to in-progress state
            statusIndicator.className = 'status-indicator in-progress';
            statusIndicator.querySelector('.indicator-icon').innerHTML = '<i class="fas fa-spinner"></i>';
            statusTitle.textContent = 'Topic In Progress';
            statusSubtitle.textContent = 'Start learning to complete this topic';
            
            // Update button
            statusActions.innerHTML = `
                <button class="btn btn-success btn-complete" data-topic-id="{{ $encryptedId }}">
                    <i class="fas fa-check-circle"></i>
                    <span>Mark as Complete</span>
                </button>
            `;
            
            // Add event listener to new button
            document.querySelector('.btn-complete').addEventListener('click', function() {
                const topicId = '{{ $encryptedId }}';
                markTopicComplete(topicId, true);
            });
        }
    }
    
    function triggerStorageUpdate() {
        const event = new StorageEvent('storage', {
            key: 'topicProgress',
            newValue: localStorage.getItem('topicProgress')
        });
        window.dispatchEvent(event);
        updateCurrentPageProgress();
    }
    
    function updateCurrentPageProgress() {
        const stats = JSON.parse(localStorage.getItem('topicProgress')) || {
            totalTopics: totalTopics,
            completedTopics: initialCompletedTopics,
            progressPercentage: 0
        };
        
        document.querySelectorAll('.progress-fill').forEach(el => {
            el.style.width = stats.progressPercentage + '%';
        });
        
        document.querySelectorAll('.progress-percent').forEach(el => {
            el.textContent = stats.progressPercentage + '%';
        });
    }
    
    window.addEventListener('storage', function(e) {
        if (e.key === 'topicProgress') {
            updateCurrentPageProgress();
        }
    });
    
    console.log('All JavaScript initialized successfully');
});
</script>
@endpush
@endsection