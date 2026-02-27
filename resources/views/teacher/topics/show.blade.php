@extends('layouts.teacher')

@section('title', $topic->title . ' - Topic Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-show.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    <div class="breadcrumb">
        <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('teacher.topics.index') }}">Topics</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">{{ Str::limit($topic->title, 30) }}</span>
    </div>

    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon"><i class="fas fa-file-alt"></i></div>
                <h1 class="card-title">{{ $topic->title }}</h1>
            </div>
            <div class="top-actions">
                @if($topic->is_published)
                    <button type="button" class="top-action-btn unpublish-btn" onclick="confirmUnpublish()">
                        <i class="fas fa-eye-slash"></i> Unpublish
                    </button>
                @else
                    <button type="button" class="top-action-btn publish-btn" onclick="confirmPublish()">
                        <i class="fas fa-eye"></i> Publish
                    </button>
                @endif
                <a href="{{ route('teacher.topics.edit', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="top-action-btn delete-btn" onclick="confirmDelete()">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="{{ route('teacher.topics.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <div class="topic-preview">
                <div class="topic-preview-avatar"><i class="fas fa-file-alt"></i></div>
                <div class="topic-preview-content">
                    <h2 class="topic-preview-title">{{ $topic->title }}</h2>
                    <div class="topic-preview-meta">
                        <span class="topic-preview-badge {{ $topic->is_published ? 'published' : 'draft' }}">
                            <i class="fas {{ $topic->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                            {{ $topic->is_published ? 'Published' : 'Draft' }}
                        </span>
                        <span><i class="fas fa-hashtag"></i> ID: {{ $topic->id }}</span>
                        @if($topic->estimated_time)
                            <span><i class="fas fa-clock"></i> {{ $topic->formatted_estimated_time }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $resourceCount = ($topic->pdf_file ? 1 : 0) + ($topic->video_link ? 1 : 0) + ($topic->attachment ? 1 : 0);
                $courseCount = $topic->courses ? $topic->courses->count() : 0;
            @endphp

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-paperclip"></i></div>
                    <div class="stat-value">{{ $resourceCount }}</div>
                    <div class="stat-label">Resources</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <div class="stat-value">{{ $courseCount }}</div>
                    <div class="stat-label">Courses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-video"></i></div>
                    <div class="stat-value">{{ $topic->hasVideo() ? 'Yes' : 'No' }}</div>
                    <div class="stat-label">Has Video</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-value">{{ $topic->created_at->format('M d') }}</div>
                    <div class="stat-label">Created</div>
                </div>
            </div>

            <div class="two-column-layout">
                <div class="form-column">

                    <div class="detail-section">
                        <h3 class="detail-section-title"><i class="fas fa-align-left"></i> Description</h3>
                        <div class="description-box">{{ $topic->description ?? 'No description provided for this topic.' }}</div>
                    </div>

                    @if($resourceCount > 0)
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-paperclip"></i> Resources
                            <span style="margin-left:auto; font-size:0.75rem; color:#718096;">
                                {{ $resourceCount }} file(s)
                            </span>
                        </h3>

                        @if($topic->pdf_file)
                        <div class="resource-card">
                            <div class="resource-header">
                                <div style="display:flex; align-items:center; gap:1rem; flex:1; min-width:0;">
                                    <div class="file-icon file-pdf">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div style="min-width:0;">
                                        <div class="resource-title">PDF Document</div>
                                        <div class="resource-subtitle">
                                            <i class="fas fa-file"></i> {{ basename($topic->pdf_file) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="resource-actions">
                                    <button onclick="openPdfModal('{{ asset('pdf/' . $topic->pdf_file) }}')"
                                            class="resource-action-btn primary">
                                        <i class="fas fa-eye"></i> View PDF
                                    </button>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    @if(file_exists(public_path('pdf/' . $topic->pdf_file)))
                                        <span class="pdf-disk-badge" style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; display: inline-block;">
                                            <i class="fas fa-check-circle"></i> File exists
                                        </span>
                                    @else
                                        <span class="pdf-disk-badge" style="background: #ef4444; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; display: inline-block;">
                                            <i class="fas fa-exclamation-triangle"></i> File missing
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

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
                                <div style="display:flex; align-items:center; gap:1rem; flex:1; min-width:0;">
                                    <div class="file-icon file-video">
                                        <i class="{{ $platformIcon }}"></i>
                                    </div>
                                    <div style="min-width:0;">
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
                        </div>
                        @endif

                        @if($topic->attachment)
                        <div class="resource-card">
                            <div class="resource-header">
                                <div style="display:flex; align-items:center; gap:1rem; flex:1; min-width:0;">
                                    <div class="file-icon" style="background: rgba(102,126,234,0.1); color: #667eea;">
                                        <i class="fas fa-link"></i>
                                    </div>
                                    <div style="min-width:0;">
                                        <div class="resource-title">Attachment Link</div>
                                        <div class="resource-subtitle">
                                            <i class="fas fa-external-link-alt"></i> External Resource
                                        </div>
                                    </div>
                                </div>
                                <div class="resource-actions">
                                    <a href="{{ $topic->attachment }}" target="_blank"
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-external-link-alt"></i> Open Link
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="url-box">{{ $topic->attachment }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>

                <div class="sidebar-column">
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-info-circle"></i> Topic Information</h3>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-hashtag"></i> Topic ID</span>
                            <span class="val">#{{ $topic->id }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user"></i> Created By</span>
                            <span class="val">
                                @if($topic->creator)
                                    {{ $topic->creator->f_name }} {{ $topic->creator->l_name }}
                                @else
                                    <span style="color:#a0aec0;">System</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Created</span>
                            <span class="val">
                                {{ $topic->created_at->format('M d, Y') }}
                                <span style="display:block; font-size:0.7rem; color:#718096;">{{ $topic->created_at->diffForHumans() }}</span>
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-clock"></i> Last Updated</span>
                            <span class="val">
                                {{ $topic->updated_at->format('M d, Y') }}
                                <span style="display:block; font-size:0.7rem; color:#718096;">{{ $topic->updated_at->diffForHumans() }}</span>
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="val" style="color:{{ $topic->is_published ? '#48bb78' : '#ed8936' }}">
                                {{ $topic->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-paperclip"></i> Resources</span>
                            <span class="val">{{ $resourceCount }}</span>
                        </div>
                        
                        @if($topic->estimated_time)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-hourglass-half"></i> Est. Time</span>
                            <span class="val">{{ $topic->formatted_estimated_time }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-book"></i> Assigned Courses</h3>
                        
                        @if($courseCount > 0)
                            <div style="margin-bottom:1rem;">
                                @foreach($topic->courses as $course)
                                    <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" 
                                       class="course-tag">
                                        {{ $course->course_code }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state" style="padding:1rem;">
                                <i class="fas fa-book-open"></i>
                                <h3>No Courses Assigned</h3>
                                <p style="margin-bottom:0.5rem;">This topic is not used in any courses yet.</p>
                                <a href="{{ route('teacher.topics.edit', $encryptedId) }}" class="btn-sm btn-sm-primary" style="text-decoration:none; display:inline-block;">
                                    <i class="fas fa-plus"></i> Assign to Course
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                        
                        @if($topic->is_published)
                            <button onclick="confirmUnpublish()" class="quick-action-link">
                                <i class="fas fa-eye-slash"></i><span>Unpublish Topic</span>
                            </button>
                        @else
                            <button onclick="confirmPublish()" class="quick-action-link">
                                <i class="fas fa-eye"></i><span>Publish Topic</span>
                            </button>
                        @endif
                        
                        <a href="{{ route('teacher.topics.edit', $encryptedId) }}" class="quick-action-link">
                            <i class="fas fa-edit"></i><span>Edit Topic Details</span>
                        </a>
                        
                        <a href="{{ route('teacher.courses.index') }}" class="quick-action-link">
                            <i class="fas fa-book"></i><span>Browse Courses</span>
                        </a>
                        
                        <a href="{{ route('teacher.topics.index') }}" class="quick-action-link">
                            <i class="fas fa-list"></i><span>All Topics</span>
                        </a>
                    </div>

                    <div class="sidebar-card help-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-lightbulb"></i> Quick Tips</h3>
                        <div class="help-text">
                            <p style="margin-bottom:0.75rem;"><strong>Publishing:</strong> Students can only see published topics in their courses.</p>
                            <p style="margin-bottom:0.75rem;"><strong>Resources:</strong> PDFs, videos, and attachments enhance student learning.</p>
                            <p><strong>Courses:</strong> Topics can be assigned to multiple courses.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PDF Modal --}}
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
        <div class="modal-body" style="padding:0; overflow:hidden; background:#525659;">
            <div class="modal-loading" id="pdfLoading">
                <div class="spinner"></div>
                <p>Loading PDF...</p>
            </div>
            <div id="pdfContainer" style="width:100%; height:100%;">
                <embed id="pdfEmbed" 
                    type="application/pdf"
                    style="width:100%; height:100%; display:none;"
                    src="">
                <iframe id="pdfIframe" 
                        style="width:100%; height:100%; border:none; background:white; display:none;"
                        src=""></iframe>
                <div id="pdfFallback" style="display:none; text-align:center; padding:3rem; background:white; height:100%;">
                    <i class="fas fa-file-pdf" style="font-size:4rem; color:#dc2626; margin-bottom:1rem;"></i>
                    <h3 style="margin-bottom:1rem;">PDF Cannot Be Displayed</h3>
                    <p style="margin-bottom:1.5rem; color:#6b7280;">Your browser cannot display this PDF directly.</p>
                    <a href="#" id="downloadLink" class="btn btn-primary" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="modal-footer" id="pdfFooter"></div>
    </div>
</div>

{{-- Video Modal --}}
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

        <div id="videoIframeWrap" class="modal-body modal-body--video" style="display:none;">
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

        <div id="videoNativeWrap" class="modal-body modal-body--video" style="display:none;">
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
                <div id="videoNativeError" class="video-error-state" style="display:none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Could not load this video file.</p>
                    <p class="video-error-hint">The format may not be supported by your browser.</p>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="videoFooter"></div>
    </div>
</div>

{{-- Google Drive Modal --}}
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

<form id="publish-form" method="POST" action="{{ route('teacher.topics.publish', $encryptedId) }}" style="display:none;">
    @csrf @method('PATCH')
</form>
<form id="delete-form" method="POST" action="{{ route('teacher.topics.destroy', $encryptedId) }}" style="display:none;">
    @csrf @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ════════════════════════════════════════════════════════════════
// ROUTES
// ════════════════════════════════════════════════════════════════
const ROUTES = {
    publishTopic: '{{ route("teacher.topics.publish", $encryptedId) }}',
    deleteTopic: '{{ route("teacher.topics.destroy", $encryptedId) }}',
};

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function showNotification(message, type = 'info') {
    Swal.fire({
        toast: true, position: 'top-end', showConfirmButton: false,
        timer: 4000, timerProgressBar: true, icon: type, title: message,
        didOpen: toast => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
}

// ════════════════════════════════════════════════════════════════
// TOPIC ACTIONS
// ════════════════════════════════════════════════════════════════
function confirmPublish() {
    Swal.fire({
        title: 'Publish Topic?',
        text: 'Once published, this topic will be visible to students in all assigned courses.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#48bb78',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Publish',
        cancelButtonText: 'Cancel'
    }).then(r => { if (r.isConfirmed) document.getElementById('publish-form').submit(); });
}

function confirmUnpublish() {
    Swal.fire({
        title: 'Unpublish Topic?',
        text: 'This topic will be hidden from students until you publish it again.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f56565',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Unpublish',
        cancelButtonText: 'Cancel'
    }).then(r => { if (r.isConfirmed) document.getElementById('publish-form').submit(); });
}

function confirmDelete() {
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
    }).then(r => { if (r.isConfirmed) document.getElementById('delete-form').submit(); });
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

    if (modalBox && modalBox.classList.contains('fullscreen')) {
        modalBox.classList.remove('fullscreen');
        const icon = document.querySelector('#pdfModalBox .modal-fullscreen i');
        if (icon) {
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }

    if (loading) loading.style.display = 'flex';
    if (pdfEmbed) pdfEmbed.style.display = 'none';
    if (pdfIframe) pdfIframe.style.display = 'none';
    if (pdfFallback) pdfFallback.style.display = 'none';
    
    const timestamp = new Date().getTime();
    const separator = pdfUrl.includes('?') ? '&' : '?';
    const finalUrl = pdfUrl + separator + 't=' + timestamp;
    
    if (downloadLink) downloadLink.href = pdfUrl;
    if (footer) footer.textContent = 'Source: ' + pdfUrl;

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
                if (loading) loading.style.display = 'none';
            };
            
            pdfIframe.onerror = function() {
                if (loading) loading.style.display = 'none';
                if (pdfIframe) pdfIframe.style.display = 'none';
                if (pdfFallback) pdfFallback.style.display = 'block';
            };
        }
        
        setTimeout(function() {
            if (loading && loading.style.display !== 'none') {
                if (loading) loading.style.display = 'none';
                if (pdfIframe) pdfIframe.style.display = 'none';
                if (pdfFallback) pdfFallback.style.display = 'block';
            }
        }, 5000);
    }

    if (pdfEmbed) {
        pdfEmbed.onload = function() {
            clearTimeout(loadTimer);
            if (loading) loading.style.display = 'none';
        };
    }
    
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

function togglePdfFullscreen() {
    const modalBox = document.getElementById('pdfModalBox');
    const fullscreenIcon = document.querySelector('#pdfModalBox .modal-fullscreen i');
    
    if (!modalBox || !fullscreenIcon) return;
    
    modalBox.classList.toggle('fullscreen');
    
    if (modalBox.classList.contains('fullscreen')) {
        fullscreenIcon.classList.remove('fa-expand');
        fullscreenIcon.classList.add('fa-compress');
        
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

function closeAllModals() {
    const modalIds = ['pdfModal', 'videoModal', 'driveModal'];
    
    modalIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
        }
    });

    resetPdfElements();
    resetVideoElements();
}

function resetVideoElements() {
    const videoIframe = document.getElementById('videoIframe');
    if (videoIframe) { videoIframe.src = ''; }

    const driveIframe = document.getElementById('driveIframe');
    if (driveIframe) { driveIframe.src = ''; }

    const vid = document.getElementById('nativeVideoPlayer');
    if (vid) { 
        vid.pause(); 
        vid.src = ''; 
        vid.style.display = 'none';
        vid.load();
    }
    
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

function closeVideoModal() {
    closeModal('videoModal');
    
    const iframe = document.getElementById('videoIframe');
    if (iframe) { iframe.src = ''; }
    
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
    if (iframe) { iframe.src = ''; }
    const loading = document.getElementById('driveLoading');
    if (loading) loading.style.display = 'flex';
}

function openSmartVideoModal(url) {
    closeAllModals();

    const yt = url.match(
        /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i
    );
    if (yt) return _openEmbedPanel(`https://www.youtube.com/embed/${yt[1]}?autoplay=1&rel=0`, url, 'YouTube');

    const vimeo = url.match(
        /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/i
    );
    if (vimeo) return _openEmbedPanel(`https://player.vimeo.com/video/${vimeo[1]}?autoplay=1`, url, 'Vimeo');

    const driveId = _extractDriveId(url);
    if (driveId) return _openDrivePanel(`https://drive.google.com/file/d/${driveId}/preview`, url);

    if (url.match(/\.(mp4|webm|mov|mkv|avi|wmv|flv|ogg|ogv|3gp|m4v)(\?.*)?$/i))
        return _openNativePanel(url);

    _openNativePanel(url);
}

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
        iframe.onload = () => { if (loading) loading.style.display = 'none'; };
    }
    
    const footer = document.getElementById('videoFooter');
    if (footer) footer.textContent = 'Source: ' + sourceUrl;
}

function _openDrivePanel(embedUrl, sourceUrl) {
    openModal('driveModal');
    
    const iframe = document.getElementById('driveIframe');
    const loading = document.getElementById('driveLoading');
    const footer = document.getElementById('driveFooter');
    
    if (loading) loading.style.display = 'flex';
    if (iframe) {
        iframe.src = embedUrl;
        iframe.onload = () => { if (loading) loading.style.display = 'none'; };
        iframe.onerror = () => {
            if (loading) loading.style.display = 'none';
            showNotification('Could not load Google Drive file.', 'error');
        };
    }
    
    if (footer) footer.textContent = 'Source: ' + sourceUrl;
}

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
            vid.play().catch(e => {});
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

// Setup modal dismiss
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeAllModals();
        });
    });
    
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAllModals();
    });

    // Session notifications
    @if(session('success')) showNotification('{{ session('success') }}', 'success'); @endif
    @if(session('error'))   showNotification('{{ session('error') }}',   'error');   @endif
    @if(session('warning')) showNotification('{{ session('warning') }}', 'warning'); @endif
});
</script>
@endpush