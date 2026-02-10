@extends('layouts.admin')

@section('title', 'Course Details - Admin Dashboard')

@push('styles')
<style>
    /* Form Container */
    .form-container {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.125rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }

    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer-modern {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-published {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    .status-draft {
        background: var(--warning-light);
        color: var(--warning-dark);
    }
    
    .detail-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-value {
        font-size: 1.125rem;
        color: var(--gray-900);
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .detail-subvalue {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin-top: -0.75rem;
        margin-bottom: 1rem;
    }
    
    .detail-section {
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
    }
    
    .detail-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .detail-section-title i {
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    /* Topics specific styles */
    .topics-section {
        margin-top: 2rem;
    }
    
    .topic-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }
    
    .topic-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    
    .topic-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        background: var(--gray-50);
    }
    
    .topic-content {
        padding: 1rem 1.5rem;
    }
    
    .topic-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
    }
    
    .topic-description {
        color: var(--gray-600);
        font-size: 0.875rem;
        line-height: 1.6;
    }
    
    .action-dropdown {
        position: relative;
    }
    
    .action-btn-small {
        padding: 0.5rem;
        color: var(--gray-600);
        border: none;
        background: none;
        cursor: pointer;
        border-radius: var(--radius-sm);
        transition: all 0.2s;
    }
    
    .action-btn-small:hover {
        background: var(--gray-100);
        color: var(--danger);
    }
    
    .search-container {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        color: var(--gray-900);
        background: var(--gray-50);
        transition: all 0.2s;
    }
    
    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        background: white;
    }
    
    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--gray-500);
    }
    
    .empty-state i {
        font-size: 3rem;
        color: var(--gray-300);
        margin-bottom: 1rem;
    }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal-container {
        background: white;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 600px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        animation: modalSlideIn 0.3s ease;
    }
    
    @keyframes modalSlideIn {
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
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--gray-50);
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }
    
    .modal-close {
        background: none;
        border: none;
        color: var(--gray-600);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        transition: all 0.2s;
    }
    
    .modal-close:hover {
        background: var(--gray-100);
    }
    
    .modal-body {
        padding: 1.5rem;
        max-height: calc(80vh - 120px);
        overflow-y: auto;
    }
    
    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: var(--gray-50);
    }
    
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
    }
    
    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }
    
    .btn-secondary:hover {
        background: var(--gray-200);
    }
    
    /* Topic List in Modal */
    .topics-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .topic-item {
        padding: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        background: var(--gray-50);
    }
    
    .topic-item:hover {
        border-color: var(--primary);
        background: var(--primary-light);
        transform: translateX(4px);
    }
    
    .topic-item.selected {
        border-color: var(--primary);
        background: var(--primary-light);
        border-width: 2px;
    }
    
    .topic-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }
    
    .topic-item-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 1rem;
    }
    
    .topic-item-description {
        color: var(--gray-600);
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    .add-btn {
        padding: 0.25rem 0.75rem;
        background: var(--success);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        opacity: 0;
        transition: all 0.2s;
    }
    
    .topic-item:hover .add-btn,
    .topic-item.selected .add-btn {
        opacity: 1;
    }
    
    .add-btn:hover {
        background: var(--success-dark);
    }
    
    .no-topics {
        text-align: center;
        padding: 2rem;
        color: var(--gray-500);
    }
    
    .no-topics i {
        font-size: 2rem;
        color: var(--gray-300);
        margin-bottom: 0.75rem;
    }
    
    /* Loading State */
    .loading {
        text-align: center;
        padding: 2rem;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--gray-200);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Notification Styles */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        z-index: 1001;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .notification.success {
        background: var(--success);
        color: white;
    }
    
    .notification.error {
        background: var(--danger);
        color: white;
    }
    
    .notification i {
        font-size: 1.25rem;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Action buttons grid */
    .action-buttons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-edit {
        background: var(--primary-light);
        color: var(--primary-dark);
    }
    
    .btn-edit:hover {
        background: var(--primary);
        color: white;
    }
    
    .btn-delete {
        background: var(--danger-light);
        color: var(--danger-dark);
    }
    
    .btn-delete:hover {
        background: var(--danger);
        color: white;
    }
    
    .btn-back {
        background: var(--gray-100);
        color: var(--gray-700);
    }
    
    .btn-back:hover {
        background: var(--gray-200);
        color: var(--gray-900);
    }
    
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .detail-section {
            padding: 1rem;
        }
        
        .detail-value {
            font-size: 1rem;
        }
        
        .action-buttons-grid {
            grid-template-columns: 1fr;
        }
        
        .topic-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .topic-content {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Course Profile Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-book card-icon"></i>
                <h2 class="card-title">Course Details: {{ $course->title }}</h2>
            </div>
            <a href="{{ route('admin.courses.edit', Crypt::encrypt($course->id)) }}" class="view-all-link">
                Edit Course <i class="fas fa-edit"></i>
            </a>
        </div>
        
        <div class="card-body">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700; margin: 0 auto 1.5rem;">
                    {{ strtoupper(substr($course->course_code, 0, 1)) }}
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.5rem;">
                    {{ $course->title }}
                </h3>
                <p style="color: var(--gray-600); margin-bottom: 1rem;">{{ $course->course_code }}</p>
                
                <div class="status-badge {{ $course->is_published ? 'status-published' : 'status-draft' }}">
                    <i class="fas {{ $course->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $course->is_published ? 'Course Published' : 'Draft Mode' }}
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i>
                        Course Information
                    </div>
                    
                    <div>
                        <div class="detail-label">Course Title</div>
                        <div class="detail-value">{{ $course->title }}</div>
                        
                        <div class="detail-label">Course Code</div>
                        <div class="detail-value">{{ $course->course_code }}</div>
                        
                        <div class="detail-label">Credits</div>
                        <div class="detail-value">{{ $course->credits ?? 3 }} units</div>
                        
                        @if($course->department)
                        <div class="detail-label">Department</div>
                        <div class="detail-value">{{ $course->department }}</div>
                        @endif
                        
                        @if($course->semester)
                        <div class="detail-label">Semester</div>
                        <div class="detail-value">{{ ucfirst($course->semester) }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Instructor Information
                    </div>
                    
                    <div>
                        @if($course->teacher)
                        <div class="detail-label">Instructor</div>
                        <div class="detail-value">
                            {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                        </div>
                        <div class="detail-subvalue">
                            <i class="fas fa-envelope"></i> {{ $course->teacher->email }}
                        </div>
                        
                        @if($course->teacher->employee_id)
                        <div class="detail-label">Employee ID</div>
                        <div class="detail-value">{{ $course->teacher->employee_id }}</div>
                        @endif
                        @else
                        <div class="detail-label">Instructor</div>
                        <div class="detail-value" style="color: var(--warning);">
                            <i class="fas fa-exclamation-triangle"></i> No Instructor Assigned
                        </div>
                        @endif
                        
                        <div class="detail-label">Course ID</div>
                        <div class="detail-value">#{{ $course->id }}</div>
                        
                        <div class="detail-label">Course Created</div>
                        <div class="detail-value">{{ $course->created_at->format('F d, Y') }}</div>
                        <div class="detail-subvalue">
                            <i class="fas fa-clock"></i> {{ $course->created_at->diffForHumans() }}
                        </div>
                        
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">{{ $course->updated_at->format('F d, Y') }}</div>
                        <div class="detail-subvalue">
                            <i class="fas fa-clock"></i> {{ $course->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-align-left"></i>
                    Course Description
                </div>
                
                <div style="padding: 1rem; background: white; border-radius: var(--radius-sm); border: 1px solid var(--gray-200);">
                    {{ $course->description ?: 'No description provided for this course.' }}
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div style="margin-top: 1.5rem; padding: 1rem; background: var(--success-light); color: var(--success-dark); border-radius: var(--radius-sm); border-left: 4px solid var(--success);">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div style="margin-top: 1.5rem; padding: 1rem; background: var(--danger-light); color: var(--danger-dark); border-radius: var(--radius-sm); border-left: 4px solid var(--danger);">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
        
        <div class="card-footer-modern">
            <div class="action-buttons-grid">
                <a href="{{ route('admin.courses.edit', Crypt::encrypt($course->id)) }}" class="action-btn btn-edit">
                    <i class="fas fa-edit"></i>
                    Edit Course
                </a>
                
                @if(!$course->is_published)
                <form action="{{ route('admin.courses.publish', Crypt::encrypt($course->id)) }}" method="POST" id="publishForm">
                    @csrf
                    <button type="submit" class="action-btn btn-edit" id="publishButton">
                        <i class="fas fa-upload"></i>
                        Publish Course
                    </button>
                </form>
                @endif
                
                <form action="{{ route('admin.courses.destroy', urlencode(Crypt::encrypt($course->id))) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-delete" id="deleteButton">
                        <i class="fas fa-trash"></i>
                        Delete Course
                    </button>
                </form>
                
                <a href="{{ route('admin.courses.index') }}" class="action-btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Topics Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-list card-icon"></i>
                <h2 class="card-title">Course Topics</h2>
            </div>
            <button onclick="openAddTopicModal()" class="view-all-link">
                <i class="fas fa-plus"></i> Add Topics
            </button>
        </div>
        
        <div class="card-body">
            <!-- Search Bar -->
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search topics..." id="topicSearch">
            </div>
            
            <!-- Topics List -->
            <div class="topics-section" id="topicsList">
                @if($course->topics && $course->topics->count() > 0)
                    @foreach($course->topics as $topic)
                    <div class="topic-card" id="topic-{{ $topic->id }}">
                        <div class="topic-header">
                            <div>
                                <div class="topic-title">{{ $topic->title }}</div>
                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>
                                    Added {{ $topic->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="action-dropdown">
                                <button class="action-btn-small" onclick="removeTopic({{ $topic->id }}, '{{ addslashes($topic->title) }}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="topic-content">
                            <div class="topic-description">
                                {{ $topic->description ?? 'No description provided for this topic.' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <div style="font-size: 1rem; font-weight: 500; color: var(--gray-600); margin-bottom: 0.5rem;">No Topics Yet</div>
                    <div style="font-size: 0.875rem; color: var(--gray-500);">Start by adding topics to this course</div>
                    <button onclick="openAddTopicModal()" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.5rem; background: var(--primary); color: white; border-radius: var(--radius); border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>Add First Topic
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-bolt card-icon"></i>
                <h2 class="card-title">Quick Actions</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <a href="{{ route('admin.courses.edit', Crypt::encrypt($course->id)) }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); border: 1px solid var(--primary); text-decoration: none; color: var(--primary-dark); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Edit Course</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Update course information</div>
                    </div>
                </a>
                
                <a href="{{ route('admin.courses.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--gray-100); border-radius: var(--radius-sm); border: 1px solid var(--gray-300); text-decoration: none; color: var(--gray-700); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--gray-300); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: var(--gray-700);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">All Courses</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">View all system courses</div>
                    </div>
                </a>
                
                <a href="#" onclick="openAddTopicModal()" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--success-light); border-radius: var(--radius-sm); border: 1px solid var(--success); text-decoration: none; color: var(--success-dark); transition: all 0.2s ease;">
                    <div style="width: 44px; height: 44px; background: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Add Topics</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Add topics to this course</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Add Topic Modal -->
    <div class="modal-overlay" id="addTopicModal">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title">Add Topics to Course</div>
                <button class="modal-close" onclick="closeAddTopicModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search available topics..." id="modalTopicSearch" onkeyup="searchTopics()">
                </div>
                
                <div id="availableTopicsList" class="topics-list">
                    <div class="loading">
                        <div class="spinner"></div>
                        <div>Loading topics...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAddTopicModal()">Cancel</button>
                <button class="btn btn-primary" onclick="addSelectedTopics()">Add Selected Topics</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle publish button click
        const publishButton = document.getElementById('publishButton');
        if (publishButton) {
            publishButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to publish this course?\n\nOnce published, the course will be visible to enrolled students.')) {
                    // Show loading state
                    const originalHTML = publishButton.innerHTML;
                    publishButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Publishing...';
                    publishButton.disabled = true;
                    
                    // Submit the form
                    document.getElementById('publishForm').submit();
                }
            });
        }
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('WARNING: Are you sure you want to delete this course?\n\nThis action cannot be undone. All course data will be permanently removed.')) {
                    // Show loading state
                    const originalHTML = deleteButton.innerHTML;
                    deleteButton.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Deleting...';
                    deleteButton.disabled = true;
                    
                    // Submit the form
                    document.getElementById('deleteForm').submit();
                }
            });
        }
        
        // Show success message from session
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
        
        @if(session('warning'))
            showNotification('{{ session('warning') }}', 'warning');
        @endif
    });
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--warning)'};
            color: white;
            z-index: 9999;
            box-shadow: var(--shadow-lg);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            max-width: 400px;
        `;
        
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon}" style="font-size: 1.25rem;"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Add CSS animations if not present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }

    // Topics management scripts
    let selectedTopics = [];
    let allAvailableTopics = [];
    let currentCourseTopics = {!! $course->topics->pluck('id')->toJson() !!};

    // Get encrypted ID from PHP
    const encryptedCourseId = '{{ Crypt::encrypt($course->id) }}';

    function openAddTopicModal() {
        document.getElementById('addTopicModal').classList.add('active');
        loadAvailableTopics();
    }

    function closeAddTopicModal() {
        document.getElementById('addTopicModal').classList.remove('active');
        selectedTopics = [];
    }

    function getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag && metaTag.content) {
            return metaTag.content;
        }
        return '{{ csrf_token() }}';
    }

    function loadAvailableTopics() {
        // Use the encrypted ID in the URL
        const routeUrl = `/admin/courses/${encryptedCourseId}/available-topics`;
        
        fetch(routeUrl, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.message || data.error);
            }
            
            if (Array.isArray(data)) {
                allAvailableTopics = data;
                renderAvailableTopics(data);
            } else {
                allAvailableTopics = [];
                renderAvailableTopics([]);
            }
        })
        .catch(error => {
            console.error('Error loading topics:', error);
            
            document.getElementById('availableTopicsList').innerHTML = `
                <div class="no-topics">
                    <i class="fas fa-exclamation-circle"></i>
                    <div style="color: var(--danger); font-weight: 500;">Error Loading Topics</div>
                    <div style="font-size: 0.875rem; color: var(--gray-500); margin-top: 0.5rem;">
                        ${error.message}
                    </div>
                    <button onclick="loadAvailableTopics()" 
                            style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border-radius: var(--radius); border: none; cursor: pointer;">
                        <i class="fas fa-redo"></i> Retry Loading Topics
                    </button>
                </div>
            `;
        });
    }

    function renderAvailableTopics(topics) {
        const container = document.getElementById('availableTopicsList');
        
        if (!Array.isArray(topics) || topics.length === 0) {
            container.innerHTML = `
                <div class="no-topics">
                    <i class="fas fa-folder-open"></i>
                    <div>No available topics to add.</div>
                    <div style="font-size: 0.875rem; color: var(--gray-500); margin-top: 0.5rem;">
                        All topics are already added to this course or no topics exist.
                    </div>
                    <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.5rem;">
                        <a href="{{ route('admin.topics.create') }}" style="color: var(--primary); text-decoration: underline;">
                            Click here to create new topics
                        </a>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = topics.map(topic => {
            let description = 'No description provided.';
            if (topic.description) {
                description = topic.description;
            } else if (topic.content) {
                description = topic.content;
            }
            
            const truncatedDesc = description.length > 150 ? 
                description.substring(0, 150) + '...' : 
                description;
            
            const isSelected = selectedTopics.includes(topic.id);
            
            return `
                <div class="topic-item ${isSelected ? 'selected' : ''}" 
                     onclick="toggleTopic(${topic.id})">
                    <div class="topic-item-header">
                        <div class="topic-item-title">${topic.title || 'Untitled Topic'}</div>
                        <button class="add-btn" onclick="event.stopPropagation(); addSingleTopic(${topic.id})">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="topic-item-description">
                        ${truncatedDesc}
                    </div>
                </div>
            `;
        }).join('');
    }

    function toggleTopic(topicId) {
        if (selectedTopics.includes(topicId)) {
            selectedTopics = selectedTopics.filter(id => id !== topicId);
        } else {
            selectedTopics.push(topicId);
        }
        
        // Update UI
        const item = document.querySelector(`.topic-item .topic-item-title`);
        if (item && item.textContent.includes('Untitled Topic')) {
            // Find the parent topic-item
            const topicItem = item.closest('.topic-item');
            if (topicItem) {
                topicItem.classList.toggle('selected', selectedTopics.includes(topicId));
            }
        }
    }

    function addSingleTopic(topicId) {
        if (currentCourseTopics.includes(topicId)) {
            alert('This topic is already added to the course.');
            return;
        }

        fetch(`/admin/courses/${encryptedCourseId}/add-topic`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                topic_id: topicId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentCourseTopics.push(topicId);
                allAvailableTopics = allAvailableTopics.filter(topic => topic.id !== topicId);
                renderAvailableTopics(allAvailableTopics);
                addTopicToDisplay(data.topic);
                showNotification('Topic added successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to add topic.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function addSelectedTopics() {
        if (selectedTopics.length === 0) {
            alert('Please select at least one topic to add.');
            return;
        }

        fetch(`/admin/courses/${encryptedCourseId}/add-topics`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                topic_ids: selectedTopics
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentCourseTopics = [...currentCourseTopics, ...selectedTopics];
                allAvailableTopics = allAvailableTopics.filter(topic => 
                    !selectedTopics.includes(topic.id)
                );
                renderAvailableTopics(allAvailableTopics);
                
                if (data.topics && Array.isArray(data.topics)) {
                    data.topics.forEach(topic => {
                        addTopicToDisplay(topic);
                    });
                }
                
                selectedTopics = [];
                
                if (allAvailableTopics.length === 0) {
                    closeAddTopicModal();
                }
                
                showNotification('Topics added successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to add topics.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function addTopicToDisplay(topic) {
        const topicsList = document.getElementById('topicsList');
        const emptyState = topicsList.querySelector('.empty-state');
        
        if (emptyState) {
            topicsList.innerHTML = '';
        }
        
        const topicElement = document.createElement('div');
        topicElement.className = 'topic-card';
        topicElement.id = `topic-${topic.id}`;
        topicElement.innerHTML = `
            <div class="topic-header">
                <div>
                    <div class="topic-title">${topic.title || 'Untitled Topic'}</div>
                    <div style="font-size: 0.75rem; color: var(--gray-500);">
                        <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>
                        Just added
                    </div>
                </div>
                <div class="action-dropdown">
                    <button class="action-btn-small" onclick="removeTopic(${topic.id}, '${(topic.title || 'Untitled Topic').replace(/'/g, "\\'")}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="topic-content">
                <div class="topic-description">
                    ${topic.description || topic.content || 'No description provided for this topic.'}
                </div>
            </div>
        `;
        
        topicsList.appendChild(topicElement);
    }

    function removeTopic(topicId, topicTitle) {
        if (!confirm(`Are you sure you want to remove "${topicTitle}" from this course?`)) {
            return;
        }

        fetch(`/admin/courses/${encryptedCourseId}/remove-topic`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                topic_id: topicId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentCourseTopics = currentCourseTopics.filter(id => id !== topicId);
                
                const topicElement = document.getElementById(`topic-${topicId}`);
                if (topicElement) {
                    topicElement.remove();
                }
                
                if (data.topic) {
                    allAvailableTopics.push(data.topic);
                    renderAvailableTopics(allAvailableTopics);
                }
                
                const topicsList = document.getElementById('topicsList');
                if (topicsList.children.length === 0) {
                    topicsList.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <div style="font-size: 1rem; font-weight: 500; color: var(--gray-600); margin-bottom: 0.5rem;">No Topics Yet</div>
                            <div style="font-size: 0.875rem; color: var(--gray-500);">Start by adding topics to this course</div>
                            <button onclick="openAddTopicModal()" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.5rem; background: var(--primary); color: white; border-radius: var(--radius); border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>Add First Topic
                            </button>
                        </div>
                    `;
                }
                
                showNotification('Topic removed successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to remove topic.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function searchTopics() {
        const searchTerm = document.getElementById('modalTopicSearch').value.toLowerCase();
        const filteredTopics = allAvailableTopics.filter(topic => {
            const title = topic.title ? topic.title.toLowerCase() : '';
            const description = topic.description ? topic.description.toLowerCase() : '';
            const content = topic.content ? topic.content.toLowerCase() : '';
            
            return title.includes(searchTerm) || 
                   description.includes(searchTerm) || 
                   content.includes(searchTerm);
        });
        renderAvailableTopics(filteredTopics);
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.getElementById('addTopicModal').classList.contains('active')) {
            closeAddTopicModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('addTopicModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('addTopicModal')) {
            closeAddTopicModal();
        }
    });

    // Search functionality for main topics list
    document.getElementById('topicSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const topicCards = document.querySelectorAll('.topic-card');
        
        topicCards.forEach(card => {
            const title = card.querySelector('.topic-title').textContent.toLowerCase();
            const description = card.querySelector('.topic-description').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endpush