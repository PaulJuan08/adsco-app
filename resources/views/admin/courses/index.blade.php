@extends('layouts.admin')

@section('title', 'Courses - Admin Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Courses</h1>
        <p>Manage and organize all academic courses</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $courses->total() ?? $courses->count() }}</div>
                <div class="stat-label">Total Courses</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-book"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $activeCourses ?? 0 }}</div>
                <div class="stat-label">Published Courses</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $assignedTeachers ?? 0 }}</div>
                <div class="stat-label">Assigned Teachers</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalStudents ?? 0 }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Courses List Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">All Courses</div>
            <div class="d-flex gap-2 align-items-center">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--secondary);"></i>
                    <input type="text" class="search-input" placeholder="Search courses..." 
                           style="padding: 8px 12px 8px 36px; border: 1px solid var(--border); border-radius: 6px; width: 200px;">
                </div>
                <a href="{{ route('admin.courses.create') }}" class="view-all" style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-plus-circle"></i>
                    Add Course
                </a>
            </div>
        </div>
        
        @if(session('success'))
        <div style="margin: 0 1.5rem 1.5rem; padding: 12px; background: #dcfce7; color: #065f46; border-radius: 8px; font-size: 0.875rem;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="margin: 0 1.5rem 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.875rem;">
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
            {{ session('error') }}
        </div>
        @endif

        @if($courses->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3 style="color: var(--dark); margin-bottom: 12px;">No courses yet</h3>
            <p style="color: var(--secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                You haven't created any courses. Start building your curriculum by adding the first course.
            </p>
            <a href="{{ route('admin.courses.create') }}" 
               style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-plus-circle"></i>
                Create Your First Course
            </a>
            <div style="margin-top: 20px; color: var(--secondary); font-size: 0.875rem;">
                <i class="fas fa-lightbulb" style="margin-right: 6px;"></i>
                Courses can be assigned to teachers and enrolled by students
            </div>
        </div>
        @else
        <!-- Courses List -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;" id="courses-table">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid var(--border);">
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">
                            Course Title
                        </th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Code</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Teacher</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Students</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Status</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <div class="course-name">{{ $course->title }}</div>
                                    <div class="course-desc">{{ Str::limit($course->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <span style="display: inline-block; padding: 4px 12px; background: #f3f4f6; color: var(--dark); border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                                {{ $course->course_code }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            @if($course->teacher)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                    {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 500;">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                                    @if($course->teacher->employee_id)
                                    <div style="font-size: 0.75rem; color: var(--secondary);">{{ $course->teacher->employee_id }}</div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <span style="color: var(--secondary); font-size: 0.875rem;">Not assigned</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 600; color: var(--dark);">{{ $course->students_count ?? 0 }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">enrolled</div>
                        </td>
                        <td style="padding: 16px;">
                            @if($course->is_published)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                    Published
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-clock" style="font-size: 10px;"></i>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.courses.show', $course->id) }}" title="View" style="padding: 8px; background: #e0e7ff; color: var(--primary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" title="Edit" style="padding: 8px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this course?')"
                                            style="padding: 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($courses instanceof \Illuminate\Pagination\AbstractPaginator && $courses->hasPages())
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} entries
            </div>
            <div style="display: flex; gap: 8px;">
                @if($courses->onFirstPage())
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Previous
                </span>
                @else
                <a href="{{ $courses->previousPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Previous
                </a>
                @endif
                
                @foreach(range(1, min(5, $courses->lastPage())) as $page)
                    @if($page == $courses->currentPage())
                    <span style="padding: 8px 12px; background: var(--primary); color: white; border-radius: 6px; font-size: 0.875rem;">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $courses->url($page) }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach
                
                @if($courses->hasMorePages())
                <a href="{{ $courses->nextPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Next
                </a>
                @else
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Next
                </span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('admin.courses.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Add New Course</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Create a new course</div>
                    </div>
                </a>
                <button id="print-report" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s; width: 100%; border: none; background: none; cursor: pointer;">
                    <div style="width: 36px; height: 36px; background: #fce7f3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #db2777;">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div style="text-align: left;">
                        <div style="font-weight: 500;">Print/Export Courses</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Print course list or export as CSV</div>
                    </div>
                </button>
                <button id="export-csv" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s; width: 100%; border: none; background: none; cursor: pointer;">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <i class="fas fa-file-csv"></i>
                    </div>
                    <div style="text-align: left;">
                        <div style="font-weight: 500;">Download CSV</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Export course data as CSV file</div>
                    </div>
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Course Statistics</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Courses This Month</span>
                        <span style="font-weight: 600;">{{ $coursesThisMonth ?? 0 }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Avg Students per Course</span>
                        <span style="font-weight: 600;">{{ $avgStudents ?? 0 }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Published Courses</span>
                        <span style="font-weight: 600;">{{ $activeCourses ?? 0 }}</span>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Courses with Teacher</span>
                        <span style="font-weight: 600;">{{ $assignedTeachers ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Print Content -->
<div id="print-content" style="display: none;">
    <div style="padding: 20px; font-family: Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #4f46e5; margin-bottom: 5px;">ADSCO Course Management Report</h1>
            <p style="color: #666; margin-bottom: 10px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
            <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
        </div>
        
        <div style="margin-bottom: 30px;">
            <h2 style="color: #333; margin-bottom: 10px;">Course Statistics Summary</h2>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px;">{{ $courses->total() ?? $courses->count() }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Total Courses</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #7c3aed; margin-bottom: 5px;">{{ $activeCourses ?? 0 }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Published Courses</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #059669; margin-bottom: 5px;">{{ $assignedTeachers ?? 0 }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Courses with Teachers</div>
                </div>
                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 24px; font-weight: bold; color: #0369a1; margin-bottom: 5px;">{{ $totalStudents ?? 0 }}</div>
                    <div style="font-size: 14px; color: #6b7280;">Total Students</div>
                </div>
            </div>
        </div>
        
        <h2 style="color: #333; margin-bottom: 15px;">Course List</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background: #f3f4f6;">
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Course Title</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Code</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Teacher</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Students</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Status</th>
                    <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Created Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $course->title }}</td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $course->course_code }}</td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb;">
                        @if($course->teacher)
                            {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                            @if($course->teacher->employee_id)
                                ({{ $course->teacher->employee_id }})
                            @endif
                        @else
                            Not Assigned
                        @endif
                    </td>
                    <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $course->students_count ?? 0 }}</td>
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
                Total Courses: {{ $courses->total() }} | 
                Generated by: {{ Auth::user()->name }} | 
                Page 1 of 1
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const courseTitle = row.querySelector('.course-name').textContent.toLowerCase();
                const courseDesc = row.querySelector('.course-desc')?.textContent?.toLowerCase() || '';
                const courseCode = row.cells[1].textContent.toLowerCase();
                
                if (courseTitle.includes(searchTerm) || courseDesc.includes(searchTerm) || courseCode.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Print functionality
    document.getElementById('print-report')?.addEventListener('click', function() {
        // Get the print content
        const printContent = document.getElementById('print-content').innerHTML;
        
        // Create a new window for printing
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Course Management Report</title>
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
                        font-size: 12px;
                    }
                    th {
                        background-color: #f3f4f6 !important;
                        -webkit-print-color-adjust: exact;
                    }
                    td, th {
                        border: 1px solid #ddd;
                        padding: 8px;
                    }
                    .stat-card {
                        display: inline-block;
                        width: 23%;
                        margin: 5px;
                        padding: 15px;
                        background: #f9fafb;
                        border-radius: 8px;
                        text-align: center;
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
        // Get table data
        const table = document.getElementById('courses-table');
        const rows = table.querySelectorAll('tr');
        const csv = [];
        
        // Add headers
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => {
            // Skip the last column (Actions) for CSV export
            if (!th.textContent.includes('Actions')) {
                headers.push(th.textContent.trim());
            }
        });
        csv.push(headers.join(','));
        
        // Add data rows
        table.querySelectorAll('tbody tr').forEach(row => {
            const cells = [];
            const columns = row.querySelectorAll('td');
            
            // Course Title
            const courseNameDiv = columns[0].querySelector('.course-name');
            cells.push(`"${courseNameDiv ? courseNameDiv.textContent.trim() : ''}"`);
            
            // Course Code
            const codeSpan = columns[1].querySelector('span');
            cells.push(`"${codeSpan ? codeSpan.textContent.trim() : ''}"`);
            
            // Teacher
            const teacherDiv = columns[2];
            let teacherName = 'Not assigned';
            if (teacherDiv.querySelector('span')) {
                teacherName = teacherDiv.querySelector('span').textContent.trim();
            } else {
                const teacherNameDiv = teacherDiv.querySelector('div:nth-child(2) div:nth-child(1)');
                if (teacherNameDiv) {
                    teacherName = teacherNameDiv.textContent.trim();
                    const teacherIdDiv = teacherDiv.querySelector('div:nth-child(2) div:nth-child(2)');
                    if (teacherIdDiv) {
                        teacherName += ` (${teacherIdDiv.textContent.trim()})`;
                    }
                }
            }
            cells.push(`"${teacherName}"`);
            
            // Students Count
            const studentsDiv = columns[3].querySelector('div:nth-child(1)');
            cells.push(studentsDiv ? studentsDiv.textContent.trim() : '0');
            
            // Status
            const statusSpan = columns[4].querySelector('span');
            cells.push(`"${statusSpan ? statusSpan.textContent.trim() : ''}"`);
            
            // Created Date (if available)
            // We can add created date if we want to include it
            // For now, we'll add placeholder
            cells.push('"Data not available"');
            
            csv.push(cells.join(','));
        });
        
        // Create and download CSV file
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', `courses_export_${new Date().toISOString().slice(0,10)}.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        alert('CSV file has been downloaded successfully!');
    });
</script>
@endpush
@endsection