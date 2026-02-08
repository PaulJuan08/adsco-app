@extends('layouts.student')

@section('title', 'Available Courses - Student Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <div class="breadcrumb">
            <a href="{{ route('student.dashboard') }}" class="text-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('student.courses.index') }}" class="text-link">My Courses</a>
            <i class="fas fa-chevron-right"></i>
            <span>Available Courses</span>
        </div>
        <h1>Available Courses</h1>
        <p>Discover and enroll in new courses</p>
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
                <div class="stat-number">{{ $availableCourses->total() }}</div>
                <div class="stat-label">Total Available</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                @php
                    $withTeacher = $availableCourses->filter(function($course) {
                        return !empty($course->teacher);
                    })->count();
                @endphp
                <div class="stat-number">{{ $withTeacher }}</div>
                <div class="stat-label">With Instructors</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                @php
                    $totalTopics = $availableCourses->sum('topics_count');
                @endphp
                <div class="stat-number">{{ $totalTopics }}</div>
                <div class="stat-label">Total Topics</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-list-check"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                @php
                    $totalStudents = $availableCourses->sum('students_count');
                @endphp
                <div class="stat-number">{{ $totalStudents }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="card main-card">
    <div class="card-header">
        <div class="card-title">All Available Courses</div>
        <a href="{{ route('student.courses.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to My Courses
        </a>
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

    @if($availableCourses->isEmpty())
    <!-- Empty State -->
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <h3>No courses available</h3>
        <p>You have enrolled in all available courses or there are no courses published yet.</p>
        <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Return to My Courses
        </a>
    </div>
    @else
    <!-- Available Courses Grid -->
    <div class="available-courses-grid full-grid">
        @foreach($availableCourses as $course)
        @php
            $encryptedId = Crypt::encrypt($course->id);
        @endphp
        <div class="available-course-card">
            <div class="available-course-header">
                <div class="available-course-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="available-course-badge">
                    @if($course->students_count > 50)
                    <span class="badge badge-popular">
                        <i class="fas fa-fire"></i> Popular
                    </span>
                    @else
                    <span class="badge badge-info">
                        <i class="fas fa-star"></i> New
                    </span>
                    @endif
                </div>
            </div>
            <div class="available-course-body">
                <h4 class="available-course-title">{{ $course->title }}</h4>
                <div class="available-course-code">{{ $course->course_code }}</div>
                <p class="available-course-description">{{ Str::limit($course->description, 100) }}</p>
                
                <div class="available-course-meta">
                    <div class="meta-item">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>{{ $course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : 'TBD' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-credit-card"></i>
                        <span>{{ $course->credits }} Credits</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-list-check"></i>
                        <span>{{ $course->topics_count ?? 0 }} Topics</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span>{{ $course->students_count ?? 0 }} Students</span>
                    </div>
                </div>
                
                <div class="available-course-tags">
                    @if($course->duration_weeks)
                    <span class="course-tag">
                        <i class="fas fa-clock"></i> {{ $course->duration_weeks }} weeks
                    </span>
                    @endif
                    @if($course->level)
                    <span class="course-tag">
                        <i class="fas fa-chart-line"></i> {{ ucfirst($course->level) }}
                    </span>
                    @endif
                    @if($course->credits >= 3)
                    <span class="course-tag">
                        <i class="fas fa-award"></i> Advanced
                    </span>
                    @endif
                </div>
            </div>
            <div class="available-course-footer">
                <form action="{{ route('student.courses.enroll', $encryptedId) }}" method="POST" class="enroll-form">
                    @csrf
                    <button type="submit" class="btn btn-success enroll-btn">
                        <i class="fas fa-user-plus"></i> Enroll Now
                    </button>
                </form>
                <a href="{{ route('student.courses.show', $encryptedId) }}" class="btn btn-outline preview-btn">
                    <i class="fas fa-eye"></i> Preview
                </a>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($availableCourses->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $availableCourses->firstItem() }} to {{ $availableCourses->lastItem() }} of {{ $availableCourses->total() }} courses
        </div>
        <div class="pagination-links">
            @if($availableCourses->onFirstPage())
            <span class="pagination-btn disabled">Previous</span>
            @else
            <a href="{{ $availableCourses->previousPageUrl() }}" class="pagination-btn">Previous</a>
            @endif
            
            @foreach(range(1, min(5, $availableCourses->lastPage())) as $page)
                @if($page == $availableCourses->currentPage())
                <span class="pagination-btn active">{{ $page }}</span>
                @else
                <a href="{{ $availableCourses->url($page) }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach
            
            @if($availableCourses->hasMorePages())
            <a href="{{ $availableCourses->nextPageUrl() }}" class="pagination-btn">Next</a>
            @else
            <span class="pagination-btn disabled">Next</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

<style>
    /* Add to existing styles */
    .full-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        padding: 1.5rem;
    }
    
    .badge-popular {
        background: #ffd8a8;
        color: #e8590c;
        border: 1px solid #ffc078;
    }
    
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--secondary);
        margin-bottom: 0.5rem;
    }
    
    .breadcrumb i {
        font-size: 0.75rem;
        opacity: 0.7;
    }
    
    .text-link {
        color: var(--primary);
        text-decoration: none;
    }
    
    .text-link:hover {
        text-decoration: underline;
    }
    
    /* Existing styles remain the same */
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enroll button confirmation
        const enrollForms = document.querySelectorAll('.enroll-form');
        enrollForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to enroll in this course?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
@endsection