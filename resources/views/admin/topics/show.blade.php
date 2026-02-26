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
                      method="POST" id="deleteForm" style="display:inline;">
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
                            $pdfUrl      = App\Http\Controllers\Admin\TopicController::getPdfUrl($topic->pdf_file);
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
                                    <a href="{{ $pdfUrl }}" download
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    Stored via <code>pdf_disk</code> &rarr;
                                    <code>public/pdf/{{ $pdfFilename }}</code>
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
                                    <a href="{{ $vUrl }}" target="_blank"
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-external-link-alt"></i> Open Link
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    <div style="color:#4a5568;margin-bottom:.4rem;">Video URL:</div>
                                    <div class="url-box">{{ $vUrl }}</div>
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
                              method="POST" id="publishForm" style="display:inline-block;">
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
                            <div style="text-align:right;">
                                <span class="info-value">{{ $topic->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align:right;">
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
                            <div style="margin-bottom:.75rem;">
                                <span class="info-label">
                                    <i class="fas fa-layer-group"></i> Total Courses
                                </span>
                                <span class="info-value"
                                      style="display:block;margin-top:.25rem;font-size:1.25rem;">
                                    {{ $topic->courses->count() }}
                                </span>
                            </div>
                            <div style="margin-top:.5rem;">
                                @foreach($topic->courses as $course)
                                    <a href="{{ route('admin.courses.show', Crypt::encrypt($course->id)) }}"
                                       class="course-tag">
                                        {{ $course->course_code }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state" style="padding:1.5rem .5rem;">
                                <i class="fas fa-book-open" style="font-size:2rem;"></i>
                                <h3 style="margin-top:.5rem;">No Courses Assigned</h3>
                                <p style="font-size:.75rem;">This topic is not used in any courses yet.</p>
                                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}"
                                   style="display:inline-block;margin-top:.75rem;padding:.5rem 1rem;
                                          background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
                                          color:white;border-radius:8px;text-decoration:none;
                                          font-size:.75rem;font-weight:600;">
                                    <i class="fas fa-plus" style="margin-right:.375rem;"></i>
                                    Assign to Course
                                </a>
                            </div>
                        @endif
                    </div>

                </div>{{-- /sidebar-column --}}
            </div>{{-- /two-column-layout --}}
        </div>{{-- /card-body --}}
    </div>{{-- /form-container --}}


    {{-- ══════════════════════════════════════════════════════════════
         MODALS  —  PDF + VIDEO only
    ══════════════════════════════════════════════════════════════════ --}}

    {{-- PDF Modal --}}
    <div class="modal-overlay" id="pdfModal">
        <div class="modal-box modal-box--pdf">
            <div class="modal-header modal-header--pdf">
                <span class="modal-header__title">
                    <i class="fas fa-file-pdf"></i> PDF Preview
                </span>
                <button class="modal-close" onclick="closePdfModal()" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body modal-body--iframe">
                <div class="modal-loading" id="pdfLoading">
                    <div class="spinner"></div>
                    <p>Loading PDF…</p>
                </div>
                <iframe id="pdfIframe" class="modal-iframe" src=""></iframe>
            </div>
            <div class="modal-footer modal-footer--dark" id="pdfFooter"></div>
        </div>
    </div>

    {{-- Unified Video Modal (YouTube / Vimeo iframe + native <video>) --}}
    <div class="modal-overlay" id="videoModal">
        <div class="modal-box modal-box--video">
            <div class="modal-header modal-header--video">
                <span class="modal-header__title" id="videoModalTitle">
                    <i class="fas fa-play-circle"></i> Video Player
                </span>
                <button class="modal-close" onclick="closeVideoModal()" aria-label="Close">&times;</button>
            </div>

            {{-- Panel A: iframe embed (YouTube, Vimeo) --}}
            <div id="videoIframeWrap" class="modal-body modal-body--video" style="display:none;">
                <div class="video-ratio-box">
                    <iframe id="videoIframe"
                            class="video-ratio-iframe"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media;
                                   gyroscope; picture-in-picture"
                            allowfullscreen src=""></iframe>
                    <div class="modal-loading video-dark-loading" id="videoIframeLoading">
                        <div class="spinner spinner--light"></div>
                        <p>Loading video…</p>
                    </div>
                </div>
            </div>

            {{-- Panel B: native <video> (direct mp4/webm/etc.) --}}
            <div id="videoNativeWrap" class="modal-body modal-body--video" style="display:none;">
                <div class="native-video-wrap">
                    <div class="modal-loading video-dark-loading" id="videoNativeLoading">
                        <div class="spinner spinner--light"></div>
                        <p>Loading video…</p>
                    </div>
                    <video id="nativeVideoPlayer"
                           controls preload="metadata"
                           class="native-video-player"
                           style="display:none;">
                        Your browser does not support the video tag.
                    </video>
                    <div id="videoNativeError" class="video-error-state" style="display:none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Could not load this video file.</p>
                        <p class="video-error-hint">
                            The format may not be supported by your browser,
                            or the file is still processing.
                        </p>
                        <a id="videoNativeDownload" href="#" download
                           class="resource-action-btn primary"
                           style="margin-top:1rem;font-size:.875rem;">
                            <i class="fas fa-download"></i> Download to Play
                        </a>
                    </div>
                </div>
            </div>

            <div class="modal-footer modal-footer--dark" id="videoFooter"></div>
        </div>
    </div>

    {{-- Google Drive Modal --}}
    <div class="modal-overlay" id="driveModal">
        <div class="modal-box modal-box--drive">
            <div class="modal-header modal-header--video">
                <span class="modal-header__title">
                    <i class="fab fa-google-drive"></i> Google Drive Viewer
                </span>
                <button class="modal-close" onclick="closeDriveModal()" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body modal-body--iframe modal-body--dark">
                <div class="modal-loading video-dark-loading" id="driveLoading">
                    <div class="spinner spinner--light"></div>
                    <p>Loading file…</p>
                </div>
                <iframe id="driveIframe" class="modal-iframe"
                        src="" allow="autoplay" allowfullscreen></iframe>
            </div>
            <div class="modal-footer modal-footer--dark" id="driveFooter"></div>
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
// TOAST
// ════════════════════════════════════════════════════════════════
function showNotification(message, type = 'info') {
    Swal.fire({
        toast: true, position: 'top-end',
        showConfirmButton: false,
        timer: 4000, timerProgressBar: true,
        icon: type, title: message,
        didOpen: toast => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
}

// ════════════════════════════════════════════════════════════════
// MODAL HELPERS
// ════════════════════════════════════════════════════════════════
function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function closeAllModals() {
    ['pdfModal', 'videoModal', 'driveModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
    const pdfIframe = document.getElementById('pdfIframe');
    if (pdfIframe) pdfIframe.src = '';

    const videoIframe = document.getElementById('videoIframe');
    if (videoIframe) { videoIframe.src = ''; videoIframe.style.opacity = '0'; }

    const driveIframe = document.getElementById('driveIframe');
    if (driveIframe) { driveIframe.src = ''; driveIframe.style.opacity = '0'; }

    const vid = document.getElementById('nativeVideoPlayer');
    if (vid) { vid.pause(); vid.src = ''; vid.style.display = 'none'; }
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

// ════════════════════════════════════════════════════════════════
// PDF MODAL
// Served via pdf_disk: public/pdf/{filename} → /pdf/{filename}
// ════════════════════════════════════════════════════════════════
function openPdfModal(pdfUrl) {
    closeAllModals();
    openModal('pdfModal');

    const iframe  = document.getElementById('pdfIframe');
    const loading = document.getElementById('pdfLoading');
    const footer  = document.getElementById('pdfFooter');

    loading.style.display = 'flex';
    iframe.style.opacity  = '0';
    iframe.src            = pdfUrl;
    footer.textContent    = 'Source: ' + pdfUrl;

    iframe.onload  = () => { loading.style.display = 'none'; iframe.style.opacity = '1'; };
    iframe.onerror = () => {
        loading.style.display = 'none';
        showNotification('Failed to load PDF. Try downloading instead.', 'error');
        closeModal('pdfModal');
    };
}
function closePdfModal() {
    closeModal('pdfModal');
    document.getElementById('pdfIframe').src = '';
}

// ════════════════════════════════════════════════════════════════
// SMART VIDEO ROUTER
// YouTube → embed iframe
// Vimeo   → embed iframe
// Google Drive → Drive modal iframe
// Direct file  → native <video>
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

    // 5. Unknown — try native, error-states gracefully
    _openNativePanel(url);
}

// ── Embed iframe panel (YouTube / Vimeo) ─────────────────────────
function _openEmbedPanel(embedUrl, sourceUrl, label) {
    openModal('videoModal');
    document.getElementById('videoModalTitle').innerHTML =
        `<i class="fas fa-play-circle"></i> ${label} Player`;
    document.getElementById('videoIframeWrap').style.display = 'block';
    document.getElementById('videoNativeWrap').style.display = 'none';

    const iframe  = document.getElementById('videoIframe');
    const loading = document.getElementById('videoIframeLoading');
    loading.style.display = 'flex';
    iframe.style.opacity  = '0';
    iframe.src = embedUrl;
    iframe.onload = () => { loading.style.display = 'none'; iframe.style.opacity = '1'; };
    document.getElementById('videoFooter').textContent = 'Source: ' + sourceUrl;
}

// ── Google Drive iframe panel ────────────────────────────────────
function _openDrivePanel(embedUrl, sourceUrl) {
    openModal('driveModal');
    const iframe  = document.getElementById('driveIframe');
    const loading = document.getElementById('driveLoading');
    loading.style.display = 'flex';
    iframe.style.opacity  = '0';
    iframe.src = embedUrl;
    iframe.onload  = () => { loading.style.display = 'none'; iframe.style.opacity = '1'; };
    iframe.onerror = () => {
        loading.style.display = 'none';
        showNotification('Could not load Google Drive file. Try opening the link directly.', 'error');
    };
    document.getElementById('driveFooter').textContent = 'Source: ' + sourceUrl;
}
function closeDriveModal() {
    closeModal('driveModal');
    const i = document.getElementById('driveIframe');
    i.src = ''; i.style.opacity = '0';
}

// ── Native <video> panel ─────────────────────────────────────────
function _openNativePanel(url) {
    openModal('videoModal');
    document.getElementById('videoModalTitle').innerHTML =
        '<i class="fas fa-film"></i> Video Player';
    document.getElementById('videoIframeWrap').style.display = 'none';
    document.getElementById('videoNativeWrap').style.display = 'block';

    const vid     = document.getElementById('nativeVideoPlayer');
    const loading = document.getElementById('videoNativeLoading');
    const errBox  = document.getElementById('videoNativeError');
    const dlBtn   = document.getElementById('videoNativeDownload');

    vid.style.display     = 'none';
    errBox.style.display  = 'none';
    loading.style.display = 'flex';
    dlBtn.href            = url;

    vid.src = url;
    vid.load();

    vid.onloadedmetadata = () => { loading.style.display = 'none'; vid.style.display = 'block'; };
    vid.oncanplay        = () => { loading.style.display = 'none'; vid.style.display = 'block'; };
    vid.onerror          = () => { loading.style.display = 'none'; errBox.style.display = 'flex'; };

    document.getElementById('videoFooter').textContent = 'Source: ' + url;
}

// ── Close video modal ────────────────────────────────────────────
function closeVideoModal() {
    closeModal('videoModal');
    const iframe = document.getElementById('videoIframe');
    iframe.src = ''; iframe.style.opacity = '0';
    const vid = document.getElementById('nativeVideoPlayer');
    vid.pause(); vid.src = ''; vid.style.display = 'none';
    document.getElementById('videoNativeError').style.display   = 'none';
    document.getElementById('videoNativeLoading').style.display = 'flex';
    document.getElementById('videoIframeLoading').style.display = 'flex';
}

// ── Drive ID extractor ───────────────────────────────────────────
function _extractDriveId(url) {
    const m1 = url.match(/\/file\/d\/([^\/?#&]+)/);
    if (m1) return m1[1];
    const m2 = url.match(/[?&]id=([^&]+)/);
    if (m2) return m2[1];
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