@push('styles')
<style>
/* ═══════════════════════════════════════════
   DISCUSSION BOARD — Enhanced
   ═══════════════════════════════════════════ */

/* ── Sidebar card & info-row (copied from course-show.css pattern) ── */
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

/* ── Top action button (frosted glass on dark header) ── */
.top-action-btn {
    color: white;
    font-size: .875rem; font-weight: 600;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .5rem 1rem;
    background: rgba(255,255,255,.15);
    border-radius: 8px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.2);
    transition: all .2s ease;
    cursor: pointer;
}
.top-action-btn:hover {
    background: rgba(255,255,255,.25);
    transform: translateY(-2px);
    color: white; text-decoration: none;
}

/* ── Avatar ── */
.disc-avatar {
    width: 40px; height: 40px; min-width: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #552b20 0%, #d3541b 100%);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1rem;
    overflow: hidden; flex-shrink: 0;
}
.disc-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.disc-avatar-sm  { width: 32px; height: 32px; min-width: 32px; font-size: .82rem; }

/* ── New post box ── */
.disc-compose {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #f0ebe8;
    box-shadow: 0 2px 10px rgba(85,43,32,.06);
    padding: 1.1rem 1.25rem;
    margin-bottom: 1.25rem;
}
.disc-compose-label {
    font-size: .7rem; font-weight: 700; color: #a0aec0;
    text-transform: uppercase; letter-spacing: .07em;
    margin-bottom: .75rem; display: flex; align-items: center; gap: .35rem;
}
.disc-compose-inner { display: flex; gap: .875rem; align-items: flex-start; }
.disc-compose-right { flex: 1; min-width: 0; }
.disc-compose-right textarea {
    width: 100%; box-sizing: border-box;
    border: 1.5px solid #e5e7eb; border-radius: 10px;
    padding: .7rem 1rem; font-size: .875rem; color: #374151;
    resize: vertical; min-height: 72px; font-family: inherit;
    transition: border-color .15s, box-shadow .15s; outline: none;
}
.disc-compose-right textarea:focus {
    border-color: #552b20;
    box-shadow: 0 0 0 3px rgba(85,43,32,.08);
}
.disc-compose-right textarea::placeholder { color: #d1d5db; }
.disc-compose-actions {
    margin-top: .45rem;
    display: flex; justify-content: flex-end; gap: .4rem;
}
.btn-post {
    padding: .42rem 1.15rem;
    background: linear-gradient(135deg, #552b20, #d3541b);
    color: #fff; border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 700; cursor: pointer;
    display: inline-flex; align-items: center; gap: .35rem;
    transition: opacity .15s, transform .1s;
}
.btn-post:hover { opacity: .9; transform: translateY(-1px); }
.btn-cancel-reply {
    padding: .42rem .9rem;
    background: #f3f4f6; color: #6b7280;
    border: none; border-radius: 8px; font-size: .82rem;
    cursor: pointer; transition: background .15s;
}
.btn-cancel-reply:hover { background: #e5e7eb; }

/* ── Thread list header ── */
.disc-feed-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .65rem;
}
.disc-feed-count {
    font-size: .78rem; font-weight: 600; color: #9ca3af;
}

/* ── Post card ── */
.disc-thread { margin-bottom: .875rem; }
.disc-post-card {
    background: #fff; border-radius: 14px;
    border: 1px solid #f0ebe8;
    box-shadow: 0 1px 8px rgba(85,43,32,.05);
    padding: 1.1rem 1.25rem;
    transition: box-shadow .15s;
}
.disc-post-card:hover { box-shadow: 0 4px 16px rgba(85,43,32,.1); }
.disc-post-inner { display: flex; gap: .875rem; align-items: flex-start; }
.disc-post-body  { flex: 1; min-width: 0; }

/* ── Post header ── */
.disc-post-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem; margin-bottom: .45rem; flex-wrap: wrap;
}
.disc-post-meta { display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
.disc-author-name { font-weight: 700; font-size: .875rem; color: #1f2937; }
.disc-role-badge {
    padding: 2px 8px; border-radius: 999px;
    font-size: .63rem; font-weight: 700; color: #fff; letter-spacing: .03em;
}
.disc-post-time { font-size: .72rem; color: #b0b9c8; }

/* ── Post text ── */
.disc-post-text {
    color: #374151; font-size: .875rem; line-height: 1.7;
    white-space: pre-wrap; word-break: break-word;
    margin-bottom: .55rem;
}

/* ── Actions row ── */
.disc-actions-row {
    display: flex; align-items: center; gap: .75rem;
}
.disc-reply-btn {
    background: none; border: none; cursor: pointer;
    color: #a0aec0; font-size: .78rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .2rem 0; transition: color .15s;
}
.disc-reply-btn:hover { color: #552b20; }
.disc-reply-count-badge {
    background: #f0ebe8; color: #552b20;
    font-size: .65rem; font-weight: 700;
    padding: 1px 7px; border-radius: 20px;
}
.disc-delete-btn {
    background: none; border: none; color: #e2e8f0;
    cursor: pointer; padding: .2rem .35rem; border-radius: 6px;
    font-size: .75rem; transition: color .15s, background .15s;
    margin-left: auto;
}
.disc-delete-btn:hover { color: #ef4444; background: #fef2f2; }

/* ── Replies section ── */
.disc-replies {
    margin-top: .875rem; padding-top: .875rem;
    border-top: 1px solid #f5f0ed;
    display: flex; flex-direction: column; gap: .55rem;
}
.disc-reply-row { display: flex; gap: .65rem; align-items: flex-start; }
.disc-reply-bubble {
    flex: 1;
    background: #fdfaf8; border-radius: 10px;
    padding: .7rem 1rem; border: 1px solid #f0ebe8;
}
.disc-reply-text {
    color: #374151; font-size: .845rem; line-height: 1.65;
    white-space: pre-wrap; word-break: break-word; margin-top: .3rem;
}

/* ── Inline reply form ── */
.disc-reply-form-wrap {
    margin-top: .875rem; padding-top: .875rem;
    border-top: 1px solid #f5f0ed;
}

/* ── Empty state ── */
.disc-empty {
    text-align: center; padding: 3.5rem 1rem; color: #c0c8d4;
}
.disc-empty i { font-size: 2.8rem; display: block; margin-bottom: 1rem; opacity: .35; }
.disc-empty p { font-size: .9rem; margin: 0 0 .35rem; font-weight: 600; }
.disc-empty small { font-size: .8rem; opacity: .7; }

/* ── Sidebar: participants ── */
.disc-participant {
    display: flex; align-items: center; gap: .55rem;
    padding: .35rem 0; border-bottom: 1px solid #f5f0ed;
}
.disc-participant:last-child { border-bottom: none; }
.disc-participant-info { flex: 1; min-width: 0; }
.disc-participant-name { font-size: .8rem; font-weight: 600; color: #2d3748; }
.disc-participant-role { font-size: .68rem; color: #a0aec0; }

@media (max-width: 640px) {
    .disc-compose, .disc-post-card { padding: .9rem 1rem; border-radius: 12px; }
}
</style>
@endpush

@php
    $roleLabels   = [1 => 'Admin', 2 => 'Registrar', 3 => 'Teacher', 4 => 'Student'];
    $roleBgColors = [1 => '#552b20', 2 => '#6366f1', 3 => '#0ea5e9', 4 => '#16a34a'];

    // Compute sidebar stats
    $totalThreads = $discussions->count();
    $totalReplies = $discussions->sum(fn($d) => $d->replies->count());
    $totalMessages = $totalThreads + $totalReplies;

    // Unique participants
    $participants = collect();
    foreach ($discussions as $post) {
        if ($post->author) $participants->push($post->author);
        foreach ($post->replies as $reply) {
            if ($reply->author) $participants->push($reply->author);
        }
    }
    $participants = $participants->unique('id')->take(8);
@endphp

<div class="dashboard-container">

    {{-- ── Header ── --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Discussion</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-comments"></i> {{ $course->title }}
                        <span class="separator">•</span>
                        <span class="pending-notice">{{ $totalMessages }} {{ Str::plural('message', $totalMessages) }}</span>
                    </p>
                </div>
            </div>
            <div style="display:flex;gap:.5rem;">
                <a href="{{ route($layout . '.discussions.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
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
        {{-- ── Main Feed ── --}}
        <div class="left-column">

            {{-- Compose box --}}
            <div class="disc-compose">
                <div class="disc-compose-label">
                    <i class="fas fa-pencil-alt"></i> Start a Discussion
                </div>
                <form action="{{ route($layout . '.courses.discussions.store', $encryptedId) }}" method="POST">
                    @csrf
                    <div class="disc-compose-inner">
                        <div class="disc-avatar">
                            @if(auth()->user()->profile_photo_url)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="">
                            @elseif(auth()->user()->sex === 'female')
                                <i class="fas fa-person-dress"></i>
                            @else
                                <i class="fas fa-person"></i>
                            @endif
                        </div>
                        <div class="disc-compose-right">
                            <textarea name="body" placeholder="Share something with the class..." required></textarea>
                            <div class="disc-compose-actions">
                                <button type="submit" class="btn-post">
                                    <i class="fas fa-paper-plane"></i> Post
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Feed header --}}
            @if($totalThreads > 0)
            <div class="disc-feed-header">
                <span class="disc-feed-count">
                    <i class="fas fa-layer-group" style="margin-right:.3rem;color:#d3541b;"></i>
                    {{ $totalThreads }} {{ Str::plural('Thread', $totalThreads) }}
                    @if($totalReplies > 0)
                        &nbsp;·&nbsp; {{ $totalReplies }} {{ Str::plural('Reply', $totalReplies) }}
                    @endif
                </span>
            </div>
            @endif

            {{-- Threads --}}
            @forelse($discussions as $post)
            <div class="disc-thread">
                <div class="disc-post-card">
                    <div class="disc-post-inner">

                        {{-- Avatar --}}
                        <div class="disc-avatar">
                            @if($post->author?->profile_photo_url)
                                <img src="{{ $post->author->profile_photo_url }}" alt="">
                            @elseif($post->author?->sex === 'female')
                                <i class="fas fa-person-dress"></i>
                            @else
                                <i class="fas fa-person"></i>
                            @endif
                        </div>

                        <div class="disc-post-body">
                            {{-- Header --}}
                            <div class="disc-post-head">
                                <div class="disc-post-meta">
                                    <span class="disc-author-name">{{ $post->author?->f_name }} {{ $post->author?->l_name }}</span>
                                    <span class="disc-role-badge" style="background:{{ $roleBgColors[$post->author?->role] ?? '#9ca3af' }};">
                                        {{ $roleLabels[$post->author?->role] ?? 'User' }}
                                    </span>
                                    <span class="disc-post-time">
                                        <i class="fas fa-clock" style="font-size:.65rem;"></i>
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @if(auth()->id() === $post->user_id || auth()->user()->role === 1)
                                <form method="POST" style="margin:0;display:inline;"
                                      action="{{ route($layout . '.courses.discussions.destroy', [$encryptedId, $post->id]) }}"
                                      onsubmit="return confirm('Delete this message?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="disc-delete-btn" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="disc-post-text">{{ $post->body }}</div>

                            {{-- Actions row --}}
                            <div class="disc-actions-row">
                                <button type="button" class="disc-reply-btn" onclick="discToggleReply({{ $post->id }})">
                                    <i class="fas fa-reply"></i>
                                    Reply
                                    @if($post->replies->count() > 0)
                                    <span class="disc-reply-count-badge">{{ $post->replies->count() }}</span>
                                    @endif
                                </button>
                            </div>

                            {{-- Existing replies --}}
                            @if($post->replies->isNotEmpty())
                            <div class="disc-replies">
                                @foreach($post->replies as $reply)
                                <div class="disc-reply-row">
                                    <div class="disc-avatar disc-avatar-sm">
                                        @if($reply->author?->profile_photo_url)
                                            <img src="{{ $reply->author->profile_photo_url }}" alt="">
                                        @elseif($reply->author?->sex === 'female')
                                            <i class="fas fa-person-dress"></i>
                                        @else
                                            <i class="fas fa-person"></i>
                                        @endif
                                    </div>
                                    <div class="disc-reply-bubble">
                                        <div class="disc-post-head" style="margin-bottom:.25rem;">
                                            <div class="disc-post-meta">
                                                <span class="disc-author-name">{{ $reply->author?->f_name }} {{ $reply->author?->l_name }}</span>
                                                <span class="disc-role-badge" style="background:{{ $roleBgColors[$reply->author?->role] ?? '#9ca3af' }};">
                                                    {{ $roleLabels[$reply->author?->role] ?? 'User' }}
                                                </span>
                                                <span class="disc-post-time">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if(auth()->id() === $reply->user_id || auth()->user()->role === 1)
                                            <form method="POST" style="margin:0;display:inline;"
                                                  action="{{ route($layout . '.courses.discussions.destroy', [$encryptedId, $reply->id]) }}"
                                                  onsubmit="return confirm('Delete this reply?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="disc-delete-btn" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                        <div class="disc-reply-text">{{ $reply->body }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- Inline reply form (hidden) --}}
                            <div id="disc-reply-form-{{ $post->id }}" class="disc-reply-form-wrap" style="display:none;">
                                <form action="{{ route($layout . '.courses.discussions.store', $encryptedId) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $post->id }}">
                                    <div class="disc-compose-inner">
                                        <div class="disc-avatar disc-avatar-sm">
                                            @if(auth()->user()->profile_photo_url)
                                                <img src="{{ auth()->user()->profile_photo_url }}" alt="">
                                            @elseif(auth()->user()->sex === 'female')
                                                <i class="fas fa-person-dress"></i>
                                            @else
                                                <i class="fas fa-person"></i>
                                            @endif
                                        </div>
                                        <div class="disc-compose-right">
                                            <textarea name="body" placeholder="Write a reply…" rows="2" required></textarea>
                                            <div class="disc-compose-actions">
                                                <button type="button" class="btn-cancel-reply" onclick="discToggleReply({{ $post->id }})">Cancel</button>
                                                <button type="submit" class="btn-post">
                                                    <i class="fas fa-reply"></i> Reply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>{{-- /.disc-post-body --}}
                    </div>{{-- /.disc-post-inner --}}
                </div>{{-- /.disc-post-card --}}
            </div>{{-- /.disc-thread --}}

            @empty
            <div class="disc-empty">
                <i class="fas fa-comments"></i>
                <p>No discussions yet</p>
                <small>Be the first to start a conversation!</small>
            </div>
            @endforelse

        </div>{{-- /.left-column --}}

        {{-- ── Sidebar ── --}}
        <div class="right-column">

            {{-- Course Info --}}
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-book"></i> Course</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-graduation-cap"></i> Title</span>
                    <span class="val" style="font-size:.75rem;">{{ Str::limit($course->title, 24) }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-hashtag"></i> Code</span>
                    <span class="val">{{ $course->course_code }}</span>
                </div>
                @if($course->teacher)
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-chalkboard-teacher"></i> Teacher</span>
                    <span class="val" style="font-size:.75rem;">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</span>
                </div>
                @endif
            </div>

            {{-- Discussion Stats --}}
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-chart-bar"></i> Stats</h3>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-layer-group"></i> Threads</span>
                    <span class="val highlight">{{ $totalThreads }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-reply"></i> Replies</span>
                    <span class="val highlight">{{ $totalReplies }}</span>
                </div>
                <div class="info-row-sm">
                    <span class="lbl"><i class="fas fa-users"></i> Participants</span>
                    <span class="val highlight">{{ $participants->count() }}</span>
                </div>
            </div>

            {{-- Participants --}}
            @if($participants->isNotEmpty())
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><i class="fas fa-users"></i> Participants</h3>
                @foreach($participants as $p)
                <div class="disc-participant">
                    <div class="disc-avatar disc-avatar-sm">
                        @if($p->profile_photo_url)
                            <img src="{{ $p->profile_photo_url }}" alt="">
                        @elseif($p->sex === 'female')
                            <i class="fas fa-person-dress"></i>
                        @else
                            <i class="fas fa-person"></i>
                        @endif
                    </div>
                    <div class="disc-participant-info">
                        <div class="disc-participant-name">{{ $p->f_name }} {{ $p->l_name }}</div>
                        <div class="disc-participant-role">{{ $roleLabels[$p->role] ?? 'User' }}</div>
                    </div>
                    <span class="disc-role-badge" style="background:{{ $roleBgColors[$p->role] ?? '#9ca3af' }};font-size:.58rem;">
                        {{ Str::limit($roleLabels[$p->role] ?? 'User', 8) }}
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

        </div>{{-- /.right-column --}}
    </div>{{-- /.content-grid --}}
</div>{{-- /.dashboard-container --}}

@push('scripts')
<script>
function discToggleReply(postId) {
    var form = document.getElementById('disc-reply-form-' + postId);
    if (!form) return;
    var hidden = form.style.display === 'none' || form.style.display === '';
    form.style.display = hidden ? 'block' : 'none';
    if (hidden) { var ta = form.querySelector('textarea'); if (ta) ta.focus(); }
}
</script>
@endpush
