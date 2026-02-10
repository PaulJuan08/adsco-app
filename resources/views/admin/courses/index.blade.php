@extends('layouts.admin')

@section('title', 'Courses - Admin Dashboard')

@section('content')
    <!-- Header with Dashboard Style -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar-large">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Courses</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-book"></i> Manage and organize all academic courses
                        @if($draftCount > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $draftCount }} draft{{ $draftCount > 1 ? 's' : '' }} pending</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($draftCount > 0)
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

    <!-- Stats Cards with Dashboard Style -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Courses</div>
                    <div class="stat-number">{{ number_format($totalCourses) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-book"></i> All courses in system
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.courses.index') }}" class="stat-link">
                    View all courses <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Published Courses</div>
                    <div class="stat-number">{{ number_format($activeCourses) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-check-circle"></i> Active and available
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.courses.index') }}?status=published" class="stat-link">
                    View published <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Assigned Teachers</div>
                    <div class="stat-number">{{ number_format($assignedTeachers) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-user-tie"></i> Courses with instructors
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.courses.index') }}?has_teacher=true" class="stat-link">
                    View assigned <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ number_format($totalStudents) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-user-graduate"></i> Enrolled across all courses
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.courses.index') }}?sort=students" class="stat-link">
                    View enrollments <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Courses List Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-book card-icon"></i>
                        <h2 class="card-title-modern">All Courses</h2>
                    </div>
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
                
                <div class="card-body-modern">
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
                    <div class="empty-state-modern">
                        <div class="empty-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="empty-title">No courses yet</h3>
                        <p class="empty-text">You haven't created any courses. Start building your curriculum by adding the first course.</p>
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
                                            <div class="count-number">{{ $course->students_count }}</div>
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
                                            <a href="{{ route('admin.courses.show', urlencode(Crypt::encrypt($course->id))) }}" 
                                            class="btn-icon view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.edit', urlencode(Crypt::encrypt($course->id))) }}" 
                                            class="btn-icon edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.courses.destroy', urlencode(Crypt::encrypt($course->id))) }}" method="POST" class="inline-form">
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
                    @endif
                </div>

                <!-- Pagination -->
                @if($courses->hasPages())
                <div class="card-footer-modern">
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
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-bolt card-icon"></i>
                        <h2 class="card-title-modern">Quick Actions</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.courses.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add New Course</div>
                                <div class="action-subtitle">Create a new course</div>
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
                    </div>
                </div>
            </div>

            <!-- Course Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-chart-pie card-icon"></i>
                        <h2 class="card-title-modern">Course Statistics</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="stats-list">
                        <div class="stat-item">
                            <span class="stat-label">Courses This Month</span>
                            <span class="stat-value">{{ $coursesThisMonth }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Avg Students per Course</span>
                            <span class="stat-value">{{ $avgStudents }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Published Courses</span>
                            <span class="stat-value">{{ $activeCourses }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Courses with Teacher</span>
                            <span class="stat-value">{{ $assignedTeachers }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-content">
            <p class="footer-text">© {{ date('Y') }} School Management System. All rights reserved.</p>
            <p class="footer-meta">
                <span><i class="fas fa-book"></i> Course Management</span>
                <span class="separator">•</span>
                <span><i class="fas fa-calendar"></i> {{ now()->format('M d, Y') }}</span>
            </p>
        </div>
    </footer>

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
@endsection

@push('styles')
<style>
    /* Color Variables - Same as dashboard */
    :root {
        --primary: #4f46e5;
        --primary-light: #eef2ff;
        --primary-dark: #3730a3;
        
        --success: #10b981;
        --success-light: #d1fae5;
        --success-dark: #059669;
        
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --warning-dark: #d97706;
        
        --info: #06b6d4;
        --info-light: #cffafe;
        --info-dark: #0891b2;
        
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --danger-dark: #dc2626;
        
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        
        --radius: 12px;
        --radius-sm: 8px;
        --radius-lg: 16px;
    }

    /* Dashboard Header - Same as dashboard */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }
    
    .user-greeting {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    
    .user-avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        border: 3px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }
    
    .greeting-text {
        color: white;
    }
    
    .welcome-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: white;
    }
    
    .welcome-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .separator {
        opacity: 0.5;
    }
    
    .pending-notice {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .header-alert {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .alert-badge {
        position: relative;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }
    
    .badge-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--danger);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        border: 2px solid var(--primary);
    }
    
    .alert-text {
        color: white;
    }
    
    .alert-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .alert-subtitle {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* Stats Grid - Same as dashboard */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-200);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-content {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-meta {
        font-size: 0.875rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .stat-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }
    
    .stat-primary .stat-icon-wrapper {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .stat-success .stat-icon-wrapper {
        background: var(--success-light);
        color: var(--success);
    }
    
    .stat-warning .stat-icon-wrapper {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .stat-info .stat-icon-wrapper {
        background: var(--info-light);
        color: var(--info);
    }
    
    .stat-footer {
        background: var(--gray-50);
        padding: 0.75rem 1.5rem;
        border-top: 1px solid var(--gray-200);
    }
    
    .stat-link {
        color: var(--gray-700);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .stat-link:hover {
        color: var(--primary);
        gap: 0.75rem;
    }

    /* Content Grid - Same as dashboard */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Dashboard Cards - Same as dashboard */
    .dashboard-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header-modern {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .card-icon {
        font-size: 1.25rem;
        color: var(--primary);
    }
    
    .card-title-modern {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }
    
    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }
    
    .card-body-modern {
        padding: 1.5rem;
    }
    
    .card-footer-modern {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Header Actions - Kept from original */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

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
        background: white;
    }

    .search-container input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    /* Buttons - Kept from original */
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
        background: var(--primary-dark);
    }

    /* Alerts - Kept from original */
    .alert {
        margin: 0 0 1.5rem 0;
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
        border: 1px solid #10b981;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .alert i {
        font-size: 1rem;
    }

    /* Table Styles - Kept from original */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .courses-table {
        width: 100%;
        border-collapse: collapse;
    }

    .courses-table thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .courses-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--gray-600);
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .courses-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .courses-table tbody tr:hover {
        background: var(--gray-50);
    }

    /* Course Info Cell - Kept from original */
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
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }

    .course-2 {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
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
        color: var(--gray-900);
        margin-bottom: 0.25rem;
        font-size: 0.9375rem;
    }

    .course-desc {
        font-size: 0.8125rem;
        color: var(--gray-600);
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }

    .course-mobile-info {
        display: none;
        margin-top: 0.5rem;
        font-size: 0.8125rem;
        color: var(--gray-600);
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
        
        .card-header-modern {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .search-container {
            min-width: unset;
            width: 100%;
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

    /* Course Code - Kept from original */
    .course-code {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: var(--gray-100);
        color: var(--gray-800);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    /* Teacher Info - Kept from original */
    .teacher-info {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .teacher-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
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
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .teacher-id {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .no-teacher {
        color: var(--gray-600);
        font-size: 0.875rem;
        font-style: italic;
    }

    /* Students Count - Kept from original */
    .students-count {
        text-align: center;
    }

    .count-number {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 1rem;
    }

    .count-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.125rem;
    }

    @media (max-width: 576px) {
        .students-count {
            text-align: left;
        }
    }

    /* Status Badges - Kept from original */
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
        background: var(--success-light);
        color: var(--success-dark);
    }

    .status-draft {
        background: var(--warning-light);
        color: var(--warning-dark);
    }

    /* Action Buttons - Kept from original */
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

    /* Empty State - Kept from original */
    .empty-state-modern {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--gray-400);
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    .empty-state-modern h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .empty-state-modern p {
        color: var(--gray-600);
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.5;
    }

    .empty-hint {
        margin-top: 1rem;
        color: var(--gray-500);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Pagination - Kept from original */
    .pagination-info {
        font-size: 0.875rem;
        color: var(--gray-600);
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
        background: var(--gray-100);
        color: var(--gray-700);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    
    .pagination-btn:hover:not(.disabled):not(.active) {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .pagination-btn.active {
        background: var(--primary);
        color: white;
    }
    
    .pagination-btn.disabled {
        background: var(--gray-200);
        color: var(--gray-400);
        cursor: not-allowed;
    }

    /* Quick Actions - Same as dashboard */
    .quick-actions-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: all 0.2s ease;
        border: 2px solid;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }
    
    .action-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }
    
    .action-primary {
        background: var(--primary-light);
        border-color: var(--primary);
    }
    
    .action-primary:hover {
        background: var(--primary);
    }
    
    .action-primary:hover .action-title,
    .action-primary:hover .action-subtitle,
    .action-primary:hover .action-icon,
    .action-primary:hover .action-arrow {
        color: white;
    }
    
    .action-success {
        background: var(--success-light);
        border-color: var(--success);
    }
    
    .action-success:hover {
        background: var(--success);
    }
    
    .action-success:hover .action-title,
    .action-success:hover .action-subtitle,
    .action-success:hover .action-icon,
    .action-success:hover .action-arrow {
        color: white;
    }
    
    .action-warning {
        background: var(--warning-light);
        border-color: var(--warning);
    }
    
    .action-warning:hover {
        background: var(--warning);
    }
    
    .action-warning:hover .action-title,
    .action-warning:hover .action-subtitle,
    .action-warning:hover .action-icon,
    .action-warning:hover .action-arrow {
        color: white;
    }
    
    .action-info {
        background: var(--info-light);
        border-color: var(--info);
    }
    
    .action-info:hover {
        background: var(--info);
    }
    
    .action-info:hover .action-title,
    .action-info:hover .action-subtitle,
    .action-info:hover .action-icon,
    .action-info:hover .action-arrow {
        color: white;
    }
    
    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-icon {
        color: var(--primary);
    }
    
    .action-success .action-icon {
        color: var(--success);
    }
    
    .action-warning .action-icon {
        color: var(--warning);
    }
    
    .action-info .action-icon {
        color: var(--info);
    }
    
    .action-content {
        flex: 1;
    }
    
    .action-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-title {
        color: var(--primary-dark);
    }
    
    .action-success .action-title {
        color: var(--success-dark);
    }
    
    .action-warning .action-title {
        color: var(--warning-dark);
    }
    
    .action-info .action-title {
        color: var(--info-dark);
    }
    
    .action-subtitle {
        font-size: 0.875rem;
        color: var(--gray-600);
        transition: all 0.2s ease;
    }
    
    .action-arrow {
        font-size: 1.125rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-arrow {
        color: var(--primary);
    }
    
    .action-success .action-arrow {
        color: var(--success);
    }
    
    .action-warning .action-arrow {
        color: var(--warning);
    }
    
    .action-info .action-arrow {
        color: var(--info);
    }

    /* Stats List - Kept from original */
    .stats-list {
        padding: 0.5rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid var(--gray-200);
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .stat-value {
        font-weight: 600;
        color: var(--gray-900);
    }

    /* Footer - Same as dashboard */
    .dashboard-footer {
        background: white;
        border-top: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: var(--shadow-sm);
    }
    
    .footer-content {
        text-align: center;
    }
    
    .footer-text {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0 0 0.5rem 0;
    }
    
    .footer-meta {
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin: 0;
    }
    
    /* Responsive Design - Same as dashboard */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-avatar-large {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 1.875rem;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .footer-meta {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .separator {
            display: none;
        }
    }

    /* Only add minimal responsive fixes */
    @media (max-width: 768px) {
        .hide-on-tablet {
            display: none !important;
        }
        
        .course-mobile-info {
            display: block;
        }
        
        .card-header-modern {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .search-container {
            min-width: unset;
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .hide-on-mobile {
            display: none !important;
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality - EXACTLY AS IN ORIGINAL
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

        // Print functionality - EXACTLY AS IN ORIGINAL
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

        // Export to CSV functionality - EXACTLY AS IN ORIGINAL
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