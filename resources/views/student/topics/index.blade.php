{{-- resources/views/student/topics/index.blade.php --}}
@extends('layouts.student')

@section('title', 'My Topics - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-index.css') }}">
<style>
.topics-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1.25rem;
    padding: .25rem 0;
}
.topic-card-item {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #f0ebe8;
    box-shadow: 0 2px 10px rgba(85,43,32,.07);
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}
.topic-card-item:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(85,43,32,.13); }
.topic-card-thumb {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    overflow: hidden;
    background: #1a1a2e;
}
.topic-card-thumb img {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .3s;
}
.topic-card-item:hover .topic-card-thumb img { transform: scale(1.04); }
.topic-card-thumb-placeholder {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.45);
}
.topic-card-thumb-placeholder.topic-1 { background: linear-gradient(135deg,#4f46e5,#7c3aed); }
.topic-card-thumb-placeholder.topic-2 { background: linear-gradient(135deg,#0891b2,#0e7490); }
.topic-card-thumb-placeholder.topic-3 { background: linear-gradient(135deg,#059669,#047857); }
.topic-card-play {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.85);
    font-size: 2.5rem;
    text-shadow: 0 2px 8px rgba(0,0,0,.4);
    transition: color .2s;
}
.topic-card-item:hover .topic-card-play { color: #fff; }
.topic-card-status {
    position: absolute;
    top: .6rem; right: .6rem;
    font-size: .65rem; font-weight: 700;
    padding: 2px 8px; border-radius: 20px;
    display: flex; align-items: center; gap: .3rem;
}
.topic-card-status.done   { background: #d1fae5; color: #065f46; }
.topic-card-status.start  { background: #eff6ff; color: #1d4ed8; }
.topic-card-body { padding: .85rem 1rem; flex: 1; }
.topic-card-title {
    font-size: .92rem; font-weight: 700; color: #1a202c;
    margin: 0 0 .4rem;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.topic-card-course {
    font-size: .75rem; color: #718096;
    display: flex; align-items: center; gap: .3rem;
    margin-bottom: .5rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.topic-card-badges { display: flex; flex-wrap: wrap; gap: .3rem; }
.topic-badge {
    font-size: .65rem; font-weight: 600;
    padding: 2px 7px; border-radius: 20px;
    display: inline-flex; align-items: center; gap: .25rem;
}
.topic-badge.video      { background: #fee2e2; color: #dc2626; }
.topic-badge.attachment { background: #dbeafe; color: #1d4ed8; }
.topic-badge.pdf        { background: #fff7ed; color: #ea580c; }
.topic-card-footer {
    padding: .65rem 1rem;
    border-top: 1px solid #f7f0ec;
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem;
}
.topic-card-date { font-size: .68rem; color: #a0aec0; }
.topic-card-view-hint {
    font-size: .68rem; color: var(--primary);
    display: flex; align-items: center; gap: .25rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="dashboard-container">

    {{-- ── Header ── --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">My Topics</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-list-check"></i> Browse and study topics from your enrolled courses
                        @if($totalTopics > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $totalTopics }} available</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    @php $pct = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0; @endphp
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($totalTopics) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-list"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Completed</div>
                    <div class="stat-number">{{ number_format($completedTopics) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Remaining</div>
                    <div class="stat-number">{{ number_format($totalTopics - $completedTopics) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">With Video</div>
                    <div class="stat-number">{{ number_format($topicsWithVideo) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-video"></i></div>
            </div>
        </div>
    </div>

    {{-- ── Main Content ── --}}
    <div class="content-grid">
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-list-check"></i> All Topics</h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                        </div>
                        @if($courses->count() > 1)
                        <select id="filter-course" style="padding:0.4rem 0.65rem;border:1px solid #e2e8f0;border-radius:8px;font-size:0.8rem;color:#4a5568;background:white;outline:none;cursor:pointer;margin-left:0.5rem;">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ Str::limit($course->title, 22) }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
                    @endif

                    @if($allTopics->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-list-check"></i></div>
                        <h3 class="empty-title">No topics available yet</h3>
                        <p class="empty-text">Topics will appear here once your teacher publishes them for your enrolled courses.</p>
                        <div class="empty-hint"><i class="fas fa-info-circle"></i> Contact your teacher if you're not seeing topics.</div>
                    </div>
                    @else
                    <div class="topics-card-grid" id="topics-grid">
                        @foreach($allTopics as $topic)
                        @php
                            $isCompleted    = in_array($topic->id, $completedTopicIds);
                            $encId          = Crypt::encrypt($topic->id);
                            $firstCourse    = $topic->courses->first();

                            // YouTube thumbnail
                            $videoLink = $topic->video_link ?? '';
                            $youtubeThumbnail = null;
                            if ($videoLink && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $videoLink, $m)) {
                                $youtubeThumbnail = 'https://img.youtube.com/vi/' . $m[1] . '/mqdefault.jpg';
                            }
                        @endphp
                        <a href="{{ route('student.topics.show', $encId) }}"
                           class="topic-card-item"
                           data-title="{{ strtolower($topic->title) }}"
                           data-course-id="{{ $firstCourse?->id }}">

                            {{-- Thumbnail --}}
                            <div class="topic-card-thumb">
                                @if($youtubeThumbnail)
                                    <img src="{{ $youtubeThumbnail }}" alt="{{ $topic->title }}" loading="lazy">
                                    <div class="topic-card-play"><i class="fab fa-youtube"></i></div>
                                @else
                                    <div class="topic-card-thumb-placeholder topic-{{ ($loop->index % 3) + 1 }}">
                                        <i class="fas fa-{{ $videoLink ? 'play-circle' : 'chalkboard' }}" style="font-size:3rem;"></i>
                                    </div>
                                    @if($videoLink)
                                    <div class="topic-card-play"><i class="fas fa-play-circle"></i></div>
                                    @endif
                                @endif
                                <span class="topic-card-status {{ $isCompleted ? 'done' : 'start' }}">
                                    <i class="fas fa-{{ $isCompleted ? 'check-circle' : 'play-circle' }}"></i>
                                    {{ $isCompleted ? 'Done' : 'Start' }}
                                </span>
                            </div>

                            {{-- Card body --}}
                            <div class="topic-card-body">
                                <p class="topic-card-title">{{ $topic->title }}</p>
                                <div class="topic-card-course">
                                    <i class="fas fa-book" style="flex-shrink:0;"></i>
                                    <span style="overflow:hidden;text-overflow:ellipsis;">
                                        @if($firstCourse)
                                            {{ Str::limit($firstCourse->title, 28) }}
                                            @if($topic->courses->count() > 1)
                                                <span style="color:#a0aec0;">+{{ $topic->courses->count() - 1 }} more</span>
                                            @endif
                                        @else
                                            No course assigned
                                        @endif
                                    </span>
                                </div>
                                <div class="topic-card-badges">
                                    @if($topic->video_link)
                                        <span class="topic-badge video"><i class="fas fa-video"></i> Video</span>
                                    @endif
                                    @if($topic->attachment)
                                        <span class="topic-badge attachment"><i class="fas fa-paperclip"></i> Attachment</span>
                                    @endif
                                    @if($topic->pdf_file)
                                        <span class="topic-badge pdf"><i class="fas fa-file-pdf"></i> PDF</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Card footer --}}
                            <div class="topic-card-footer">
                                <span class="topic-card-date">{{ $topic->created_at->format('M d, Y') }}</span>
                                <span class="topic-card-view-hint"><i class="fas fa-eye"></i> View</span>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    <div id="topics-empty" style="display:none;padding:2rem;text-align:center;color:#a0aec0;font-size:0.875rem;">
                        <i class="fas fa-search" style="font-size:1.5rem;margin-bottom:0.5rem;display:block;"></i>
                        No topics match your search.
                    </div>
                    @endif
                </div>

                @if($allTopics instanceof \Illuminate\Pagination\AbstractPaginator && $allTopics->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $allTopics->firstItem() }} to {{ $allTopics->lastItem() }} of {{ $allTopics->total() }} topics
                    </div>
                    <div class="pagination-links">{{ $allTopics->links() }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="right-column">
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-chart-pie"></i> Progress Overview</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-list"></i> Total Topics</span>
                    <span class="val">{{ $totalTopics }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-check-circle"></i> Completed</span>
                    <span class="val">{{ $completedTopics }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-clock"></i> Remaining</span>
                    <span class="val">{{ $totalTopics - $completedTopics }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-percent"></i> Completion</span>
                    <span class="val highlight">{{ $pct }}%</span>
                </div>
                @if($totalTopics > 0)
                <div style="margin-top:0.75rem;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                    <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,var(--primary),#d3541b);border-radius:3px;"></div>
                </div>
                @endif
            </div>

            @if($recentlyCompleted->isNotEmpty())
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-history"></i> Recently Completed</h3>
                @foreach($recentlyCompleted as $prog)
                <div class="info-row-sm" style="align-items:flex-start;">
                    <span class="lbl" style="flex-shrink:0;"><i class="fas fa-check-circle" style="color:var(--success);"></i></span>
                    <span class="val" style="font-size:0.75rem;">
                        {{ $prog->topic?->title ?? 'Unknown' }}
                        <span style="display:block;font-size:0.68rem;color:#718096;">{{ $prog->completed_at?->diffForHumans() }}</span>
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            @if($courses->isNotEmpty())
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-book"></i> My Courses</h3>
                @foreach($courses as $course)
                <div class="info-row-sm">
                    <span class="lbl" style="font-size:0.78rem;">{{ Str::limit($course->title, 22) }}</span>
                    <span class="val" style="font-size:0.72rem;color:#a0aec0;">{{ $course->topics_count }} topic{{ $course->topics_count != 1 ? 's' : '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-lightbulb"></i> Study Tips</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-clock"></i> Schedule</span>
                    <span class="val" style="font-size:0.73rem;">Study at the same time daily</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-pencil-alt"></i> Notes</span>
                    <span class="val" style="font-size:0.73rem;">Take notes as you go</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-check"></i> Complete</span>
                    <span class="val" style="font-size:0.73rem;">Mark topics done to track progress</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput  = document.getElementById('search-topics');
    const courseFilter = document.getElementById('filter-course');
    const emptyMsg     = document.getElementById('topics-empty');
    const cards        = document.querySelectorAll('#topics-grid .topic-card-item');

    function filterCards() {
        const term     = (searchInput?.value || '').toLowerCase().trim();
        const courseId = courseFilter?.value || '';
        let visible = 0;
        cards.forEach(card => {
            const matchTitle  = !term || (card.dataset.title || '').includes(term);
            const matchCourse = !courseId || card.dataset.courseId === courseId;
            card.style.display = (matchTitle && matchCourse) ? '' : 'none';
            if (matchTitle && matchCourse) visible++;
        });
        if (emptyMsg) emptyMsg.style.display = (visible === 0 && cards.length > 0) ? 'block' : 'none';
    }

    searchInput?.addEventListener('input', filterCards);
    courseFilter?.addEventListener('change', filterCards);
});
</script>
@endpush
