@extends('layouts.teacher')

@section('title', 'My Courses')

@section('content')
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
                <div class="stat-number">{{ $courses->count() }}</div>
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
<div class="card">
    <div class="card-header">
        <h2 class="card-title">My Course List</h2>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search courses..." id="search-input">
            </div>
            <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Course
            </a>
        </div>
    </div>

    @if($courses->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <h3>No courses assigned yet</h3>
        <p>You don't have any courses assigned to you yet.</p>
        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create First Course
        </a>
    </div>
    @else
    <div class="table-container">
        <table class="course-table">
            <thead>
                <tr>
                    <th>Course Title</th>
                    <th>Code</th>
                    <th>Students</th>
                    <th>Topics</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr class="course-row" data-search="{{ strtolower($course->title . ' ' . $course->course_code) }}">
                    <td>
                        <div class="course-info">
                            <div class="course-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div>
                                <div class="course-title">{{ $course->title }}</div>
                                <div class="course-desc">{{ Str::limit($course->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge">{{ $course->course_code }}</span>
                    </td>
                    <td>
                        <div class="student-count">
                            <span class="count-number">{{ $course->students_count ?? 0 }}</span>
                            <span class="count-label">students</span>
                        </div>
                    </td>
                    <td>
                        <div class="topic-count">
                            <span class="count-number">{{ $course->topics_count ?? 0 }}</span>
                            <span class="count-label">topics</span>
                        </div>
                    </td>
                    <td>
                        @if($course->is_published)
                            <span class="status published">
                                <i class="fas fa-circle"></i> Published
                            </span>
                        @else
                            <span class="status draft">
                                <i class="fas fa-circle"></i> Draft
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="date">{{ $course->created_at->format('M d, Y') }}</div>
                        <div class="time">{{ $course->created_at->diffForHumans() }}</div>
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
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure?')">
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

    @if($courses->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $courses->firstItem() }}-{{ $courses->lastItem() }} of {{ $courses->total() }}
        </div>
        <div class="pagination-links">
            {{ $courses->links() }}
        </div>
    </div>
    @endif
    @endif
</div>

<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .icon-courses {
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
        color: white;
    }
    
    .icon-users {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .icon-files {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    /* Course Info */
    .course-info {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .course-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    
    .course-title {
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }
    
    .course-desc {
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    /* Count Styles */
    .student-count, .topic-count {
        text-align: center;
    }
    
    .count-number {
        display: block;
        font-weight: 600;
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    .count-label {
        font-size: 0.75rem;
        color: var(--secondary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Status */
    .status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 4px 12px;
        border-radius: 20px;
    }
    
    .status i {
        font-size: 0.5rem;
    }
    
    .status.published {
        background: #dcfce7;
        color: #166534;
    }
    
    .status.draft {
        background: #fee2e2;
        color: #991b1b;
    }
    
    /* Rest of the styles from your admin index */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }
    
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    /* Search Box */
    .search-box {
        position: relative;
        min-width: 250px;
    }
    
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--secondary);
    }
    
    .search-box input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
    }
    
    /* Table Styles */
    .table-container {
        overflow-x: auto;
    }
    
    .course-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .course-table thead {
        background: #f8fafc;
        border-bottom: 2px solid var(--border);
    }
    
    .course-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.875rem;
    }
    
    .course-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .course-table tbody tr:hover {
        background: #f9fafb;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
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
    
    .btn-icon.view:hover {
        background: #dbeafe;
        transform: translateY(-1px);
    }
    
    .btn-icon.edit:hover {
        background: #fef3c7;
        transform: translateY(-1px);
    }
    
    .btn-icon.delete:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }
    
    /* Badge */
    .badge {
        display: inline-block;
        padding: 4px 12px;
        background: #e0e7ff;
        color: var(--primary);
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-input');
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
    });
</script>
@endsection