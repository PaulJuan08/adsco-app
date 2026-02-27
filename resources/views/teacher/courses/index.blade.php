@extends('layouts.teacher')

@section('title', 'My Courses - Teacher Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Dashboard Header — consistent with dashboard.css -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->l_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">My Courses</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-book"></i> Manage your assigned courses
                        <span class="separator">•</span>
                        <span class="pending-notice">
                            <i class="fas fa-book"></i> {{ $totalCourses ?? $courses->total() }} courses · {{ $publishedCourses ?? 0 }} published
                        </span>
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('teacher.courses.create') }}" class="top-action-btn">
                    <i class="fas fa-plus-circle"></i> Add Course
                </a>
                <a href="{{ route('teacher.topics.index') }}" class="top-action-btn">
                    <i class="fas fa-chalkboard"></i> Topics
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('teacher.courses.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">My Courses</div>
                    <div class="stat-number">{{ number_format($totalCourses ?? $courses->total()) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-book"></i></div>
            </div>
            <div class="stat-link">View all courses <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('teacher.courses.index') }}?status=published" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published Courses</div>
                    <div class="stat-number">{{ number_format($publishedCourses ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-link">View published <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('teacher.courses.index') }}?sort=students" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ number_format($totalStudents ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-link">View enrollments <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('teacher.topics.index') }}" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">My Topics</div>
                    <div class="stat-number">{{ number_format($totalTopics ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
            <div class="stat-link">Manage topics <i class="fas fa-arrow-right"></i></div>
        </a>
    </div>

    <!-- Courses List — full width -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-book" style="color: var(--primary); margin-right: 0.5rem;"></i>
                My Courses
            </h2>
            <div class="header-actions-bar">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search courses..." id="search-courses">
                </div>
                <div class="filter-container">
                    <select class="form-select" id="status-filter">
                        <option value="">All Statuses</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
                <button id="print-report" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
                <button id="export-csv" class="btn btn-secondary">
                    <i class="fas fa-file-csv"></i> Export
                </button>
                <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Course
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

            @if($courses->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-book-open"></i></div>
                    <h3 class="empty-title">No courses yet</h3>
                    <p class="empty-text">Start by adding your first course.</p>
                    <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Create Your First Course
                    </a>
                    <div class="empty-hint">
                        <i class="fas fa-lightbulb"></i> Courses are learning units that can contain multiple topics
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="courses-table" id="courses-table">
                        <thead>
                            <tr>
                                <th>Course Title</th>
                                <th class="hide-on-mobile">Code</th>
                                <th class="hide-on-tablet">Created By</th>
                                <th>Students</th>
                                <th class="hide-on-tablet">Topics</th>
                                <th class="hide-on-tablet">Status</th>
                                <th class="hide-on-tablet">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            @php
                                try { $encryptedId = Crypt::encrypt($course->id); }
                                catch (\Exception $e) { $encryptedId = ''; }
                                
                                // Get status display
                                $status = $course->status; // This uses the accessor from the model
                                $statusClass = '';
                                $statusIcon = '';
                                
                                switch($status) {
                                    case 'active':
                                        $statusClass = 'badge-success';
                                        $statusIcon = 'fa-check-circle';
                                        break;
                                    case 'draft':
                                        $statusClass = 'badge-warning';
                                        $statusIcon = 'fa-clock';
                                        break;
                                    default:
                                        $statusClass = 'badge-secondary';
                                        $statusIcon = 'fa-circle';
                                }
                            @endphp
                            <tr class="clickable-row"
                                data-href="{{ $encryptedId ? route('teacher.courses.show', ['encryptedId' => $encryptedId]) : '#' }}"
                                data-title="{{ strtolower($course->title) }}"
                                data-code="{{ strtolower($course->course_code ?? '') }}"
                                data-status="{{ $status }}"
                                data-course-id="{{ $course->id }}"
                                data-encrypted="{{ $encryptedId }}">
                                <td>
                                    <div class="course-info-cell">
                                        <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div class="course-details">
                                            <div class="course-name">{{ $course->title }}</div>
                                            @if($course->description)
                                            <div class="course-desc">{{ Str::limit($course->description, 60) }}</div>
                                            @endif
                                            <div class="course-mobile-info">
                                                <div class="course-code-mobile">
                                                    <i class="fas fa-code"></i> {{ $course->course_code ?? 'No code' }}
                                                </div>
                                                @if($course->credits)
                                                <div class="credits-mobile">
                                                    <i class="fas fa-star"></i> {{ $course->credits }} Credits
                                                </div>
                                                @endif
                                                @if($course->is_published)
                                                    <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Published</span>
                                                @else
                                                    <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Draft</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-on-mobile">
                                    <span class="course-code">{{ $course->course_code ?? '—' }}</span>
                                </td>
                                <td class="hide-on-tablet">
                                    @if($course->creator)
                                        <div class="creator-info">
                                            <div class="creator-name">{{ $course->creator->f_name }} {{ $course->creator->l_name }}</div>
                                            <div class="creator-role">Teacher</div>
                                        </div>
                                    @else
                                        <span class="no-creator">System</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="students-count">
                                        <div class="count-number">{{ $course->students_count ?? 0 }}</div>
                                        <div class="count-label">enrolled</div>
                                    </div>
                                </td>
                                <td class="hide-on-tablet">
                                    <div class="topics-count">
                                        <div class="count-number">{{ $course->topics_count ?? 0 }}</div>
                                        <div class="count-label">topics</div>
                                    </div>
                                </td>
                                <td class="hide-on-tablet">
                                    @if($course->is_published)
                                        <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Published</span>
                                        @if($status == 'active')
                                        <div class="status-detail"><small>Active</small></div>
                                        @endif
                                    @else
                                        <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Draft</span>
                                    @endif
                                </td>
                                <td class="hide-on-tablet">
                                    <span class="item-date">{{ $course->created_at->format('M d, Y') }}</span>
                                    @if($course->credits)
                                    <div class="credits-badge">{{ $course->credits }} Credits</div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($courses instanceof \Illuminate\Pagination\AbstractPaginator && $courses->hasPages())
        <div class="card-footer">
            <div class="pagination-info">
                Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} entries
            </div>
            <div class="pagination-links">
                @if($courses->onFirstPage())
                    <span class="pagination-btn disabled">Previous</span>
                @else
                    <a href="{{ $courses->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif

                @foreach(range(1, $courses->lastPage()) as $page)
                    @if($page == $courses->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @elseif(abs($page - $courses->currentPage()) <= 2)
                        <a href="{{ $courses->url($page) }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($courses->hasMorePages())
                    <a href="{{ $courses->nextPageUrl() }}" class="pagination-btn">Next</a>
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
            My Courses • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>

    <!-- Hidden Print Content -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5;">My Courses Report</h1>
                <p style="color: #666;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;">Course Statistics Summary</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #eef2ff; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #4f46e5;">{{ $courses->total() ?? $courses->count() }}</div>
                        <div style="font-size: 14px; color: #4f46e5;">Total Courses</div>
                    </div>
                    <div style="background: #dcfce7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #059669;">{{ $publishedCourses ?? 0 }}</div>
                        <div style="font-size: 14px; color: #059669;">Published</div>
                    </div>
                    <div style="background: #fee2e2; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #dc2626;">{{ $draftCourses ?? 0 }}</div>
                        <div style="font-size: 14px; color: #dc2626;">Draft</div>
                    </div>
                    <div style="background: #dbeafe; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $totalStudents ?? 0 }}</div>
                        <div style="font-size: 14px; color: #2563eb;">Total Students</div>
                    </div>
                </div>
            </div>
            
            <h2 style="color: #333; margin-bottom: 15px;">Course List</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Course Title</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Code</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Students</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Topics</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Credits</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    @php
                        $status = $course->status;
                        $statusText = $status == 'active' ? 'Active' : ($status == 'draft' ? 'Draft' : ($course->is_published ? 'Published' : 'Draft'));
                    @endphp
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $course->title }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $course->course_code ?? '—' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $course->students_count ?? 0 }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $course->topics_count ?? 0 }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $course->credits ?? '—' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $statusText }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $course->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="color: #6b7280; font-size: 14px;">
                    Total Courses: {{ $courses->total() ?? $courses->count() }} | 
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

    // Search + status filter
    const searchInput = document.getElementById('search-courses');
    const statusFilter = document.getElementById('status-filter');

    function filterRows() {
        const term = searchInput?.value.toLowerCase() ?? '';
        const status = statusFilter?.value ?? '';
        document.querySelectorAll('.clickable-row').forEach(row => {
            const matchSearch = !term || 
                row.dataset.title.includes(term) || 
                row.dataset.code.includes(term);
            const matchStatus = !status || row.dataset.status === status;
            row.style.display = matchSearch && matchStatus ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterRows);

    // Status filter — server-side redirect
    statusFilter?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        this.value ? url.searchParams.set('status', this.value) : url.searchParams.delete('status');
        window.location.href = url.toString();
    });

    // Print functionality
    document.getElementById('print-report')?.addEventListener('click', function () {
        const content = document.getElementById('print-content').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>My Courses Report</title>
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
        const rows = [['Course Title', 'Code', 'Students', 'Topics', 'Credits', 'Status', 'Created']];
        document.querySelectorAll('#courses-table tbody tr').forEach(row => {
            if (row.style.display === 'none') return;
            const cols = row.querySelectorAll('td');
            
            // Get status
            let status = 'Unknown';
            const statusBadge = cols[4]?.querySelector('.item-badge');
            if (statusBadge) {
                status = statusBadge.textContent.trim();
            }
            
            // Get credits
            let credits = '';
            const creditsBadge = cols[5]?.querySelector('.credits-badge');
            if (creditsBadge) {
                credits = creditsBadge.textContent.trim();
            } else {
                credits = cols[0]?.querySelector('.credits-mobile')?.textContent.trim()?.replace('Credits', '')?.trim() || '—';
            }
            
            rows.push([
                cols[0]?.querySelector('.course-name')?.textContent.trim() || '',
                cols[1]?.querySelector('.course-code')?.textContent.trim() || cols[0]?.querySelector('.course-code-mobile')?.textContent.trim() || '',
                cols[2]?.querySelector('.count-number')?.textContent.trim() || '0',
                cols[3]?.querySelector('.count-number')?.textContent.trim() || cols[0]?.querySelector('.stat-item-mobile:nth-child(2)')?.textContent.trim().replace(/\D/g, '') || '0',
                credits,
                status,
                cols[5]?.querySelector('.item-date')?.textContent.trim() || '',
            ].map(v => `"${v.replace(/"/g, '""')}"`));
        });
        const blob = new Blob(['\uFEFF' + rows.map(r => r.join(',')).join('\n')], { type: 'text/csv;charset=utf-8;' });
        const a = Object.assign(document.createElement('a'), { 
            href: URL.createObjectURL(blob), 
            download: `my_courses_${new Date().toISOString().slice(0,10)}.csv` 
        });
        document.body.appendChild(a); a.click(); a.remove();
    });

});
</script>
@endpush