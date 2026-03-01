{{-- resources/views/student/courses/show.blade.php --}}
@extends('layouts.student')

@section('title', $course->title . ' - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-show.css') }}">
<style>
    .content-wrapper .form-container { margin: 0 auto; width: 100%; max-width: 1200px; border-radius: 0; }
    @media (min-width: 769px) {
        .content-wrapper .form-container { margin: 1.5rem auto; width: 95%; border-radius: 20px; }
    }
    .enrollment-info {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white; padding: 1rem 1.5rem; border-radius: 12px;
        margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;
    }
    .enrollment-info i { font-size: 1.5rem; }
    .enrollment-info p { margin: 0; font-size: 0.95rem; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
            <h2 class="card-title">{{ $course->title }}</h2>
        </div>
        <div class="top-actions">
            <a href="{{ route('student.courses.index') }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
            @if(isset($enrollment) && $enrollment && $enrollment->grade)
            <a href="{{ route('student.courses.grades', $encryptedId) }}" class="top-action-btn">
                <i class="fas fa-chart-bar"></i> View Grades
            </a>
            @endif
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
        <div class="message-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="message-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @if(!isset($enrollment) || !$enrollment)
        <div class="enrollment-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Not enrolled in this course?</strong>
                <p>Only administrators can enroll students in courses. Please contact the registrar for assistance.</p>
            </div>
        </div>
        @endif

        {{-- Course avatar / banner --}}
        <div class="course-avatar-section">
            <div class="course-details-avatar">{{ strtoupper(substr($course->title, 0, 1)) }}</div>
            <h1 class="course-title">{{ $course->title }}</h1>
            <div class="course-code">{{ $course->course_code }} • {{ $course->credits }} Credits</div>
            <div class="course-status-container">
                @if(isset($enrollment) && $enrollment)
                    @if($enrollment->grade)
                    <span class="status-badge status-published">
                        <i class="fas fa-check-circle"></i> Completed ({{ $enrollment->grade }}%)
                    </span>
                    @else
                    <span class="status-badge" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;">
                        <i class="fas fa-clock"></i> In Progress
                    </span>
                    @endif
                @endif
                @if($course->teacher)
                <span class="status-badge" style="background:linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%);color:white;">
                    <i class="fas fa-chalkboard-teacher"></i> {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                </span>
                @endif
            </div>
        </div>

        {{-- Progress bar --}}
        @if(isset($enrollment) && $enrollment && isset($courseProgress))
        <div style="margin-bottom:2rem;padding:1.5rem;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:16px;color:white;box-shadow:0 10px 25px rgba(102,126,234,0.3);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h3 style="font-size:1.125rem;font-weight:700;margin:0;display:flex;align-items:center;gap:0.5rem;">
                    <i class="fas fa-chart-line"></i> Your Progress
                </h3>
                <span style="font-size:2rem;font-weight:800;">{{ $courseProgress['percentage'] }}%</span>
            </div>
            <div style="height:10px;background:rgba(255,255,255,0.3);border-radius:5px;overflow:hidden;margin-bottom:1rem;">
                <div style="width:{{ $courseProgress['percentage'] }}%;height:100%;background:white;border-radius:5px;"></div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;text-align:center;">
                <div><div style="font-size:1.5rem;font-weight:700;">{{ $courseProgress['completed'] }}</div><div style="font-size:0.875rem;opacity:0.9;">Completed</div></div>
                <div><div style="font-size:1.5rem;font-weight:700;">{{ $courseProgress['remaining'] }}</div><div style="font-size:0.875rem;opacity:0.9;">Remaining</div></div>
                <div><div style="font-size:1.5rem;font-weight:700;">{{ $courseProgress['total'] }}</div><div style="font-size:0.875rem;opacity:0.9;">Total Topics</div></div>
            </div>
        </div>
        @endif

        {{-- Details grid --}}
        <div class="details-grid">
            <div class="detail-section">
                <h3 class="detail-section-title"><i class="fas fa-info-circle"></i> Course Information</h3>
                <div class="detail-row"><span class="detail-label">Course Code</span><span class="detail-value">{{ $course->course_code }}</span></div>
                <div class="detail-row"><span class="detail-label">Credits</span><span class="detail-value">{{ $course->credits }}</span></div>
                @if($course->duration_weeks)
                <div class="detail-row"><span class="detail-label">Duration</span><span class="detail-value">{{ $course->duration_weeks }} weeks</span></div>
                @endif
                @if($course->level)
                <div class="detail-row"><span class="detail-label">Level</span><span class="detail-value">{{ ucfirst($course->level) }}</span></div>
                @endif
                @if(isset($enrollment) && $enrollment && $enrollment->grade)
                <div class="detail-row">
                    <span class="detail-label">Your Grade</span>
                    <span class="detail-value" style="color:#48bb78;font-size:1.25rem;">{{ $enrollment->grade }}%</span>
                </div>
                @endif
            </div>
            <div class="detail-section">
                <h3 class="detail-section-title"><i class="fas fa-align-left"></i> Description</h3>
                <div class="description-box">{{ $course->description ?? 'No description provided.' }}</div>
            </div>
        </div>

        {{-- Topics — controller already filtered to published only --}}
        <div class="topics-section">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h3 style="font-size:1.25rem;font-weight:700;color:#2d3748;display:flex;align-items:center;gap:0.5rem;">
                    <i class="fas fa-list" style="color:#667eea;"></i> Course Topics
                    <span style="margin-left:0.75rem;padding:0.25rem 0.75rem;background:#e0e7ff;color:#4f46e5;border-radius:50px;font-size:0.875rem;font-weight:600;">
                        {{ $topics->count() }} Topics
                    </span>
                </h3>
                @if($topics->count() > 0)
                <div class="search-container" style="width:300px;margin-bottom:0;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                </div>
                @endif
            </div>

            @if($topics->isEmpty())
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3>No Published Topics Yet</h3>
                <p>Your instructor hasn't published any topics for this course yet.</p>
            </div>
            @else
            <div class="topics-list" id="topics-list">
                @foreach($topics as $topic)
                <div class="topic-card" data-title="{{ strtolower($topic->title) }}">
                    <div class="topic-header">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div style="width:36px;height:36px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <h4 class="topic-title">{{ $topic->title }}</h4>
                                <div style="display:flex;gap:0.5rem;margin-top:0.25rem;">
                                    @if($topic->video_link)
                                    <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.125rem 0.5rem;background:#fef2f2;color:#dc2626;border-radius:50px;font-size:0.75rem;font-weight:600;border:1px solid #fecaca;">
                                        <i class="fas fa-video"></i> Video
                                    </span>
                                    @endif
                                    @if($topic->attachment)
                                    <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.125rem 0.5rem;background:#eff6ff;color:#3b82f6;border-radius:50px;font-size:0.75rem;font-weight:600;border:1px solid #dbeafe;">
                                        <i class="fas fa-paperclip"></i> Attachment
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            @if(isset($completedTopicIds) && in_array($topic->id, $completedTopicIds))
                            <span class="status-badge status-published" style="padding:0.375rem 1rem;">
                                <i class="fas fa-check-circle"></i> Completed
                            </span>
                            @endif
                            <a href="{{ route('student.topics.show', Crypt::encrypt($topic->id)) }}"
                               class="btn btn-primary" style="padding:0.5rem 1.25rem;">
                                @if(isset($completedTopicIds) && in_array($topic->id, $completedTopicIds))
                                    <i class="fas fa-redo"></i> Review
                                @else
                                    <i class="fas fa-play"></i> Start
                                @endif
                            </a>
                        </div>
                    </div>
                    @if($topic->description)
                    <div class="topic-content">
                        <div class="topic-description">{{ $topic->description }}</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-topics');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            document.querySelectorAll('.topic-card').forEach(card => {
                card.style.display = (!term || (card.dataset.title || '').includes(term)) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush