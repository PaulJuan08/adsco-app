@extends('layouts.student')

@section('title', 'Topics - Student Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}" class="text-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('student.courses.index') }}" class="text-link">My Courses</a>
            <i class="fas fa-chevron-right"></i>
            <span>Topics</span>
        </div>
        <h1>Course Topics</h1>
        <p>Access learning materials and track your progress</p>
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
                <div class="stat-number">{{ $totalTopics ?? 0 }}</div>
                <div class="stat-label">Total Topics</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-book text-primary"></i> In your courses
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $completedTopics ?? 0 }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-trophy text-success"></i> Topics finished
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $topicsWithVideo ?? 0 }}</div>
                <div class="stat-label">Video Topics</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-video"></i>
            </div>
        </div>
        <div class="text-sm text-secondary">
            <i class="fas fa-play-circle text-danger"></i> Available videos
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number" id="overall-progress">
                    @if($totalTopics > 0)
                        {{ round(($completedTopics / $totalTopics) * 100) }}%
                    @else
                        0%
                    @endif
                </div>
                <div class="stat-label">Overall Progress</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <!-- Progress bar that will update -->
        <div class="progress-bar mt-2">
            <div class="progress-fill" id="progress-fill" 
                style="width: @if($totalTopics > 0){{ round(($completedTopics / $totalTopics) * 100) }}@else 0 @endif%">
            </div>
        </div>
        <div class="text-sm text-secondary mt-1">
            <span id="completed-count">{{ $completedTopics }}</span> of 
            <span id="total-count">{{ $totalTopics }}</span> topics completed
        </div>
    </div>
</div>

