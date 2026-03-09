@push('styles')
<style>
/* ═══════════════════════════════════════
   DISCUSSIONS INDEX
   ═══════════════════════════════════════ */

/* ── Sidebar card & info-row ── */
:root {
    --primary: #552b20;
    --primary-dark: #3d1f17;
    --gray-200: #edf2f7;
    --gray-300: #e2e8f0;
    --gray-600: #718096;
    --gray-800: #2d3748;
}
.sidebar-card {
    background: white;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    border: 1px solid var(--gray-300);
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
    position: relative;
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.sidebar-card:last-child { margin-bottom: 0; }
.sidebar-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
}
.sidebar-card-title {
    font-size: 1rem; font-weight: 700; color: var(--gray-800);
    margin-bottom: 1.25rem;
    display: flex; align-items: center; gap: .5rem;
    padding-bottom: .75rem;
    border-bottom: 2px solid var(--gray-200);
}
.sidebar-card-title i { color: var(--primary); }
.info-row-sm {
    display: flex; justify-content: space-between; align-items: center;
    padding: .5rem 0;
    border-bottom: 1px dashed var(--gray-200);
    font-size: .875rem;
}
.info-row-sm:last-child { border-bottom: none; }
.info-row-sm .lbl {
    color: var(--gray-600);
    display: flex; align-items: center; gap: .5rem;
}
.info-row-sm .lbl i { color: var(--primary); width: 16px; }
.info-row-sm .val { font-weight: 600; color: var(--gray-800); }
.info-row-sm .val.highlight { color: var(--primary); font-size: 1.125rem; }

