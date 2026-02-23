{{-- resources/views/admin/enrollments/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Enrollment Management - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/enrollment.css') }}">
<style>
    /* Additional styles for enrollment page */
    .enrollment-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    @media (max-width: 1024px) {
        .enrollment-container {
            grid-template-columns: 1fr;
        }
    }
    
    .course-selector {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .course-selector-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 1px solid #e2e8f0;
    }
    
    .course-selector-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .course-selector-header p {
        font-size: 0.8125rem;
        color: #64748b;
    }
    
    .course-selector-body {
        padding: 1.5rem;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .course-item {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }
    
    .course-item:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    
    .course-item.selected {
        border-color: #667eea;
        background: #f5f3ff;
        border-width: 2px;
    }
    
    .course-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .course-item-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.9375rem;
    }
    
    .course-item-code {
        font-size: 0.75rem;
        color: #667eea;
        background: #e0e7ff;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
    }
    
    .course-item-desc {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .course-item-meta {
        display: flex;
        gap: 0.75rem;
        font-size: 0.6875rem;
        color: #94a3b8;
    }
    
    .course-item-meta i {
        margin-right: 0.25rem;
    }
    
    .filters-panel {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .filters-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }
    
    .filter-label {
        font-size: 0.6875rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .filter-select, .filter-input {
        padding: 0.625rem 0.875rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.8125rem;
        color: #1e293b;
        background: white;
        transition: all 0.2s ease;
    }
    
    .filter-select:focus, .filter-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .filter-select:hover, .filter-input:hover {
        border-color: #cbd5e1;
    }
    
    .search-wrapper {
        position: relative;
        width: 100%;
    }
    
    .search-wrapper i {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.8125rem;
    }
    
    .search-wrapper input {
        padding-left: 2.25rem;
    }
    
    .students-list {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: white;
    }
    
    .student-item {
        display: flex;
        align-items: center;
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #edf2f7;
        transition: background 0.2s ease;
    }
    
    .student-item:last-child {
        border-bottom: none;
    }
    
    .student-item:hover {
        background: #f8fafc;
    }
    
    .student-item.disabled {
        opacity: 0.6;
        background: #f1f5f9;
    }
    
    .student-checkbox {
        margin-right: 1rem;
    }
    
    .student-checkbox input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #667eea;
    }
    
    .student-checkbox input:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
    
    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .student-info {
        flex: 1;
    }
    
    .student-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .student-email {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }
    
    .student-details {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        font-size: 0.6875rem;
    }
    
    .student-badge {
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
    }
    
    .badge-college {
        background: #e0f2fe;
        color: #0369a1;
    }
    
    .badge-program {
        background: #dcfce7;
        color: #166534;
    }
    
    .badge-year {
        background: #fef9c3;
        color: #854d0e;
    }
    
    .badge-enrolled {
        background: #e0e7ff;
        color: #4f46e5;
    }
    
    .student-status {
        font-size: 0.6875rem;
        padding: 0.25rem 0.5rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .student-status.enrolled {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .selected-count {
        background: #667eea;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .enrolled-students-panel {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    
    .enrolled-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .enrolled-header h3 {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .enrolled-count {
        background: #e2e8f0;
        color: #334155;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .enrolled-list {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .enrolled-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid #edf2f7;
    }
    
    .enrolled-item:last-child {
        border-bottom: none;
    }
    
    .enrolled-item-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .enrolled-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
    }
    
    .enrolled-info h4 {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.8125rem;
        margin-bottom: 0.125rem;
    }
    
    .enrolled-info p {
        font-size: 0.6875rem;
        color: #64748b;
    }
    
    .enrolled-remove {
        color: #ef4444;
        background: none;
        border: none;
        font-size: 0.6875rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .enrolled-remove:hover {
        background: #fef2f2;
    }
    
    .bulk-upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
    }
    
    .bulk-upload-area:hover {
        border-color: #667eea;
        background: #f5f3ff;
    }
    
    .bulk-upload-area i {
        font-size: 2rem;
        color: #94a3b8;
        margin-bottom: 0.5rem;
    }
    
    .bulk-upload-area p {
        font-size: 0.8125rem;
        color: #475569;
        margin-bottom: 0.25rem;
    }
    
    .bulk-upload-area small {
        font-size: 0.6875rem;
        color: #94a3b8;
    }
    
    .bulk-upload-area.has-file {
        border-color: #48bb78;
        background: #f0fff4;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .pagination-info {
        font-size: 0.75rem;
        color: #64748b;
        padding: 0.75rem 1.5rem;
        border-top: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .pagination-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .pagination-btn {
        padding: 0.25rem 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.75rem;
        color: #475569;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .pagination-btn:hover:not(.disabled) {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    
    .pagination-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-btn.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
</style>
@endpush

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
                    <h1 class="welcome-title">Enrollment Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-user-graduate"></i> Enroll students in courses
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.courses.index') }}" class="top-action-btn">
                    <i class="fas fa-book"></i> Manage Courses
                </a>
                <a href="{{ route('admin.users.index') }}?role=4" class="top-action-btn">
                    <i class="fas fa-users"></i> View Students
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Courses</div>
                    <div class="stat-number">{{ $courses->count() }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published</div>
                    <div class="stat-number">{{ $courses->where('is_published', true)->count() }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Enrollments</div>
                    <div class="stat-number">{{ $courses->sum('students_count') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg per Course</div>
                    <div class="stat-number">{{ $courses->count() > 0 ? round($courses->sum('students_count') / $courses->count(), 1) : 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Container -->
    <div class="enrollment-container">
        <!-- Left Column - Course Selection -->
        <div>
            <div class="course-selector">
                <div class="course-selector-header">
                    <h3>
                        <i class="fas fa-graduation-cap" style="color: #667eea;"></i>
                        Select a Course
                    </h3>
                    <p>Choose a course to manage student enrollments</p>
                </div>
                <div class="course-selector-body">
                    @foreach($courses as $course)
                    <div class="course-item {{ $selectedCourse && $selectedCourse->id == $course->id ? 'selected' : '' }}" 
                         data-course-id="{{ $course->id }}"
                         data-course-title="{{ $course->title }}"
                         data-course-code="{{ $course->course_code }}"
                         onclick="selectCourse({{ $course->id }}, '{{ addslashes($course->title) }}', '{{ $course->course_code }}')">
                        <div class="course-item-header">
                            <span class="course-item-title">{{ $course->title }}</span>
                            <span class="course-item-code">{{ $course->course_code }}</span>
                        </div>
                        <div class="course-item-desc">{{ Str::limit($course->description, 100) }}</div>
                        <div class="course-item-meta">
                            <span><i class="fas fa-user-graduate"></i> {{ $course->students_count }} enrolled</span>
                            <span><i class="fas fa-tag"></i> {{ $course->credits }} credits</span>
                            <span><i class="fas {{ $course->is_published ? 'fa-check-circle text-success' : 'fa-clock text-warning' }}"></i> 
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($courses->isEmpty())
                    <div class="empty-state" style="padding: 2rem; text-align: center;">
                        <i class="fas fa-book" style="font-size: 2.5rem; color: #cbd5e0;"></i>
                        <h3 style="margin-top: 1rem; color: #64748b;">No Courses Available</h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Create a course first to manage enrollments.</p>
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i> Create Course
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Bulk Upload Section -->
            <div class="bulk-upload-area" id="bulkUploadArea" onclick="document.getElementById('csvFile').click()" style="margin-top: 1.5rem;">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Upload CSV for Bulk Enrollment</p>
                <small>Click to select a CSV file with student IDs</small>
                <input type="file" id="csvFile" accept=".csv" style="display: none;" onchange="handleFileSelect(this)">
                <div id="selectedFileInfo" style="display: none; margin-top: 0.5rem; padding: 0.5rem; background: white; border-radius: 6px; font-size: 0.75rem;"></div>
            </div>
        </div>

        <!-- Right Column - Student Selection -->
        <div>
            <div class="filters-panel">
                <div class="filters-title">
                    <i class="fas fa-filter" style="color: #667eea;"></i>
                    Filter Students
                </div>
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">College</label>
                        <select class="filter-select" id="collegeFilter" onchange="loadPrograms(this.value)">
                            <option value="all">All Colleges</option>
                            @foreach($colleges as $college)
                                <option value="{{ $college->id }}">{{ $college->college_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Program</label>
                        <select class="filter-select" id="programFilter">
                            <option value="all">All Programs</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Year Level</label>
                        <select class="filter-select" id="yearFilter">
                            <option value="all">All Years</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="filter-row">
                    <div class="filter-group" style="grid-column: span 3;">
                        <label class="filter-label">Search</label>
                        <div class="search-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" class="filter-input" id="searchFilter" placeholder="Search by name, email, or student ID...">
                        </div>
                    </div>
                </div>
                
                <div class="filter-row" style="margin-bottom: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <button class="btn btn-primary btn-sm" onclick="loadStudents()">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="dashboard-card" style="margin-top: 0;">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-users" style="color: var(--primary);"></i>
                        Select Students
                    </h2>
                    <div class="header-actions">
                        <span class="selected-count" id="selectedCount">0 selected</span>
                    </div>
                </div>
                
                <div class="card-body" style="padding: 0;">
                    <!-- Students List -->
                    <div id="studentsList" class="students-list">
                        <div style="text-align: center; padding: 3rem;">
                            <div class="loading-spinner" style="margin: 0 auto 1rem;"></div>
                            <p style="color: #64748b; font-size: 0.875rem;">Select a course and apply filters to load students...</p>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <div id="pagination" class="pagination-info" style="display: none;">
                        <span id="paginationInfo"></span>
                        <div class="pagination-controls" id="paginationControls"></div>
                    </div>
                </div>
                
                <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span id="selectedStudentsInfo">No students selected</span>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-outline-secondary btn-sm" onclick="clearSelections()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                        <button class="btn btn-success btn-sm" onclick="enrollStudents()" id="enrollButton" disabled>
                            <i class="fas fa-user-plus"></i> Enroll Selected
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enrolled Students Panel -->
            <div class="enrolled-students-panel" style="margin-top: 1.5rem;" id="enrolledPanel" style="display: none;">
                <div class="enrolled-header">
                    <h3>
                        <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                        Enrolled Students
                    </h3>
                    <span class="enrolled-count" id="enrolledCount">0</span>
                </div>
                <div class="enrolled-list" id="enrolledList">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Enrollment Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============ GLOBAL VARIABLES ============
    let selectedCourse = {{ $selectedCourse ? $selectedCourse->id : 'null' }};
    let selectedCourseTitle = '{{ $selectedCourse ? addslashes($selectedCourse->title) : '' }}';
    let selectedCourseCode = '{{ $selectedCourse ? $selectedCourse->course_code : '' }}';
    let selectedStudentIds = [];
    let currentPage = 1;
    let lastPage = 1;
    let totalStudents = 0;
    let currentFilters = {};

    // ============ COURSE SELECTION ============
    function selectCourse(courseId, courseTitle, courseCode) {
        // Remove selected class from all courses
        document.querySelectorAll('.course-item').forEach(item => {
            item.classList.remove('selected');
        });
        
        // Add selected class to clicked course
        const courseItem = document.querySelector(`.course-item[data-course-id="${courseId}"]`);
        if (courseItem) {
            courseItem.classList.add('selected');
        }
        
        selectedCourse = courseId;
        selectedCourseTitle = courseTitle;
        selectedCourseCode = courseCode;
        
        // Clear selections
        selectedStudentIds = [];
        updateSelectedCount();
        
        // Load enrolled students
        loadEnrolledStudents(courseId);
        
        // Load available students with current filters
        loadStudents();
        
        // Show notification
        showNotification(`Selected course: ${courseTitle}`, 'info');
    }

    // ============ LOAD PROGRAMS BY COLLEGE ============
    function loadPrograms(collegeId) {
        const programSelect = document.getElementById('programFilter');
        
        if (!collegeId || collegeId === 'all') {
            programSelect.innerHTML = '<option value="all">All Programs</option>';
            return;
        }
        
        fetch(`/admin/enrollments/programs/${collegeId}`)
            .then(response => response.json())
            .then(programs => {
                let options = '<option value="all">All Programs</option>';
                programs.forEach(program => {
                    options += `<option value="${program.id}">${program.program_name} (${program.program_code})</option>`;
                });
                programSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error loading programs:', error);
            });
    }

    // ============ LOAD STUDENTS ============
    function loadStudents(page = 1) {
        if (!selectedCourse) {
            Swal.fire({
                icon: 'warning',
                title: 'No Course Selected',
                text: 'Please select a course first.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }
        
        const collegeId = document.getElementById('collegeFilter').value;
        const programId = document.getElementById('programFilter').value;
        const year = document.getElementById('yearFilter').value;
        const search = document.getElementById('searchFilter').value;
        
        currentFilters = { collegeId, programId, year, search };
        
        const studentsList = document.getElementById('studentsList');
        studentsList.innerHTML = '<div style="text-align: center; padding: 2rem;"><div class="loading-spinner" style="margin: 0 auto 1rem;"></div><p>Loading students...</p></div>';
        
        let url = `/admin/enrollments/students?page=${page}&course_id=${selectedCourse}`;
        if (collegeId && collegeId !== 'all') url += `&college_id=${collegeId}`;
        if (programId && programId !== 'all') url += `&program_id=${programId}`;
        if (year && year !== 'all') url += `&college_year=${year}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderStudents(data);
                currentPage = data.current_page;
                lastPage = data.last_page;
                totalStudents = data.total;
                updatePagination();
            })
            .catch(error => {
                console.error('Error loading students:', error);
                studentsList.innerHTML = '<div style="text-align: center; padding: 2rem; color: #ef4444;"><i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i><p>Error loading students. Please try again.</p></div>';
            });
    }

    // ============ RENDER STUDENTS ============
    function renderStudents(data) {
        const studentsList = document.getElementById('studentsList');
        
        if (data.data.length === 0) {
            studentsList.innerHTML = '<div class="empty-state" style="padding: 2rem; text-align: center;"><i class="fas fa-users" style="font-size: 2.5rem; color: #cbd5e0;"></i><h3 style="margin-top: 1rem; color: #64748b;">No Students Found</h3><p style="color: #94a3b8;">Try adjusting your filters.</p></div>';
            return;
        }
        
        let html = '';
        data.data.forEach(student => {
            const isSelected = selectedStudentIds.includes(student.id);
            const isEnrolled = student.is_enrolled;
            
            html += `
                <div class="student-item ${isEnrolled ? 'disabled' : ''}" data-student-id="${student.id}">
                    <div class="student-checkbox">
                        <input type="checkbox" 
                               value="${student.id}" 
                               ${isSelected ? 'checked' : ''} 
                               ${isEnrolled ? 'disabled' : ''}
                               onchange="toggleStudent(${student.id}, this.checked)">
                    </div>
                    <div class="student-avatar">
                        ${student.f_name.charAt(0).toUpperCase()}${student.l_name.charAt(0).toUpperCase()}
                    </div>
                    <div class="student-info">
                        <div class="student-name">
                            ${student.f_name} ${student.l_name}
                            ${student.student_id ? `<span class="student-badge badge-college">ID: ${student.student_id}</span>` : ''}
                            ${isEnrolled ? '<span class="student-status enrolled"><i class="fas fa-check-circle"></i> Already Enrolled</span>' : ''}
                        </div>
                        <div class="student-email">${student.email}</div>
                        <div class="student-details">
                            ${student.college ? `<span class="student-badge badge-college"><i class="fas fa-university"></i> ${student.college.college_name}</span>` : ''}
                            ${student.program ? `<span class="student-badge badge-program"><i class="fas fa-graduation-cap"></i> ${student.program.program_name}</span>` : ''}
                            ${student.college_year ? `<span class="student-badge badge-year"><i class="fas fa-calendar"></i> ${student.college_year}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        studentsList.innerHTML = html;
        document.getElementById('pagination').style.display = 'flex';
    }

    // ============ TOGGLE STUDENT SELECTION ============
    function toggleStudent(studentId, checked) {
        if (checked) {
            if (!selectedStudentIds.includes(studentId)) {
                selectedStudentIds.push(studentId);
            }
        } else {
            selectedStudentIds = selectedStudentIds.filter(id => id !== studentId);
        }
        
        updateSelectedCount();
    }

    // ============ UPDATE SELECTED COUNT ============
    function updateSelectedCount() {
        const count = selectedStudentIds.length;
        document.getElementById('selectedCount').textContent = count + ' selected';
        document.getElementById('selectedStudentsInfo').textContent = count + ' student(s) selected';
        
        const enrollButton = document.getElementById('enrollButton');
        if (count > 0 && selectedCourse) {
            enrollButton.disabled = false;
        } else {
            enrollButton.disabled = true;
        }
    }

    // ============ CLEAR SELECTIONS ============
    function clearSelections() {
        selectedStudentIds = [];
        updateSelectedCount();
        
        // Uncheck all checkboxes
        document.querySelectorAll('.student-checkbox input[type="checkbox"]:not(:disabled)').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    // ============ LOAD ENROLLED STUDENTS ============
    function loadEnrolledStudents(courseId) {
        const enrolledPanel = document.getElementById('enrolledPanel');
        const enrolledList = document.getElementById('enrolledList');
        const enrolledCount = document.getElementById('enrolledCount');
        
        fetch(`/admin/enrollments/course/${courseId}/students`)
            .then(response => response.json())
            .then(students => {
                if (students.length === 0) {
                    enrolledPanel.style.display = 'none';
                    return;
                }
                
                enrolledPanel.style.display = 'block';
                enrolledCount.textContent = students.length;
                
                let html = '';
                students.forEach(student => {
                    html += `
                        <div class="enrolled-item">
                            <div class="enrolled-item-left">
                                <div class="enrolled-avatar">
                                    ${student.name.charAt(0).toUpperCase()}
                                </div>
                                <div class="enrolled-info">
                                    <h4>${student.name}</h4>
                                    <p>${student.email} • ${student.student_id || 'No ID'}</p>
                                </div>
                            </div>
                            <button class="enrolled-remove" onclick="removeStudent(${student.id}, '${student.name}')">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    `;
                });
                
                enrolledList.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading enrolled students:', error);
            });
    }

    // ============ ENROLL STUDENTS ============
    function enrollStudents() {
        if (selectedStudentIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Students Selected',
                text: 'Please select at least one student to enroll.'
            });
            return;
        }
        
        Swal.fire({
            title: 'Enroll Students?',
            html: `You are about to enroll <strong>${selectedStudentIds.length} student(s)</strong> in <strong>${selectedCourseTitle}</strong>.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#48bb78',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Enroll',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const enrollButton = document.getElementById('enrollButton');
                enrollButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
                enrollButton.disabled = true;
                
                fetch('{{ route("admin.enrollments.enroll") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        course_id: selectedCourse,
                        student_ids: selectedStudentIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Clear selections
                        selectedStudentIds = [];
                        updateSelectedCount();
                        
                        // Reload students and enrolled list
                        loadStudents(currentPage);
                        loadEnrolledStudents(selectedCourse);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to enroll students. Please try again.'
                    });
                })
                .finally(() => {
                    enrollButton.innerHTML = '<i class="fas fa-user-plus"></i> Enroll Selected';
                    enrollButton.disabled = selectedStudentIds.length === 0;
                });
            }
        });
    }

    // ============ REMOVE STUDENT ============
    function removeStudent(studentId, studentName) {
        Swal.fire({
            title: 'Remove Student?',
            html: `Are you sure you want to remove <strong>${studentName}</strong> from this course?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.enrollments.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        course_id: selectedCourse,
                        student_id: studentId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Reload students and enrolled list
                        loadStudents(currentPage);
                        loadEnrolledStudents(selectedCourse);
                        
                        // Remove from selected if present
                        selectedStudentIds = selectedStudentIds.filter(id => id !== studentId);
                        updateSelectedCount();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove student. Please try again.'
                    });
                });
            }
        });
    }

    // ============ BULK UPLOAD ============
    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        
        if (!selectedCourse) {
            Swal.fire({
                icon: 'warning',
                title: 'No Course Selected',
                text: 'Please select a course first.'
            });
            input.value = '';
            return;
        }
        
        const bulkArea = document.getElementById('bulkUploadArea');
        const fileInfo = document.getElementById('selectedFileInfo');
        
        bulkArea.classList.add('has-file');
        fileInfo.style.display = 'block';
        fileInfo.innerHTML = `<i class="fas fa-check-circle" style="color: #48bb78;"></i> Selected: ${file.name}`;
        
        // Ask for confirmation
        Swal.fire({
            title: 'Bulk Enroll?',
            html: `Upload <strong>${file.name}</strong> to bulk enroll students in <strong>${selectedCourseTitle}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#48bb78',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Upload',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                uploadBulkFile(file);
            } else {
                input.value = '';
                bulkArea.classList.remove('has-file');
                fileInfo.style.display = 'none';
            }
        });
    }

    function uploadBulkFile(file) {
        const formData = new FormData();
        formData.append('csv_file', file);
        formData.append('course_id', selectedCourse);
        
        Swal.fire({
            title: 'Uploading...',
            html: 'Please wait while we process the file.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route("admin.enrollments.bulk") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                let message = data.message;
                if (data.not_found && data.not_found.length > 0) {
                    message += '<br><br>Not found IDs: ' + data.not_found.join(', ');
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: message
                });
                
                // Reload students and enrolled list
                loadStudents(currentPage);
                loadEnrolledStudents(selectedCourse);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to upload file. Please try again.'
            });
        })
        .finally(() => {
            document.getElementById('csvFile').value = '';
            document.getElementById('bulkUploadArea').classList.remove('has-file');
            document.getElementById('selectedFileInfo').style.display = 'none';
        });
    }

    // ============ PAGINATION ============
    function updatePagination() {
        const pagination = document.getElementById('pagination');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        
        paginationInfo.textContent = `Page ${currentPage} of ${lastPage} • ${totalStudents} total students`;
        
        let controls = '';
        
        // Previous button
        controls += `<button class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="loadStudents(${currentPage - 1})">Previous</button>`;
        
        // Page numbers
        const start = Math.max(1, currentPage - 2);
        const end = Math.min(lastPage, currentPage + 2);
        
        for (let i = start; i <= end; i++) {
            controls += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="loadStudents(${i})">${i}</button>`;
        }
        
        // Next button
        controls += `<button class="pagination-btn" ${currentPage === lastPage ? 'disabled' : ''} onclick="loadStudents(${currentPage + 1})">Next</button>`;
        
        paginationControls.innerHTML = controls;
    }

    // ============ RESET FILTERS ============
    function resetFilters() {
        document.getElementById('collegeFilter').value = 'all';
        document.getElementById('programFilter').innerHTML = '<option value="all">All Programs</option>';
        document.getElementById('yearFilter').value = 'all';
        document.getElementById('searchFilter').value = '';
        
        if (selectedCourse) {
            loadStudents();
        }
    }

    // ============ SHOW NOTIFICATION ============
    function showNotification(message, type = 'info') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: type,
            title: message
        });
    }

    // ============ INITIAL LOAD ============
    document.addEventListener('DOMContentLoaded', function() {
        // If a course is pre-selected, load its enrolled students
        @if($selectedCourse)
            loadEnrolledStudents({{ $selectedCourse->id }});
            loadStudents();
        @endif
        
        // Load programs for initial college if any
        const collegeFilter = document.getElementById('collegeFilter');
        if (collegeFilter.value && collegeFilter.value !== 'all') {
            loadPrograms(collegeFilter.value);
        }
    });
</script>
@endpush