@extends('layouts.teacher')

@section('title', 'My Courses - Teacher Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">My Courses</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-book"></i> Manage and organize your courses
                        @if(isset($draftCount) && $draftCount > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $draftCount }} draft{{ $draftCount > 1 ? 's' : '' }} pending</span>
                        @endif
                    </p>
                </div>
            </div>
            @if(isset($draftCount) && $draftCount > 0)
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-edit"></i>
                    <span class="badge-count">{{ $draftCount }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Draft Courses</div>
                    <div class="alert-subtitle">{{ $draftCount }} course{{ $draftCount > 1 ? 's' : '' }} in draft status</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('teacher.courses.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">My Courses</div>
                    <div class="stat-number">{{ $courses->total() ?? $courses->count() }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-link">
                View all courses <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('teacher.courses.index') }}?status=published" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published</div>
                    <div class="stat-number">{{ $publishedCourses ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-link">
                View published <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('teacher.courses.index') }}?sort=students" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ $totalStudents ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-link">
                View enrollments <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('teacher.topics.index') }}" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ $totalTopics ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-link">
                Manage topics <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Courses List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-book" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        My Course List
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search courses..." id="search-courses">
                        </div>
                        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> New Course
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    @endif

                    @if($courses->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h3 class="empty-title">No courses assigned yet</h3>
                            <p class="empty-text">You don't have any courses assigned to you yet. Create your first course to get started.</p>
                            <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i>
                                Create Your First Course
                            </a>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                Courses can contain topics and materials for your students
                            </div>
                        </div>
                    @else
                        <!-- Courses List -->
                        <div class="table-responsive">
                            <table class="courses-table" id="courses-table">
                                <thead>
                                    <tr>
                                        <th>Course Title</th>
                                        <th class="hide-on-mobile">Code</th>
                                        <th>Students</th>
                                        <th class="hide-on-tablet">Topics</th>
                                        <th class="hide-on-tablet">Status</th>
                                        <th class="hide-on-tablet">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                    <tr class="clickable-row" 
                                        data-href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}"
                                        data-title="{{ strtolower($course->title) }}" 
                                        data-code="{{ strtolower($course->course_code) }}">
                                        <td>
                                            <div class="course-info-cell">
                                                <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div class="course-details">
                                                    <div class="course-name">{{ $course->title }}</div>
                                                    <div class="course-desc">{{ Str::limit($course->description, 50) }}</div>
                                                    <div class="course-mobile-info">
                                                        <div class="course-code-mobile">{{ $course->course_code }}</div>
                                                        <div class="stats-mobile">
                                                            <span class="stat-item-mobile">
                                                                <i class="fas fa-user-graduate"></i>
                                                                {{ $course->students_count ?? 0 }}
                                                            </span>
                                                            <span class="stat-item-mobile">
                                                                <i class="fas fa-file-alt"></i>
                                                                {{ $course->topics_count ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hide-on-mobile">
                                            <span class="course-code">{{ $course->course_code }}</span>
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
                                                <span class="item-badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Published
                                                </span>
                                            @else
                                                <span class="item-badge badge-warning">
                                                    <i class="fas fa-clock"></i> Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="hide-on-tablet">
                                            <div class="created-date">{{ $course->created_at->format('M d, Y') }}</div>
                                            <div class="created-ago">{{ $course->created_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
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
                        
                        @foreach(range(1, min(5, $courses->lastPage())) as $page)
                            @if($page == $courses->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                            @else
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
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('teacher.courses.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create New Course</div>
                                <div class="action-subtitle">Add a new course to your list</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <button id="print-report" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-file-export"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Print/Export Courses</div>
                                <div class="action-subtitle">Print course list or export as CSV</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                        
                        <button id="export-csv" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Download CSV</div>
                                <div class="action-subtitle">Export course data as CSV file</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                        
                        <a href="{{ route('teacher.topics.create') }}" class="action-card action-secondary">
                            <div class="action-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add Topic</div>
                                <div class="action-subtitle">Create learning topic for a course</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Course Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Course Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <a href="{{ route('teacher.courses.index') }}?month={{ now()->format('Y-m') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Courses This Month</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $coursesThisMonth ?? 0 }}</div>
                        </a>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Avg Students per Course</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $avgStudents ?? 0 }}</div>
                        </div>
                        
                        <a href="{{ route('teacher.courses.index') }}?status=published" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Published Courses</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $publishedCourses ?? 0 }}</div>
                        </a>
                        
                        <a href="{{ route('teacher.courses.index') }}?status=draft" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--gray-400), var(--gray-600));">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Draft Courses</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $draftCourses ?? 0 }}</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
                <h1 style="color: #4f46e5; margin-bottom: 5px;">My Courses Report</h1>
                <p style="color: #666; margin-bottom: 10px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;">Course Statistics Summary</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #eef2ff; padding: 15px; border-radius: 8px; border: 1px solid #c7d2fe;">
                        <div style="font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px;">{{ $courses->total() ?? $courses->count() }}</div>
                        <div style="font-size: 14px; color: #4f46e5;">Total Courses</div>
                    </div>
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 24px; font-weight: bold; color: #d97706; margin-bottom: 5px;">{{ $publishedCourses ?? 0 }}</div>
                        <div style="font-size: 14px; color: #d97706;">Published Courses</div>
                    </div>
                    <div style="background: #fee2e2; padding: 15px; border-radius: 8px; border: 1px solid #fecaca;">
                        <div style="font-size: 24px; font-weight: bold; color: #dc2626; margin-bottom: 5px;">{{ $draftCourses ?? 0 }}</div>
                        <div style="font-size: 14px; color: #dc2626;">Draft Courses</div>
                    </div>
                    <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd;">
                        <div style="font-size: 24px; font-weight: bold; color: #0284c7; margin-bottom: 5px;">{{ $totalStudents ?? 0 }}</div>
                        <div style="font-size: 14px; color: #0284c7;">Total Students</div>
                    </div>
                </div>
            </div>
            
            <h2 style="color: #333; margin-bottom: 15px;">Course List</h2>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Course Title</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Code</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Students</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Topics</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $course->title }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $course->course_code }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $course->students_count ?? 0 }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $course->topics_count ?? 0 }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">
                            @if($course->is_published)
                                <span style="color: #059669; font-weight: 500;">Published</span>
                            @else
                                <span style="color: #d97706; font-weight: 500;">Draft</span>
                            @endif
                        </td>
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

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-index.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make rows clickable - exactly like admin pattern
        const clickableRows = document.querySelectorAll('.clickable-row');
        
        clickableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't redirect if user clicked on a link or button
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
            
            // Add hover effect
            row.style.cursor = 'pointer';
        });

        // Search functionality
        const searchInput = document.getElementById('search-courses');
        const courseRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && courseRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                courseRows.forEach(row => {
                    const courseTitle = row.dataset.title || '';
                    const courseCode = row.dataset.code || '';
                    
                    if (searchTerm === '' || 
                        courseTitle.includes(searchTerm) || 
                        courseCode.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Print functionality
        document.getElementById('print-report')?.addEventListener('click', function() {
            const printContent = document.getElementById('print-content').innerHTML;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>My Courses Report</title>
                    <style>
                        @media print {
                            @page {
                                size: landscape;
                                margin: 0.5in;
                            }
                            body {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            table {
                                page-break-inside: auto;
                            }
                            tr {
                                page-break-inside: avoid;
                                page-break-after: auto;
                            }
                        }
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 20px;
                        }
                        h1, h2, h3 {
                            margin-top: 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th {
                            background-color: #f3f4f6 !important;
                            -webkit-print-color-adjust: exact;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 100);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });

        // Export to CSV functionality
        document.getElementById('export-csv')?.addEventListener('click', function() {
            const table = document.getElementById('courses-table');
            const rows = table.querySelectorAll('tr');
            const csv = [];
            
            // Add headers (excluding Actions column - there is none now)
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            // Add Created Date header for CSV
            headers.push('Created Date');
            csv.push(headers.join(','));
            
            // Add data rows
            table.querySelectorAll('tbody tr').forEach(row => {
                const cells = [];
                const columns = row.querySelectorAll('td');
                
                // Course Title
                const courseNameDiv = columns[0].querySelector('.course-name');
                cells.push(`"${courseNameDiv ? courseNameDiv.textContent.trim() : ''}"`);
                
                // Course Code (use desktop or mobile version)
                let courseCode = '';
                if (columns[1]) {
                    const desktopCode = columns[1].querySelector('.course-code');
                    if (desktopCode) {
                        courseCode = desktopCode.textContent.trim();
                    }
                } else {
                    const mobileCode = columns[0].querySelector('.course-code-mobile');
                    if (mobileCode) {
                        courseCode = mobileCode.textContent.trim();
                    }
                }
                cells.push(`"${courseCode}"`);
                
                // Students Count
                const studentsCountDiv = columns[2].querySelector('.count-number');
                cells.push(studentsCountDiv ? studentsCountDiv.textContent.trim() : '0');
                
                // Topics Count
                let topicsCount = '0';
                if (columns[3]) {
                    const topicsCountDiv = columns[3].querySelector('.count-number');
                    if (topicsCountDiv) {
                        topicsCount = topicsCountDiv.textContent.trim();
                    }
                } else {
                    const mobileTopics = columns[0].querySelector('.stat-item-mobile:nth-child(2)');
                    if (mobileTopics) {
                        topicsCount = mobileTopics.textContent.trim().replace(/\D/g, '');
                    }
                }
                cells.push(topicsCount);
                
                // Status (use desktop or show as unknown if hidden)
                let status = 'Unknown';
                if (columns[4]) {
                    const statusBadge = columns[4].querySelector('.item-badge');
                    if (statusBadge) {
                        status = statusBadge.textContent.trim();
                    }
                }
                cells.push(`"${status}"`);
                
                // Created Date
                let createdDate = 'Data not available';
                if (columns[5]) {
                    const createdDateDiv = columns[5].querySelector('.created-date');
                    if (createdDateDiv) {
                        createdDate = createdDateDiv.textContent.trim();
                    }
                }
                cells.push(`"${createdDate}"`);
                
                csv.push(cells.join(','));
            });
            
            // Create and download CSV file
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `my_courses_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Show success message
            alert('CSV file has been downloaded successfully!');
        });
    });
</script>
@endpush