<!-- Course Filter Tabs -->
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" data-tab="all">All Topics</button>
        @foreach($courses as $course)
            <button class="tab-btn" data-tab="course-{{ $course->id }}">
                {{ $course->course_code }}
            </button>
        @endforeach
    </div>
    
    <!-- All Topics Tab -->
    <div class="tab-content active" id="all-tab">
        @if($allTopics->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h3>No topics available</h3>
            <p>You don't have access to any topics yet. Enroll in a course to get started.</p>
            <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                <i class="fas fa-book"></i> Browse Courses
            </a>
        </div>
        @else
        <div class="topics-grid">
            @foreach($allTopics as $topic)
                @php
                    $encryptedId = Crypt::encrypt($topic->id);
                    $isCompleted = in_array($topic->id, $completedTopicIds);
                    
                    // Get the first course for this topic
                    $course = $topic->courses->first() ?? $topic->course ?? null;
                @endphp
                
                @if($course)
                <div class="topic-card topic-{{ ($loop->index % 3) + 1 }} {{ $isCompleted ? 'completed' : '' }}">
                    <div class="topic-card-header">
                        <div class="topic-icon">
                            @if($topic->video_link)
                                <i class="fas fa-video"></i>
                            @elseif($topic->attachment)
                                <i class="fas fa-file-alt"></i>
                            @else
                                <i class="fas fa-chalkboard"></i>
                            @endif
                        </div>
                        <div class="topic-status">
                            @if($isCompleted)
                                <div class="status-container">
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                    <a href="{{ route('student.topics.show', $encryptedId) }}" 
                                    class="btn btn-sm btn-outline-success ms-2 review-btn">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            @else
                                <span class="badge badge-primary">
                                    <i class="fas fa-clock"></i> In Progress
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="topic-card-body">
                        <div class="course-badge">
                            <i class="fas fa-book"></i>
                            {{ $course->course_code ?? 'General' }}
                        </div>
                        <h3 class="topic-title">{{ $topic->title }}</h3>
                        
                        @if($topic->video_link)
                        <div class="topic-video-indicator">
                            <i class="fas fa-video"></i> Video available
                        </div>
                        @endif
                        
                        @if($topic->attachment)
                        <div class="topic-attachment-indicator">
                            <i class="fas fa-paperclip"></i> Materials available
                        </div>
                        @endif
                    </div>
                    <div class="topic-card-footer">
                        <div class="action-buttons">
                            <a href="{{ route('student.topics.show', $encryptedId) }}" class="btn btn-primary">
                                <i class="fas fa-play-circle"></i>
                                {{ $isCompleted ? 'Review Topic' : 'Start Learning' }}
                            </a>
                            @if($isCompleted)
                            <button class="btn btn-outline-secondary btn-sm mark-incomplete-btn" 
                                    data-topic-id="{{ $encryptedId }}">
                                <i class="fas fa-undo"></i> Incomplete
                            </button>
                            @endif
                        </div>
                        <div class="topic-meta">
                            @if($topic->estimated_time)
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $topic->estimated_time }}
                                </span>
                            @endif
                            <span class="meta-item">
                                <i class="fas fa-calendar"></i>
                                {{ $topic->created_at->format('M d') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
    
    <!-- Course-specific Tabs -->
    @foreach($courses as $course)
        <div class="tab-content" id="course-{{ $course->id }}-tab">
            @php
                $courseTopics = $course->topics;
            @endphp
            
            @if($courseTopics->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>No topics in this course</h3>
                <p>This course doesn't have any topics yet. Check back later or contact your instructor.</p>
            </div>
            @else
            <div class="topics-list">
                @foreach($courseTopics as $topic)
                    @php
                        $encryptedId = Crypt::encrypt($topic->id);
                        $isCompleted = Auth::user()->completedTopics()
                            ->where('topic_id', $topic->id)
                            ->exists();
                    @endphp
                    <div class="topic-item {{ $isCompleted ? 'completed' : '' }}">
                        <div class="topic-item-header">
                            <div class="topic-item-icon">
                                @if($topic->video_link)
                                    <i class="fas fa-video"></i>
                                @elseif($topic->attachment)
                                    <i class="fas fa-file-alt"></i>
                                @else
                                    <i class="fas fa-chalkboard"></i>
                                @endif
                            </div>
                            <div class="topic-item-info">
                                <h4 class="topic-item-title">{{ $topic->title }}</h4>
                                <div class="topic-item-meta">
                                    @if($topic->estimated_time)
                                        <span class="meta-item">
                                            <i class="fas fa-clock"></i> {{ $topic->estimated_time }}
                                        </span>
                                    @endif
                                    @if($topic->video_link)
                                        <span class="meta-item">
                                            <i class="fas fa-video"></i> Video
                                        </span>
                                    @endif
                                    @if($topic->attachment)
                                        <span class="meta-item">
                                            <i class="fas fa-paperclip"></i> Materials
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="topic-item-status">
                                @if($isCompleted)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                @else
                                    <span class="badge badge-primary">
                                        <i class="fas fa-clock"></i> In Progress
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="topic-item-actions">
                            <a href="{{ route('student.topics.show', Crypt::encrypt($topic->id)) }}" class="btn btn-outline">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('student.topics.show', Crypt::encrypt($topic->id)) }}" class="btn btn-primary">
                                <i class="fas fa-play-circle"></i> 
                                {{ $isCompleted ? 'Review' : 'Learn' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    @endforeach
</div>

<!-- Recently Completed -->
@if($recentlyCompleted->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h3><i class="fas fa-trophy"></i> Recently Completed</h3>
    </div>
    <div class="card-body">
        <div class="completed-grid">
            @foreach($recentlyCompleted as $completed)
                @php
                    $topic = $completed->topic;
                    if (!$topic) continue; // Skip if topic doesn't exist
                    
                    $encryptedId = Crypt::encrypt($topic->id);
                    
                    // Get course for this topic - handle both relationships
                    $course = null;
                    if ($topic->courses && $topic->courses->isNotEmpty()) {
                        $course = $topic->courses->first();
                    } elseif ($topic->course) {
                        $course = $topic->course;
                    }
                @endphp
                
                <div class="completed-item">
                    <div class="completed-icon">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="completed-info">
                        <div class="completed-title">{{ $topic->title }}</div>
                        <div class="completed-course">
                            @if($course)
                                {{ $course->course_code }}
                            @else
                                No Course Assigned
                            @endif
                        </div>
                        <div class="completed-date">
                            <i class="fas fa-calendar-check"></i>
                            {{ $completed->completed_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="completed-actions">
                        <a href="{{ route('student.topics.show', $encryptedId) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-redo"></i> Review
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Progress Overview -->
<div class="card mt-4">
    <div class="card-header">
        <h3><i class="fas fa-chart-pie"></i> Progress by Course</h3>
    </div>
    <div class="card-body">
        <div class="progress-chart">
            @foreach($courses as $course)
                @php
                    // Get topics for this course
                    $courseTopics = $course->topics;
                    
                    // Get completed topics for this course
                    $completedCourseTopics = 0;
                    foreach ($courseTopics as $topic) {
                        if (in_array($topic->id, $completedTopicIds)) {
                            $completedCourseTopics++;
                        }
                    }
                    
                    // Calculate progress percentage
                    $courseProgress = $courseTopics->count() > 0 
                        ? round(($completedCourseTopics / $courseTopics->count()) * 100)
                        : 0;
                @endphp
                <div class="progress-item">
                    <div class="progress-course">
                        <div class="course-code">{{ $course->course_code }}</div>
                        <div class="course-title">{{ Str::limit($course->title, 30) }}</div>
                    </div>
                    <div class="progress-track">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $courseProgress }}%"></div>
                        </div>
                        <div class="progress-percent">{{ $courseProgress }}%</div>
                    </div>
                    <div class="progress-stats">
                        <span class="stat-number">{{ $completedCourseTopics }}/{{ $courseTopics->count() }}</span>
                        <span class="stat-label">topics</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    /* Breadcrumb */
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
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

    /* Stat Card */
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
    }

    .stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }

    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--secondary);
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-secondary {
        color: var(--secondary);
    }

    .progress-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        border-radius: 4px;
    }

    /* Tabs */
    .tabs-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .tabs-header {
        display: flex;
        border-bottom: 1px solid var(--border);
        background: #f9fafb;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .tab-btn {
        padding: 1rem 1.5rem;
        background: none;
        border: none;
        font-size: 0.9375rem;
        font-weight: 500;
        color: var(--secondary);
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
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

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 1.5rem;
    }

    .tab-content.active {
        display: block;
    }

    /* Topics Grid */
    .topics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .topics-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }

    @media (max-width: 576px) {
        .topics-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Topic Card */
    .topic-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .topic-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .topic-card.completed {
        border-left: 4px solid #10b981;
    }

    .topic-1 .topic-icon {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .topic-2 .topic-icon {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .topic-3 .topic-icon {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }

    .topic-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.25rem 1.25rem 0;
    }

    .topic-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .topic-card-body {
        padding: 1rem 1.25rem;
    }

    .course-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        background: #e0e7ff;
        color: var(--primary);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .topic-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }

    .topic-video-indicator, .topic-attachment-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-right: 0.5rem;
    }

    .topic-video-indicator {
        background: #fee2e2;
        color: #dc2626;
    }

    .topic-attachment-indicator {
        background: #f0fdf4;
        color: #059669;
    }

    .topic-card-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .topic-meta {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: var(--secondary);
    }

    /* Topics List (for course tabs) */
    .topics-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .topic-item {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .topic-item.completed {
        border-left: 4px solid #10b981;
    }

    .topic-item-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
    }

    .topic-item-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .topic-item-info {
        flex: 1;
        min-width: 0;
    }

    .topic-item-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .topic-item-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .topic-item-actions {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 0.75rem;
        background: #f9fafb;
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

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary-light);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .btn-block {
        width: 100%;
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

    /* Recently Completed */
    .completed-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }

    .completed-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .completed-icon {
        font-size: 1.5rem;
        color: #10b981;
    }

    .completed-info {
        flex: 1;
        min-width: 0;
    }

    .completed-title {
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .completed-course {
        font-size: 0.75rem;
        color: var(--primary);
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .completed-date {
        font-size: 0.75rem;
        color: var(--secondary);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Progress Chart */
    .progress-chart {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .progress-item {
        display: grid;
        grid-template-columns: 200px 1fr 100px;
        gap: 1rem;
        align-items: center;
    }

    @media (max-width: 768px) {
        .progress-item {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
    }

    .progress-course {
        min-width: 0;
    }

    .course-code {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .course-title {
        font-size: 0.875rem;
        color: var(--secondary);
        line-height: 1.4;
    }

    .progress-track {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
    }

    .progress-percent {
        width: 60px;
        text-align: right;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.875rem;
    }

    .progress-stats {
        text-align: right;
        min-width: 100px;
    }

    .stat-number {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
        display: block;
        margin-bottom: 0.125rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: #f9fafb;
    }

    .card-header h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
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

    /* Text Link */
    .text-link {
        color: var(--primary);
        text-decoration: none;
        transition: color 0.2s;
    }

    .text-link:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    /* Utility */
    .mt-4 {
        margin-top: 1.5rem;
    }

    /* Status container for completed topics */
    .status-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Review button styling */
    .review-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .review-btn:hover {
        background-color: #d1fae5;
        border-color: #10b981;
    }

    /* Action buttons container */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    /* Mark incomplete button */
    .mark-incomplete-btn:hover {
        background-color: #fef3c7;
        border-color: #f59e0b;
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

        // Search functionality
        const searchInput = document.getElementById('search-topics');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const topicCards = document.querySelectorAll('.topic-card');
                const topicItems = document.querySelectorAll('.topic-item');
                
                // Search in grid view
                topicCards.forEach(card => {
                    const title = card.querySelector('.topic-title')?.textContent.toLowerCase() || '';
                    const course = card.querySelector('.course-badge')?.textContent.toLowerCase() || '';
                    
                    if (searchTerm === '' || 
                        title.includes(searchTerm) || 
                        course.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Search in list view
                topicItems.forEach(item => {
                    const title = item.querySelector('.topic-item-title')?.textContent.toLowerCase() || '';
                    
                    if (searchTerm === '' || title.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Mark topic as completed
        const markCompleteBtns = document.querySelectorAll('.mark-complete-btn');
        markCompleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const topicId = this.dataset.topicId;
                const btn = this;
                
                btn.classList.add('loading');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Marking...';
                btn.disabled = true;
                
                // AJAX request to mark topic as completed
                fetch(`/student/topics/${topicId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Completed';
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-success');
                        btn.classList.remove('loading');
                        
                        // Update progress
                        location.reload();
                    } else {
                        btn.innerHTML = '<i class="fas fa-play-circle"></i> Mark Complete';
                        btn.classList.remove('loading');
                        btn.disabled = false;
                        alert(data.message || 'Error marking topic as complete');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btn.innerHTML = '<i class="fas fa-play-circle"></i> Mark Complete';
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    alert('Network error. Please try again.');
                });
            });
        });

        // Mark topic as incomplete from index page
        document.querySelectorAll('.mark-incomplete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const topicId = this.dataset.topicId;
                const btn = this;
                
                // Show loading state
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                
                // AJAX request to mark topic as incomplete
                fetch(`/student/topics/${topicId}/incomplete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success notification
                        showNotification('Topic marked as incomplete!', 'success');
                        
                        // Update localStorage with new stats
                        if (data.stats) {
                            localStorage.setItem('topicProgress', JSON.stringify(data.stats));
                            updateProgressUI(data.stats);
                        }
                        
                        // Reload page after delay to show updated status
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                        showNotification(data.message || 'Error marking topic as incomplete.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                    showNotification('Network error. Please try again.', 'error');
                });
            });
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
            
            // Close button
            alert.querySelector('.alert-close').addEventListener('click', function() {
                alert.remove();
            });
        }
    });

    // Listen for storage events (for cross-tab updates)
    window.addEventListener('storage', function(e) {
        if (e.key === 'topicProgress') {
            updateProgressFromStorage();
        }
    });

    // Function to update progress from localStorage
    function updateProgressFromStorage() {
        const stats = JSON.parse(localStorage.getItem('topicProgress'));
        if (stats) {
            updateProgressUI(stats);
        }
    }

    // Function to update progress UI
    function updateProgressUI(stats) {
        // Update progress percentage
        document.querySelectorAll('#overall-progress').forEach(el => {
            el.textContent = stats.progressPercentage + '%';
        });
        
        // Update progress bar width
        document.querySelectorAll('#progress-fill').forEach(el => {
            el.style.width = stats.progressPercentage + '%';
        });
        
        // Update counts
        document.querySelectorAll('#completed-count').forEach(el => {
            el.textContent = stats.completedTopics;
        });
        
        document.querySelectorAll('#total-count').forEach(el => {
            el.textContent = stats.totalTopics;
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const stats = JSON.parse(localStorage.getItem('topicProgress'));
        if (stats) {
            updateProgressUI(stats);
        }
    });
</script>
@endpush
@endsection