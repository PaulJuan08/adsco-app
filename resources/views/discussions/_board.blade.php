@push('styles')
<style>
/* ===================== DISCUSSION BOARD ===================== */
.disc-container {
    padding: 1.5rem;
    max-width: 860px;
    margin: 0 auto;
}
.disc-flash {
    padding: .75rem 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    font-size: .875rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.disc-flash.success { background: #ecfdf5; color: #065f46; border: 1px solid #6ee7b7; }
.disc-flash.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }

/* New Post Form */
.disc-new-post {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: 1.5rem;
    border: 1px solid #f0ebe8;
}
.disc-new-label {
    font-size: .75rem;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: .75rem;
}
.disc-new-inner {
    display: flex;
    gap: .875rem;
    align-items: flex-start;
}
.disc-avatar {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #552b20 0%, #d3541b 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1rem;
    overflow: hidden;
    flex-shrink: 0;
}
.disc-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.disc-avatar-sm  { width: 32px; height: 32px; min-width: 32px; font-size: .85rem; }
.disc-textarea-wrap { flex: 1; }
.disc-textarea-wrap textarea {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: .7rem 1rem;
    font-size: .9rem;
    color: #374151;
    resize: vertical;
    min-height: 75px;
    font-family: inherit;
    transition: border-color .15s;
    outline: none;
    box-sizing: border-box;
}
.disc-textarea-wrap textarea:focus {
    border-color: #552b20;
    box-shadow: 0 0 0 3px rgba(85,43,32,.08);
}
.disc-textarea-wrap textarea::placeholder { color: #d1d5db; }
.disc-form-actions {
    margin-top: .5rem;
    display: flex;
    justify-content: flex-end;
    gap: .4rem;
}
.btn-disc-post {
    padding: .42rem 1.15rem;
    background: linear-gradient(135deg, #552b20 0%, #d3541b 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .15s, transform .1s;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}
.btn-disc-post:hover { opacity: .9; transform: translateY(-1px); }
.btn-disc-cancel {
    padding: .42rem .95rem;
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    border-radius: 8px;
    font-size: .85rem;
    cursor: pointer;
    transition: background .15s;
}
.btn-disc-cancel:hover { background: #e5e7eb; }

/* Thread count */
.disc-count {
    font-size: .8rem;
    color: #9ca3af;
    margin-bottom: .65rem;
}

/* Post Card */
.disc-post-wrap { margin-bottom: 1rem; }
.disc-post-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid #f0ebe8;
    transition: box-shadow .15s;
}
.disc-post-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.09); }
.disc-post-inner { display: flex; gap: .875rem; align-items: flex-start; }
.disc-post-content { flex: 1; min-width: 0; }
.disc-post-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .5rem;
    margin-bottom: .4rem;
    flex-wrap: wrap;
}
.disc-post-meta { display: flex; align-items: center; gap: .45rem; flex-wrap: wrap; }
.disc-author-name { font-weight: 600; font-size: .875rem; color: #1f2937; }
.disc-role-badge {
    padding: .12rem .5rem;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: .03em;
}
.disc-post-time { font-size: .75rem; color: #9ca3af; }
.disc-delete-form { margin: 0; display: inline; }
.disc-delete-btn {
    background: none;
    border: none;
    color: #d1d5db;
    cursor: pointer;
    padding: .2rem .35rem;
    border-radius: 6px;
    font-size: .78rem;
    transition: color .15s, background .15s;
    line-height: 1;
}
.disc-delete-btn:hover { color: #ef4444; background: #fef2f2; }
.disc-post-text {
    color: #374151;
    font-size: .9rem;
    line-height: 1.7;
    white-space: pre-wrap;
    word-break: break-word;
    margin-bottom: .65rem;
}
.disc-reply-toggle {
    background: none;
    border: none;
    color: #9ca3af;
    font-size: .8rem;
    cursor: pointer;
    padding: .2rem 0;
    transition: color .15s;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
}
.disc-reply-toggle:hover { color: #552b20; }

/* Replies */
.disc-replies {
    margin-top: .875rem;
    padding-top: .875rem;
    border-top: 1px solid #f3f4f6;
    display: flex;
    flex-direction: column;
    gap: .65rem;
}
.disc-reply-row { display: flex; gap: .65rem; align-items: flex-start; }
.disc-reply-bubble {
    flex: 1;
    background: #fafaf9;
    border-radius: 10px;
    padding: .75rem 1rem;
    border: 1px solid #f0ebe8;
}
.disc-reply-text {
    color: #374151;
    font-size: .875rem;
    line-height: 1.65;
    white-space: pre-wrap;
    word-break: break-word;
    margin-top: .35rem;
}

/* Inline Reply Form */
.disc-reply-form-wrap {
    margin-top: .875rem;
    padding-top: .875rem;
    border-top: 1px solid #f3f4f6;
}

/* Empty State */
.disc-empty {
    text-align: center;
    padding: 3.5rem 1rem;
    color: #9ca3af;
}
.disc-empty i { font-size: 2.5rem; display: block; margin-bottom: .75rem; opacity: .35; }
.disc-empty p { font-size: .9rem; margin: 0; }

@media (max-width: 640px) {
    .disc-container { padding: 1rem .75rem; }
    .disc-post-card, .disc-new-post { padding: 1rem; border-radius: 12px; }
}
</style>
@endpush

@php
    $roleLabels   = [1 => 'Admin', 2 => 'Registrar', 3 => 'Teacher', 4 => 'Student'];
    $roleBgColors = [1 => '#552b20', 2 => '#6366f1', 3 => '#0ea5e9', 4 => '#16a34a'];
@endphp

<div class="form-container">
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                <i class="fas fa-comments"></i>
            </div>
            <h1 class="card-title">Discussion</h1>
        </div>
        <div class="top-actions">
            <a href="{{ route($layout . '.courses.show', $encryptedId) }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back to Course
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="disc-container">

            @if(session('success'))
                <div class="disc-flash success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="disc-flash error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            {{-- ── New Post Form ── --}}
            <div class="disc-new-post">
                <div class="disc-new-label"><i class="fas fa-pencil-alt" style="margin-right:.35rem;"></i>Start a Discussion</div>
                <form action="{{ route($layout . '.courses.discussions.store', $encryptedId) }}" method="POST">
                    @csrf
                    <div class="disc-new-inner">
                        <div class="disc-avatar">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="">
                            @elseif(auth()->user()->sex === 'female')
                                <i class="fas fa-person-dress"></i>
                            @else
                                <i class="fas fa-person"></i>
                            @endif
                        </div>
                        <div class="disc-textarea-wrap">
                            <textarea name="body" placeholder="Share something with the class..." required></textarea>
                            <div class="disc-form-actions">
                                <button type="submit" class="btn-disc-post">
                                    <i class="fas fa-paper-plane"></i> Post
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($discussions->count() > 0)
                <div class="disc-count">
                    {{ $discussions->count() }} {{ Str::plural('thread', $discussions->count()) }}
                </div>
            @endif

            {{-- ── Threads ── --}}
            @forelse($discussions as $post)
            <div class="disc-post-wrap">
                <div class="disc-post-card">
                    <div class="disc-post-inner">

                        {{-- Author avatar --}}
                        <div class="disc-avatar">
                            @if($post->author?->avatar)
                                <img src="{{ Storage::url($post->author->avatar) }}" alt="">
                            @elseif($post->author?->sex === 'female')
                                <i class="fas fa-person-dress"></i>
                            @else
                                <i class="fas fa-person"></i>
                            @endif
                        </div>

                        <div class="disc-post-content">
                            {{-- Header: name / badge / time / delete --}}
                            <div class="disc-post-top">
                                <div class="disc-post-meta">
                                    <span class="disc-author-name">{{ $post->author?->f_name }} {{ $post->author?->l_name }}</span>
                                    <span class="disc-role-badge" style="background:{{ $roleBgColors[$post->author?->role] ?? '#9ca3af' }};">
                                        {{ $roleLabels[$post->author?->role] ?? 'User' }}
                                    </span>
                                    <span class="disc-post-time">{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                                @if(auth()->id() === $post->user_id || auth()->user()->role === 1)
                                <form class="disc-delete-form" method="POST"
                                      action="{{ route($layout . '.courses.discussions.destroy', [$encryptedId, $post->id]) }}"
                                      onsubmit="return confirm('Delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="disc-delete-btn" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="disc-post-text">{{ $post->body }}</div>

                            {{-- Reply toggle --}}
                            <button type="button" class="disc-reply-toggle" onclick="discToggleReply({{ $post->id }})">
                                <i class="fas fa-reply"></i>
                                @if($post->replies->count() > 0)
                                    {{ $post->replies->count() }} {{ Str::plural('Reply', $post->replies->count()) }}
                                @else
                                    Reply
                                @endif
                            </button>

                            {{-- Existing replies --}}
                            @if($post->replies->isNotEmpty())
                            <div class="disc-replies">
                                @foreach($post->replies as $reply)
                                <div class="disc-reply-row">
                                    <div class="disc-avatar disc-avatar-sm">
                                        @if($reply->author?->avatar)
                                            <img src="{{ Storage::url($reply->author->avatar) }}" alt="">
                                        @elseif($reply->author?->sex === 'female')
                                            <i class="fas fa-person-dress"></i>
                                        @else
                                            <i class="fas fa-person"></i>
                                        @endif
                                    </div>
                                    <div class="disc-reply-bubble">
                                        <div class="disc-post-top">
                                            <div class="disc-post-meta">
                                                <span class="disc-author-name">{{ $reply->author?->f_name }} {{ $reply->author?->l_name }}</span>
                                                <span class="disc-role-badge" style="background:{{ $roleBgColors[$reply->author?->role] ?? '#9ca3af' }};">
                                                    {{ $roleLabels[$reply->author?->role] ?? 'User' }}
                                                </span>
                                                <span class="disc-post-time">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if(auth()->id() === $reply->user_id || auth()->user()->role === 1)
                                            <form class="disc-delete-form" method="POST"
                                                  action="{{ route($layout . '.courses.discussions.destroy', [$encryptedId, $reply->id]) }}"
                                                  onsubmit="return confirm('Delete this reply?')">
                                                @csrf
                                                @method('DELETE')
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
                                    <div class="disc-new-inner">
                                        <div class="disc-avatar disc-avatar-sm">
                                            @if(auth()->user()->avatar)
                                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="">
                                            @elseif(auth()->user()->sex === 'female')
                                                <i class="fas fa-person-dress"></i>
                                            @else
                                                <i class="fas fa-person"></i>
                                            @endif
                                        </div>
                                        <div class="disc-textarea-wrap">
                                            <textarea name="body" placeholder="Write a reply..." rows="2" required></textarea>
                                            <div class="disc-form-actions">
                                                <button type="button" class="btn-disc-cancel" onclick="discToggleReply({{ $post->id }})">Cancel</button>
                                                <button type="submit" class="btn-disc-post">
                                                    <i class="fas fa-reply"></i> Reply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>{{-- /.disc-post-content --}}
                    </div>{{-- /.disc-post-inner --}}
                </div>{{-- /.disc-post-card --}}
            </div>{{-- /.disc-post-wrap --}}
            @empty
            <div class="disc-empty">
                <i class="fas fa-comments"></i>
                <p>No discussions yet. Be the first to start a conversation!</p>
            </div>
            @endforelse

        </div>{{-- /.disc-container --}}
    </div>{{-- /.card-body --}}
</div>{{-- /.form-container --}}

@push('scripts')
<script>
function discToggleReply(postId) {
    var form = document.getElementById('disc-reply-form-' + postId);
    if (!form) return;
    var hidden = form.style.display === 'none' || form.style.display === '';
    form.style.display = hidden ? 'block' : 'none';
    if (hidden) {
        var ta = form.querySelector('textarea');
        if (ta) { ta.focus(); }
    }
}
</script>
@endpush
