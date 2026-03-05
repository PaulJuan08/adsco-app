@extends('layouts.admin')

@section('title', 'Topics - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-index.css') }}">
<style>
.topics-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
}
.topic-card-item:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(85,43,32,.13); }
.topic-card-thumb {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 */
    cursor: pointer;
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
.topic-card-status.published { background: #d1fae5; color: #065f46; }
.topic-card-status.draft     { background: #fef3c7; color: #92400e; }
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
.topic-badge.time       { background: #f3f4f6; color: #6b7280; }
.topic-card-footer {
    padding: .65rem 1rem;
    border-top: 1px solid #f7f0ec;
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem;
}
.topic-card-creator {
    display: flex; align-items: center; gap: .4rem;
    font-size: .72rem; color: #718096;
    min-width: 0;
}
.topic-card-creator span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90px; }
.topic-card-date { font-size: .68rem; color: #a0aec0; }
.topic-card-actions { display: flex; gap: .3rem; }
.btn-card-action {
    width: 28px; height: 28px;
    border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .75rem;
    border: none; cursor: pointer;
    text-decoration: none;
    transition: all .15s;
}
.btn-card-edit { background: #fef3c7; color: #d97706; }
.btn-card-edit:hover { background: #d97706; color: #fff; }
.btn-card-view { background: #eff6ff; color: #3b82f6; }
.btn-card-view:hover { background: #3b82f6; color: #fff; }
</style>
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Dashboard Header — consistent with dashboard.css -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Topic Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-chalkboard"></i> Manage all learning topics across courses
                        <span class="separator">•</span>
                        <span class="pending-notice">
                            <i class="fas fa-chalkboard"></i> {{ $topics->total() ?? $topics->count() }} topics · {{ $publishedTopics ?? 0 }} published
                        </span>
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.topics.create') }}" class="top-action-btn">
                    <i class="fas fa-plus-circle"></i> Add Topic
                </a>
                <a href="{{ route('admin.courses.index') }}" class="top-action-btn">
                    <i class="fas fa-book"></i> Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.topics.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($topics->total() ?? $topics->count()) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-chalkboard"></i></div>
            </div>
            <div class="stat-link">View all topics <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.topics.index') }}?status=published" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published Topics</div>
                    <div class="stat-number">{{ number_format($publishedTopics ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-link">View published <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.topics.index') }}?has_video=true" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">With Video</div>
                    <div class="stat-number">{{ number_format($topicsWithVideo ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-video"></i></div>
            </div>
            <div class="stat-link">View with video <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.topics.index') }}?status=draft" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Draft Topics</div>
                    <div class="stat-number">{{ number_format($draftTopics ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-link">View drafts <i class="fas fa-arrow-right"></i></div>
        </a>
    </div>

    <!-- Topics List — full width -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-chalkboard" style="color: var(--primary); margin-right: 0.5rem;"></i>
                All Topics
            </h2>
            <div class="header-actions-bar">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                </div>
                <div class="filter-container">
                    <select class="form-select" id="course-filter">
                        <option value="">All Courses</option>
                        @foreach($courses ?? [] as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }} ({{ $course->course_code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <button id="print-report" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
                <button id="export-csv" class="btn btn-secondary">
                    <i class="fas fa-file-csv"></i> Export
                </button>
                <a href="{{ route('admin.topics.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Topic
                </a>
            </div>
        </div>

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

            @if($topics->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-chalkboard"></i></div>
                    <h3 class="empty-title">No topics yet</h3>
                    <p class="empty-text">Start by adding the first learning topic.</p>
                    <a href="{{ route('admin.topics.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Create Your First Topic
                    </a>
                    <div class="empty-hint">
                        <i class="fas fa-lightbulb"></i> Topics are learning units under each course
                    </div>
                </div>
            @else
                <div class="topics-card-grid" id="topics-table">
                    @foreach($topics as $topic)
                    @php
                        try { $encryptedId = Crypt::encrypt($topic->id); }
                        catch (\Exception $e) { $encryptedId = ''; }

                        // Get primary course (first course in the relationship)
                        $primaryCourse = $topic->primary_course;
                        $courseCount = $topic->courses->count();

                        // Get creator info
                        $creatorName = 'System';
                        $creatorRole = 'Auto-generated';
                        $creatorAvatar = 'S';
                        $creatorColor = '#6b7280';

                        if($topic->creator) {
                            $creatorName = $topic->creator->f_name . ' ' . $topic->creator->l_name;
                            $creatorAvatar = strtoupper(substr($topic->creator->f_name, 0, 1)) . strtoupper(substr($topic->creator->l_name, 0, 1));

                            if($topic->creator->role == 1) {
                                $creatorRole = 'Admin';
                                $creatorColor = '#ef4444';
                            } elseif($topic->creator->role == 3) {
                                $creatorRole = 'Teacher';
                                $creatorColor = '#10b981';
                            } elseif($topic->creator->role == 4) {
                                $creatorRole = 'Student';
                                $creatorColor = '#8b5cf6';
                            } else {
                                $creatorRole = 'Staff';
                                $creatorColor = '#6b7280';
                            }
                        }

                        // Get updater info
                        $updaterName = null;
                        $updaterRole = '';
                        $updaterAvatar = '—';
                        $updaterColor = '#6b7280';

                        if($topic->updater) {
                            $updaterName = $topic->updater->f_name . ' ' . $topic->updater->l_name;
                            $updaterAvatar = strtoupper(substr($topic->updater->f_name, 0, 1)) . strtoupper(substr($topic->updater->l_name, 0, 1));
                            if($topic->updater->role == 1) {
                                $updaterRole = 'Admin';
                                $updaterColor = '#ef4444';
                            } elseif($topic->updater->role == 3) {
                                $updaterRole = 'Teacher';
                                $updaterColor = '#10b981';
                            } else {
                                $updaterRole = 'Staff';
                                $updaterColor = '#6b7280';
                            }
                        }

                        // YouTube thumbnail extraction
                        $videoLink = $topic->video_link ?? '';
                        $youtubeThumbnail = null;
                        if ($videoLink) {
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $videoLink, $m)) {
                                $youtubeThumbnail = 'https://img.youtube.com/vi/' . $m[1] . '/mqdefault.jpg';
                            }
                        }
                    @endphp
                    <div class="topic-card-item"
                        data-title="{{ strtolower($topic->title) }}"
                        data-course="{{ strtolower($primaryCourse->title ?? '') }}"
                        data-course-id="{{ $primaryCourse->id ?? '' }}"
                        data-creator="{{ strtolower($creatorName) }}"
                        data-topic-id="{{ $topic->id }}"
                        data-encrypted="{{ $encryptedId }}">

                        {{-- Thumbnail --}}
                        <a href="{{ $encryptedId ? route('admin.topics.show', ['encryptedId' => $encryptedId]) : '#' }}" class="topic-card-thumb" style="display:block;">
                            @if($youtubeThumbnail)
                                <img src="{{ $youtubeThumbnail }}" alt="{{ $topic->title }}" loading="lazy">
                                <div class="topic-card-play"><i class="fab fa-youtube"></i></div>
                            @else
                                <div class="topic-card-thumb-placeholder topic-{{ ($loop->index % 3) + 1 }}">
                                    <i class="fas fa-{{ $topic->hasVideo() ? 'play-circle' : 'chalkboard' }}" style="font-size:3rem;"></i>
                                </div>
                                @if($topic->hasVideo())
                                <div class="topic-card-play"><i class="fas fa-play-circle"></i></div>
                                @endif
                            @endif
                            <span class="topic-card-status {{ $topic->is_published ? 'published' : 'draft' }}">
                                <i class="fas fa-{{ $topic->is_published ? 'check-circle' : 'clock' }}"></i>
                                {{ $topic->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </a>

                        {{-- Card body --}}
                        <div class="topic-card-body">
                            <p class="topic-card-title">{{ $topic->title }}</p>
                            <div class="topic-card-course">
                                <i class="fas fa-book" style="flex-shrink:0;"></i>
                                <span style="overflow:hidden;text-overflow:ellipsis;">
                                    @if($primaryCourse)
                                        {{ Str::limit($primaryCourse->title, 30) }}
                                        @if($courseCount > 1)
                                            <span style="color:#a0aec0;">+{{ $courseCount - 1 }} more</span>
                                        @endif
                                    @else
                                        No course assigned
                                    @endif
                                </span>
                            </div>
                            <div class="topic-card-badges">
                                @if($topic->hasVideo())
                                    <span class="topic-badge video topic-video-indicator"><i class="fas fa-video"></i> Video</span>
                                @endif
                                @if($topic->hasAttachment())
                                    <span class="topic-badge attachment topic-attachment-indicator"><i class="fas fa-paperclip"></i> Attachment</span>
                                @endif
                                @if($topic->estimated_time)
                                    <span class="topic-badge time"><i class="fas fa-clock"></i> {{ $topic->formatted_estimated_time }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Card footer --}}
                        <div class="topic-card-footer">
                            <div class="topic-card-creator">
                                <div class="creator-avatar mini" style="background:{{ $creatorColor }};width:22px;height:22px;font-size:.6rem;flex-shrink:0;">{{ $creatorAvatar }}</div>
                                <span>{{ $creatorName }}</span>
                            </div>
                            <span class="topic-card-date item-date">{{ $topic->created_at->format('M d, Y') }}</span>
                            <div class="topic-card-actions">
                                @if($encryptedId)
                                    <a href="{{ route('admin.topics.edit', ['encryptedId' => $encryptedId]) }}" class="btn-card-action btn-card-edit" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="{{ route('admin.topics.show', ['encryptedId' => $encryptedId]) }}" class="btn-card-action btn-card-view" title="View"><i class="fas fa-eye"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($topics instanceof \Illuminate\Pagination\AbstractPaginator && $topics->hasPages())
        <div class="card-footer">
            <div class="pagination-info">
                Showing {{ $topics->firstItem() }} to {{ $topics->lastItem() }} of {{ $topics->total() }} entries
            </div>
            <div class="pagination-links">
                @if($topics->onFirstPage())
                    <span class="pagination-btn disabled">Previous</span>
                @else
                    <a href="{{ $topics->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif

                @foreach(range(1, $topics->lastPage()) as $page)
                    @if($page == $topics->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @elseif(abs($page - $topics->currentPage()) <= 2)
                        <a href="{{ $topics->url($page) }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($topics->hasMorePages())
                    <a href="{{ $topics->nextPageUrl() }}" class="pagination-btn">Next</a>
                @else
                    <span class="pagination-btn disabled">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Hidden Print Content -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5;">ADSCO Topic Management Report</h1>
                <p style="color: #666;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;">Topic Statistics Summary</h2>
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #eef2ff; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #4f46e5;">{{ $topics->total() ?? $topics->count() }}</div>
                        <div style="font-size: 14px; color: #4f46e5;">Total Topics</div>
                    </div>
                    <div style="background: #dcfce7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #059669;">{{ $publishedTopics ?? 0 }}</div>
                        <div style="font-size: 14px; color: #059669;">Published</div>
                    </div>
                    <div style="background: #fee2e2; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #dc2626;">{{ $draftTopics ?? 0 }}</div>
                        <div style="font-size: 14px; color: #dc2626;">Draft</div>
                    </div>
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #d97706;">{{ $topicsWithVideo ?? 0 }}</div>
                        <div style="font-size: 14px; color: #d97706;">With Video</div>
                    </div>
                    <div style="background: #dbeafe; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $topicsWithAttachment ?? 0 }}</div>
                        <div style="font-size: 14px; color: #2563eb;">With Attachments</div>
                    </div>
                </div>
            </div>
            
            <h2 style="color: #333; margin-bottom: 15px;">Topic List</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Topic Title</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Course(s)</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Created By</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Has Video</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Has Attachment</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                    @php
                        $primaryCourse = $topic->primary_course;
                        $courseCount = $topic->courses->count();
                        
                        $creatorName = 'System';
                        if($topic->creator) {
                            $creatorName = $topic->creator->f_name . ' ' . $topic->creator->l_name;
                            if($topic->creator->role == 1) $creatorName .= ' (Admin)';
                            elseif($topic->creator->role == 2) $creatorName .= ' (Registrar)';
                            elseif($topic->creator->role == 3) $creatorName .= ' (Teacher)';
                            elseif($topic->creator->role == 4) $creatorName .= ' (Student)';
                        }
                    @endphp
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $topic->title }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">
                            @if($primaryCourse)
                                {{ $primaryCourse->title }} ({{ $primaryCourse->course_code }})
                                @if($courseCount > 1)
                                    <br><small>+ {{ $courseCount - 1 }} more course(s)</small>
                                @endif
                            @else
                                No course assigned
                            @endif
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $creatorName }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $topic->hasVideo() ? 'Yes' : 'No' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $topic->hasAttachment() ? 'Yes' : 'No' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $topic->is_published ? 'Published' : 'Draft' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $topic->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="color: #6b7280; font-size: 14px;">
                    Total Topics: {{ $topics->total() ?? $topics->count() }} | 
                    Generated by: {{ Auth::user()->f_name }} {{ Auth::user()->l_name }} | 
                    Page 1 of 1
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Search + course filter
    const searchInput = document.getElementById('search-topics');
    const courseFilter = document.getElementById('course-filter');

    function filterRows() {
        const term = searchInput?.value.toLowerCase() ?? '';
        const courseId = courseFilter?.value ?? '';
        document.querySelectorAll('.topic-card-item').forEach(card => {
            const matchSearch = !term ||
                card.dataset.title.includes(term) ||
                (card.dataset.course && card.dataset.course.includes(term)) ||
                (card.dataset.creator && card.dataset.creator.includes(term));
            const matchCourse = !courseId || card.dataset.courseId === courseId;
            card.style.display = matchSearch && matchCourse ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterRows);

    // Course filter — server-side redirect
    courseFilter?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        this.value ? url.searchParams.set('course_id', this.value) : url.searchParams.delete('course_id');
        window.location.href = url.toString();
    });

    // Print functionality
    document.getElementById('print-report')?.addEventListener('click', function () {
        const content = document.getElementById('print-content').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>Topic Report</title>
            <style>
                body{font-family:Arial,sans-serif;padding:20px;} 
                table{width:100%;border-collapse:collapse;} 
                th{background:#f3f4f6;}
                @media print {
                    @page { size: landscape; margin: 0.5in; }
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
            </head><body>${content}<script>
                window.onload=()=>{ window.print(); setTimeout(()=>window.close(), 100); }
            <\/script></body></html>`);
        win.document.close();
    });

    // Export CSV
    document.getElementById('export-csv')?.addEventListener('click', function () {
        const rows = [['Topic Title', 'Course(s)', 'Created By', 'Has Video', 'Has Attachment', 'Status', 'Created']];
        document.querySelectorAll('.topic-card-item').forEach(card => {
            if (card.style.display === 'none') return;

            // Title
            const title = card.querySelector('.topic-card-title')?.textContent.trim() || '';

            // Course
            const courseText = card.querySelector('.topic-card-course span')?.textContent.trim() || 'No course assigned';

            // Creator
            const creatorText = card.querySelector('.topic-card-creator span')?.textContent.trim() || 'System';

            // Badges
            const hasVideo = card.querySelector('.topic-video-indicator') ? 'Yes' : 'No';
            const hasAttachment = card.querySelector('.topic-attachment-indicator') ? 'Yes' : 'No';

            // Status
            const statusBadge = card.querySelector('.topic-card-status');
            const status = statusBadge ? statusBadge.textContent.trim() : 'Unknown';

            // Date
            const date = card.querySelector('.item-date')?.textContent.trim() || '';

            rows.push([title, courseText, creatorText, hasVideo, hasAttachment, status, date]
                .map(v => `"${v.replace(/"/g, '""')}"`));
        });

        const blob = new Blob(['\uFEFF' + rows.map(r => r.join(',')).join('\n')], { type: 'text/csv;charset=utf-8;' });
        const a = Object.assign(document.createElement('a'), {
            href: URL.createObjectURL(blob),
            download: `topics_${new Date().toISOString().slice(0,10)}.csv`
        });
        document.body.appendChild(a); a.click(); a.remove();
    });

});
</script>
@endpush