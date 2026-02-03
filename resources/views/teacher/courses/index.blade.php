@extends('layouts.teacher')

@section('title', 'My Courses - Teacher Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>My Courses</h1>
        <p>Manage and organize your courses</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $courses->total() ?? $courses->count() }}</div>
                <div class="stat-label">My Courses</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-book"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $publishedCourses ?? 0 }}</div>
                <div class="stat-label">Published</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-chalkboard-teacher"></i>
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
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $totalTopics ?? 0 }}</div>
                <div class="stat-label">Total Topics</div>
            </div>
            <div class="stat-icon icon-files">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Courses List Card -->
    <div class="card main-card">
        <div class="card-header">
            <div class="card-title">My Course List</div>
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search courses..." id="search-courses">
                </div>
                <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    New Course
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
            <h3>No courses assigned yet</h3>
            <p>You don't have any courses assigned to you yet.</p>
            <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Create First Course
            </a>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr class="course-row" 
                        data-search="{{ strtolower($course->title . ' ' . $course->course_code) }}"
                        data-students="{{ $course->students_count ?? 0 }}"
                        data-topics="{{ $course->topics_count ?? 0 }}">
                        <td>
                            <div class="course-info-cell">
                                <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="course-details">
                                    <div class="course-name">{{ $course->title }}</div>
                                    <div class="course-description">{{ Str::limit($course->description, 50) }}</div>
                                    <div class="course-mobile-info">
                                        <div class="code-mobile">{{ $course->course_code }}</div>
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
                                <div class="count-label">students</div>
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
                                <span class="status-badge status-published">
                                    <i class="fas fa-circle"></i>
                                    Published
                                </span>
                            @else
                                <span class="status-badge status-draft">
                                    <i class="fas fa-circle"></i>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="hide-on-tablet">
                            <div class="created-date">{{ $course->created_at->format('M d, Y') }}</div>
                            <div class="created-ago">{{ $course->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" 
                                   class="btn-icon view" title="View Course">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('teacher.courses.edit', Crypt::encrypt($course->id)) }}" 
                                   class="btn-icon edit" title="Edit Course">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('teacher.courses.destroy', Crypt::encrypt($course->id)) }}" 
                                      method="POST" class="inline-form"
                                      onsubmit="return confirm('Are you sure you want to delete this course?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Delete Course">
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
                Showing {{ $courses->firstItem() }}-{{ $courses->lastItem() }} of {{ $courses->total() }}
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
                <a href="{{ route('teacher.courses.create') }}" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Create New Course</div>
                        <div class="quick-action-subtitle">Add a new course to your list</div>
                    </div>
                </a>
                <button id="export-courses" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Export Courses</div>
                        <div class="quick-action-subtitle">Export course list as CSV</div>
                    </div>
                </button>
                <a href="{{ route('teacher.topics.create') }}" class="quick-action-item">
                    <div class="quick-action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Add Topic</div>
                        <div class="quick-action-subtitle">Add learning topic to course</div>
                    </div>
                </a>
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
                    <span class="stat-value">{{ $publishedCourses ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Draft Courses</span>
                    <span class="stat-value">{{ $draftCourses ?? 0 }}</span>
                </div>
            </div>
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

    .course-description {
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

    .code-mobile {
        display: inline-block;
        background: #f3f4f6;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        margin-right: 1rem;
        font-weight: 500;
    }

    .stats-mobile {
        display: inline-flex;
        gap: 1rem;
    }

    .stat-item-mobile {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-item-mobile i {
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
        
        .stats-mobile {
            gap: 0.5rem;
        }
    }

    /* Course Code */
    .course-code {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #f3f4f6;
        color: var(--dark);
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }

    /* Count Styles */
    .students-count, .topics-count {
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
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.125rem;
    }

    @media (max-width: 576px) {
        .students-count, .topics-count {
            text-align: left;
        }
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 0.5rem;
    }

    .status-published {
        background: #dcfce7;
        color: #166534;
    }

    .status-draft {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Created Date */
    .created-date {
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .created-ago {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.125rem;
    }

    /* Action Buttons - Consistent with other pages */
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
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        flex-shrink: 0;
    }

    .quick-action-icon:first-child {
        background: #e0e7ff;
    }

    .quick-action-icon:nth-child(2) {
        background: #fce7f3;
        color: #db2777;
    }

    .quick-action-icon:last-child {
        background: #dcfce7;
        color: var(--success);
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

    .icon-files {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
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
                    const searchData = row.dataset.search || '';
                    
                    if (searchTerm === '' || searchData.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Export functionality
        document.getElementById('export-courses')?.addEventListener('click', function() {
            // Get table data
            const table = document.getElementById('courses-table');
            const rows = table.querySelectorAll('tr');
            const csv = [];
            
            // Add headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
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
                
                // Course Code (use desktop or mobile version)
                let courseCode = '';
                const desktopCode = columns[1].querySelector('.course-code');
                if (desktopCode) {
                    courseCode = desktopCode.textContent.trim();
                } else {
                    const mobileCode = columns[0].querySelector('.code-mobile');
                    if (mobileCode) {
                        courseCode = mobileCode.textContent.trim();
                    }
                }
                cells.push(`"${courseCode}"`);
                
                // Students Count
                const studentsDiv = columns[2].querySelector('.count-number');
                cells.push(studentsDiv ? studentsDiv.textContent.trim() : '0');
                
                // Topics Count (use desktop or mobile)
                let topicsCount = '0';
                const desktopTopics = columns[3]?.querySelector('.count-number');
                if (desktopTopics) {
                    topicsCount = desktopTopics.textContent.trim();
                } else {
                    const mobileTopics = columns[0].querySelector('.stat-item-mobile:nth-child(2)');
                    if (mobileTopics) {
                        topicsCount = mobileTopics.textContent.trim().replace('📄 ', '');
                    }
                }
                cells.push(topicsCount);
                
                // Status (use desktop or show as unknown if hidden)
                let status = 'Unknown';
                const statusBadge = columns[4]?.querySelector('.status-badge');
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
            link.setAttribute('download', `my_courses_export_${new Date().toISOString().slice(0,10)}.csv`);
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