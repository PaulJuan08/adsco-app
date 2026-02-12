@extends('layouts.student')

@section('title', 'Topics - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-index.css') }}">
<!-- NO additional styles - using only topic-index.css -->
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header - Using same pattern as course-index.css -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Course Topics</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-chalkboard"></i> Access learning materials and track your progress
                        @if($totalTopics > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $totalTopics }} total topics</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="header-alert">
                <div class="alert-badge" style="background: var(--success-light);">
                    <i class="fas fa-trophy" style="color: var(--success);"></i>
                    <span class="badge-count" style="background: var(--success);">{{ $completedTopics ?? 0 }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Completed</div>
                    <div class="alert-subtitle">{{ $completedTopics ?? 0 }} of {{ $totalTopics ?? 0 }} topics</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Using stats-grid-compact from topic-index.css (same as course-index.css) -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ $totalTopics ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-link">
                <i class="fas fa-book"></i> In your courses
            </div>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Completed</div>
                    <div class="stat-number">{{ $completedTopics ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-link">
                <i class="fas fa-trophy"></i> Topics finished
            </div>
        </div>
        
        <div class="stat-card stat-card-danger">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Video Topics</div>
                    <div class="stat-number">{{ $topicsWithVideo ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-video"></i>
                </div>
            </div>
            <div class="stat-link">
                <i class="fas fa-play-circle"></i> Available videos
            </div>
        </div>
        
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Overall Progress</div>
                    <div class="stat-number" id="overall-progress">
                        @if($totalTopics > 0)
                            {{ round(($completedTopics / $totalTopics) * 100) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div style="margin-top: 0.5rem;">
                <div style="height: 6px; background: var(--gray-200); border-radius: 3px; overflow: hidden;">
                    <div id="progress-fill" style="width: @if($totalTopics > 0){{ round(($completedTopics / $totalTopics) * 100) }}@else 0 @endif%; height: 100%; background: var(--success); border-radius: 3px;"></div>
                </div>
            </div>
            <div class="stat-link">
                <span id="completed-count">{{ $completedTopics }}</span> of 
                <span id="total-count">{{ $totalTopics }}</span> topics completed
            </div>
        </div>
    </div>

    <!-- Main Content Grid - Using content-grid from course-index.css pattern -->
    <div class="content-grid">
        <!-- Left Column - Topics List -->
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-list" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        All Topics
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
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

                    @if($allTopics->isEmpty())
                    <!-- Empty State - Using empty-state from topic-index.css -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="empty-title">No topics available</h3>
                        <p class="empty-text">You don't have access to any topics yet. Enroll in a course to get started.</p>
                        <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                            <i class="fas fa-book"></i> Browse Courses
                        </a>
                        <div class="empty-hint">
                            <i class="fas fa-lightbulb"></i>
                            Topics are organized by course
                        </div>
                    </div>
                    @else
                    <!-- Topics Table - Using topics-table from topic-index.css -->
                    <div class="table-responsive">
                        <table class="topics-table">
                            <thead>
                                <tr>
                                    <th>Topic Title</th>
                                    <th class="hide-on-mobile">Course</th>
                                    <th class="hide-on-tablet">Type</th>
                                    <th>Status</th>
                                    <th class="hide-on-tablet">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allTopics as $topic)
                                @php
                                    $encryptedId = Crypt::encrypt($topic->id);
                                    $isCompleted = in_array($topic->id, $completedTopicIds);
                                    $course = $topic->courses->first() ?? $topic->course ?? null;
                                @endphp
                                @if($course)
                                <tr class="clickable-row" 
                                    data-href="{{ route('student.topics.show', $encryptedId) }}"
                                    data-title="{{ strtolower($topic->title) }}">
                                    <td>
                                        <div class="topic-info-cell">
                                            <div class="topic-icon topic-{{ ($loop->index % 4) + 1 }}">
                                                @if($topic->video_link)
                                                    <i class="fas fa-video"></i>
                                                @elseif($topic->attachment)
                                                    <i class="fas fa-file-alt"></i>
                                                @else
                                                    <i class="fas fa-chalkboard"></i>
                                                @endif
                                            </div>
                                            <div class="topic-details">
                                                <div class="topic-name">{{ $topic->title }}</div>
                                                <div class="topic-mobile-info">
                                                    <span class="status-mobile">
                                                        @if($isCompleted)
                                                            <i class="fas fa-check-circle" style="color: var(--success);"></i> Completed
                                                        @else
                                                            <i class="fas fa-clock" style="color: var(--primary);"></i> In Progress
                                                        @endif
                                                    </span>
                                                    @if($course)
                                                    <span class="date-mobile">
                                                        <i class="fas fa-book"></i> {{ $course->course_code }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hide-on-mobile">
                                        @if($course)
                                        <span class="course-code">{{ $course->course_code }}</span>
                                        @else
                                        <span class="no-teacher">No course</span>
                                        @endif
                                    </td>
                                    <td class="hide-on-tablet">
                                        @if($topic->video_link)
                                            <span class="item-badge badge-danger">
                                                <i class="fas fa-video"></i> Video
                                            </span>
                                        @elseif($topic->attachment)
                                            <span class="item-badge badge-info">
                                                <i class="fas fa-paperclip"></i> Materials
                                            </span>
                                        @else
                                            <span class="item-badge badge-warning">
                                                <i class="fas fa-chalkboard"></i> Text
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isCompleted)
                                            <span class="item-badge badge-success">
                                                <i class="fas fa-check-circle"></i> Completed
                                            </span>
                                        @else
                                            <span class="item-badge badge-primary">
                                                <i class="fas fa-clock"></i> In Progress
                                            </span>
                                        @endif
                                    </td>
                                    <td class="hide-on-tablet">
                                        <div class="created-date">{{ $topic->created_at->format('M d, Y') }}</div>
                                        <div class="created-ago">{{ $topic->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recently Completed Section -->
            @if($recentlyCompleted->count() > 0)
            <div class="dashboard-card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-trophy" style="color: var(--success); margin-right: 0.5rem;"></i>
                        Recently Completed
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        @foreach($recentlyCompleted as $completed)
                        @php
                            $topic = $completed->topic;
                            if (!$topic) continue;
                            $encryptedId = Crypt::encrypt($topic->id);
                            $course = $topic->courses->first() ?? $topic->course ?? null;
                        @endphp
                        <div class="list-item clickable-item" onclick="window.location='{{ route('student.topics.show', $encryptedId) }}'">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $topic->title }}</div>
                                <div style="display: flex; gap: 0.75rem; font-size: var(--font-size-xs); color: var(--gray-600);">
                                    @if($course)
                                    <span><i class="fas fa-book"></i> {{ $course->course_code }}</span>
                                    @endif
                                    <span><i class="fas fa-calendar"></i> {{ $completed->completed_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="students-count">
                                <a href="{{ route('student.topics.show', $encryptedId) }}" class="btn btn-outline btn-sm" onclick="event.stopPropagation();">
                                    <i class="fas fa-redo"></i> Review
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('student.courses.index') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Browse Courses</div>
                                <div class="action-subtitle">Enroll in new courses</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="#" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Continue Learning</div>
                                <div class="action-subtitle">Resume last topic</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Progress by Course Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Progress by Course
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        @foreach($courses as $course)
                        @php
                            $courseTopics = $course->topics;
                            $completedCourseTopics = 0;
                            foreach ($courseTopics as $topic) {
                                if (in_array($topic->id, $completedTopicIds)) {
                                    $completedCourseTopics++;
                                }
                            }
                            $courseProgress = $courseTopics->count() > 0 
                                ? round(($completedCourseTopics / $courseTopics->count()) * 100)
                                : 0;
                        @endphp
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                {{ strtoupper(substr($course->course_code, 0, 1)) }}
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $course->course_code }}</div>
                                <div style="font-size: var(--font-size-xs); color: var(--gray-600);">{{ Str::limit($course->title, 30) }}</div>
                                <div style="margin-top: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; font-size: var(--font-size-xs); margin-bottom: 0.25rem;">
                                        <span>Progress</span>
                                        <span style="font-weight: var(--font-semibold); color: var(--primary);">{{ $courseProgress }}%</span>
                                    </div>
                                    <div style="height: 4px; background: var(--gray-200); border-radius: 2px; overflow: hidden;">
                                        <div style="width: {{ $courseProgress }}%; height: 100%; background: var(--primary); border-radius: 2px;"></div>
                                    </div>
                                    <div style="margin-top: 0.25rem; font-size: var(--font-size-xs); color: var(--gray-600);">
                                        {{ $completedCourseTopics }}/{{ $courseTopics->count() }} topics
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Student Dashboard • Last accessed {{ now()->format('M d, Y h:i A') }}
        </p>
    </footer>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make rows clickable
        const clickableRows = document.querySelectorAll('.clickable-row');
        
        clickableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                    e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById('search-topics');
        const topicRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && topicRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                topicRows.forEach(row => {
                    const topicTitle = row.dataset.title || '';
                    if (searchTerm === '' || topicTitle.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection