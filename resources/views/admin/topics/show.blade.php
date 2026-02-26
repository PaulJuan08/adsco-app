@extends('layouts.admin')

@section('title', 'Topic Details - ' . $topic->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-show.css') }}">
@endpush

@section('content')
    <div class="form-container">

        {{-- ── CARD HEADER ── --}}
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-file-alt card-icon"></i>
                <h2 class="card-title">Topic Details</h2>
            </div>
            <div class="top-actions">
                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}"
                      method="POST" id="deleteForm" class="inline-form">
                    @csrf @method('DELETE')
                    <button type="submit" class="top-action-btn delete-btn" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                <a href="{{ route('admin.topics.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">

            {{-- ── TOPIC PREVIEW BANNER ── --}}
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

            {{-- ── ALERTS ── --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="two-column-layout">

                {{-- ══ LEFT COLUMN ══ --}}
                <div class="form-column">

                    {{-- Description --}}
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-align-left"></i> Topic Description
                        </div>
                        <div class="description-box">
                            {{ $topic->description ?: 'No description provided for this topic.' }}
                        </div>
                    </div>

                    {{-- Resources: PDF + Video only --}}
                    @if($topic->pdf_file || $topic->video_link)
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-paperclip"></i> Resources
                        </div>

                        {{-- ════ PDF FILE ════ --}}
                        @if($topic->pdf_file)
                        @php
                            $pdfUrl = App\Http\Controllers\Admin\TopicController::getPdfUrl($topic->pdf_file);
                            $pdfFilename = $topic->pdf_file;
                        @endphp
                        <div class="resource-card">
                            <div class="resource-header">
                                <div class="resource-meta">
                                    <div class="file-icon file-pdf">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">PDF Document</div>
                                        <div class="resource-subtitle">
                                            <i class="fas fa-file"></i> {{ $pdfFilename }}
                                        </div>
                                    </div>
                                </div>
                                <div class="resource-actions">
                                    <button onclick="openPdfModal('{{ $pdfUrl }}')"
                                            class="resource-action-btn primary">
                                        <i class="fas fa-eye"></i> View PDF
                                    </button>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <span>File stored in: <code>public/pdf/{{ $pdfFilename }}</code></span>
                                        @if(file_exists(public_path('pdf/' . $pdfFilename)))
                                            <span class="pdf-disk-badge" style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem;">
                                                <i class="fas fa-check-circle"></i> File exists
                                            </span>
                                        @else
                                            <span class="pdf-disk-badge" style="background: #ef4444; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem;">
                                                <i class="fas fa-exclamation-triangle"></i> File missing
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ════ VIDEO LINK ════ --}}
                        @if($topic->video_link)
                        @php
                            $vUrl = $topic->video_link;
                            if (str_contains($vUrl, 'youtube.com') || str_contains($vUrl, 'youtu.be')) {
                                $platformLabel = 'YouTube';
                                $platformIcon  = 'fab fa-youtube';
                            } elseif (str_contains($vUrl, 'vimeo.com')) {
                                $platformLabel = 'Vimeo';
                                $platformIcon  = 'fab fa-vimeo-v';
                            } elseif (str_contains($vUrl, 'drive.google.com')) {
                                $platformLabel = 'Google Drive';
                                $platformIcon  = 'fab fa-google-drive';
                            } else {
                                $host          = parse_url($vUrl, PHP_URL_HOST);
                                $platformLabel = $host ? str_replace('www.', '', $host) : 'Video Link';
                                $platformIcon  = 'fas fa-video';
                            }
                        @endphp
                        <div class="resource-card">
                            <div class="resource-header">
                                <div class="resource-meta">
                                    <div class="file-icon file-video">
                                        <i class="{{ $platformIcon }}"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">Video Content</div>
                                        <div class="resource-subtitle">
                                            <i class="{{ $platformIcon }}"></i> {{ $platformLabel }}
                                        </div>
                                    </div>
                                </div>
                                <div class="resource-actions">
                                    <button onclick="openSmartVideoModal('{{ $vUrl }}')"
                                            class="resource-action-btn primary">
                                        <i class="fas fa-play"></i> Play Video
                                    </button>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    Click "Play Video" to watch in modal
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                    @endif

                    {{-- Publish button (drafts only) --}}
                    @if(!$topic->is_published)
                    <div class="publish-section">
                        <form action="{{ route('admin.topics.publish', Crypt::encrypt($topic->id)) }}"
                              method="POST" id="publishForm" class="inline-form">
                            @csrf
                            <button type="submit" class="publish-btn" id="publishButton">
                                <i class="fas fa-upload"></i> Publish Topic
                            </button>
                        </form>
                    </div>
                    @endif

                </div>{{-- /form-column --}}

                {{-- ══ RIGHT SIDEBAR ══ --}}
                <div class="sidebar-column">

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
                            <div class="info-value-wrapper">
                                <span class="info-value">{{ $topic->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div class="info-value-wrapper">
                                <span class="info-value">{{ $topic->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="info-value">
                                @if($topic->is_published)
                                    <button type="button" 
                                            class="top-action-btn unpublish-btn"
                                            onclick="confirmUnpublish('{{ Crypt::encrypt($topic->id) }}')">
                                        <i class="fas fa-eye-slash"></i> Unpublish
                                    </button>
                                @else
                                    <button type="button" 
                                            class="top-action-btn publish-btn"
                                            onclick="confirmPublish('{{ Crypt::encrypt($topic->id) }}')">
                                        <i class="fas fa-eye"></i> Publish
                                    </button>
                                @endif
                            </span>
                        </div>
                        @php
                            $resourceCount = ($topic->pdf_file ? 1 : 0) + ($topic->video_link ? 1 : 0);
                        @endphp
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-paperclip"></i> Resources</span>
                            <span class="info-value">{{ $resourceCount }} file(s)</span>
                        </div>
                        @if($topic->pdf_file)
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hdd"></i> PDF Storage</span>
                            <span class="info-value pdf-disk-badge">pdf_disk</span>
                        </div>
                        @endif
                    </div>

                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-book"></i> Assigned Courses
                        </div>
                        @if($topic->courses && $topic->courses->count() > 0)
                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-layer-group"></i> Total Courses
                                </span>
                                <span class="course-count">
                                    {{ $topic->courses->count() }}
                                </span>
                            </div>
                            <div class="assigned-courses">
                                @foreach($topic->courses as $course)
                                    <a href="{{ route('admin.courses.show', Crypt::encrypt($course->id)) }}"
                                       class="course-tag">
                                        {{ $course->course_code }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-book-open"></i>
                                <h3>No Courses Assigned</h3>
                                <p>This topic is not used in any courses yet.</p>
                                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}"
                                   class="btn">
                                    <i class="fas fa-plus"></i> Assign to Course
                                </a>
                            </div>
                        @endif
                    </div>

                </div>{{-- /sidebar-column --}}
            </div>{{-- /two-column-layout --}}
        </div>{{-- /card-body --}}
    </div>{{-- /form-container --}}


    {{-- ══════════════════════════════════════════════════════════════
         MODALS  —  PDF + VIDEO only with fullscreen toggle
    ══════════════════════════════════════════════════════════════════ --}}

    {{-- PDF Modal with fullscreen toggle --}}
    <div class="modal-overlay" id="pdfModal">
        <div class="modal-box modal-box--pdf" id="pdfModalBox">
            <div class="modal-header modal-header--pdf">
                <span class="modal-header__title">
                    <i class="fas fa-file-pdf"></i>
                    <span>PDF Viewer</span>
                </span>
                <div class="modal-header-actions">
                    <button class="modal-fullscreen" onclick="togglePdfFullscreen()" title="Toggle fullscreen">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="modal-close" onclick="closePdfModal()" title="Close">&times;</button>
                </div>
            </div>
            <div class="modal-body" style="padding: 0; overflow: hidden; background: #525659;">
                <div class="modal-loading" id="pdfLoading">
                    <div class="spinner"></div>
                    <p>Loading PDF...</p>
                </div>
                <div id="pdfContainer" style="width: 100%; height: 100%;">
                    <embed id="pdfEmbed" 
                        type="application/pdf"
                        style="width: 100%; height: 100%; display: none;"
                        src="">
                    <iframe id="pdfIframe" 
                            style="width: 100%; height: 100%; border: none; background: white; display: none;"
                            src=""></iframe>
                    <div id="pdfFallback" style="display: none; text-align: center; padding: 3rem; background: white; height: 100%;">
                        <i class="fas fa-file-pdf" style="font-size: 4rem; color: #dc2626; margin-bottom: 1rem;"></i>
                        <h3 style="margin-bottom: 1rem;">PDF Cannot Be Displayed</h3>
                        <p style="margin-bottom: 1.5rem; color: #6b7280;">Your browser cannot display this PDF directly.</p>
                        <a href="#" id="downloadLink" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="pdfFooter"></div>
        </div>
    </div>

    {{-- Video Modal - NO fullscreen toggle --}}
    <div class="modal-overlay" id="videoModal">
        <div class="modal-box modal-box--video" id="videoModalBox">
            <div class="modal-header modal-header--video">
                <span class="modal-header__title" id="videoModalTitle">
                    <i class="fas fa-play-circle"></i>
                    <span>Video Player</span>
                </span>
                <div class="modal-header-actions">
                    <button class="modal-close" onclick="closeVideoModal()" title="Close">&times;</button>
                </div>
            </div>

            {{-- Iframe embed (YouTube, Vimeo) --}}
            <div id="videoIframeWrap" class="modal-body modal-body--video" style="display: none;">
                <div class="modal-loading video-dark-loading" id="videoIframeLoading">
                    <div class="spinner"></div>
                    <p>Loading video player...</p>
                </div>
                <iframe id="videoIframe"
                        class="video-ratio-iframe"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                        allowfullscreen
                        src=""></iframe>
            </div>

            {{-- Native video player --}}
            <div id="videoNativeWrap" class="modal-body modal-body--video" style="display: none;">
                <div class="modal-loading video-dark-loading" id="videoNativeLoading">
                    <div class="spinner"></div>
                    <p>Loading video...</p>
                </div>
                <div class="native-video-wrap">
                    <video id="nativeVideoPlayer"
                        controls
                        controlslist="nodownload"
                        preload="metadata"
                        class="native-video-player">
                        Your browser does not support the video tag.
                    </video>
                    <div id="videoNativeError" class="video-error-state" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Could not load this video file.</p>
                        <p class="video-error-hint">
                            The format may not be supported by your browser.
                        </p>
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="videoFooter"></div>
        </div>
    </div>

    {{-- Google Drive Modal - NO fullscreen toggle --}}
    <div class="modal-overlay" id="driveModal">
        <div class="modal-box modal-box--drive" id="driveModalBox">
            <div class="modal-header modal-header--video">
                <span class="modal-header__title">
                    <i class="fab fa-google-drive"></i>
                    <span>Google Drive Viewer</span>
                </span>
                <div class="modal-header-actions">
                    <button class="modal-close" onclick="closeDriveModal()" title="Close">&times;</button>
                </div>
            </div>
            <div class="modal-body modal-body--dark">
                <div class="modal-loading video-dark-loading" id="driveLoading">
                    <div class="spinner"></div>
                    <p>Loading file from Google Drive...</p>
                </div>
                <iframe id="driveIframe" 
                        class="modal-iframe"
                        src=""
                        allow="autoplay; fullscreen"
                        allowfullscreen></iframe>
            </div>
            <div class="modal-footer" id="driveFooter"></div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ════════════════════════════════════════════════════════════════
// DOM READY
// ════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    // Publish confirm
    const publishButton = document.getElementById('publishButton');
    if (publishButton) {
        publishButton.addEventListener('click', function (e) {
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
            }).then(result => {
                if (result.isConfirmed) {
                    publishButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing…';
                    publishButton.disabled = true;
                    document.getElementById('publishForm').submit();
                }
            });
        });
    }

    // Delete confirm
    const deleteButton = document.getElementById('deleteButton');
    if (deleteButton) {
        deleteButton.addEventListener('click', function (e) {
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
            }).then(result => {
                if (result.isConfirmed) {
                    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting…';
                    deleteButton.disabled = true;
                    document.getElementById('deleteForm').submit();
                }
            });
        });
    }

    // Session flash toasts
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    setupModalDismiss();
});

// ════════════════════════════════════════════════════════════════
// TOAST NOTIFICATION
// ════════════════════════════════════════════════════════════════
function showNotification(message, type = 'info') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        icon: type,
        title: message,
        didOpen: toast => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
}

// ════════════════════════════════════════════════════════════════
// MODAL HELPERS
// ════════════════════════════════════════════════════════════════
function openModal(id) { 
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeModal(id) { 
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'none';
    }
}

function setupModalDismiss() {
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeAllModals();
        });
    });
    
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAllModals();
    });
}

function closeAllModals() {
    // Close all modals
    const modalIds = ['pdfModal', 'videoModal', 'driveModal'];
    
    modalIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
        }
    });

    // Reset PDF elements
    resetPdfElements();
    
    // Reset video elements
    resetVideoElements();
}

function resetPdfElements() {
    const pdfEmbed = document.getElementById('pdfEmbed');
    const pdfIframe = document.getElementById('pdfIframe');
    const pdfFallback = document.getElementById('pdfFallback');
    const pdfLoading = document.getElementById('pdfLoading');
    const modalBox = document.getElementById('pdfModalBox');
    
    if (pdfEmbed) {
        pdfEmbed.src = '';
        pdfEmbed.style.display = 'none';
    }
    if (pdfIframe) {
        pdfIframe.src = '';
        pdfIframe.style.display = 'none';
    }
    if (pdfFallback) {
        pdfFallback.style.display = 'none';
    }
    if (pdfLoading) {
        pdfLoading.style.display = 'flex';
    }
    if (modalBox && modalBox.classList.contains('fullscreen')) {
        modalBox.classList.remove('fullscreen');
        const icon = document.querySelector('#pdfModalBox .modal-fullscreen i');
        if (icon) {
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }
}

function resetVideoElements() {
    // Reset video iframe
    const videoIframe = document.getElementById('videoIframe');
    if (videoIframe) { 
        videoIframe.src = ''; 
    }

    // Reset Drive iframe
    const driveIframe = document.getElementById('driveIframe');
    if (driveIframe) { 
        driveIframe.src = ''; 
    }

    // Reset native video player
    const vid = document.getElementById('nativeVideoPlayer');
    if (vid) { 
        vid.pause(); 
        vid.src = ''; 
        vid.style.display = 'none';
        vid.load();
    }
    
    // Reset video displays
    const videoIframeWrap = document.getElementById('videoIframeWrap');
    const videoNativeWrap = document.getElementById('videoNativeWrap');
    const videoNativeError = document.getElementById('videoNativeError');
    const videoIframeLoading = document.getElementById('videoIframeLoading');
    const videoNativeLoading = document.getElementById('videoNativeLoading');
    
    if (videoIframeWrap) videoIframeWrap.style.display = 'none';
    if (videoNativeWrap) videoNativeWrap.style.display = 'none';
    if (videoNativeError) videoNativeError.style.display = 'none';
    if (videoIframeLoading) videoIframeLoading.style.display = 'none';
    if (videoNativeLoading) videoNativeLoading.style.display = 'flex';
}

// ════════════════════════════════════════════════════════════════
// PDF MODAL
// ════════════════════════════════════════════════════════════════
function openPdfModal(pdfUrl) {
    console.log('Opening PDF URL:', pdfUrl);
    
    closeAllModals();
    openModal('pdfModal');

    const pdfEmbed = document.getElementById('pdfEmbed');
    const pdfIframe = document.getElementById('pdfIframe');
    const loading = document.getElementById('pdfLoading');
    const footer = document.getElementById('pdfFooter');
    const modalBox = document.getElementById('pdfModalBox');
    const downloadLink = document.getElementById('downloadLink');
    const pdfFallback = document.getElementById('pdfFallback');

    // Reset any previous fullscreen state
    if (modalBox && modalBox.classList.contains('fullscreen')) {
        modalBox.classList.remove('fullscreen');
        const icon = document.querySelector('#pdfModalBox .modal-fullscreen i');
        if (icon) {
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }

    // Reset displays
    if (loading) loading.style.display = 'flex';
    if (pdfEmbed) pdfEmbed.style.display = 'none';
    if (pdfIframe) pdfIframe.style.display = 'none';
    if (pdfFallback) pdfFallback.style.display = 'none';
    
    // Add timestamp to prevent caching
    const timestamp = new Date().getTime();
    const separator = pdfUrl.includes('?') ? '&' : '?';
    const finalUrl = pdfUrl + separator + 't=' + timestamp;
    
    // Set download link
    if (downloadLink) downloadLink.href = pdfUrl;
    if (footer) footer.textContent = 'Source: ' + pdfUrl;

    // Try embed tag first (best PDF support)
    if (pdfEmbed) {
        pdfEmbed.src = finalUrl;
        pdfEmbed.style.display = 'block';
    }
    
    let loadTimer = setTimeout(function() {
        if (loading && loading.style.display !== 'none') {
            console.warn('PDF load timeout - trying iframe');
            tryFallback();
        }
    }, 5000);

    function tryFallback() {
        clearTimeout(loadTimer);
        if (pdfEmbed) pdfEmbed.style.display = 'none';
        if (pdfIframe) {
            pdfIframe.src = finalUrl;
            pdfIframe.style.display = 'block';
        }
        
        if (pdfIframe) {
            pdfIframe.onload = function() {
                console.log('PDF iframe loaded');
                if (loading) loading.style.display = 'none';
            };
            
            pdfIframe.onerror = function() {
                console.log('PDF iframe failed');
                if (loading) loading.style.display = 'none';
                if (pdfIframe) pdfIframe.style.display = 'none';
                if (pdfFallback) pdfFallback.style.display = 'block';
            };
        }
        
        // Final timeout for iframe
        setTimeout(function() {
            if (loading && loading.style.display !== 'none') {
                if (loading) loading.style.display = 'none';
                if (pdfIframe) pdfIframe.style.display = 'none';
                if (pdfFallback) pdfFallback.style.display = 'block';
            }
        }, 5000);
    }

    // Handle embed load
    if (pdfEmbed) {
        pdfEmbed.onload = function() {
            console.log('PDF embed loaded');
            clearTimeout(loadTimer);
            if (loading) loading.style.display = 'none';
        };
    }
    
    // Check after delay
    setTimeout(function() {
        if (loading && loading.style.display !== 'none') {
            tryFallback();
        }
    }, 2000);
}

function closePdfModal() {
    closeModal('pdfModal');
    resetPdfElements();
}

// ════════════════════════════════════════════════════════════════
// PDF FULLSCREEN TOGGLE
// ════════════════════════════════════════════════════════════════
function togglePdfFullscreen() {
    const modalBox = document.getElementById('pdfModalBox');
    const fullscreenIcon = document.querySelector('#pdfModalBox .modal-fullscreen i');
    
    if (!modalBox || !fullscreenIcon) return;
    
    modalBox.classList.toggle('fullscreen');
    
    if (modalBox.classList.contains('fullscreen')) {
        fullscreenIcon.classList.remove('fa-expand');
        fullscreenIcon.classList.add('fa-compress');
        
        // Force resize of PDF elements
        const pdfEmbed = document.getElementById('pdfEmbed');
        const pdfIframe = document.getElementById('pdfIframe');
        if (pdfEmbed) {
            pdfEmbed.style.height = window.innerHeight - 120 + 'px';
        }
        if (pdfIframe) {
            pdfIframe.style.height = window.innerHeight - 120 + 'px';
        }
    } else {
        fullscreenIcon.classList.remove('fa-compress');
        fullscreenIcon.classList.add('fa-expand');
        
        // Reset size
        const pdfEmbed = document.getElementById('pdfEmbed');
        const pdfIframe = document.getElementById('pdfIframe');
        if (pdfEmbed) {
            pdfEmbed.style.height = '100%';
        }
        if (pdfIframe) {
            pdfIframe.style.height = '100%';
        }
    }
}

// ════════════════════════════════════════════════════════════════
// VIDEO MODAL FUNCTIONS
// ════════════════════════════════════════════════════════════════
function closeVideoModal() {
    closeModal('videoModal');
    
    const iframe = document.getElementById('videoIframe');
    if (iframe) {
        iframe.src = '';
    }
    
    const vid = document.getElementById('nativeVideoPlayer');
    if (vid) {
        vid.pause();
        vid.src = '';
        vid.style.display = 'none';
        vid.load();
    }
    
    const videoNativeError = document.getElementById('videoNativeError');
    const videoNativeLoading = document.getElementById('videoNativeLoading');
    const videoIframeLoading = document.getElementById('videoIframeLoading');
    const videoIframeWrap = document.getElementById('videoIframeWrap');
    const videoNativeWrap = document.getElementById('videoNativeWrap');
    
    if (videoNativeError) videoNativeError.style.display = 'none';
    if (videoNativeLoading) videoNativeLoading.style.display = 'flex';
    if (videoIframeLoading) videoIframeLoading.style.display = 'flex';
    if (videoIframeWrap) videoIframeWrap.style.display = 'none';
    if (videoNativeWrap) videoNativeWrap.style.display = 'none';
}

function closeDriveModal() {
    closeModal('driveModal');
    const iframe = document.getElementById('driveIframe');
    if (iframe) {
        iframe.src = '';
    }
    const loading = document.getElementById('driveLoading');
    if (loading) loading.style.display = 'flex';
}

// ════════════════════════════════════════════════════════════════
// SMART VIDEO ROUTER
// ════════════════════════════════════════════════════════════════
function openSmartVideoModal(url) {
    closeAllModals();

    // 1. YouTube
    const yt = url.match(
        /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i
    );
    if (yt) return _openEmbedPanel(`https://www.youtube.com/embed/${yt[1]}?autoplay=1&rel=0`, url, 'YouTube');

    // 2. Vimeo
    const vimeo = url.match(
        /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/i
    );
    if (vimeo) return _openEmbedPanel(`https://player.vimeo.com/video/${vimeo[1]}?autoplay=1`, url, 'Vimeo');

    // 3. Google Drive
    const driveId = _extractDriveId(url);
    if (driveId) return _openDrivePanel(`https://drive.google.com/file/d/${driveId}/preview`, url);

    // 4. Direct video file
    if (url.match(/\.(mp4|webm|mov|mkv|avi|wmv|flv|ogg|ogv|3gp|m4v)(\?.*)?$/i))
        return _openNativePanel(url);

    // 5. Unknown — try native
    _openNativePanel(url);
}

// Embed iframe panel (YouTube / Vimeo)
function _openEmbedPanel(embedUrl, sourceUrl, label) {
    openModal('videoModal');
    
    const titleElement = document.getElementById('videoModalTitle');
    if (titleElement) {
        titleElement.innerHTML = `<i class="fas fa-play-circle"></i> <span>${label} Player</span>`;
    }
    
    const iframeWrap = document.getElementById('videoIframeWrap');
    const nativeWrap = document.getElementById('videoNativeWrap');
    
    if (iframeWrap) iframeWrap.style.display = 'block';
    if (nativeWrap) nativeWrap.style.display = 'none';

    const iframe = document.getElementById('videoIframe');
    const loading = document.getElementById('videoIframeLoading');
    
    if (loading) loading.style.display = 'flex';
    if (iframe) {
        iframe.src = embedUrl;
        iframe.onload = () => { 
            if (loading) loading.style.display = 'none'; 
        };
    }
    
    const footer = document.getElementById('videoFooter');
    if (footer) footer.textContent = 'Source: ' + sourceUrl;
}

// Google Drive iframe panel
function _openDrivePanel(embedUrl, sourceUrl) {
    openModal('driveModal');
    
    const iframe = document.getElementById('driveIframe');
    const loading = document.getElementById('driveLoading');
    const footer = document.getElementById('driveFooter');
    
    if (loading) loading.style.display = 'flex';
    if (iframe) {
        iframe.src = embedUrl;
        iframe.onload = () => { 
            if (loading) loading.style.display = 'none'; 
        };
        iframe.onerror = () => {
            if (loading) loading.style.display = 'none';
            showNotification('Could not load Google Drive file.', 'error');
        };
    }
    
    if (footer) footer.textContent = 'Source: ' + sourceUrl;
}

// Native <video> panel
function _openNativePanel(url) {
    openModal('videoModal');
    
    const titleElement = document.getElementById('videoModalTitle');
    if (titleElement) {
        titleElement.innerHTML = '<i class="fas fa-film"></i> <span>Video Player</span>';
    }
    
    const iframeWrap = document.getElementById('videoIframeWrap');
    const nativeWrap = document.getElementById('videoNativeWrap');
    
    if (iframeWrap) iframeWrap.style.display = 'none';
    if (nativeWrap) nativeWrap.style.display = 'block';

    const vid = document.getElementById('nativeVideoPlayer');
    const loading = document.getElementById('videoNativeLoading');
    const errBox = document.getElementById('videoNativeError');
    const footer = document.getElementById('videoFooter');

    if (vid) {
        vid.style.display = 'none';
        vid.src = '';
    }
    if (errBox) errBox.style.display = 'none';
    if (loading) loading.style.display = 'flex';
    
    if (vid) {
        vid.src = url;
        vid.load();

        vid.onloadedmetadata = () => { 
            if (loading) loading.style.display = 'none'; 
            vid.style.display = 'block'; 
            
            // Try to autoplay
            vid.play().catch(e => {
                console.log('Autoplay prevented:', e);
            });
        };
        
        vid.oncanplay = () => { 
            if (loading) loading.style.display = 'none'; 
            vid.style.display = 'block'; 
        };
        
        vid.onerror = () => { 
            if (loading) loading.style.display = 'none'; 
            if (errBox) errBox.style.display = 'flex'; 
            vid.style.display = 'none';
        };
    }

    if (footer) footer.textContent = 'Source: ' + url;
}

// Drive ID extractor
function _extractDriveId(url) {
    const patterns = [
        /\/file\/d\/([^\/?#&]+)/,
        /[?&]id=([^&]+)/,
        /\/open\?id=([^&]+)/,
        /\/d\/([^\/?#&]+)/
    ];
    
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match) return match[1];
    }
    
    return null;
}

// ============ PUBLISH/UNPUBLISH FUNCTIONS ============
function confirmPublish(encryptedId) {
    Swal.fire({
        title: 'Publish Topic?',
        text: 'Once published, this topic will be visible to students in all assigned courses.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#48bb78',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Publish',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/topics/${encryptedId}/publish`;
            form.style.display = 'none';
            
            const csrf = document.createElement('input');
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.name = '_method';
            method.value = 'PATCH';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmUnpublish(encryptedId) {
    Swal.fire({
        title: 'Unpublish Topic?',
        text: 'This topic will be hidden from students until you publish it again.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f56565',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Unpublish',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/topics/${encryptedId}/publish`;
            form.style.display = 'none';
            
            const csrf = document.createElement('input');
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.name = '_method';
            method.value = 'PATCH';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush