@extends('layouts.admin')

@section('title', 'Topics - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-index.css') }}">
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
                <div class="table-responsive">
                    <table class="topics-table" id="topics-table">
                        <thead>
                            <tr>
                                <th>Topic Title</th>
                                <th class="hide-on-mobile">Course(s)</th>
                                <th class="hide-on-tablet">Created By</th>
                                <th class="hide-on-tablet">Status</th>
                                <th class="hide-on-tablet">Created</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                    } elseif($topic->creator->role == 2) {
                                        $creatorRole = 'Registrar';
                                        $creatorColor = '#3b82f6';
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
                            @endphp
                            <tr class="clickable-row"
                                data-href="{{ $encryptedId ? route('admin.topics.show', ['encryptedId' => $encryptedId]) : '#' }}"
                                data-title="{{ strtolower($topic->title) }}"
                                data-course="{{ strtolower($primaryCourse->title ?? '') }}"
                                data-course-id="{{ $primaryCourse->id ?? '' }}"
                                data-creator="{{ strtolower($creatorName) }}"
                                data-topic-id="{{ $topic->id }}"
                                data-encrypted="{{ $encryptedId }}">
                                <td>
                                    <div class="topic-info-cell">
                                        <div class="topic-icon topic-{{ ($loop->index % 3) + 1 }}">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="topic-details">
                                            <div class="topic-name">{{ $topic->title }}</div>
                                            @if($topic->hasVideo())
                                            <div class="topic-video-indicator">
                                                <i class="fas fa-video"></i> Has video content
                                            </div>
                                            @endif
                                            @if($topic->estimated_time)
                                            <div class="topic-time-indicator">
                                                <i class="fas fa-clock"></i> {{ $topic->formatted_estimated_time }}
                                            </div>
                                            @endif
                                            <div class="topic-mobile-info">
                                                @if($primaryCourse)
                                                <div class="course-mobile">
                                                    <i class="fas fa-book"></i> {{ Str::limit($primaryCourse->title, 30) }}
                                                    @if($courseCount > 1)
                                                    <span class="badge-count">+{{ $courseCount - 1 }}</span>
                                                    @endif
                                                </div>
                                                @else
                                                <div class="course-mobile">
                                                    <i class="fas fa-book"></i> No course
                                                </div>
                                                @endif
                                                <div class="creator-mobile">
                                                    <i class="fas fa-user"></i> {{ $creatorName }}
                                                </div>
                                                @if($topic->is_published)
                                                    <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Published</span>
                                                @else
                                                    <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Draft</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-on-mobile">
                                    @if($primaryCourse)
                                    <div class="course-info">
                                        <div class="course-avatar">
                                            {{ strtoupper(substr($primaryCourse->title, 0, 1)) }}
                                        </div>
                                        <div class="course-details">
                                            <div class="course-name">{{ Str::limit($primaryCourse->title, 25) }}</div>
                                            <div class="course-code">{{ $primaryCourse->course_code }}</div>
                                            @if($courseCount > 1)
                                            <div class="course-count">
                                                <i class="fas fa-layer-group"></i> +{{ $courseCount - 1 }} more
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <span class="no-course">No course assigned</span>
                                    @endif
                                </td>
                                <td class="hide-on-tablet">
                                    <div class="creator-info">
                                        <div class="creator-avatar mini" style="background: {{ $creatorColor }};">
                                            {{ $creatorAvatar }}
                                        </div>
                                        <div class="creator-details">
                                            <div class="creator-name">{{ $creatorName }}</div>
                                            <div class="creator-role">{{ $creatorRole }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-on-tablet">
                                    @if($topic->is_published)
                                        <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Published</span>
                                    @else
                                        <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Draft</span>
                                    @endif
                                </td>
                                <td class="hide-on-tablet">
                                    <span class="item-date">{{ $topic->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Topic Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>

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

    // Clickable rows
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function (e) {
            if (e.target.closest('a, button')) return;
            const href = this.dataset.href;
            if (href && href !== '#') window.location.href = href;
        });
    });

    // Search + course filter
    const searchInput = document.getElementById('search-topics');
    const courseFilter = document.getElementById('course-filter');

    function filterRows() {
        const term = searchInput?.value.toLowerCase() ?? '';
        const courseId = courseFilter?.value ?? '';
        document.querySelectorAll('.clickable-row').forEach(row => {
            const matchSearch = !term || 
                row.dataset.title.includes(term) || 
                (row.dataset.course && row.dataset.course.includes(term)) ||
                (row.dataset.creator && row.dataset.creator.includes(term));
            const matchCourse = !courseId || row.dataset.courseId === courseId;
            row.style.display = matchSearch && matchCourse ? '' : 'none';
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
        document.querySelectorAll('#topics-table tbody tr').forEach(row => {
            if (row.style.display === 'none') return;
            
            // Get course names
            const courseCell = row.querySelector('.course-info');
            let courseText = 'No course assigned';
            if (courseCell) {
                const courseName = courseCell.querySelector('.course-name')?.textContent.trim() || '';
                const courseCode = courseCell.querySelector('.course-code')?.textContent.trim() || '';
                const courseCount = courseCell.querySelector('.course-count')?.textContent.trim() || '';
                courseText = courseName + (courseCode ? ' (' + courseCode + ')' : '') + (courseCount ? ' ' + courseCount : '');
            }
            
            // Get creator name
            const creatorCell = row.querySelector('.creator-info');
            let creatorText = 'System';
            if (creatorCell) {
                const creatorName = creatorCell.querySelector('.creator-name')?.textContent.trim() || 'System';
                creatorText = creatorName;
            }
            
            // Get has video status
            const hasVideo = row.querySelector('.topic-video-indicator') ? 'Yes' : 'No';
            
            // Get has attachment status
            const hasAttachment = row.querySelector('.topic-attachment-indicator') ? 'Yes' : 'No';
            
            // Get status
            const statusBadge = row.querySelector('.item-badge');
            const status = statusBadge ? statusBadge.textContent.trim() : 'Unknown';
            
            // Get date
            const dateCell = row.querySelector('.item-date');
            const date = dateCell ? dateCell.textContent.trim() : '';
            
            rows.push([
                row.querySelector('.topic-name')?.textContent.trim() || '',
                courseText,
                creatorText,
                hasVideo,
                hasAttachment,
                status,
                date
            ].map(v => `"${v.replace(/"/g, '""')}"`));
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