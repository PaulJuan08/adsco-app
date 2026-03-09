{{-- resources/views/student/courses/show.blade.php --}}
@extends('layouts.student')

@section('title', $course->title . ' - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-show.css') }}">
<style>
/* ── Journey Hero ── */
.journey-hero {
    background: linear-gradient(135deg, #552b20 0%, #8b3a2a 50%, #d3541b 100%);
    border-radius: 16px;
    padding: 1.75rem 2rem;
    color: white;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.journey-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.journey-hero-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem; margin-bottom: 1.25rem;
}
.journey-course-title {
    font-size: 1.4rem; font-weight: 800; line-height: 1.2;
    margin: 0 0 .3rem;
    text-shadow: 0 2px 8px rgba(0,0,0,.2);
}
.journey-course-meta {
    font-size: .8rem; opacity: .85;
    display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
}
.journey-pct {
    font-size: 2.8rem; font-weight: 900; line-height: 1;
    text-shadow: 0 3px 12px rgba(0,0,0,.25);
    white-space: nowrap;
}
.journey-pct span { font-size: 1rem; font-weight: 600; opacity: .8; }
.journey-bar-wrap {
    height: 10px; background: rgba(255,255,255,.25); border-radius: 5px;
    overflow: hidden; margin-bottom: 1rem;
}
.journey-bar-fill {
    height: 100%; border-radius: 5px;
    background: linear-gradient(90deg, #ddb238, #f0c040);
    transition: width .6s ease;
    box-shadow: 0 0 10px rgba(221,178,56,.6);
}
.journey-stats {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: .75rem; text-align: center;
}
.journey-stat-val { font-size: 1.6rem; font-weight: 800; line-height: 1; }
.journey-stat-lbl { font-size: .72rem; opacity: .8; margin-top: .15rem; }

/* ── Learning Path ── */
.learning-path { position: relative; }
.path-step {
    display: flex; gap: 1rem; align-items: flex-start;
    padding: .9rem 1rem;
    border-radius: 12px;
    margin-bottom: .5rem;
    transition: background .15s, box-shadow .15s;
    text-decoration: none; color: inherit;
    position: relative;
}
.path-step.completed    { background: #f0fff4; border: 1px solid #c6f6d5; }
.path-step.next-up      { background: #fff8f0; border: 1.5px solid #f6ad55; box-shadow: 0 4px 14px rgba(211,84,27,.1); }
.path-step.upcoming     { background: #fafafa; border: 1px solid #e2e8f0; }
.path-step:hover        { box-shadow: 0 4px 16px rgba(85,43,32,.12); transform: translateX(2px); }

.step-indicator {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 800; flex-shrink: 0;
}
.step-indicator.done     { background: #48bb78; color: white; }
.step-indicator.active   { background: linear-gradient(135deg,#d3541b,#ddb238); color: white; box-shadow: 0 3px 10px rgba(211,84,27,.35); }
.step-indicator.pending  { background: #e2e8f0; color: #718096; }

.step-body { flex: 1; min-width: 0; }
.step-title {
    font-size: .9rem; font-weight: 700; color: #2d3748;
    margin: 0 0 .35rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.path-step.completed .step-title { color: #276749; }
.step-badges { display: flex; gap: .3rem; flex-wrap: wrap; }
.step-badge {
    font-size: .63rem; font-weight: 600;
    padding: 1px 6px; border-radius: 20px;
    display: inline-flex; align-items: center; gap: .2rem;
}
.step-badge.video      { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.step-badge.attachment { background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; }
.step-badge.pdf        { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }

.step-action {
    display: flex; align-items: center;
    font-size: .75rem; font-weight: 700;
    padding: .35rem .85rem; border-radius: 20px;
    white-space: nowrap; flex-shrink: 0; align-self: center;
    text-decoration: none; gap: .3rem;
}
.step-action.done    { background: #c6f6d5; color: #276749; }
.step-action.active  { background: linear-gradient(135deg,#d3541b,#ddb238); color: white; box-shadow: 0 2px 8px rgba(211,84,27,.3); }
.step-action.review  { background: #e0e7ff; color: #4338ca; }

/* Next-up label */
.next-badge {
    position: absolute; top: -8px; left: 54px;
    background: linear-gradient(135deg,#d3541b,#ddb238);
    color: white; font-size: .6rem; font-weight: 800;
    padding: 2px 8px; border-radius: 20px; text-transform: uppercase;
    letter-spacing: .5px;
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
                    <h1 class="welcome-title">{{ $course->title }}</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-route"></i> Learning Journey
                        <span class="separator">•</span>
                        <span class="pending-notice">{{ $course->course_code }}</span>
                        @if($courseProgress['total'] > 0)
                        <span class="separator">•</span>
                        <span class="pending-notice">{{ $courseProgress['percentage'] }}% complete</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="header-quick-actions" style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a href="{{ route('student.courses.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Courses
                </a>
                @if(isset($enrollment) && $enrollment)
                <a href="{{ route('student.courses.discussions', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-comments"></i> Discussion
                </a>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1rem;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1rem;"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- ── Two-column layout ── --}}
    <div class="content-grid">
        <div class="left-column">

            {{-- ── Journey Hero ── --}}
            @if($courseProgress['total'] > 0)
            <div class="journey-hero">
                <div class="journey-hero-top">
                    <div>
                        <h2 class="journey-course-title">Your Learning Journey</h2>
                        <div class="journey-course-meta">
                            <span><i class="fas fa-book"></i> {{ $courseProgress['total'] }} topics</span>
                            @if($course->teacher)
                            <span><i class="fas fa-chalkboard-teacher"></i> {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</span>
                            @endif
                            @if($courseProgress['percentage'] >= 100)
                            <span style="background:rgba(255,255,255,.2);padding:2px 8px;border-radius:20px;font-weight:700;"><i class="fas fa-trophy"></i> Course Completed!</span>
                            @endif
                        </div>
                    </div>
                    <div class="journey-pct">
                        {{ $courseProgress['percentage'] }}<span>%</span>
                    </div>
                </div>
                <div class="journey-bar-wrap">
                    <div class="journey-bar-fill" style="width:{{ $courseProgress['percentage'] }}%;"></div>
                </div>
                <div class="journey-stats">
                    <div>
                        <div class="journey-stat-val">{{ $courseProgress['completed'] }}</div>
                        <div class="journey-stat-lbl">Completed</div>
                    </div>
                    <div>
                        <div class="journey-stat-val">{{ $courseProgress['remaining'] }}</div>
                        <div class="journey-stat-lbl">Remaining</div>
                    </div>
                    <div>
                        <div class="journey-stat-val">{{ $courseProgress['total'] }}</div>
                        <div class="journey-stat-lbl">Total Topics</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Description ── --}}
            @if($course->description)
            <div class="dashboard-card" style="margin-bottom:1.25rem;">
                <div class="card-header" style="padding:.75rem 1.25rem;">
                    <h2 class="card-title" style="font-size:.95rem;"><i class="fas fa-align-left"></i> About this Course</h2>
                </div>
                <div class="card-body" style="padding:1rem 1.25rem;">
                    <div class="rich-text" style="font-size:.88rem;color:#4a5568;line-height:1.7;">{!! $course->description !!}</div>
                </div>
            </div>
            @endif

            {{-- ── Learning Path ── --}}
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-route"></i> Learning Path
                    </h2>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        @if($topics->count() > 0)
                        <span style="background:#e0e7ff;color:#4338ca;padding:.2rem .75rem;border-radius:20px;font-size:.78rem;font-weight:700;">
                            {{ $topics->count() }} {{ Str::plural('Topic', $topics->count()) }}
                        </span>
                        @endif
                        @if($topics->count() > 4)
                        <div class="search-container" style="margin:0;">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if($topics->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-map"></i></div>
                        <h3 class="empty-title">No topics published yet</h3>
                        <p class="empty-text">Your instructor hasn't published any topics for this course yet. Check back soon!</p>
                    </div>
                    @else
                    <div class="learning-path" id="topics-list">
                        @foreach($topics as $i => $topic)
                        @php
                            $done    = in_array($topic->id, $completedTopicIds);
                            $isNext  = isset($nextTopic) && $nextTopic->id === $topic->id;
                            $encTopic = Crypt::encrypt($topic->id);
                            $stepClass = $done ? 'completed' : ($isNext ? 'next-up' : 'upcoming');
                            $indicClass = $done ? 'done' : ($isNext ? 'active' : 'pending');
                        @endphp
                        <a href="{{ route('student.topics.show', $encTopic) }}"
                           class="path-step {{ $stepClass }}"
                           data-title="{{ strtolower($topic->title) }}">

                            @if($isNext && !$done)
                            <span class="next-badge"><i class="fas fa-bolt"></i> Next Up</span>
                            @endif

                            <div class="step-indicator {{ $indicClass }}">
                                @if($done)
                                    <i class="fas fa-check"></i>
                                @elseif($isNext)
                                    <i class="fas fa-play"></i>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>

                            <div class="step-body">
                                <div class="step-title">{{ $topic->title }}</div>
                                <div class="step-badges">
                                    @if($topic->video_link)
                                        <span class="step-badge video"><i class="fas fa-video"></i> Video</span>
                                    @endif
                                    @if($topic->attachment)
                                        <span class="step-badge attachment"><i class="fas fa-paperclip"></i> Attachment</span>
                                    @endif
                                    @if($topic->pdf_file)
                                        <span class="step-badge pdf"><i class="fas fa-file-pdf"></i> PDF</span>
                                    @endif
                                    @if(!$topic->video_link && !$topic->attachment && !$topic->pdf_file)
                                        <span class="step-badge" style="background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;"><i class="fas fa-align-left"></i> Reading</span>
                                    @endif
                                </div>
                            </div>

                            <span class="step-action {{ $done ? 'review' : ($isNext ? 'active' : 'done') }}" style="{{ !$done && !$isNext ? 'background:#f3f4f6;color:#a0aec0;' : '' }}">
                                @if($done)
                                    <i class="fas fa-redo"></i> Review
                                @elseif($isNext)
                                    <i class="fas fa-play"></i> Start
                                @else
                                    <i class="fas fa-lock"></i> Locked
                                @endif
                            </span>
                        </a>
                        @endforeach

                        <div id="topics-empty" style="display:none;padding:1.5rem;text-align:center;color:#a0aec0;font-size:.875rem;">
                            <i class="fas fa-search" style="font-size:1.5rem;margin-bottom:.5rem;display:block;"></i>
                            No topics match your search.
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Sidebar ── --}}
        <div class="right-column">

            {{-- Course Details --}}
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-info-circle"></i> Course Details</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-hashtag"></i> Code</span>
                    <span class="val">{{ $course->course_code }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-star"></i> Credits</span>
                    <span class="val">{{ $course->credits }}</span>
                </div>
                @if($course->duration_weeks)
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-calendar"></i> Duration</span>
                    <span class="val">{{ $course->duration_weeks }} weeks</span>
                </div>
                @endif
                @if($course->level)
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-layer-group"></i> Level</span>
                    <span class="val">{{ ucfirst($course->level) }}</span>
                </div>
                @endif
                @if($course->teacher)
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-chalkboard-teacher"></i> Teacher</span>
                    <span class="val">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</span>
                </div>
                @endif
                @if(isset($enrollment) && $enrollment)
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-calendar-check"></i> Enrolled</span>
                    <span class="val">{{ $enrollment->enrolled_at ? \Carbon\Carbon::parse($enrollment->enrolled_at)->format('M d, Y') : 'N/A' }}</span>
                </div>
                @endif
            </div>

            {{-- Achievement / Grade --}}
            @if(isset($enrollment) && $enrollment && $enrollment->grade)
            <div class="sidebar-card" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fde68a;">
                <h3 class="sidebar-card-title" style="color:#92400e;"><i class="fas fa-trophy" style="color:#d97706;"></i> Your Achievement</h3>
                <div style="text-align:center;padding:.5rem 0;">
                    <div style="font-size:2.5rem;font-weight:900;color:#d97706;">{{ $enrollment->grade }}%</div>
                    <div style="font-size:.78rem;color:#92400e;font-weight:600;">Final Grade</div>
                </div>
            </div>
            @elseif($courseProgress['percentage'] >= 100)
            <div class="sidebar-card" style="background:linear-gradient(135deg,#f0fff4,#c6f6d5);border:1px solid #9ae6b4;">
                <h3 class="sidebar-card-title" style="color:#276749;"><i class="fas fa-medal" style="color:#48bb78;"></i> Quest Complete!</h3>
                <div style="text-align:center;padding:.5rem 0;font-size:.82rem;color:#276749;font-weight:600;">
                    You've completed all topics in this course.
                </div>
            </div>
            @endif

            {{-- Next step CTA --}}
            @if(isset($nextTopic) && $nextTopic)
            <div class="sidebar-card" style="background:linear-gradient(135deg,#fff8f0,#fff3e0);border:1px solid #f6ad55;">
                <h3 class="sidebar-card-title" style="color:#7c3d0e;"><i class="fas fa-bolt" style="color:#d3541b;"></i> Continue Learning</h3>
                <p style="font-size:.8rem;color:#7c3d0e;margin:0 0 .75rem;line-height:1.5;">Your next topic is ready:</p>
                <div style="font-size:.85rem;font-weight:700;color:#2d3748;margin-bottom:.75rem;">{{ Str::limit($nextTopic->title, 40) }}</div>
                <a href="{{ route('student.topics.show', Crypt::encrypt($nextTopic->id)) }}"
                   style="display:block;text-align:center;padding:.55rem 1rem;background:linear-gradient(135deg,#d3541b,#ddb238);color:white;border-radius:10px;font-weight:700;font-size:.82rem;text-decoration:none;">
                    <i class="fas fa-play"></i> Start Topic
                </a>
            </div>
            @endif

            {{-- Quick Actions --}}
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                @if(isset($enrollment) && $enrollment)
                <a href="{{ route('student.courses.discussions', $encryptedId) }}"
                   style="display:flex;align-items:center;gap:.5rem;padding:.5rem .75rem;border-radius:8px;background:#eff6ff;color:#3b82f6;font-size:.82rem;font-weight:600;text-decoration:none;margin-bottom:.4rem;">
                    <i class="fas fa-comments"></i> Open Discussion
                </a>
                @if($enrollment->grade)
                <a href="{{ route('student.courses.grades', $encryptedId) }}"
                   style="display:flex;align-items:center;gap:.5rem;padding:.5rem .75rem;border-radius:8px;background:#f0fff4;color:#48bb78;font-size:.82rem;font-weight:600;text-decoration:none;margin-bottom:.4rem;">
                    <i class="fas fa-chart-bar"></i> View Grades
                </a>
                @endif
                @endif
                <a href="{{ route('student.topics.index') }}"
                   style="display:flex;align-items:center;gap:.5rem;padding:.5rem .75rem;border-radius:8px;background:#fefce8;color:#ca8a04;font-size:.82rem;font-weight:600;text-decoration:none;">
                    <i class="fas fa-list"></i> All My Topics
                </a>
            </div>

            {{-- Tips --}}
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-lightbulb"></i> Study Tips</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-check-circle"></i> Order</span>
                    <span class="val" style="font-size:.73rem;">Follow topics in sequence</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-pencil-alt"></i> Notes</span>
                    <span class="val" style="font-size:.73rem;">Use the notes box on each topic</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-redo"></i> Review</span>
                    <span class="val" style="font-size:.73rem;">Revisit completed topics anytime</span>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-topics');
    const emptyMsg    = document.getElementById('topics-empty');
    const steps       = document.querySelectorAll('#topics-list .path-step');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            let visible = 0;
            steps.forEach(step => {
                const match = !term || (step.dataset.title || '').includes(term);
                step.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (emptyMsg) emptyMsg.style.display = (visible === 0 && steps.length > 0) ? 'block' : 'none';
        });
    }
});
</script>
@endpush
