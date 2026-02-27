@extends('layouts.student')

@section('title', $topic->title . ' - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-show.css') }}">
<style>
/* ── Fixes & extensions for student topic view ── */

/* info-row used in sidebar (replaces missing class) */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.5rem 0;
    border-bottom: 1px dashed var(--gray-200);
    font-size: 0.875rem;
    gap: 0.5rem;
}
.info-row:last-child { border-bottom: none; }

.info-label {
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}
.info-label i { color: var(--primary); width: 16px; }

.info-value {
    font-weight: 600;
    color: var(--gray-800);
    text-align: right;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(102,126,234,0.4);
}
.btn-outline-secondary {
    background: white;
    color: var(--gray-700);
    border: 1.5px solid var(--gray-300);
}
.btn-outline-secondary:hover {
    background: var(--gray-100);
    border-color: var(--gray-500);
    transform: translateY(-2px);
}

/* topic-preview-id */
.topic-preview-id {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

/* Video embed wrapper */
.video-embed-wrapper {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
    border-radius: var(--radius);
    background: #000;
}
.video-embed-wrapper iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    border: none;
}

/* PDF Modal — matches topic-show.css modal-overlay pattern */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.95);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    backdrop-filter: blur(5px);
}
.modal-overlay.active { display: flex; }

/* Progress card accent */
.progress-card-complete {
    border-color: rgba(72,187,120,0.4) !important;
}
.progress-card-complete::before {
    background: linear-gradient(90deg, var(--success) 0%, var(--success-dark) 100%) !important;
}

