@extends('layouts.student')

@section('title', 'My Courses - Student Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>My Courses</h1>
        <p>Browse available courses and manage your enrollments</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
        <span class="badge badge-student">
            <i class="fas fa-user-graduate"></i> Student
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $enrolledCourses->count() }}</div>
                <div class="stat-label">Enrolled Courses</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-check-circle text-success"></i> Active enrollments
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $completedCount }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-trophy text-warning"></i> Courses finished
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ number_format($averageGrade, 1) }}</div>
                <div class="stat-label">Average Grade</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-percentage text-primary"></i> Overall performance
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $availableCourses->count() }}</div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-clock text-info"></i> Ready to enroll
        </div>
    </div>
</div>

<!-- Course Tabs -->
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" data-tab="enrolled">My Enrolled Courses</button>
        <button class="tab-btn" data-tab="available">Available Courses</button>
    </div>
    
    <!-- Enrolled Courses Tab -->
    <div class="tab-content active" id="enrolled-tab">
        @if($enrolledCourses->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <h3>No enrolled courses yet</h3>
            <p>You haven't enrolled in any courses yet. Browse available courses and enroll to get started.</p>
            <button class="btn btn-primary switch-tab" data-tab="available">
                <i class="fas fa-book-open"></i>
                Browse Available Courses
            </button>
        </div>
        @else
        <div class="courses-grid">
            @foreach($enrolledCourses as $enrollment)
            @php
                $course = $enrollment->course;
                $encryptedId = Crypt::encrypt($course->id);
            @endphp
            <div class="course-card">
                <div class="course-card-header">
                    <div class="course-icon course-{{ ($loop->index % 4) + 1 }}">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="course-status">
                        @if($enrollment->grade)
                            <span class="badge badge-success">Completed</span>
                        @else
                            <span class="badge badge-primary">In Progress</span>
                        @endif
                    </div>
                </div>
                <div class="course-card-body">
                    <h3 class="course-title">{{ $course->title }}</h3>
                    <div class="course-code">{{ $course->course_code }}</div>
                    <p class="course-description">{{ Str::limit($course->description, 100) }}</p>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>{{ $course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : 'TBD' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-credit-card"></i>
                            <span>{{ $course->credits }} Credits</span>
                        </div>
                    </div>
                    
                    @if($enrollment->grade)
                    <div class="grade-display">
                        <div class="grade-label">Your Grade</div>
                        <div class="grade-value">{{ $enrollment->grade }}% ({{ $enrollment->getGradeLetterAttribute() }})</div>
                    </div>
                    @endif
                </div>
                <div class="course-card-footer">
                    <a href="{{ route('student.courses.show', $encryptedId) }}" class="btn btn-primary">
                        <i class="fas fa-door-open"></i> Access Course
                    </a>
                    <div class="action-links">
                        <a href="{{ route('student.courses.topics', $encryptedId) }}" class="text-link">
                            <i class="fas fa-list"></i> Topics
                        </a>
                        <a href="{{ route('student.courses.grades', $encryptedId) }}" class="text-link">
                            <i class="fas fa-chart-bar"></i> Grades
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    
    <!-- Available Courses Tab -->
    <div class="tab-content" id="available-tab">
        @if($availableCourses->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h3>No courses available</h3>
            <p>There are no courses available for enrollment at the moment.</p>
            <p class="text-sm">Check back later or contact your registrar for course offerings.</p>
        </div>
        @else
        <div class="courses-grid">
            @foreach($availableCourses as $course)
            <div class="course-card">
                <div class="course-card-header">
                    <div class="course-icon course-{{ ($loop->index % 4) + 1 }}">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="course-status">
                        <span class="badge badge-info">Available</span>
                    </div>
                </div>
                <div class="course-card-body">
                    <h3 class="course-title">{{ $course->title }}</h3>
                    <div class="course-code">{{ $course->course_code }}</div>
                    <p class="course-description">{{ Str::limit($course->description, 100) }}</p>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>{{ $course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : 'TBD' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-credit-card"></i>
                            <span>{{ $course->credits }} Credits</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $course->students_count ?? 0 }} enrolled</span>
                        </div>
                    </div>
                    
                    <div class="course-topics">
                        <i class="fas fa-list-check"></i>
                        <span>{{ $course->topics_count ?? 0 }} topics available</span>
                    </div>
                </div>
                <div class="course-card-footer">
                    <form action="{{ route('student.courses.enroll', ['course' => $course->id]) }}" method="POST" class="enroll-form">
                        @csrf
                        <button type="submit" class="btn btn-success enroll-btn">
                            <i class="fas fa-user-plus"></i> Enroll Now
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline course-details-btn" data-course-id="{{ $course->id }}">
                        <i class="fas fa-info-circle"></i> Details
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Course Details Modal -->
<div class="modal" id="courseDetailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalCourseTitle"></h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-section">
                <h4>Course Description</h4>
                <p id="modalCourseDescription"></p>
            </div>
            <div class="modal-grid">
                <div class="modal-item">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <div>
                        <div class="label">Instructor</div>
                        <div id="modalCourseTeacher"></div>
                    </div>
                </div>
                <div class="modal-item">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <div class="label">Credits</div>
                        <div id="modalCourseCredits"></div>
                    </div>
                </div>
                <div class="modal-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <div class="label">Duration</div>
                        <div id="modalCourseDuration"></div>
                    </div>
                </div>
                <div class="modal-item">
                    <i class="fas fa-users"></i>
                    <div>
                        <div class="label">Enrolled</div>
                        <div id="modalCourseEnrolled"></div>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <h4>Learning Outcomes</h4>
                <ul id="modalLearningOutcomes"></ul>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline modal-close">Close</button>
            <form id="modalEnrollForm" method="POST" style="display: none;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Enroll Now
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Responsive Design */
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
    }

    .stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--secondary);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .text-sm {
        font-size: 0.75rem;
    }

    .text-secondary {
        color: var(--secondary);
    }

    /* Tabs */
    .tabs-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .tabs-header {
        display: flex;
        border-bottom: 1px solid var(--border);
        background: #f9fafb;
    }

    .tab-btn {
        padding: 1rem 2rem;
        background: none;
        border: none;
        font-size: 0.9375rem;
        font-weight: 500;
        color: var(--secondary);
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
    }

    .tab-btn:hover {
        color: var(--primary);
        background: rgba(67, 97, 238, 0.05);
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background: white;
    }

    @media (max-width: 576px) {
        .tabs-header {
            flex-direction: column;
        }
        
        .tab-btn {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 2rem;
    }

    .tab-content.active {
        display: block;
    }

    @media (max-width: 768px) {
        .tab-content {
            padding: 1rem;
        }
    }

    /* Courses Grid */
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .courses-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }

    @media (max-width: 576px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Course Card */
    .course-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .course-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.5rem 1.5rem 0;
    }

    .course-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
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

    .course-4 {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .course-card-body {
        padding: 1rem 1.5rem;
    }

    .course-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .course-code {
        font-size: 0.875rem;
        color: var(--primary);
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .course-description {
        font-size: 0.875rem;
        color: var(--secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .course-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: var(--secondary);
    }

    .meta-item i {
        width: 16px;
        color: var(--primary);
    }

    .course-topics {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: var(--primary);
        padding: 0.5rem;
        background: var(--primary-light);
        border-radius: 6px;
        margin-top: 0.5rem;
    }

    .grade-display {
        padding: 0.75rem;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-radius: 8px;
        margin-top: 1rem;
        border: 1px solid #bbf7d0;
    }

    .grade-label {
        font-size: 0.75rem;
        color: #166534;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .grade-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #166534;
    }

    .course-card-footer {
        padding: 1rem 1.5rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .enroll-form {
        margin: 0;
    }

    .action-links {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 0.5rem;
    }

    .text-link {
        font-size: 0.8125rem;
        color: var(--primary);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        transition: color 0.2s;
    }

    .text-link:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        justify-content: center;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #4f46e5;
        transform: translateY(-1px);
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #0da271;
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary-light);
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-student {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-primary {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .badge-success {
        background: #dcfce7;
        color: #065f46;
    }

    .badge-info {
        background: #e0f2fe;
        color: #075985;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: #d1d5db;
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

    .switch-tab {
        margin: 0 auto;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalSlide 0.3s ease;
    }

    @keyframes modalSlide {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .modal-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        flex: 1;
        margin-right: 1rem;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--secondary);
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .modal-close:hover {
        background: var(--light);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-section {
        margin-bottom: 1.5rem;
    }

    .modal-section h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.75rem;
    }

    .modal-section p {
        color: var(--secondary);
        line-height: 1.6;
        margin: 0;
    }

    .modal-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 480px) {
        .modal-grid {
            grid-template-columns: 1fr;
        }
    }

    .modal-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .modal-item i {
        font-size: 1.25rem;
        color: var(--primary);
    }

    .modal-item .label {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-bottom: 0.125rem;
    }

    .modal-item div:last-child {
        font-weight: 500;
        color: var(--dark);
    }

    .modal-section ul {
        margin: 0;
        padding-left: 1.25rem;
        color: var(--secondary);
        line-height: 1.6;
    }

    .modal-section li {
        margin-bottom: 0.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
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

    /* Loading States */
    .enroll-btn.loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .enroll-btn.loading i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                
                // Update active tab button
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update active tab content
                tabContents.forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });

        // Switch tab from empty state
        document.querySelectorAll('.switch-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                const targetTab = document.querySelector(`[data-tab="${tabId}"]`);
                if (targetTab) {
                    targetTab.click();
                }
            });
        });

        // Course details modal
        const modal = document.getElementById('courseDetailsModal');
        const courseDetailsBtns = document.querySelectorAll('.course-details-btn');
        const closeModalBtns = document.querySelectorAll('.modal-close');

        // Route helper
        const routes = {
            enroll: "{{ route('student.courses.enroll', ['course' => ':courseId']) }}"
        };

        // Mock course data - in real app, this would come from API
        const courseData = {
            @foreach($availableCourses as $course)
            {{ $course->id }}: {
                title: "{{ addslashes($course->title) }}",
                description: "{{ addslashes($course->description ?? 'No description available.') }}",
                teacher: "{{ addslashes($course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : 'Not assigned') }}",
                credits: "{{ $course->credits }} Credits",
                duration: "{{ $course->getDurationInWeeksAttribute() ?? 'Flexible' }} weeks",
                enrolled: "{{ $course->students_count ?? 0 }} students",
                outcomes: ["Complete all course topics", "Pass all quizzes", "Submit required assignments"]
            },
            @endforeach
        };

        courseDetailsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const courseId = this.dataset.courseId;
                const data = courseData[courseId];
                
                if (data) {
                    document.getElementById('modalCourseTitle').textContent = data.title;
                    document.getElementById('modalCourseDescription').textContent = data.description;
                    document.getElementById('modalCourseTeacher').textContent = data.teacher;
                    document.getElementById('modalCourseCredits').textContent = data.credits;
                    document.getElementById('modalCourseDuration').textContent = data.duration;
                    document.getElementById('modalCourseEnrolled').textContent = data.enrolled;
                    
                    // Update learning outcomes
                    const outcomesList = document.getElementById('modalLearningOutcomes');
                    outcomesList.innerHTML = '';
                    data.outcomes.forEach(outcome => {
                        const li = document.createElement('li');
                        li.textContent = outcome;
                        outcomesList.appendChild(li);
                    });
                    
                    // Update enroll form action
                    const enrollForm = document.getElementById('modalEnrollForm');
                    enrollForm.style.display = 'block';
                    enrollForm.action = routes.enroll.replace(':courseId', courseId);
                    
                    // Show modal
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        // Close modal
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        // Close modal on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Enroll button loading state
        const enrollForms = document.querySelectorAll('.enroll-form');
        enrollForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('.enroll-btn');
                button.classList.add('loading');
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
                
                // Optional: You can add AJAX submission here
                // e.preventDefault();
                // Submit via AJAX and handle response
            });
        });

        // Search functionality (if needed in future)
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const courseCards = document.querySelectorAll('.course-card');
                
                courseCards.forEach(card => {
                    const title = card.querySelector('.course-title')?.textContent.toLowerCase() || '';
                    const code = card.querySelector('.course-code')?.textContent.toLowerCase() || '';
                    const description = card.querySelector('.course-description')?.textContent.toLowerCase() || '';
                    
                    if (searchTerm === '' || 
                        title.includes(searchTerm) || 
                        code.includes(searchTerm) || 
                        description.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });

    // Handle success messages
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    function showNotification(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
            <button class="alert-close">&times;</button>
        `;
        
        document.querySelector('.tabs-container').insertAdjacentElement('beforebegin', alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
</script>
@endpush
@endsection