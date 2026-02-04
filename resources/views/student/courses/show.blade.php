@extends('layouts.student')

@section('title', $course->title . ' - Student Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>{{ $course->title }}</h1>
        <p>{{ $course->course_code }} • {{ $course->credits }} Credits</p>
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

<!-- Topics Section -->
<div class="card topics-section">
    <div class="card-header">
        <div class="header-left">
            <h2 class="card-title">Course Topics</h2>
            <div class="topics-count">{{ $totalTopics }} Topics</div>
        </div>
        @if($course->teacher)
        <div class="instructor-badge">
            <div class="instructor-avatar-small">
                {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
            </div>
            <div class="instructor-info">
                <div class="instructor-name">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                <div class="instructor-role">Instructor</div>
            </div>
        </div>
        @endif
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
    
    @if($topics->isEmpty())
    <div class="empty-state">
        <i class="fas fa-book-open"></i>
        <h3>No Topics Yet</h3>
        <p>Topics will be added by your instructor soon.</p>
    </div>
    @else
    <div class="topics-list">
        @foreach($topics as $topic)
        <div class="topic-item" data-topic-id="{{ $topic->id }}">
            <div class="topic-header">
                <div class="topic-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="topic-info">
                    <h3 class="topic-title">{{ $topic->title }}</h3>
                    <div class="topic-tags">
                        @if($topic->video_link)
                        <span class="topic-tag video-tag">
                            <i class="fas fa-video"></i> Video Available
                        </span>
                        @endif
                        @if($topic->attachment)
                        <span class="topic-tag attachment-tag">
                            <i class="fas fa-paperclip"></i> Attachment
                        </span>
                        @endif
                    </div>
                </div>
                <div class="topic-status">
                    @if(in_array($topic->id, $completedTopicIds))
                    <div class="status-actions">
                        <span class="status-badge completed">
                            <i class="fas fa-check-circle"></i> Completed
                        </span>
                        <a href="{{ route('student.topics.show', Crypt::encrypt($topic->id)) }}" 
                        class="btn btn-outline-success btn-sm ms-2 review-btn">
                            <i class="fas fa-redo"></i> Review
                        </a>
                    </div>
                    @else
                    <a href="{{ route('student.topics.show', Crypt::encrypt($topic->id)) }}" 
                    class="btn btn-primary start-topic-btn" 
                    data-topic-id="{{ Crypt::encrypt($topic->id) }}">
                        <i class="fas fa-play"></i> Start
                    </a>
                    @endif
                </div>
            </div>
            
            @if($topic->description)
            <div class="topic-description">
                {{ $topic->description }}
            </div>
            @endif
            
            <div class="topic-footer">
                <div class="topic-meta">
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Added {{ $topic->created_at->diffForHumans() }}</span>
                    </div>
                    @if(in_array($topic->id, $completedTopicIds))
                        <div class="meta-item">
                            <i class="fas fa-calendar-check text-success"></i>
                            <span>Completed</span>
                        </div>
                    @endif

                </div>
                <div class="topic-actions">
                    @if($topic->attachment)
                    <a href="{{ $topic->attachment }}" target="_blank" class="btn btn-sm btn-outline">
                        <i class="fas fa-download"></i> Download
                    </a>
                    @endif
                    <!-- Add Review button for completed topics -->
                    @if(in_array($topic->id, $completedTopicIds))
                    <a href="{{ route('student.topics.show', Crypt::encrypt($topic->id)) }}" 
                    class="btn btn-sm btn-outline-success review-btn">
                        <i class="fas fa-redo"></i> Review Topic
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Topic View Modal -->
<div class="modal" id="topicModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTopicTitle"></h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="modalTopicContent">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading topic content...</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary modal-close">Close</button>
            <button class="btn btn-primary" id="markAsCompleteBtn">
                <i class="fas fa-check-circle"></i> Mark as Complete
            </button>
        </div>
    </div>
</div>

<style>
    /* Base Variables */
    :root {
        --primary: #4361ee;
        --primary-light: #e0e7ff;
        --primary-dark: #3a56d4;
        --secondary: #6c757d;
        --success: #10b981;
        --success-light: #d1fae5;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --light: #f8f9fa;
        --dark: #1f2937;
        --dark-light: #374151;
        --border: #e5e7eb;
        --shadow: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.1);
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
    }

    .greeting h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
        line-height: 1.2;
    }

    .greeting p {
        color: var(--secondary);
        font-size: 0.875rem;
        margin: 0;
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

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-student {
        background: var(--success-light);
        color: #065f46;
    }

    /* Quick Actions */
    .quick-actions-row {
        margin-bottom: 1.5rem;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        text-decoration: none;
        color: var(--dark);
        transition: all 0.2s ease;
        border: 1px solid var(--border);
    }

    .action-btn:hover {
        background: white;
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
    }

    .action-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .action-text h4 {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: var(--dark);
    }

    .action-text p {
        font-size: 0.75rem;
        color: var(--secondary);
        margin: 0;
    }

    /* Progress Card */
    .progress-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .progress-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .progress-percentage {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: #f3f4f6;
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary) 0%, #7c3aed 100%);
        border-radius: 5px;
        transition: width 0.3s ease;
    }

    .progress-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .progress-stats .stat {
        text-align: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .progress-stats .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
        line-height: 1;
    }

    .progress-stats .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    /* Details Grid */
    .details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1024px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .card:last-child {
        margin-bottom: 0;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Course Overview */
    .course-description {
        color: var(--secondary);
        line-height: 1.6;
        margin-bottom: 2rem;
        font-size: 0.9375rem;
    }

    .course-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .detail-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-light);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.125rem;
        flex-shrink: 0;
    }

    .detail-label {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-weight: 600;
        color: var(--dark);
        font-size: 0.9375rem;
    }

    /* Course Stats */
    .stats-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .stat-card:hover {
        background: var(--primary-light);
        transform: translateX(4px);
    }

    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-card .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.125rem;
        line-height: 1;
    }

    .stat-card .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    /* Topics Section */
    .topics-section .card-header {
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .topics-count {
        padding: 0.375rem 0.875rem;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .instructor-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .instructor-avatar-small {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .instructor-info {
        flex: 1;
        min-width: 0;
    }

    .instructor-name {
        font-weight: 600;
        color: var(--dark);
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .instructor-role {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    /* Topics List */
    .topics-list {
        padding: 0.5rem;
    }

    .topic-item {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
        background: white;
    }

    .topic-item:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
        transform: translateY(-2px);
    }

    .topic-item:last-child {
        margin-bottom: 0;
    }

    .topic-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .topic-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .topic-info {
        flex: 1;
        min-width: 0;
    }

    .topic-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .topic-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .topic-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .video-tag {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .attachment-tag {
        background: #eff6ff;
        color: var(--primary);
        border: 1px solid #dbeafe;
    }

    .topic-status {
        flex-shrink: 0;
        margin-left: auto;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge.completed {
        background: var(--success-light);
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .topic-description {
        color: var(--secondary);
        line-height: 1.6;
        margin-bottom: 1rem;
        font-size: 0.9375rem;
        padding-left: 4rem;
    }

    .topic-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left: 4rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }

    .topic-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .topic-actions {
        display: flex;
        gap: 0.5rem;
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
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border-color: var(--border);
    }

    .btn-outline:hover {
        background: var(--primary-light);
        border-color: var(--primary);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: var(--secondary);
        border-color: var(--border);
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state i {
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
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.5;
    }

    /* Alerts */
    .alert {
        margin: 1rem 1.5rem;
        padding: 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert i {
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    .alert-success {
        background: var(--success-light);
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
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
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 800px;
        max-height: 90vh;
        overflow: hidden;
        animation: modalSlide 0.3s ease;
        box-shadow: var(--shadow-lg);
    }

    @keyframes modalSlide {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        background: #f9fafb;
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
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #e5e7eb;
        color: var(--dark);
    }

    .modal-body {
        padding: 1.5rem;
        max-height: 60vh;
        overflow-y: auto;
    }

    .modal-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #f9fafb;
    }

    /* Loading State */
    .loading {
        text-align: center;
        padding: 3rem 1rem;
    }

    .spinner {
        width: 48px;
        height: 48px;
        border: 3px solid var(--primary-light);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.5rem;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading p {
        color: var(--secondary);
        font-size: 0.9375rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .top-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem;
        }
        
        .user-info {
            width: 100%;
            justify-content: flex-end;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
        
        .progress-stats {
            grid-template-columns: 1fr;
        }
        
        .topic-header {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .topic-status {
            width: 100%;
            margin-left: 0;
        }
        
        .status-badge, .btn {
            width: 100%;
            justify-content: center;
        }
        
        .topic-description,
        .topic-footer {
            padding-left: 0;
        }
        
        .modal-footer {
            flex-direction: column;
        }
        
        .modal-footer .btn {
            width: 100%;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    // Get encrypted course ID
    const encryptedCourseId = '{{ $encryptedId }}';

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Topic Modal functionality
    const topicModal = document.getElementById('topicModal');
    const modalTopicTitle = document.getElementById('modalTopicTitle');
    const modalTopicContent = document.getElementById('modalTopicContent');
    const markAsCompleteBtn = document.getElementById('markAsCompleteBtn');
    let currentTopicId = null;

    // Start topic button handlers
    document.querySelectorAll('.start-topic-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const encryptedTopicId = this.dataset.topicId;
            window.location.href = `/student/topics/${encryptedTopicId}`;
        });
    });

    // Review button handlers
    document.querySelectorAll('.review-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            window.location.href = href;
        });
    });

    // Close modal buttons
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            closeModal();
        });
    });

    // Close modal on outside click
    if (topicModal) {
        topicModal.addEventListener('click', function(e) {
            if (e.target === topicModal) {
                closeModal();
            }
        });
    }

    // Close modal function
    function closeModal() {
        if (topicModal) {
            topicModal.classList.remove('active');
        }
        document.body.style.overflow = 'auto';
        currentTopicId = null;
    }

    // Load topic content
    function loadTopicContent(encryptedTopicId) {
        fetch(`/student/courses/${encodeURIComponent(encryptedCourseId)}/topics/${encodeURIComponent(encryptedTopicId)}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTopicContent(data.topic);
            } else {
                showError('Failed to load topic content: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading topic:', error);
            showError('Unable to load topic content. Please try again later.');
        });
    }

    // Show error in modal
    function showError(message) {
        modalTopicContent.innerHTML = `
            <div class="error-state">
                <i class="fas fa-exclamation-circle"></i>
                <h4>Error Loading Topic</h4>
                <p>${message}</p>
                <button onclick="loadTopicContent('${currentTopicId}')" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Retry
                </button>
            </div>
        `;
    }

    // Render topic content
    function renderTopicContent(topic) {
        let contentHtml = '';
        
        if (topic.video_link) {
            contentHtml += `
                <div class="video-section">
                    <h4><i class="fas fa-video"></i> Video Content</h4>
                    <div class="video-container">
                        ${getVideoEmbed(topic.video_link)}
                    </div>
                </div>
            `;
        }
        
        if (topic.description || topic.content) {
            const content = topic.description || topic.content;
            contentHtml += `
                <div class="content-section">
                    <h4><i class="fas fa-file-alt"></i> Content</h4>
                    <div class="content-text">
                        ${content}
                    </div>
                </div>
            `;
        }
        
        if (topic.attachment) {
            contentHtml += `
                <div class="attachment-section">
                    <h4><i class="fas fa-paperclip"></i> Attachment</h4>
                    <div class="attachment-link">
                        <a href="${topic.attachment}" target="_blank" class="btn btn-outline">
                            <i class="fas fa-external-link-alt"></i> Open Attachment
                        </a>
                    </div>
                </div>
            `;
        }
        
        if (!contentHtml) {
            contentHtml = `
                <div class="no-content">
                    <i class="fas fa-info-circle"></i>
                    <p>No content available for this topic.</p>
                </div>
            `;
        }
        
        modalTopicContent.innerHTML = contentHtml;
    }

    // Get video embed code
    function getVideoEmbed(videoLink) {
        if (videoLink.includes('youtube.com/watch?v=')) {
            const videoId = videoLink.split('v=')[1].split('&')[0];
            return `<iframe width="100%" height="400" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
        } else if (videoLink.includes('youtu.be/')) {
            const videoId = videoLink.split('youtu.be/')[1].split('?')[0];
            return `<iframe width="100%" height="400" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`;
        } else if (videoLink.includes('vimeo.com/')) {
            const videoId = videoLink.split('vimeo.com/')[1].split('?')[0];
            return `<iframe width="100%" height="400" src="https://player.vimeo.com/video/${videoId}" frameborder="0" allowfullscreen></iframe>`;
        } else if (videoLink.match(/\.(mp4|webm|ogg)$/i)) {
            return `<video width="100%" height="400" controls>
                <source src="${videoLink}" type="video/mp4">
                Your browser does not support the video tag.
            </video>`;
        } else {
            return `<a href="${videoLink}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt"></i> Watch Video
            </a>`;
        }
    }

    // Mark topic as complete
    if (markAsCompleteBtn) {
        markAsCompleteBtn.addEventListener('click', function() {
            if (!currentTopicId) return;
            
            const btn = this;
            const originalHTML = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Marking...';
            
            fetch(`/student/courses/${encryptedCourseId}/topics/${currentTopicId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const topicItem = document.querySelector(`[data-topic-id="${currentTopicId}"]`);
                    if (topicItem) {
                        const statusDiv = topicItem.querySelector('.topic-status');
                        if (statusDiv) {
                            statusDiv.innerHTML = `
                                <span class="status-badge completed">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                            `;
                        }
                    }
                    
                    showNotification('Topic marked as completed!', 'success');
                    closeModal();
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message || 'Failed to mark topic as complete.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        });
    }

    // Show notification
    function showNotification(message, type) {
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification-toast notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        `;
        
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Add notification animations
    const notificationStyle = document.createElement('style');
    notificationStyle.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    
    if (!document.querySelector('#notification-styles')) {
        notificationStyle.id = 'notification-styles';
        document.head.appendChild(notificationStyle);
    }
    
    // Handle escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && topicModal && topicModal.classList.contains('active')) {
            closeModal();
        }
    });

    // Initialize page functionality
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Course page loaded');
        
        const downloadButtons = document.querySelectorAll('.btn-sm.btn-outline');
        downloadButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                console.log('Download clicked:', this.href);
            });
        });
    });
</script>
@endpush