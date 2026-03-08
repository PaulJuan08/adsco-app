@php
    $videoLink      = $topic->video_link ?? '';
    $youtubeEmbedId = null;
    if ($videoLink) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $videoLink, $m)) {
            $youtubeEmbedId = $m[1];
        }
    }
    $pdfUrl    = $topic->pdf_file ? asset('pdf/' . $topic->pdf_file) : null;
    $hasVideo  = $youtubeEmbedId || $videoLink;
    $hasPdf    = (bool) $pdfUrl;
    $hasBoth   = $hasVideo && $hasPdf;
    $hasMedia  = $hasVideo || $hasPdf;
@endphp

<div style="display:flex;gap:1.5rem;align-items:flex-start;min-height:0;">

    {{-- ── Left: Media panel ── --}}
    @if($hasMedia)
    <div style="flex:0 0 55%;min-width:0;">

        {{-- Tab switcher (only when both video AND pdf exist) --}}
        @if($hasBoth)
        <div style="display:flex;gap:.4rem;margin-bottom:.85rem;">
            <button id="ts-tab-video" onclick="tsShowTab('video')"
                    style="padding:.35rem .9rem;border-radius:20px;border:none;cursor:pointer;font-size:.78rem;font-weight:700;background:#552b20;color:#fff;transition:background .2s,color .2s;">
                <i class="fas fa-play-circle"></i> Video
            </button>
            <button id="ts-tab-pdf" onclick="tsShowTab('pdf')"
                    style="padding:.35rem .9rem;border-radius:20px;border:none;cursor:pointer;font-size:.78rem;font-weight:700;background:#f3f4f6;color:#374151;transition:background .2s,color .2s;">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </div>
        @endif

        {{-- Video panel --}}
        @if($hasVideo)
        <div id="ts-panel-video">
            @if($youtubeEmbedId)
            <div style="position:relative;padding-top:56.25%;border-radius:12px;overflow:hidden;background:#000;">
                <iframe src="https://www.youtube.com/embed/{{ $youtubeEmbedId }}?rel=0&modestbranding=1"
                        style="position:absolute;inset:0;width:100%;height:100%;border:none;"
                        allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture"
                        allowfullscreen></iframe>
            </div>
            @else
            <a href="{{ $videoLink }}" target="_blank" rel="noopener"
               style="display:flex;align-items:center;justify-content:center;gap:.6rem;padding:2rem;background:#f8fafc;border:2px dashed #cbd5e1;border-radius:12px;color:#552b20;font-weight:700;font-size:.9rem;text-decoration:none;">
                <i class="fas fa-external-link-alt" style="font-size:1.4rem;"></i>
                Open Video Link
            </a>
            @endif
        </div>
        @endif

        {{-- PDF panel --}}
        @if($hasPdf)
        <div id="ts-panel-pdf"@if($hasVideo) style="display:none;"@endif>
            <iframe src="{{ $pdfUrl }}"
                    style="width:100%;height:430px;border:none;border-radius:12px;background:#f3f4f6;">
            </iframe>
        </div>
        @endif

        {{-- Attachment external link --}}
        @if($topic->attachment)
        <div style="margin-top:.75rem;">
            <a href="{{ $topic->attachment }}" target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:.4rem;padding:.4rem .85rem;border-radius:8px;background:#dbeafe;color:#1d4ed8;font-size:.78rem;font-weight:700;text-decoration:none;">
                <i class="fas fa-paperclip"></i> Open Attachment
            </a>
        </div>
        @endif

    </div>
    @endif

    {{-- ── Right: Details panel ── --}}
    <div style="flex:1;min-width:0;{{ !$hasMedia ? 'width:100%;' : '' }}display:flex;flex-direction:column;gap:.65rem;">

        {{-- Title + status --}}
        <div>
            <h3 style="margin:0 0 .45rem;font-size:1.05rem;font-weight:700;color:#1a202c;line-height:1.3;">{{ $topic->title }}</h3>
            <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;{{ $topic->is_published ? 'background:#d1fae5;color:#065f46;' : 'background:#fef3c7;color:#92400e;' }}">
                <i class="fas fa-{{ $topic->is_published ? 'check-circle' : 'clock' }}"></i>
                {{ $topic->is_published ? 'Published' : 'Draft' }}
            </span>
        </div>

        {{-- Description --}}
        @if($topic->description)
        <p style="font-size:.85rem;color:#4a5568;line-height:1.65;margin:0;">{{ $topic->description }}</p>
        @endif

        {{-- Course(s) --}}
        @if($topic->courses && $topic->courses->isNotEmpty())
        <div>
            <div style="font-size:.68rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.35rem;">Course(s)</div>
            @foreach($topic->courses as $course)
            <div style="font-size:.82rem;color:#374151;padding:.2rem 0;display:flex;align-items:center;gap:.35rem;">
                <i class="fas fa-book" style="color:#552b20;font-size:.7rem;"></i>
                {{ $course->title }}
                @if($course->course_code)
                <span style="color:#9ca3af;">({{ $course->course_code }})</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Meta --}}
        <div style="font-size:.72rem;color:#a0aec0;display:flex;flex-direction:column;gap:.2rem;">
            @if($topic->created_at)
            <span><i class="fas fa-calendar-plus" style="width:14px;"></i> Created {{ $topic->created_at->format('M d, Y') }}</span>
            @endif
            @if($topic->updated_at && $topic->updated_at->ne($topic->created_at))
            <span><i class="fas fa-pencil-alt" style="width:14px;"></i> Updated {{ $topic->updated_at->format('M d, Y') }}</span>
            @endif
        </div>

        {{-- Actions --}}
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:auto;padding-top:.5rem;border-top:1px solid #f0ebe8;">
            @if(!empty($editUrl))
            <button onclick="openCrudModal('{{ $editUrl }}', 'Edit Topic')"
                    style="padding:.45rem 1rem;border-radius:8px;background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border:none;font-size:.8rem;font-weight:700;cursor:pointer;">
                <i class="fas fa-edit"></i> Edit
            </button>
            @endif
            <button onclick="closeCrudModal()"
                    style="padding:.45rem 1rem;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.8rem;font-weight:600;cursor:pointer;">
                Close
            </button>
        </div>

    </div>
</div>

@if($hasBoth)
<script>
function tsShowTab(tab) {
    var vPanel = document.getElementById('ts-panel-video');
    var pPanel = document.getElementById('ts-panel-pdf');
    var vTab   = document.getElementById('ts-tab-video');
    var pTab   = document.getElementById('ts-tab-pdf');
    if (vPanel) vPanel.style.display = tab === 'video' ? 'block' : 'none';
    if (pPanel) pPanel.style.display = tab === 'pdf'   ? 'block' : 'none';
    if (vTab) { vTab.style.background = tab === 'video' ? '#552b20' : '#f3f4f6'; vTab.style.color = tab === 'video' ? '#fff' : '#374151'; }
    if (pTab) { pTab.style.background = tab === 'pdf'   ? '#552b20' : '#f3f4f6'; pTab.style.color = tab === 'pdf'   ? '#fff' : '#374151'; }
}
</script>
@endif
