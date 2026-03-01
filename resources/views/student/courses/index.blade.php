{{-- resources/views/student/courses/index.blade.php --}}
@extends('layouts.student')

@section('title', 'My Courses - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    {{-- ── Header ── --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">My Courses</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-book-open"></i> Access your enrolled courses and track progress
                        @if($enrolledCourses->count() > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $enrolledCourses->count() }} enrolled</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Enrolled Courses</div>
                    <div class="stat-number">{{ number_format($enrolledCourses->count()) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-book"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Completed Courses</div>
                    <div class="stat-number">{{ number_format($overallStats['completed_courses'] ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-number">{{ number_format($overallStats['in_progress_courses'] ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($overallStats['total_topics'] ?? 0) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-list-check"></i></div>
            </div>
        </div>
    </div>

    {{-- ── Main Content ── --}}
    <div class="content-grid">
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-book-open"></i> My Enrolled Courses</h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search my courses..." id="search-courses">
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
                    @endif

                    @if($enrolledCourses->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-book-open"></i></div>
                        <h3 class="empty-title">No courses enrolled yet</h3>
                        <p class="empty-text">You haven't been enrolled in any courses yet. Please contact the administrator to enroll you.</p>
                        <div class="empty-hint"><i class="fas fa-info-circle"></i> Only administrators can enroll students in courses.</div>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="courses-table">
                            <thead>
                                <tr>
                                    <th>Course Title</th>
                                    <th class="hide-on-mobile">Code</th>
                                    <th class="hide-on-tablet">Teacher</th>
                                    <th>Progress</th>
                                    <th class="hide-on-tablet">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrolledCourses as $enrollment)
                                @php
                                    $course      = $enrollment->course;
                                    $encryptedId = Crypt::encrypt($course->id);
                                    $pd          = $enrollment->progress_data ?? ['total'=>0,'completed'=>0,'percentage'=>0,'is_completed'=>false];
                                    $isCompleted = $pd['is_completed'] || $enrollment->grade !== null;
                                @endphp
                                <tr class="clickable-row"
                                    data-href="{{ route('student.courses.show', $encryptedId) }}"
                                    data-title="{{ strtolower($course->title) }}">
                                    <td>
                                        <div class="course-info-cell">
                                            <div class="course-icon course-{{ ($loop->index % 4) + 1 }}">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div class="course-details">
                                                <div class="course-name">{{ $course->title }}</div>
                                                <div class="course-desc">{{ Str::limit($course->description, 50) }}</div>
                                                <div class="course-mobile-info">
                                                    <div class="course-code-mobile">{{ $course->course_code }}</div>
                                                    @if($course->teacher)
                                                    <div class="teacher-mobile">
                                                        <i class="fas fa-chalkboard-teacher"></i>
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
                                            <div class="teacher-avatar">{{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}</div>
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
                                            @if($isCompleted)
                                                <div class="count-number">{{ $enrollment->grade ?? 100 }}%</div>
                                                <div class="count-label">Completed</div>
                                            @else
                                                <div class="count-number">{{ $pd['percentage'] }}%</div>
                                                <div class="count-label">
                                                    @if($pd['total'] > 0)
                                                        {{ $pd['completed'] }}/{{ $pd['total'] }} topics
                                                    @else
                                                        No topics
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="hide-on-tablet">
                                        @if($isCompleted)
                                            <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Completed</span>
                                        @else
                                            <span class="item-badge badge-primary"><i class="fas fa-clock"></i> In Progress</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                @if($enrolledCourses instanceof \Illuminate\Pagination\AbstractPaginator && $enrolledCourses->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $enrolledCourses->firstItem() }} to {{ $enrolledCourses->lastItem() }} of {{ $enrolledCourses->total() }} courses
                    </div>
                    <div class="pagination-links">{{ $enrolledCourses->links() }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="right-column">
            <div class="dashboard-card">
                <div class="card-header"><h2 class="card-title"><i class="fas fa-chart-pie"></i> Learning Overview</h2></div>
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background:linear-gradient(135deg,var(--primary-light),var(--primary))"><i class="fas fa-book"></i></div>
                            <div class="item-info"><div class="item-name">Total Enrollments</div></div>
                            <div class="stat-number">{{ $enrolledCourses->count() }}</div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background:linear-gradient(135deg,var(--success-light),var(--success))"><i class="fas fa-graduation-cap"></i></div>
                            <div class="item-info"><div class="item-name">Completed Courses</div></div>
                            <div class="stat-number">{{ $overallStats['completed_courses'] ?? 0 }}</div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background:linear-gradient(135deg,var(--warning-light),var(--warning))"><i class="fas fa-chart-line"></i></div>
                            <div class="item-info"><div class="item-name">In Progress</div></div>
                            <div class="stat-number">{{ $overallStats['in_progress_courses'] ?? 0 }}</div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background:linear-gradient(135deg,var(--info-light),var(--info))"><i class="fas fa-star"></i></div>
                            <div class="item-info"><div class="item-name">Average Grade</div></div>
                            <div class="stat-number">{{ $overallStats['average_grade'] ?? 0 }}%</div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background:linear-gradient(135deg,var(--primary-light),var(--primary-dark))"><i class="fas fa-percent"></i></div>
                            <div class="item-info"><div class="item-name">Completion Rate</div></div>
                            <div class="stat-number">
                                @php $tt = $overallStats['total_topics'] ?? 0; $ct = $overallStats['completed_topics'] ?? 0; @endphp
                                {{ $tt > 0 ? round(($ct / $tt) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header"><h2 class="card-title"><i class="fas fa-history"></i> Recent Activity</h2></div>
                <div class="card-body">
                    <div class="items-list">
                        @forelse($recentActivities as $activity)
                        <div class="list-item">
                            <div class="item-avatar" style="background:var(--gray-100);color:var(--gray-700)">
                                @if($activity['type'] === 'grade')
                                    <i class="fas fa-graduation-cap" style="color:var(--success)"></i>
                                @elseif($activity['type'] === 'enrollment')
                                    <i class="fas fa-user-plus" style="color:var(--info)"></i>
                                @else
                                    <i class="fas fa-book-open" style="color:var(--primary)"></i>
                                @endif
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $activity['text'] }}</div>
                                <div class="item-meta">{{ $activity['time'] }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-info-circle"></i></div>
                            <p class="empty-text">No recent activity</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header"><h2 class="card-title"><i class="fas fa-lightbulb"></i> Study Tips</h2></div>
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background:var(--warning-light);color:var(--warning-dark)"><i class="fas fa-clock"></i></div>
                            <div class="item-info"><div class="item-name">Consistent Schedule</div><div class="item-meta">Study at the same time each day</div></div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background:var(--warning-light);color:var(--warning-dark)"><i class="fas fa-pencil-alt"></i></div>
                            <div class="item-info"><div class="item-name">Take Notes</div><div class="item-meta">Improves retention by 34%</div></div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background:var(--warning-light);color:var(--warning-dark)"><i class="fas fa-users"></i></div>
                            <div class="item-info"><div class="item-name">Study Groups</div><div class="item-meta">Collaborate with classmates</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p class="footer-note">Student Dashboard • Last accessed {{ now()->format('M d, Y h:i A') }}</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.target.closest('a') || e.target.closest('button')) return;
            const href = this.dataset.href;
            if (href) window.location.href = href;
        });
    });

    const searchInput = document.getElementById('search-courses');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.style.display = (!term || (row.dataset.title || '').includes(term)) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush