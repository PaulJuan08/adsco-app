{{-- resources/views/student/topics/show.blade.php --}}
@extends('layouts.student')

@section('title', $topic->title . ' - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-show.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="form-container">

    {{-- ── Card Header ── --}}
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon">
                @if($topic->video_link) <i class="fas fa-video"></i>
                @elseif($topic->attachment) <i class="fas fa-paperclip"></i>
                @else <i class="fas fa-chalkboard"></i>
                @endif
            </div>
            <h2 class="card-title">{{ $topic->title }}</h2>
        </div>
        <div class="top-actions">
            {{-- $encryptedCourseId is pre-built by the controller --}}
            <a href="{{ route('student.courses.show', $encryptedCourseId) }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back to Course
            </a>
            <a href="{{ route('student.topics.index') }}" class="top-action-btn">
                <i class="fas fa-list"></i> All Topics
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

        {{-- ── Topic Preview ── --}}
        <div class="topic-preview">
            <div class="topic-preview-avatar">{{ strtoupper(substr($topic->title, 0, 1)) }}</div>
            <h1 class="topic-preview-title">{{ $topic->title }}</h1>
            <div class="topic-preview-meta">
                @if($isCompleted)
                <span class="topic-preview-badge published">
                    <i class="fas fa-check-circle"></i> Completed
                    @if($completionDate) ({{ $completionDate->format('M d, Y') }}) @endif
                </span>
                @else
                <span class="topic-preview-badge draft">
                    <i class="fas fa-clock"></i> In Progress
                </span>
                @endif

                @if($topic->estimated_time)
                <span class="topic-preview-badge" style="background:linear-gradient(135deg,var(--info) 0%,var(--info-dark) 100%);color:white;">
                    <i class="fas fa-hourglass-half"></i> {{ $topic->estimated_time }}
                </span>
                @endif
            </div>
            <div class="topic-preview-id"><i class="fas fa-hashtag"></i> Topic ID: {{ $topic->id }}</div>
        </div>

        {{-- ── Two-column layout ── --}}
        <div class="two-column-layout">

            {{-- LEFT — content --}}
            <div class="form-column">

                {{-- Description --}}
                @if($topic->description)
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-align-left"></i> Description</h3>
                    <div class="description-box">{{ $topic->description }}</div>
                </div>
                @endif

                {{-- Video --}}
                @if($topic->video_link)
                @php
                    $embedUrl = null;
                    if (str_contains($topic->video_link, 'youtube.com/watch?v=')) {
                        $vid      = substr($topic->video_link, strpos($topic->video_link, 'v=') + 2);
                        $vid      = strtok($vid, '&');
                        $embedUrl = $vid ? "https://www.youtube.com/embed/{$vid}" : null;
                    } elseif (str_contains($topic->video_link, 'youtu.be/')) {
                        $vid      = substr($topic->video_link, strrpos($topic->video_link, '/') + 1);
                        $vid      = strtok($vid, '?');
                        $embedUrl = $vid ? "https://www.youtube.com/embed/{$vid}" : null;
                    } elseif (str_contains($topic->video_link, 'vimeo.com/')) {
                        $vid      = substr($topic->video_link, strrpos($topic->video_link, '/') + 1);
                        $vid      = strtok($vid, '?');
                        $embedUrl = $vid ? "https://player.vimeo.com/video/{$vid}" : null;
                    }
                @endphp
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-video"></i> Video Lesson</h3>
                    <div class="resource-card">
                        <div class="resource-header">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="file-icon file-video"><i class="fas fa-video"></i></div>
                                <div>
                                    <div class="resource-title">Video Lesson</div>
                                    <div class="resource-description">{{ $embedUrl ? 'YouTube / Vimeo Video' : 'External Video Link' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="resource-content">
                            @if($embedUrl)
                            <div style="position:relative;width:100%;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:var(--radius);">
                                <iframe src="{{ $embedUrl }}" title="{{ $topic->title }}" frameborder="0"
                                    allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture"
                                    allowfullscreen
                                    style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;">
                                </iframe>
                            </div>
                            @else
                            <div style="text-align:center;padding:1.5rem;">
                                <div style="font-size:2.5rem;color:var(--gray-400);margin-bottom:1rem;"><i class="fas fa-external-link-alt"></i></div>
                                <p style="color:var(--gray-600);margin-bottom:1.5rem;">This video cannot be embedded. Click below to watch on the original platform.</p>
                                <a href="{{ $topic->video_link }}" target="_blank" class="resource-action-btn primary">
                                    <i class="fas fa-play-circle"></i> Watch on Platform
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- PDF Materials --}}
                @if($topic->pdf_file)
                @php
                    $pdfUrl      = App\Http\Controllers\Student\TopicController::getPdfUrl($topic->pdf_file);
                    $pdfFilename = basename(str_replace('/storage/pdfs/', '', $topic->pdf_file));
                @endphp
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-file-pdf" style="color:var(--danger);"></i> PDF Materials</h3>
                    <div class="resource-card">
                        <div class="resource-header">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="file-icon file-pdf"><i class="fas fa-file-pdf"></i></div>
                                <div>
                                    <div class="resource-title">PDF Document</div>
                                    <div class="resource-description">{{ $pdfFilename }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="resource-content">
                            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;justify-content:space-between;align-items:center;">
                                <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                                    <span style="display:flex;align-items:center;gap:0.5rem;font-size:var(--font-size-xs);color:var(--gray-600);">
                                        <i class="fas fa-file-pdf"></i> PDF Document
                                    </span>
                                    <span style="display:flex;align-items:center;gap:0.5rem;font-size:var(--font-size-xs);color:var(--gray-600);">
                                        <i class="fas fa-clock"></i> Ready to View
                                    </span>
                                </div>
                                <div style="display:flex;gap:0.75rem;">
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

                {{-- Attachment --}}
                @if($topic->attachment)
                @php
                    $ext   = strtolower(pathinfo($topic->attachment, PATHINFO_EXTENSION));
                    $icon  = match(true) {
                        $ext === 'pdf'                    => 'fas fa-file-pdf',
                        in_array($ext, ['doc','docx'])    => 'fas fa-file-word',
                        in_array($ext, ['xls','xlsx'])    => 'fas fa-file-excel',
                        in_array($ext, ['ppt','pptx'])    => 'fas fa-file-powerpoint',
                        in_array($ext, ['jpg','jpeg','png','gif']) => 'fas fa-file-image',
                        default                           => 'fas fa-file',
                    };
                    $color = match(true) {
                        $ext === 'pdf'                    => 'file-pdf',
                        in_array($ext, ['doc','docx'])    => 'file-word',
                        in_array($ext, ['xls','xlsx'])    => 'file-excel',
                        in_array($ext, ['ppt','pptx'])    => 'file-powerpoint',
                        in_array($ext, ['jpg','jpeg','png','gif']) => 'file-image',
                        default                           => 'file-generic',
                    };
                @endphp
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-paperclip"></i> Learning Materials</h3>
                    <div class="resource-card">
                        <div class="resource-header">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="file-icon {{ $color }}"><i class="{{ $icon }}"></i></div>
                                <div>
                                    <div class="resource-title">Topic Materials</div>
                                    <div class="resource-description">Supplementary resources for this topic</div>
                                </div>
                            </div>
                        </div>
                        <div class="resource-content">
                            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;justify-content:space-between;align-items:center;">
                                <span style="display:flex;align-items:center;gap:0.5rem;font-size:var(--font-size-xs);color:var(--gray-600);">
                                    <i class="{{ $icon }}"></i> {{ strtoupper($ext) }} File
                                </span>
                                <div style="display:flex;gap:0.75rem;">
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

            </div>{{-- /form-column --}}

            {{-- RIGHT — sidebar --}}
            <div class="sidebar-column">

                {{-- Course link --}}
                @if(isset($course))
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-book"></i> Course</h3>
                    <a href="{{ route('student.courses.show', $encryptedCourseId) }}" class="course-tag">
                        {{ $course->course_code }}: {{ Str::limit($course->title, 30) }}
                    </a>
                </div>
                @endif

                {{-- Progress / completion toggle --}}
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-chart-line"></i> Your Progress</h3>

                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-flag"></i> Status</span>
                        <span class="info-value" style="color:{{ $isCompleted ? 'var(--success)' : 'var(--primary)' }}">
                            {{ $isCompleted ? 'Completed' : 'In Progress' }}
                        </span>
                    </div>

                    @if($isCompleted && isset($completionDate))
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar"></i> Completed On</span>
                        <span class="info-value">{{ $completionDate->format('M d, Y') }}</span>
                    </div>
                    @endif

                    <div style="margin-top:1.5rem;">
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

                {{-- Topic meta --}}
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-info-circle"></i> Topic Info</h3>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-hashtag"></i> ID</span>
                        <span class="info-value">{{ $topic->id }}</span>
                    </div>
                    @if($topic->estimated_time)
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-hourglass-half"></i> Est. Time</span>
                        <span class="info-value">{{ $topic->estimated_time }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-plus"></i> Created</span>
                        <span class="info-value">{{ $topic->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($topic->updated_at != $topic->created_at)
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-check"></i> Updated</span>
                        <span class="info-value">{{ $topic->updated_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>

            </div>{{-- /sidebar-column --}}
        </div>{{-- /two-column-layout --}}
    </div>{{-- /card-body --}}
</div>{{-- /form-container --}}

{{-- PDF Modal --}}
<div class="pdf-modal-overlay" id="pdfModal">
    <div class="pdf-modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-file-pdf"></i> PDF Preview</h3>
            <button class="modal-close" id="closePdfModal">&times;</button>
        </div>
        <div style="flex:1;position:relative;background:var(--gray-100);">
            <iframe id="pdfIframe" style="width:100%;height:100%;border:none;"></iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── PDF modal ──────────────────────────────────────────────────
function openPdfModal(url) {
    const modal  = document.getElementById('pdfModal');
    const iframe = document.getElementById('pdfIframe');
    if (!modal || !iframe) return;
    modal.classList.add('active');
    iframe.src = url;
}
function closePdfModal() {
    const modal  = document.getElementById('pdfModal');
    const iframe = document.getElementById('pdfIframe');
    if (modal)  modal.classList.remove('active');
    if (iframe) iframe.src = '';
}

// ── Mark complete / incomplete ─────────────────────────────────
function markTopicComplete(encryptedId, complete) {
    const url = complete
        ? `/student/topics/${encryptedId}/complete`
        : `/student/topics/${encryptedId}/incomplete`;

    Swal.fire({
        title: 'Please wait…',
        text: complete ? 'Marking as complete…' : 'Marking as incomplete…',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Success!', text: data.message, timer: 1500, showConfirmButton: false })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Something went wrong.' });
        }
    })
    .catch(err => {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Network Error', text: 'Please check your connection and try again.' });
    });
}

// ── DOM ready ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('closePdfModal')?.addEventListener('click', closePdfModal);
    document.getElementById('pdfModal')?.addEventListener('click', e => {
        if (e.target === document.getElementById('pdfModal')) closePdfModal();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && document.getElementById('pdfModal')?.classList.contains('active')) {
            closePdfModal();
        }
    });
});
</script>
@endpush