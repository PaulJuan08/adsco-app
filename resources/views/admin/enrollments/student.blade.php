{{-- resources/views/admin/enrollments/student.blade.php --}}

@extends('layouts.admin')

@section('title', 'Student Enrollments - Admin Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr($student->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">{{ $student->f_name }} {{ $student->l_name }}</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-user-graduate"></i> Student Enrollments
                        @if($student->student_id)
                            <span class="separator">•</span>
                            <span class="pending-notice">ID: {{ $student->student_id }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.users.show', Crypt::encrypt($student->id)) }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back to Profile
                </a>
                <a href="{{ route('admin.enrollments.index') }}" class="top-action-btn">
                    <i class="fas fa-users"></i> Manage Enrollments
                </a>
            </div>
        </div>
    </div>

    <!-- Student Info Cards -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Student ID</div>
                    <div class="stat-number">{{ $student->student_id ?? 'N/A' }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">College</div>
                    <div class="stat-number">{{ $student->college->college_name ?? 'N/A' }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-university"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Program</div>
                    <div class="stat-number">{{ $student->program->program_name ?? 'N/A' }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Year Level</div>
                    <div class="stat-number">{{ $student->college_year ?? 'N/A' }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Container -->
    <div class="content-grid">
        <!-- Left Column - Current Enrollments -->
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        Current Enrollments
                    </h2>
                    <div class="header-actions">
                        <span class="item-badge badge-success">{{ $enrolledCourses->count() }} Enrolled</span>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($enrolledCourses->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-book-open"></i>
                        <h3>Not Enrolled in Any Courses</h3>
                        <p>This student hasn't been enrolled in any courses yet.</p>
                    </div>
                    @else
                    <div class="items-list">
                        @foreach($enrolledCourses as $enrollment)
                        @php
                            $course = $enrollment->course;
                        @endphp
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $course->title }}</div>
                                <div class="item-details">
                                    {{ $course->course_code }} • Enrolled {{ $enrollment->enrolled_at->diffForHumans() }}
                                </div>
                                <div class="item-meta">
                                    <span class="item-badge badge-primary">{{ $course->credits }} Credits</span>
                                    <span class="item-badge {{ $enrollment->grade ? 'badge-success' : 'badge-warning' }}">
                                        <i class="fas {{ $enrollment->grade ? 'fa-check-circle' : 'fa-clock' }}"></i>
                                        {{ $enrollment->grade ? 'Completed ('.$enrollment->grade.'%)' : 'In Progress' }}
                                    </span>
                                </div>
                            </div>
                            <div class="action-dropdown">
                                <button class="action-btn-small" onclick="removeEnrollment({{ $enrollment->id }}, '{{ addslashes($course->title) }}')">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

                <!-- Right Column - Available Courses -->
        <div class="right-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-plus-circle" style="color: var(--primary);"></i>
                        Available Courses
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search courses..." id="searchCourses">
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($availableCourses->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>All Courses Enrolled</h3>
                        <p>This student is already enrolled in all available courses.</p>
                    </div>
                    @else
                    <div class="items-list" id="availableCoursesList">
                        @foreach($availableCourses as $course)
                        <div class="list-item" data-title="{{ strtolower($course->title) }}" data-code="{{ strtolower($course->course_code) }}">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $course->title }}</div>
                                <div class="item-details">
                                    {{ $course->course_code }} • {{ $course->credits }} Credits
                                </div>
                                <div class="item-meta">
                                    <span class="item-badge badge-info">{{ $course->credits }} Credits</span>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-success btn-sm" onclick="enrollInCourse({{ $course->id }}, '{{ addslashes($course->title) }}')">
                                    <i class="fas fa-user-plus"></i> Enroll
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="dashboard-card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary);"></i>
                        Enrollment Summary
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Currently Enrolled</div>
                            </div>
                            <div class="stat-number">{{ $enrolledCourses->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Available Courses</div>
                            </div>
                            <div class="stat-number">{{ $availableCourses->count() }}</div>
                        </div>
                        
                        @php
                            $totalCredits = $enrolledCourses->sum(function($enrollment) {
                                return $enrollment->course->credits ?? 0;
                            });
                        @endphp
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Credits</div>
                            </div>
                            <div class="stat-number">{{ $totalCredits }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Student Enrollments • {{ $student->f_name }} {{ $student->l_name }} • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>

<!-- Hidden Forms -->
<form id="enrollForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="course_id" id="enrollCourseId">
    <input type="hidden" name="student_id" value="{{ $student->id }}">
</form>

<form id="removeForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="enrollment_id" id="removeEnrollmentId">
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality for available courses
        const searchInput = document.getElementById('searchCourses');
        const courseItems = document.querySelectorAll('#availableCoursesList .list-item');
        
        if (searchInput && courseItems.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                courseItems.forEach(item => {
                    const title = item.dataset.title || '';
                    const code = item.dataset.code || '';
                    
                    if (searchTerm === '' || title.includes(searchTerm) || code.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    });

    function enrollInCourse(courseId, courseTitle) {
        Swal.fire({
            title: 'Enroll Student?',
            html: `Are you sure you want to enroll <strong>{{ $student->f_name }} {{ $student->l_name }}</strong> in <strong>${courseTitle}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#48bb78',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Enroll',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('enrollForm');
                document.getElementById('enrollCourseId').value = courseId;
                
                // Show loading
                Swal.fire({
                    title: 'Enrolling...',
                    html: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch('{{ route("admin.enrollments.enroll") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        course_id: courseId,
                        student_ids: [{{ $student->id }}]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Enrolled!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
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
                        text: 'Failed to enroll student. Please try again.'
                    });
                });
            }
        });
    }

    function removeEnrollment(enrollmentId, courseTitle) {
        Swal.fire({
            title: 'Remove Enrollment?',
            html: `Are you sure you want to remove <strong>{{ $student->f_name }} {{ $student->l_name }}</strong> from <strong>${courseTitle}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Removing...',
                    html: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch('{{ route("admin.enrollments.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        course_id: {{ $enrolledCourses->first() ? $enrolledCourses->first()->course_id : 'null' }},
                        student_id: {{ $student->id }}
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
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
                        text: 'Failed to remove enrollment. Please try again.'
                    });
                });
            }
        });
    }
</script>
@endpush