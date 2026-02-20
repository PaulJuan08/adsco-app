@extends('layouts.student')

@section('title', $topic->title . ' - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-show.css') }}">
<!-- NO additional styles - using only topic-show.css -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="form-container">
    <!-- Card Header - Using exact classes from topic-show.css (matches course-show.css pattern) -->
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon">
                @if($topic->video_link)
                    <i class="fas fa-video"></i>
                @elseif($topic->attachment)
                    <i class="fas fa-paperclip"></i>
                @else
                    <i class="fas fa-chalkboard"></i>
                @endif
            </div>
            <h2 class="card-title">{{ $topic->title }}</h2>
        </div>
        <div class="top-actions">
            <a href="{{ route('student.courses.show', Crypt::encrypt($course->id ?? $topic->course_id)) }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back to Course
            </a>
            <a href="{{ route('student.topics.index') }}" class="top-action-btn">
                <i class="fas fa-list"></i> All Topics
            </a>
        </div>
    </div>
    
    <div class="card-body">
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
        
        <!-- Topic Preview Section - Using topic-preview from topic-show.css -->
        <div class="topic-preview">
            <div class="topic-preview-avatar">
                {{ strtoupper(substr($topic->title, 0, 1)) }}
            </div>
            <h1 class="topic-preview-title">{{ $topic->title }}</h1>
            
            <div class="topic-preview-meta">
                @php
                    $isCompleted = Auth::user()->completedTopics()
                        ->where('topic_id', $topic->id)
                        ->exists();
                @endphp
                
                @if($isCompleted)
                <span class="topic-preview-badge published">
                    <i class="fas fa-check-circle"></i> Completed
                    @if($completionDate)
                        ({{ $completionDate->format('M d, Y') }})
                    @endif
                </span>
                @else
                <span class="topic-preview-badge draft">
                    <i class="fas fa-clock"></i> In Progress
                </span>
                @endif
                
                @if($topic->estimated_time)
                <span class="topic-preview-badge" style="background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%); color: white;">
                    <i class="fas fa-hourglass-half"></i> {{ $topic->estimated_time }}
                </span>
                @endif
            </div>
            
            <div class="topic-preview-id">
                <i class="fas fa-hashtag"></i> Topic ID: {{ $topic->id }}
            </div>
        </div>
        
        <!-- Two Column Layout - Using two-column-layout from topic-show.css -->
        <div class="two-column-layout">
            <!-- Left Column - Main Content -->
            <div class="form-column">
                <!-- Description Section -->
                @if($topic->description)
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-align-left"></i> Description
                    </h3>
                    <div class="description-box">
                        {{ $topic->description }}
                    </div>
                </div>
                @endif
                
                <!-- Video Section -->
                @if($topic->video_link)
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-video"></i> Video Lesson
                    </h3>
                    
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
                    
                    <div class="resource-card">
                        <div class="resource-header">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="file-icon file-video">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div>
                                    <div class="resource-title">Video Lesson</div>
                                    <div class="resource-description">
                                        @if($embedUrl)
                                            YouTube/Vimeo Video
                                        @else
                                            External Video Link
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="resource-content">
                            @if($embedUrl)
                            <div style="position: relative; width: 100%; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: var(--radius);">
                                <iframe 
                                    src="{{ $embedUrl }}" 
                                    title="{{ $topic->title }}"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;">
                                </iframe>
                            </div>
                            @else
                            <div style="text-align: center; padding: 1.5rem;">
                                <div style="font-size: 2.5rem; color: var(--gray-400); margin-bottom: 1rem;">
                                    <i class="fas fa-external-link-alt"></i>
                                </div>
                                <p style="color: var(--gray-600); margin-bottom: 1.5rem;">
                                    This video cannot be embedded. Click the button below to watch on the original platform.
                                </p>
                                <a href="{{ $topic->video_link }}" target="_blank" class="resource-action-btn primary">
                                    <i class="fas fa-play-circle"></i> Watch on Platform
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- PDF Materials Section -->
                @if($topic->pdf_file)
                @php
                    // Get PDF URL using the helper method from Student TopicController
                    $pdfUrl = App\Http\Controllers\Student\TopicController::getPdfUrl($topic->pdf_file);
                    $pdfFilename = basename(str_replace('/storage/pdfs/', '', $topic->pdf_file));
                @endphp
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-file-pdf" style="color: var(--danger);"></i> PDF Materials
                    </h3>
                    
                    <div class="resource-card">
                        <div class="resource-header">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="file-icon file-pdf">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div>
                                    <div class="resource-title">PDF Document</div>
                                    <div class="resource-description">{{ $pdfFilename }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="resource-content">
                            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: space-between; align-items: center;">
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <span style="display: flex; align-items: center; gap: 0.5rem; font-size: var(--font-size-xs); color: var(--gray-600);">
                                        <i class="fas fa-file-pdf"></i> PDF Document
                                    </span>
                                    <span style="display: flex; align-items: center; gap: 0.5rem; font-size: var(--font-size-xs); color: var(--gray-600);">
                                        <i class="fas fa-clock"></i> Ready to View
                                    </span>
                                </div>
                                <div style="display: flex; gap: 0.75rem;">
                                    <button onclick="openPdfModal('{{ $pdfUrl }}')" class="resource-action-btn primary">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="{{ $pdfUrl }}" download class="resource-action-btn secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Learning Materials Section -->
                @if($topic->attachment)
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-paperclip"></i> Learning Materials
                    </h3>
                    
                    @php
                        $icon = 'fas fa-file';
                        $color = 'file-generic';
                        
                        if (str_contains($topic->attachment, '.pdf')) {
                            $icon = 'fas fa-file-pdf';
                            $color = 'file-pdf';
                        } elseif (str_contains($topic->attachment, '.doc') || str_contains($topic->attachment, '.docx')) {
                            $icon = 'fas fa-file-word';
                            $color = 'file-word';
                        } elseif (str_contains($topic->attachment, '.xls') || str_contains($topic->attachment, '.xlsx')) {
                            $icon = 'fas fa-file-excel';
                            $color = 'file-excel';
                        } elseif (str_contains($topic->attachment, '.ppt') || str_contains($topic->attachment, '.pptx')) {
                            $icon = 'fas fa-file-powerpoint';
                            $color = 'file-powerpoint';
                        } elseif (str_contains($topic->attachment, '.jpg') || str_contains($topic->attachment, '.jpeg') || 
                                  str_contains($topic->attachment, '.png') || str_contains($topic->attachment, '.gif')) {
                            $icon = 'fas fa-file-image';
                            $color = 'file-image';
                        }
                    @endphp
                    
                    <div class="resource-card">
                        <div class="resource-header">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="file-icon {{ $color }}">
                                    <i class="{{ $icon }}"></i>
                                </div>
                                <div>
                                    <div class="resource-title">Topic Materials</div>
                                    <div class="resource-description">Supplementary resources for this topic</div>
                                </div>
                            </div>
                        </div>
                        <div class="resource-content">
                            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: space-between; align-items: center;">
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <span style="display: flex; align-items: center; gap: 0.5rem; font-size: var(--font-size-xs); color: var(--gray-600);">
                                        <i class="{{ $icon }}"></i> {{ strtoupper(pathinfo($topic->attachment, PATHINFO_EXTENSION)) }} File
                                    </span>
                                    <span style="display: flex; align-items: center; gap: 0.5rem; font-size: var(--font-size-xs); color: var(--gray-600);">
                                        <i class="fas fa-download"></i> Download Available
                                    </span>
                                </div>
                                <div style="display: flex; gap: 0.75rem;">
                                    <a href="{{ $topic->attachment }}" target="_blank" class="resource-action-btn secondary">
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                    <a href="{{ $topic->attachment }}" download class="resource-action-btn primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Right Column - Sidebar -->
            <div class="sidebar-column">
                <!-- Course Information Card -->
                @if(isset($course))
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-book"></i> Course
                    </h3>
                    <a href="{{ route('student.courses.show', Crypt::encrypt($course->id)) }}" class="course-tag">
                        {{ $course->course_code }}: {{ Str::limit($course->title, 30) }}
                    </a>
                </div>
                @endif
                
                <!-- Status & Progress Card -->
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-chart-line"></i> Your Progress
                    </h3>
                    
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-flag"></i> Status
                        </span>
                        <span class="info-value">
                            @if($isCompleted)
                                <span style="color: var(--success);">Completed</span>
                            @else
                                <span style="color: var(--primary);">In Progress</span>
                            @endif
                        </span>
                    </div>
                    
                    @if($isCompleted && isset($completionDate))
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-calendar"></i> Completed On
                        </span>
                        <span class="info-value">{{ $completionDate->format('M d, Y') }}</span>
                    </div>
                    @endif
                    
                    <div style="margin-top: 1.5rem;">
                        @if(!$isCompleted)
                        <button class="btn btn-primary" style="width: 100%;" onclick="markTopicComplete('{{ $encryptedId }}', true)">
                            <i class="fas fa-check-circle"></i> Mark as Complete
                        </button>
                        @else
                        <button class="btn btn-outline-secondary" style="width: 100%;" onclick="markTopicComplete('{{ $encryptedId }}', false)">
                            <i class="fas fa-undo"></i> Mark as Incomplete
                        </button>
                        @endif
                    </div>
                </div>
                
                <!-- Topic Info Card -->
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-info-circle"></i> Topic Info
                    </h3>
                    
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-hashtag"></i> ID
                        </span>
                        <span class="info-value">{{ $topic->id }}</span>
                    </div>
                    
                    @if($topic->estimated_time)
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-hourglass-half"></i> Est. Time
                        </span>
                        <span class="info-value">{{ $topic->estimated_time }}</span>
                    </div>
                    @endif
                    
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-calendar-plus"></i> Created
                        </span>
                        <span class="info-value">{{ $topic->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    @if($topic->updated_at != $topic->created_at)
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-calendar-check"></i> Updated
                        </span>
                        <span class="info-value">{{ $topic->updated_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PDF Modal - Using modal styles from topic-show.css -->
<div class="pdf-modal-overlay" id="pdfModal">
    <div class="pdf-modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-file-pdf"></i> PDF Preview</h3>
            <button class="modal-close" id="closePdfModal">&times;</button>
        </div>
        <div style="flex: 1; position: relative; background: var(--gray-100);">
            <iframe id="pdfIframe" style="width: 100%; height: 100%; border: none;"></iframe>
            <div id="pdfLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; display: none;">
                <div class="loading-spinner" style="width: 40px; height: 40px; border: 4px solid rgba(102,126,234,0.2); border-top: 4px solid var(--primary); border-radius: 50%; margin: 0 auto 1rem;"></div>
                <p style="color: var(--gray-600);">Loading PDF...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ========== GLOBAL PDF MODAL FUNCTIONS ==========
function openPdfModal(pdfUrl) {
    const modal = document.getElementById('pdfModal');
    const iframe = document.getElementById('pdfIframe');
    const loading = document.getElementById('pdfLoading');
    
    if (!modal || !iframe) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'PDF preview is not available. Please try downloading the file.'
        });
        return;
    }
    
    modal.classList.add('active');
    if (loading) loading.style.display = 'block';
    
    iframe.src = pdfUrl;
    
    iframe.onload = () => {
        if (loading) loading.style.display = 'none';
    };
    
    iframe.onerror = () => {
        if (loading) loading.style.display = 'none';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load PDF. Please try downloading the file instead.'
        });
        closePdfModal();
    };
}

function closePdfModal() {
    const modal = document.getElementById('pdfModal');
    const iframe = document.getElementById('pdfIframe');
    const loading = document.getElementById('pdfLoading');
    
    if (modal) modal.classList.remove('active');
    if (iframe) iframe.src = '';
    if (loading) loading.style.display = 'none';
}

// ========== TOPIC COMPLETION ==========
function markTopicComplete(topicId, complete) {
    const url = complete 
        ? `/student/topics/${topicId}/complete`
        : `/student/topics/${topicId}/incomplete`;
    
    const actionText = complete ? 'marking as complete' : 'marking as incomplete';
    
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
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
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

// ========== DOM CONTENT LOADED ==========
document.addEventListener('DOMContentLoaded', function() {
    // PDF Modal Event Listeners
    const closeBtn = document.getElementById('closePdfModal');
    if (closeBtn) {
        closeBtn.addEventListener('click', closePdfModal);
    }
    
    const modal = document.getElementById('pdfModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePdfModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('pdfModal');
            if (modal && modal.classList.contains('active')) {
                closePdfModal();
            }
        }
    });
});
</script>
@endpush
@endsection