/* Course discussion cards */
.disc-course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}
.disc-course-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #f0ebe8;
    box-shadow: 0 2px 10px rgba(85,43,32,.06);
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
}
.disc-course-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(85,43,32,.12);
}
.disc-course-card-header {
    padding: 1.1rem 1.25rem .75rem;
    border-bottom: 1px solid #f5f0ed;
    display: flex;
    gap: .75rem;
    align-items: flex-start;
}
.disc-course-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.1rem; flex-shrink: 0;
}
.disc-course-icon.color-1 { background: linear-gradient(135deg, #552b20, #d3541b); }
.disc-course-icon.color-2 { background: linear-gradient(135deg, #0891b2, #0e7490); }
.disc-course-icon.color-3 { background: linear-gradient(135deg, #059669, #047857); }
.disc-course-icon.color-4 { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
.disc-course-title-wrap { flex: 1; min-width: 0; }
.disc-course-name {
    font-size: .9rem; font-weight: 700; color: #1a202c;
    margin: 0 0 .2rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.disc-course-code {
    font-size: .72rem; color: #a0aec0; font-weight: 600;
}
.disc-activity-dot {
    width: 9px; height: 9px; border-radius: 50%;
    background: #48bb78; flex-shrink: 0; margin-top: .4rem;
    box-shadow: 0 0 0 3px rgba(72,187,120,.2);
}
.disc-activity-dot.inactive { background: #e2e8f0; box-shadow: none; }

.disc-course-stats {
    display: flex; gap: 0; border-bottom: 1px solid #f5f0ed;
}
.disc-stat-cell {
    flex: 1; padding: .65rem .75rem; text-align: center;
    border-right: 1px solid #f5f0ed;
}
.disc-stat-cell:last-child { border-right: none; }
.disc-stat-num {
    font-size: 1.2rem; font-weight: 800; color: #2d3748; line-height: 1;
}
.disc-stat-lbl {
    font-size: .63rem; color: #a0aec0; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em; margin-top: .15rem;
}

.disc-course-footer {
    padding: .7rem 1.25rem;
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem;
    flex: 1; align-items: flex-end;
}
.disc-last-activity {
    font-size: .72rem; color: #a0aec0;
    display: flex; align-items: center; gap: .3rem;
}
.disc-last-activity strong { color: #718096; }
.disc-open-btn {
    font-size: .75rem; font-weight: 700; color: var(--primary);
    display: flex; align-items: center; gap: .3rem;
    background: #fef5f0; padding: .3rem .75rem;
    border-radius: 20px; white-space: nowrap;
    transition: background .15s;
}
.disc-course-card:hover .disc-open-btn {
    background: var(--primary); color: white;
}

/* No-activity state */
.disc-course-card.no-activity { opacity: .75; }
.disc-course-card.no-activity:hover { opacity: 1; }
.disc-no-msg {
    padding: .6rem 1.25rem;
    font-size: .78rem; color: #c0c8d4; font-style: italic;
    border-bottom: 1px solid #f5f0ed;
}

/* Empty state */
.disc-index-empty {
    text-align: center; padding: 4rem 1rem; color: #c0c8d4;
}
.disc-index-empty i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: .3; }
.disc-index-empty p { font-size: .95rem; margin: 0 0 .35rem; font-weight: 600; color: #a0aec0; }
.disc-index-empty small { font-size: .8rem; }
</style>
@endpush

@php
    $roleLabels   = [1 => 'Admin', 2 => 'Registrar', 3 => 'Teacher', 4 => 'Student'];
    $roleBgColors = [1 => '#552b20', 2 => '#6366f1', 3 => '#0ea5e9', 4 => '#16a34a'];
    $iconColors   = ['color-1', 'color-2', 'color-3', 'color-4'];
@endphp

<div class="dashboard-container">

    {{-- ── Header ── --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Discussions</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-comments"></i> Class conversations across your courses
                        @if($courses->count() > 0)
                        <span class="separator">•</span>
                        <span class="pending-notice">{{ $courses->count() }} {{ Str::plural('course', $courses->count()) }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1rem;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1rem;"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="content-grid">
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-comments"></i> Course Discussions</h2>
                    @if($courses->count() > 3)
                    <div class="search-container" style="margin:0;">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-input" placeholder="Search courses..." id="search-disc">
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    @if($courses->isEmpty())
                    <div class="disc-index-empty">
                        <i class="fas fa-comments"></i>
                        <p>No courses available</p>
                        <small>You have no courses assigned yet.</small>
                    </div>
                    @else
                    <div class="disc-course-grid" id="disc-grid">
                        @foreach($courses as $i => $course)
                        @php
                            $hasActivity = $course->total_count > 0;
                            $iconClass   = $iconColors[$i % 4];
                        @endphp
                        <a href="{{ route($layout . '.courses.discussions', $course->encrypted_id) }}"
                           class="disc-course-card {{ !$hasActivity ? 'no-activity' : '' }}"
                           data-title="{{ strtolower($course->title) }} {{ strtolower($course->course_code) }}">

                            {{-- Header --}}
                            <div class="disc-course-card-header">
                                <div class="disc-course-icon {{ $iconClass }}">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="disc-course-title-wrap">
                                    <div class="disc-course-name">{{ $course->title }}</div>
                                    <div class="disc-course-code">
                                        {{ $course->course_code }}
                                        @if($course->teacher)
                                        <span style="color:#cbd5e0;margin:0 .3rem;">·</span>
                                        {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                                        @endif
                                    </div>
                                </div>
                                <div class="disc-activity-dot {{ !$hasActivity ? 'inactive' : '' }}" title="{{ $hasActivity ? 'Has activity' : 'No activity yet' }}"></div>
                            </div>

                            {{-- Stats --}}
                            <div class="disc-course-stats">
                                <div class="disc-stat-cell">
                                    <div class="disc-stat-num">{{ number_format($course->thread_count) }}</div>
                                    <div class="disc-stat-lbl">Threads</div>
                                </div>
                                <div class="disc-stat-cell">
                                    <div class="disc-stat-num">{{ number_format($course->reply_count) }}</div>
                                    <div class="disc-stat-lbl">Replies</div>
                                </div>
                                <div class="disc-stat-cell">
                                    <div class="disc-stat-num">{{ number_format($course->total_count) }}</div>
                                    <div class="disc-stat-lbl">Total</div>
                                </div>
                            </div>

                            {{-- Last activity / CTA --}}
                            @if($hasActivity && $course->last_activity)
                            <div class="disc-course-footer">
                                <div class="disc-last-activity">
                                    <i class="fas fa-clock"></i>
                                    Last activity
                                    <strong>{{ $course->last_activity->diffForHumans() }}</strong>
                                </div>
                                <span class="disc-open-btn">
                                    <i class="fas fa-comments"></i> Open
                                </span>
                            </div>
                            @else
                            <div class="disc-no-msg">No messages yet — start the conversation!</div>
                            <div class="disc-course-footer" style="justify-content:flex-end;">
                                <span class="disc-open-btn">
                                    <i class="fas fa-plus"></i> Start
                                </span>
                            </div>
                            @endif

                        </a>
                        @endforeach
                    </div>

                    <div id="disc-empty" style="display:none;" class="disc-index-empty">
                        <i class="fas fa-search"></i>
                        <p>No courses match your search</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="right-column">

            {{-- Overview stats --}}
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-chart-bar"></i> Overview</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-book"></i> My Courses</span>
                    <span class="val highlight">{{ $courses->count() }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-comments"></i> Active</span>
                    <span class="val highlight">{{ $coursesWithActivity }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-layer-group"></i> Threads</span>
                    <span class="val">{{ number_format($totalThreads) }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-reply"></i> Replies</span>
                    <span class="val">{{ number_format($totalReplies) }}</span>
                </div>
            </div>

            {{-- Active courses (sorted by most recent activity) --}}
            @php $activeCourses = $courses->filter(fn($c) => $c->total_count > 0); @endphp
            @if($activeCourses->isNotEmpty())
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-fire"></i> Recent Activity</h3>
                @foreach($activeCourses->take(5) as $c)
                <div class="info-row-sm" style="align-items:flex-start;">
                    <span class="lbl" style="flex-shrink:0;font-size:.72rem;">
                        <i class="fas fa-graduation-cap" style="color:var(--primary);"></i>
                    </span>
                    <span class="val" style="font-size:.75rem;">
                        {{ Str::limit($c->title, 22) }}
                        <span style="display:block;font-size:.67rem;color:#a0aec0;">
                            {{ $c->last_activity?->diffForHumans() ?? 'No activity' }}
                        </span>
                    </span>
                    <span style="font-size:.65rem;color:#718096;white-space:nowrap;font-weight:700;">
                        {{ $c->total_count }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Guidelines --}}
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-shield-alt"></i> Guidelines</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-heart"></i> Respect</span>
                    <span class="val" style="font-size:.72rem;">Be kind and constructive</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-bullseye"></i> On-topic</span>
                    <span class="val" style="font-size:.72rem;">Stay relevant to the course</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-question-circle"></i> Ask</span>
                    <span class="val" style="font-size:.72rem;">No question is too small</span>
                </div>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-disc');
    const emptyMsg    = document.getElementById('disc-empty');
    const cards       = document.querySelectorAll('#disc-grid .disc-course-card');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            let visible = 0;
            cards.forEach(card => {
                const match = !term || (card.dataset.title || '').includes(term);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (emptyMsg) emptyMsg.style.display = (visible === 0 && cards.length > 0) ? 'block' : 'none';
        });
    }
});
</script>
@endpush
