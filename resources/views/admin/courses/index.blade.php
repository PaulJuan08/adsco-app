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
    <div class="card main-card">
        <div class="card-header">
            <div class="card-title">All Courses</div>
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search courses..." id="search-courses">
                </div>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Add Course
                </a>
            </div>
        </div>
        
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
            <h3>No courses yet</h3>
            <p>You haven't created any courses. Start building your curriculum by adding the first course.</p>
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Create Your First Course
            </a>
            <div class="empty-hint">
                <i class="fas fa-lightbulb"></i>
                Courses can be assigned to teachers and enrolled by students
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
                        <th class="hide-on-tablet">Teacher</th>
                        <th>Students</th>
                        <th class="hide-on-tablet">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr class="course-row" 
                        data-title="{{ strtolower($course->title) }}" 
                        data-code="{{ strtolower($course->course_code) }}"
                        data-teacher="{{ strtolower($course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : '') }}">
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
                                        @if($course->teacher)
                                        <div class="teacher-mobile">
                                            <i class="fas fa-user-tie"></i>
                                            {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="hide-on-mobile">
                            <span class="course-code">{{ $course->course_code }}</span>
                        </td>
                        <td class="hide-on-tablet">
                            @if($course->teacher)
                            <div class="teacher-info">
                                <div class="teacher-avatar">
                                    {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                                </div>
                                <div class="teacher-details">
                                    <div class="teacher-name">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                                    @if($course->teacher->employee_id)
                                    <div class="teacher-id">{{ $course->teacher->employee_id }}</div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <span class="no-teacher">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="students-count">
                                <div class="count-number">{{ $course->students_count ?? 0 }}</div>
                                <div class="count-label">enrolled</div>
                            </div>
                        </td>
                        <td class="hide-on-tablet">
                            @if($course->is_published)
                                <span class="status-badge status-published">
                                    <i class="fas fa-check-circle"></i>
                                    Published
                                </span>
                            @else
                                <span class="status-badge status-draft">
                                    <i class="fas fa-clock"></i>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.courses.show', Crypt::encrypt($course->id)) }}" 
                                   class="btn-icon view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.edit', Crypt::encrypt($course->id)) }}" 
                                   class="btn-icon edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.courses.destroy', Crypt::encrypt($course->id)) }}" method="POST" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this course?')">
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
        <div class="pagination-container">
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
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div class="sidebar-container">
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="quick-actions">
                <a href="{{ route('admin.courses.create') }}" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Add New Course</div>
                        <div class="quick-action-subtitle">Create a new course</div>
                    </div>
                </a>
                <button id="print-report" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Print/Export Courses</div>
                        <div class="quick-action-subtitle">Print course list or export as CSV</div>
                    </div>
                </button>
                <button id="export-csv" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-file-csv"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Download CSV</div>
                        <div class="quick-action-subtitle">Export course data as CSV file</div>
                    </div>
                </button>
            </div>
        </div>
        
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Course Statistics</div>
            </div>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="stat-label">Courses This Month</span>
                    <span class="stat-value">{{ $coursesThisMonth ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Avg Students per Course</span>
                    <span class="stat-value">{{ $avgStudents ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Published Courses</span>
                    <span class="stat-value">{{ $activeCourses ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Courses with Teacher</span>
                    <span class="stat-value">{{ $assignedTeachers ?? 0 }}</span>
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

<style>
    /* Responsive CSS Variables */
    :root {
        --primary: #4361ee;
        --primary-light: #e0e7ff;
        --secondary: #6c757d;
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --light: #f8f9fa;
        --dark: #343a40;
        --border: #e9ecef;
    }

    /* Responsive Grid Layouts */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .sidebar-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .content-grid {
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .card-title {
            text-align: center;
        }
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
    }

    /* Search Container */
    .search-container {
        position: relative;
        min-width: 200px;
    }

    .search-container i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--secondary);
    }

    .search-container input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .search-container input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    @media (max-width: 768px) {
        .search-container {
            min-width: unset;
            width: 100%;
        }
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
        white-space: nowrap;
    }

    .btn:hover {
        background: #4f46e5;
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .courses-table {
        width: 100%;
        border-collapse: collapse;
    }

    .courses-table thead {
        background: #f9fafb;
        border-bottom: 2px solid var(--border);
    }

    .courses-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--secondary);
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .courses-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .courses-table tbody tr:hover {
        background: #f9fafb;
    }

    /* Course Info Cell */
    .course-info-cell {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .course-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .course-1 {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .course-2 {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .course-3 {
        background: linear-gradient(135deg, #db2777 0%, #ec4899 100%);
    }

    .course-details {
        flex: 1;
        min-width: 0;
    }

    .course-name {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
        font-size: 0.9375rem;
    }

    .course-desc {
        font-size: 0.8125rem;
        color: var(--secondary);
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }

    .course-mobile-info {
        display: none;
        margin-top: 0.5rem;
        font-size: 0.8125rem;
        color: var(--secondary);
    }

    .course-code-mobile {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-right: 1rem;
    }

    .teacher-mobile {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .teacher-mobile i {
        font-size: 0.75rem;
    }

    /* Hide/Show Columns for Responsive */
    .hide-on-mobile {
        display: table-cell;
    }

    .hide-on-tablet {
        display: table-cell;
    }

    @media (max-width: 768px) {
        .hide-on-tablet {
            display: none;
        }
        
        .course-mobile-info {
            display: block;
        }
    }

    @media (max-width: 576px) {
        .hide-on-mobile {
            display: none;
        }
        
        .courses-table th,
        .courses-table td {
            padding: 0.75rem;
        }
        
        .course-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .course-name {
            font-size: 0.875rem;
        }
    }

    /* Course Code */
    .course-code {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #f3f4f6;
        color: var(--dark);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    /* Teacher Info */
    .teacher-info {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .teacher-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .teacher-details {
        min-width: 0;
    }

    .teacher-name {
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .teacher-id {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .no-teacher {
        color: var(--secondary);
        font-size: 0.875rem;
        font-style: italic;
    }

    /* Students Count */
    .students-count {
        text-align: center;
    }

    .count-number {
        font-weight: 600;
        color: var(--dark);
        font-size: 1rem;
    }

    .count-label {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.125rem;
    }

    @media (max-width: 576px) {
        .students-count {
            text-align: left;
        }
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 0.625rem;
    }

    .status-published {
        background: #dcfce7;
        color: #166534;
    }

    .status-draft {
        background: #fef3c7;
        color: #92400e;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
        font-size: 0.875rem;
    }

    .btn-icon.view {
        color: #3b82f6;
    }

    .btn-icon.edit {
        color: #f59e0b;
    }

    .btn-icon.delete {
        color: #ef4444;
    }

    .btn-icon:hover {
        background: rgba(0,0,0,0.05);
        transform: translateY(-1px);
    }

    .inline-form {
        display: inline;
    }

    @media (max-width: 576px) {
        .btn-icon {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--secondary);
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--secondary);
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.5;
    }

    .empty-hint {
        margin-top: 1rem;
        color: var(--secondary);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Alerts */
    .alert {
        margin: 0 1.5rem 1.5rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert i {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .alert {
            margin: 0 1rem 1rem;
        }
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
    }

    @media (min-width: 768px) {
        .pagination-container {
            flex-direction: row;
        }
    }

    .pagination-info {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .pagination-links {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-btn {
        padding: 0.5rem 0.75rem;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .pagination-btn:hover:not(.disabled):not(.active) {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.active {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.disabled {
        background: #f3f4f6;
        color: var(--secondary);
        cursor: not-allowed;
    }

    /* Sidebar */
    .sidebar-card {
        margin-bottom: 1.5rem;
    }

    .sidebar-card:last-child {
        margin-bottom: 0;
    }

    .quick-actions {
        padding: 0.5rem;
    }

    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--dark);
        transition: background 0.2s;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }

    .quick-action-item:hover {
        background: #f9fafb;
    }

    .quick-action-icon {
        width: 36px;
        height: 36px;
        background: #e0e7ff;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        flex-shrink: 0;
    }

    .quick-action-content {
        text-align: left;
        flex: 1;
        min-width: 0;
    }

    .quick-action-title {
        font-weight: 500;
        margin-bottom: 0.125rem;
        font-size: 0.875rem;
    }

    .quick-action-subtitle {
        font-size: 0.75rem;
        color: var(--secondary);
        line-height: 1.4;
    }

    /* Stats List */
    .stats-list {
        padding: 0.5rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .stat-value {
        font-weight: 600;
        color: var(--dark);
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .top-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }

    .greeting h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .greeting p {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .icon-courses {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #4f46e5;
    }

    .icon-users {
        background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        color: #db2777;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-courses');
        const courseRows = document.querySelectorAll('.course-row');
        
        if (searchInput && courseRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                courseRows.forEach(row => {
                    const courseTitle = row.dataset.title || '';
                    const courseCode = row.dataset.code || '';
                    const teacherName = row.dataset.teacher || '';
                    
                    if (searchTerm === '' || 
                        courseTitle.includes(searchTerm) || 
                        courseCode.includes(searchTerm) || 
                        teacherName.includes(searchTerm)) {
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
            
            // Add headers (excluding Actions column)
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                if (!th.textContent.includes('Actions')) {
                    headers.push(th.textContent.trim());
                }
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
                const desktopCode = columns[1].querySelector('.course-code');
                if (desktopCode) {
                    courseCode = desktopCode.textContent.trim();
                } else {
                    const mobileCode = columns[0].querySelector('.course-code-mobile');
                    if (mobileCode) {
                        courseCode = mobileCode.textContent.trim();
                    }
                }
                cells.push(`"${courseCode}"`);
                
                // Teacher
                let teacherName = 'Not assigned';
                const teacherInfo = columns[2].querySelector('.teacher-info');
                if (teacherInfo) {
                    const teacherNameDiv = teacherInfo.querySelector('.teacher-name');
                    if (teacherNameDiv) {
                        teacherName = teacherNameDiv.textContent.trim();
                        const teacherIdDiv = teacherInfo.querySelector('.teacher-id');
                        if (teacherIdDiv) {
                            teacherName += ` (${teacherIdDiv.textContent.trim()})`;
                        }
                    }
                } else {
                    const mobileTeacher = columns[0].querySelector('.teacher-mobile');
                    if (mobileTeacher) {
                        teacherName = mobileTeacher.textContent.trim().replace('👨‍🏫 ', '');
                    }
                }
                cells.push(`"${teacherName}"`);
                
                // Students Count
                const studentsCountDiv = columns[3].querySelector('.count-number');
                cells.push(studentsCountDiv ? studentsCountDiv.textContent.trim() : '0');
                
                // Status (use desktop or show as unknown if hidden)
                let status = 'Unknown';
                const statusBadge = columns[4].querySelector('.status-badge');
                if (statusBadge) {
                    status = statusBadge.textContent.trim();
                }
                cells.push(`"${status}"`);
                
                // Created Date placeholder
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
    });
</script>
@endpush
@endsection