/* Resource card file icon colours */
.file-word   { background: rgba(37,99,235,0.1); color: #2563eb; }
.file-excel  { background: rgba(5,150,105,0.1); color: #059669; }
.file-powerpoint { background: rgba(217,119,6,0.1); color: #d97706; }
.file-image  { background: rgba(124,58,237,0.1); color: #7c3aed; }
.file-generic{ background: rgba(107,114,128,0.1); color: #6b7280; }

/* Completion badge */
.badge-complete {
    background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(72,187,120,0.3);
}
.badge-progress {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}

/* No-resources empty state inside detail-section */
.no-resource-msg {
    text-align: center;
    padding: 1.5rem;
    color: var(--gray-500);
    font-size: 0.875rem;
}
.no-resource-msg i {
    font-size: 2rem;
    color: var(--gray-300);
    display: block;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="dashboard-container">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <i class="fas fa-chevron-right"></i>
        @if(isset($course))
            <a href="{{ route('student.courses.index') }}">Courses</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('student.courses.show', Crypt::encrypt($course->id)) }}">
                {{ Str::limit($course->title, 25) }}
            </a>
            <i class="fas fa-chevron-right"></i>
        @else
            <a href="{{ route('student.topics.index') }}">Topics</a>
            <i class="fas fa-chevron-right"></i>
        @endif
        <span class="current">{{ Str::limit($topic->title, 30) }}</span>
    </div>

    <div class="form-container">

        {{-- ── CARD HEADER ── --}}
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon">
                    @if($topic->video_link)
                        <i class="fas fa-video"></i>
                    @elseif($topic->pdf_file)
                        <i class="fas fa-file-pdf"></i>
                    @elseif($topic->attachment)
                        <i class="fas fa-paperclip"></i>
                    @else
                        <i class="fas fa-chalkboard"></i>
                    @endif
                </div>
                <h1 class="card-title">{{ $topic->title }}</h1>
            </div>
            <div class="top-actions">
                @if(isset($course))
                    <a href="{{ route('student.courses.show', Crypt::encrypt($course->id)) }}" class="top-action-btn">
                        <i class="fas fa-arrow-left"></i> Back to Course
                    </a>
                @endif
                <a href="{{ route('student.topics.index') }}" class="top-action-btn">
                    <i class="fas fa-list"></i> All Topics
                </a>
            </div>
        </div>

        {{-- ── CARD BODY ── --}}
        <div class="card-body">

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

            {{-- Topic Preview Bar --}}
            @php
                $isCompleted = Auth::user()->completedTopics()
                    ->where('topic_id', $topic->id)
                    ->exists();
            @endphp

            <div class="topic-preview">
                <div class="topic-preview-avatar">
                    <i class="fas {{ $topic->video_link ? 'fa-video' : ($topic->pdf_file ? 'fa-file-pdf' : 'fa-book-open') }}"></i>
                </div>
                <div class="topic-preview-content">
                    <h2 class="topic-preview-title">{{ $topic->title }}</h2>
                    <div class="topic-preview-meta">
                        @if($isCompleted)
                            <span class="topic-preview-badge published">
                                <i class="fas fa-check-circle"></i> Completed
                                @if(isset($completionDate) && $completionDate)
                                    · {{ $completionDate->format('M d, Y') }}
                                @endif
                            </span>
                        @else
                            <span class="topic-preview-badge draft">
                                <i class="fas fa-clock"></i> In Progress
                            </span>
                        @endif

                        @if(isset($course))
                            <span>
                                <i class="fas fa-book"></i>
                                {{ $course->course_code }}
                            </span>
                        @endif

                        @if($topic->estimated_time)
                            <span>
                                <i class="fas fa-hourglass-half"></i> {{ $topic->estimated_time }}
                            </span>
                        @endif

                        @php
                            $resourceCount = ($topic->pdf_file ? 1 : 0)
                                           + ($topic->video_link ? 1 : 0)
                                           + ($topic->attachment ? 1 : 0);
                        @endphp
                        @if($resourceCount)
                            <span>
                                <i class="fas fa-paperclip"></i> {{ $resourceCount }} resource{{ $resourceCount > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                    <div class="topic-preview-id">
                        <i class="fas fa-hashtag"></i> Topic #{{ $topic->id }}
                    </div>
                </div>
            </div>

            {{-- ── TWO-COLUMN LAYOUT ── --}}
            <div class="two-column-layout">

                {{-- ── LEFT: MAIN CONTENT ── --}}
                <div class="form-column">

                    {{-- Description --}}
                    @if($topic->description)
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-align-left"></i> Description
                        </h3>
                        <div class="description-box">{{ $topic->description }}</div>
                    </div>
                    @endif

                    {{-- Learning Outcomes --}}
                    @if($topic->learning_outcomes)
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-bullseye"></i> Learning Outcomes
                        </h3>
                        <div class="description-box">{{ $topic->learning_outcomes }}</div>
                    </div>
                    @endif

                    {{-- ── RESOURCES ── --}}
                    @if($resourceCount > 0)
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-paperclip"></i> Resources
                            <span style="margin-left:auto; font-size:0.75rem; color:#718096;">
                                {{ $resourceCount }} file{{ $resourceCount > 1 ? 's' : '' }}
                            </span>
                        </h3>

                        {{-- Video --}}
                        @if($topic->video_link)
                        @php
                            $vUrl    = $topic->video_link;
                            $embedUrl = null;
                            $platform = 'External';
                            $platformIcon = 'fas fa-video';

                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vUrl, $m)) {
                                $embedUrl = "https://www.youtube.com/embed/{$m[1]}?rel=0";
                                $platform = 'YouTube'; $platformIcon = 'fab fa-youtube';
                            } elseif (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/i', $vUrl, $m)) {
                                $embedUrl = "https://player.vimeo.com/video/{$m[1]}";
                                $platform = 'Vimeo'; $platformIcon = 'fab fa-vimeo-v';
                            } elseif (str_contains($vUrl, 'drive.google.com')) {
                                $platform = 'Google Drive'; $platformIcon = 'fab fa-google-drive';
                            }
                        @endphp
                        <div class="resource-card">
                            <div class="resource-header">
                                <div class="resource-meta">
                                    <div class="file-icon file-video">
                                        <i class="{{ $platformIcon }}"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">Video Lesson</div>
                                        <div class="resource-subtitle">
                                            <i class="{{ $platformIcon }}"></i> {{ $platform }}
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
                                        <i class="fas fa-external-link-alt"></i> Open
                                    </a>
                                </div>
                            </div>

                            @if($embedUrl)
                            <div class="resource-content">
                                <div class="video-embed-wrapper">
                                    <iframe src="{{ $embedUrl }}"
                                            title="{{ $topic->title }}"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- PDF --}}
                        @if($topic->pdf_file)
                        @php
                            $pdfUrl      = App\Http\Controllers\Student\TopicController::getPdfUrl($topic->pdf_file);
                            $pdfFilename = basename($topic->pdf_file);
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
                        </div>
                        @endif

                        {{-- Attachment link --}}
                        @if($topic->attachment)
                        @php
                            $ext  = strtolower(pathinfo($topic->attachment, PATHINFO_EXTENSION));
                            $attIcon  = match(true) {
                                in_array($ext, ['pdf'])              => ['fas fa-file-pdf',        'file-pdf'],
                                in_array($ext, ['doc','docx'])       => ['fas fa-file-word',       'file-word'],
                                in_array($ext, ['xls','xlsx','csv']) => ['fas fa-file-excel',      'file-excel'],
                                in_array($ext, ['ppt','pptx'])       => ['fas fa-file-powerpoint', 'file-powerpoint'],
                                in_array($ext, ['jpg','jpeg','png','gif','bmp','svg']) => ['fas fa-file-image', 'file-image'],
                                default                              => ['fas fa-link',             'file-generic'],
                            };
                        @endphp
                        <div class="resource-card">
                            <div class="resource-header">
                                <div class="resource-meta">
                                    <div class="file-icon {{ $attIcon[1] }}">
                                        <i class="{{ $attIcon[0] }}"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">Attachment</div>
                                        <div class="resource-subtitle">
                                            <i class="fas fa-external-link-alt"></i> External Resource
                                        </div>
                                    </div>
                                </div>
                                <div class="resource-actions">
                                    <a href="{{ $topic->attachment }}" target="_blank"
                                       class="resource-action-btn primary">
                                        <i class="fas fa-external-link-alt"></i> Open
                                    </a>
                                    <a href="{{ $topic->attachment }}" download
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-download"></i> Download
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

                    {{-- No content state --}}
                    @if(!$topic->description && !$topic->learning_outcomes && $resourceCount === 0)
                    <div class="detail-section">
                        <div class="no-resource-msg">
                            <i class="fas fa-inbox"></i>
                            No content has been added to this topic yet.
                        </div>
                    </div>
                    @endif

                </div>{{-- /form-column --}}

                {{-- ── RIGHT: SIDEBAR ── --}}
                <div class="sidebar-column">

                    {{-- Progress Card --}}
                    <div class="sidebar-card {{ $isCompleted ? 'progress-card-complete' : '' }}">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-chart-line"></i> Your Progress
                        </h3>

                        <div class="info-row-sm" style="margin-bottom:1rem;">
                            <span class="lbl"><i class="fas fa-flag"></i> Status</span>
                            <span class="val" style="color:{{ $isCompleted ? '#48bb78' : '#667eea' }};">
                                {{ $isCompleted ? 'Completed' : 'In Progress' }}
                            </span>
                        </div>

                        @if($isCompleted && isset($completionDate) && $completionDate)
                        <div class="info-row-sm" style="margin-bottom:1rem;">
                            <span class="lbl"><i class="fas fa-calendar-check"></i> Completed On</span>
                            <span class="val">{{ $completionDate->format('M d, Y') }}</span>
                        </div>
                        @endif

                        <div style="margin-top:1.25rem;">
                            @if(!$isCompleted)
                                <button class="btn btn-primary" style="width:100%;"
                                        onclick="markTopicComplete('{{ $encryptedId }}', true)">
                                    <i class="fas fa-check-circle"></i> Mark as Complete
                                </button>
                            @else
                                <button class="btn btn-outline-secondary" style="width:100%;"
                                        onclick="markTopicComplete('{{ $encryptedId }}', false)">
                                    <i class="fas fa-undo"></i> Mark as Incomplete
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Course Card --}}
                    @if(isset($course))
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-book"></i> Course
                        </h3>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-code"></i> Code</span>
                            <span class="val">{{ $course->course_code }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-heading"></i> Title</span>
                            <span class="val">{{ Str::limit($course->title, 20) }}</span>
                        </div>
                        <div style="margin-top:1rem;">
                            <a href="{{ route('student.courses.show', Crypt::encrypt($course->id)) }}"
                               class="quick-action-link">
                                <i class="fas fa-arrow-left"></i>
                                <span>Back to Course</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Topic Info Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> Topic Info
                        </h3>

                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-hashtag"></i> ID</span>
                            <span class="val">#{{ $topic->id }}</span>
                        </div>

                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-paperclip"></i> Resources</span>
                            <span class="val">{{ $resourceCount }}</span>
                        </div>

                        @if($topic->video_link)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-video"></i> Video</span>
                            <span class="val" style="color:#48bb78;">Yes</span>
                        </div>
                        @endif

                        @if($topic->pdf_file)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-file-pdf"></i> PDF</span>
                            <span class="val" style="color:#48bb78;">Yes</span>
                        </div>
                        @endif

                        @if($topic->estimated_time)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-hourglass-half"></i> Est. Time</span>
                            <span class="val">{{ $topic->estimated_time }}</span>
                        </div>
                        @endif

                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Created</span>
                            <span class="val">{{ $topic->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </h3>

                        @if($topic->pdf_file)
                        @php $pdfUrlSidebar = App\Http\Controllers\Student\TopicController::getPdfUrl($topic->pdf_file); @endphp
                        <button onclick="openPdfModal('{{ $pdfUrlSidebar }}')" class="quick-action-link">
                            <i class="fas fa-file-pdf"></i><span>View PDF</span>
                        </button>
                        @endif

                        @if($topic->video_link)
                        <button onclick="openSmartVideoModal('{{ $topic->video_link }}')" class="quick-action-link">
                            <i class="fas fa-play-circle"></i><span>Play Video</span>
                        </button>
                        @endif

                        @if($topic->attachment)
                        <a href="{{ $topic->attachment }}" target="_blank" class="quick-action-link">
                            <i class="fas fa-external-link-alt"></i><span>Open Attachment</span>
                        </a>
                        @endif

                        <a href="{{ route('student.topics.index') }}" class="quick-action-link">
                            <i class="fas fa-list"></i><span>All Topics</span>
                        </a>
                    </div>

                    {{-- Help Card --}}
                    <div class="sidebar-card help-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-lightbulb"></i> Tips</h3>
                        <div class="help-text">
                            <p style="margin-bottom:0.75rem;"><strong>Resources:</strong> Use the View / Play buttons to access PDFs and videos.</p>
                            <p><strong>Progress:</strong> Mark topics complete to track your course progress.</p>
                        </div>
                    </div>

                </div>{{-- /sidebar-column --}}

            </div>{{-- /two-column-layout --}}
        </div>{{-- /card-body --}}
    </div>{{-- /form-container --}}
</div>{{-- /dashboard-container --}}

{{-- ═══════════════════════════ PDF MODAL ═══════════════════════════ --}}
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
                <embed id="pdfEmbed" type="application/pdf"
                       style="width:100%; height:100%; display:none;" src="">
                <iframe id="pdfIframe"
                        style="width:100%; height:100%; border:none; background:white; display:none;"
                        src=""></iframe>
                <div id="pdfFallback"
                     style="display:none; text-align:center; padding:3rem; background:white; height:100%;">
                    <i class="fas fa-file-pdf" style="font-size:4rem; color:#dc2626; margin-bottom:1rem;"></i>
                    <h3 style="margin-bottom:1rem;">PDF Cannot Be Displayed</h3>
                    <p style="margin-bottom:1.5rem; color:#6b7280;">Your browser cannot display this PDF inline.</p>
                    <a href="#" id="downloadLink" class="resource-action-btn primary" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="modal-footer" id="pdfFooter"></div>
    </div>
</div>

{{-- ═══════════════════════════ VIDEO MODAL ═══════════════════════════ --}}
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
            <div class="modal-loading" id="videoIframeLoading">
                <div class="spinner"></div>
                <p>Loading video player...</p>
            </div>
            <iframe id="videoIframe" class="video-ratio-iframe"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                    allowfullscreen src=""></iframe>
        </div>

        <div id="videoNativeWrap" class="modal-body modal-body--video" style="display:none;">
            <div class="modal-loading" id="videoNativeLoading">
                <div class="spinner"></div>
                <p>Loading video...</p>
            </div>
            <div class="native-video-wrap">
                <video id="nativeVideoPlayer" controls controlslist="nodownload"
                       preload="metadata" class="native-video-player">
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

{{-- ═══════════════════════════ DRIVE MODAL ═══════════════════════════ --}}
<div class="modal-overlay" id="driveModal">
    <div class="modal-box modal-box--drive">
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
            <div class="modal-loading" id="driveLoading">
                <div class="spinner"></div>
                <p>Loading from Google Drive...</p>
            </div>
            <iframe id="driveIframe" class="modal-iframe"
                    allow="autoplay; fullscreen" allowfullscreen src=""></iframe>
        </div>
        <div class="modal-footer" id="driveFooter"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ══════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function openModal(id)  { const m = document.getElementById(id); if (m) m.classList.add('active'); }
function closeModal(id) { const m = document.getElementById(id); if (m) m.classList.remove('active'); }

function closeAllModals() {
    ['pdfModal','videoModal','driveModal'].forEach(closeModal);
    resetPdfElements();
    resetVideoElements();
}

// ══════════════════════════════════════════════
// PDF MODAL
// ══════════════════════════════════════════════
function openPdfModal(pdfUrl) {
    closeAllModals();
    openModal('pdfModal');

    const embed    = document.getElementById('pdfEmbed');
    const iframe   = document.getElementById('pdfIframe');
    const loading  = document.getElementById('pdfLoading');
    const fallback = document.getElementById('pdfFallback');
    const footer   = document.getElementById('pdfFooter');
    const dlLink   = document.getElementById('downloadLink');

    if (loading)  loading.style.display  = 'flex';
    if (embed)    embed.style.display    = 'none';
    if (iframe)   iframe.style.display   = 'none';
    if (fallback) fallback.style.display = 'none';
    if (dlLink)   dlLink.href = pdfUrl;
    if (footer)   footer.textContent = 'Source: ' + pdfUrl;

    const finalUrl = pdfUrl + (pdfUrl.includes('?') ? '&' : '?') + 't=' + Date.now();

    function tryFallback() {
        if (embed)  { embed.style.display  = 'none'; }
        if (iframe) {
            iframe.src = finalUrl;
            iframe.style.display = 'block';
            iframe.onload  = () => { if (loading) loading.style.display = 'none'; };
            iframe.onerror = () => {
                if (loading)  loading.style.display  = 'none';
                if (iframe)   iframe.style.display   = 'none';
                if (fallback) fallback.style.display = 'block';
            };
            setTimeout(() => {
                if (loading && loading.style.display !== 'none') {
                    loading.style.display  = 'none';
                    iframe.style.display   = 'none';
                    if (fallback) fallback.style.display = 'block';
                }
            }, 6000);
        }
    }

    if (embed) {
        embed.src = finalUrl;
        embed.style.display = 'block';
        embed.onload = () => { if (loading) loading.style.display = 'none'; };
        setTimeout(() => {
            if (loading && loading.style.display !== 'none') tryFallback();
        }, 2500);
    } else {
        tryFallback();
    }
}

function closePdfModal() { closeModal('pdfModal'); resetPdfElements(); }

function resetPdfElements() {
    ['pdfEmbed','pdfIframe'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.src = ''; el.style.display = 'none'; }
    });
    const fb = document.getElementById('pdfFallback');
    const ld = document.getElementById('pdfLoading');
    if (fb) fb.style.display = 'none';
    if (ld) ld.style.display = 'flex';
    const box = document.getElementById('pdfModalBox');
    if (box && box.classList.contains('fullscreen')) {
        box.classList.remove('fullscreen');
        const ic = box.querySelector('.modal-fullscreen i');
        if (ic) { ic.className = 'fas fa-expand'; }
    }
}

function togglePdfFullscreen() {
    const box = document.getElementById('pdfModalBox');
    const ic  = box?.querySelector('.modal-fullscreen i');
    if (!box || !ic) return;
    box.classList.toggle('fullscreen');
    ic.className = box.classList.contains('fullscreen') ? 'fas fa-compress' : 'fas fa-expand';
}

// ══════════════════════════════════════════════
// VIDEO MODAL
// ══════════════════════════════════════════════
function openSmartVideoModal(url) {
    closeAllModals();
    const ytMatch    = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
    const vimeoMatch = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
    const driveId    = _extractDriveId(url);

    if (ytMatch)    return _openEmbedPanel(`https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1&rel=0`, url, 'YouTube');
    if (vimeoMatch) return _openEmbedPanel(`https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`, url, 'Vimeo');
    if (driveId)    return _openDrivePanel(`https://drive.google.com/file/d/${driveId}/preview`, url);
    _openNativePanel(url);
}

function _openEmbedPanel(embedUrl, sourceUrl, label) {
    openModal('videoModal');
    const title = document.getElementById('videoModalTitle');
    if (title) title.innerHTML = `<i class="fas fa-play-circle"></i> <span>${label} Player</span>`;
    const iw = document.getElementById('videoIframeWrap');
    const nw = document.getElementById('videoNativeWrap');
    if (iw) iw.style.display = 'block';
    if (nw) nw.style.display = 'none';
    const iframe  = document.getElementById('videoIframe');
    const loading = document.getElementById('videoIframeLoading');
    if (loading) loading.style.display = 'flex';
    if (iframe)  { iframe.src = embedUrl; iframe.onload = () => { if (loading) loading.style.display = 'none'; }; }
    const footer = document.getElementById('videoFooter');
    if (footer) footer.textContent = 'Source: ' + sourceUrl;
}

function _openDrivePanel(embedUrl, sourceUrl) {
    openModal('driveModal');
    const iframe  = document.getElementById('driveIframe');
    const loading = document.getElementById('driveLoading');
    const footer  = document.getElementById('driveFooter');
    if (loading) loading.style.display = 'flex';
    if (iframe)  { iframe.src = embedUrl; iframe.onload = () => { if (loading) loading.style.display = 'none'; }; }
    if (footer)  footer.textContent = 'Source: ' + sourceUrl;
}

function _openNativePanel(url) {
    openModal('videoModal');
    const title = document.getElementById('videoModalTitle');
    if (title) title.innerHTML = '<i class="fas fa-film"></i> <span>Video Player</span>';
    const iw = document.getElementById('videoIframeWrap');
    const nw = document.getElementById('videoNativeWrap');
    if (iw) iw.style.display = 'none';
    if (nw) nw.style.display = 'block';
    const vid     = document.getElementById('nativeVideoPlayer');
    const loading = document.getElementById('videoNativeLoading');
    const errBox  = document.getElementById('videoNativeError');
    const footer  = document.getElementById('videoFooter');
    if (vid)    { vid.style.display = 'none'; vid.src = ''; }
    if (errBox) errBox.style.display = 'none';
    if (loading) loading.style.display = 'flex';
    if (vid) {
        vid.src = url; vid.load();
        vid.onloadedmetadata = () => { if (loading) loading.style.display = 'none'; vid.style.display = 'block'; vid.play().catch(()=>{}); };
        vid.onerror = () => { if (loading) loading.style.display = 'none'; if (errBox) errBox.style.display = 'flex'; };
    }
    if (footer) footer.textContent = 'Source: ' + url;
}

function _extractDriveId(url) {
    const patterns = [/\/file\/d\/([^\/?#&]+)/, /[?&]id=([^&]+)/, /\/open\?id=([^&]+)/, /\/d\/([^\/?#&]+)/];
    for (const p of patterns) { const m = url.match(p); if (m) return m[1]; }
    return null;
}

function resetVideoElements() {
    const iframe = document.getElementById('videoIframe');
    const drive  = document.getElementById('driveIframe');
    const vid    = document.getElementById('nativeVideoPlayer');
    if (iframe) iframe.src = '';
    if (drive)  drive.src  = '';
    if (vid)    { vid.pause(); vid.src = ''; vid.style.display = 'none'; vid.load(); }
    ['videoIframeWrap','videoNativeWrap'].forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
    ['videoNativeError'].forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
    const vl = document.getElementById('videoNativeLoading');
    const il = document.getElementById('videoIframeLoading');
    if (vl) vl.style.display = 'flex';
    if (il) il.style.display = 'flex';
}

function closeVideoModal() { closeModal('videoModal'); resetVideoElements(); }
function closeDriveModal() {
    closeModal('driveModal');
    const iframe = document.getElementById('driveIframe');
    if (iframe) iframe.src = '';
    const ld = document.getElementById('driveLoading');
    if (ld) ld.style.display = 'flex';
}

// ══════════════════════════════════════════════
// TOPIC COMPLETION
// ══════════════════════════════════════════════
function markTopicComplete(topicId, complete) {
    const url = complete
        ? `/student/topics/${topicId}/complete`
        : `/student/topics/${topicId}/complete`;   // adjust if you have a separate incomplete route

    Swal.fire({
        title: 'Please wait...',
        text: complete ? 'Marking topic as complete…' : 'Marking topic as incomplete…',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ complete })
    })
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Done!', text: data.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Something went wrong.' });
        }
    })
    .catch(err => {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Network Error', text: 'Please check your connection and try again.' });
    });
}

// ══════════════════════════════════════════════
// GLOBAL KEYBOARD & CLICK-OUTSIDE
// ══════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => { if (e.target === overlay) closeAllModals(); });
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllModals(); });

    @if(session('success')) 
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'{{ session("success") }}', showConfirmButton:false, timer:4000, timerProgressBar:true }); 
    @endif
    @if(session('error')) 
        Swal.fire({ toast:true, position:'top-end', icon:'error', title:'{{ session("error") }}', showConfirmButton:false, timer:4000, timerProgressBar:true }); 
    @endif
});
</script>
@endpush