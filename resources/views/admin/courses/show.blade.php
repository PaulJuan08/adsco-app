@extends('layouts.admin')

@section('title', 'Course Details - Admin Dashboard')

@push('styles')
<style>
    .topics-section {
        margin-top: 2rem;
    }
    
    .topic-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    
    .topic-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .topic-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .topic-content {
        padding: 1rem 1.5rem;
    }
    
    .topic-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
    }
    
    .topic-description {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    .action-dropdown {
        position: relative;
    }
    
    .action-btn {
        padding: 0.5rem;
        color: #6b7280;
        border: none;
        background: none;
        cursor: pointer;
        border-radius: 4px;
    }
    
    .action-btn:hover {
        background: #f3f4f6;
    }
    
    .search-container {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #1f2937;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .section-title i {
        color: #4f46e5;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .course-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .info-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.5rem;
    }
    
    .info-label {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .info-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .info-subvalue {
        color: #6b7280;
        font-size: 0.875rem;
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
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .modal-close {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 6px;
    }
    
    .modal-close:hover {
        background: #f3f4f6;
    }
    
    .modal-body {
        padding: 1.5rem;
        max-height: calc(80vh - 120px);
        overflow-y: auto;
    }
    
    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .btn-primary {
        background: #4f46e5;
        color: white;
    }
    
    .btn-primary:hover {
        background: #4338ca;
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    
    /* Topic List in Modal */
    .topics-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .topic-item {
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .topic-item:hover {
        border-color: #4f46e5;
        background: #f8fafc;
    }
    
    .topic-item.selected {
        border-color: #4f46e5;
        background: #f0f9ff;
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
        color: #1f2937;
        font-size: 1rem;
    }
    
    .topic-item-description {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    .add-btn {
        padding: 0.25rem 0.75rem;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 4px;
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
        background: #059669;
    }
    
    .no-topics {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }
    
    .no-topics i {
        font-size: 2rem;
        color: #d1d5db;
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
        border: 3px solid #e5e7eb;
        border-top-color: #4f46e5;
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
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
    }
    
    .notification.success {
        background: #10b981;
        color: white;
    }
    
    .notification.error {
        background: #ef4444;
        color: white;
    }
    
    .notification i {
        margin-right: 0.5rem;
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
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Course Details</h1>
        <p>View detailed information about this course</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Left Column - Course Information -->
    <div>
        <!-- Course Header -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="card-title">{{ $course->title }}</div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    @if($course->is_published)
                    <span style="padding: 0.375rem 1rem; background: #10b98110; color: #10b981; border-radius: 20px; font-size: 0.875rem; font-weight: 500; border: 1px solid #10b98130;">
                        <i class="fas fa-check-circle" style="margin-right: 0.375rem;"></i>Published
                    </span>
                    @else
                    <span style="padding: 0.375rem 1rem; background: #f59e0b10; color: #f59e0b; border-radius: 20px; font-size: 0.875rem; font-weight: 500; border: 1px solid #f59e0b30;">
                        <i class="fas fa-clock" style="margin-right: 0.375rem;"></i>Draft
                    </span>
                    @endif
                    
                    <a href="{{ route('admin.courses.index') }}" 
                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #f3f4f6; color: #4b5563; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                </div>
            </div>
            
            <div style="padding: 1.5rem;">
                @if(session('success'))
                <div style="margin-bottom: 1.5rem; padding: 0.75rem; background: #dcfce7; color: #065f46; border-radius: 6px; font-size: 0.875rem; border-left: 4px solid #10b981;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div style="margin-bottom: 1.5rem; padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 6px; font-size: 0.875rem; border-left: 4px solid #dc2626;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                    {{ session('error') }}
                </div>
                @endif
                
                <!-- Course Information Grid -->
                <div class="course-info-grid">
                    <div class="info-card">
                        <div class="info-label">Course Code</div>
                        <div class="info-value">{{ $course->course_code }}</div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Credits</div>
                        <div class="info-value">{{ $course->credits ?? 3 }}</div>
                        <div class="info-subvalue">Academic Units</div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Course ID</div>
                        <div class="info-value">#{{ $course->id }}</div>
                        <div class="info-subvalue">Unique Identifier</div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Enrolled Students</div>
                        <div class="info-value">
                            @php
                                $studentCount = $course->students ? $course->students->count() : 0;
                            @endphp
                            {{ $studentCount }}
                        </div>
                        <div class="info-subvalue">Active Enrollments</div>
                    </div>
                </div>
                
                <!-- Description Section -->
                <div style="margin-bottom: 2rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Description</div>
                    <div style="padding: 1.5rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; color: #374151; line-height: 1.6;">
                        {{ $course->description ?: 'No description provided for this course.' }}
                    </div>
                </div>
                
                <!-- Instructor Information -->
                @if($course->teacher)
                <div style="margin-bottom: 2rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Instructor</div>
                    <div style="padding: 1.5rem; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.5rem;">
                                {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                                    {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                                </div>
                                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">{{ $course->teacher->email }}</div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    @if($course->teacher->employee_id)
                                    <span style="padding: 0.25rem 0.75rem; background: #e0e7ff; color: #4f46e5; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                        ID: {{ $course->teacher->employee_id }}
                                    </span>
                                    @endif
                                    <span style="padding: 0.25rem 0.75rem; background: #dcfce7; color: #166534; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                        <i class="fas fa-chalkboard-teacher" style="margin-right: 0.25rem;"></i>Instructor
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Topics Section -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="card-title">Topics</div>
                <button onclick="openAddTopicModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #4f46e5; color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;">
                    <i class="fas fa-plus"></i>Add Topics
                </button>
            </div>
            
            <div style="padding: 1.5rem;">
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
                                    <div style="font-size: 0.75rem; color: #9ca3af;">
                                        <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>
                                        Added {{ $topic->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="action-dropdown">
                                    <button class="action-btn" onclick="removeTopic({{ $topic->id }}, '{{ $topic->title }}')">
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
                        <div style="font-size: 1rem; font-weight: 500; color: #6b7280; margin-bottom: 0.5rem;">No Topics Yet</div>
                        <div style="font-size: 0.875rem; color: #9ca3af;">Start by adding topics to this course</div>
                        <button onclick="openAddTopicModal()" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.5rem; background: #4f46e5; color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;">
                            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i>Add First Topic
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Sidebar -->
    <div>
        <!-- Course Actions -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Course Actions</div>
            </div>
            <div style="padding: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="{{ route('admin.courses.edit', Crypt::encrypt($course->id)) }}" 
                       style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; color: #374151; transition: all 0.2s;">
                        <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500; font-size: 0.875rem;">Edit Course</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Modify course information</div>
                        </div>
                        <i class="fas fa-chevron-right" style="margin-left: auto; color: #9ca3af;"></i>
                    </a>

                    <button onclick="if(confirm('Are you sure you want to delete this course? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }"
                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; color: #dc2626; cursor: pointer; width: 100%;">
                        <div style="width: 32px; height: 32px; background: #dc2626; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div style="text-align: left;">
                            <div style="font-weight: 500; font-size: 0.875rem;">Delete Course</div>
                            <div style="font-size: 0.75rem; color: #f87171;">Permanently remove course</div>
                        </div>
                        <i class="fas fa-chevron-right" style="margin-left: auto; color: #f87171;"></i>
                    </button>
                    
                    <form id="delete-form" action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Course Metadata -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Course Metadata</div>
            </div>
            <div style="padding: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Created</div>
                        <div style="font-weight: 500; color: #1f2937;">{{ $course->created_at->format('F d, Y') }}</div>
                        <div style="font-size: 0.75rem; color: #9ca3af;">{{ $course->created_at->format('h:i A') }} • {{ $course->created_at->diffForHumans() }}</div>
                    </div>
                    
                    <div>
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Last Updated</div>
                        <div style="font-weight: 500; color: #1f2937;">{{ $course->updated_at->format('F d, Y') }}</div>
                        <div style="font-size: 0.75rem; color: #9ca3af;">{{ $course->updated_at->format('h:i A') }} • {{ $course->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick Stats</div>
            </div>
            <div style="padding: 1rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="padding: 1rem; background: #f0f9ff; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #0369a1; margin-bottom: 0.25rem;">
                            @php
                                $studentCount = $course->students ? $course->students->count() : 0;
                            @endphp
                            {{ $studentCount }}
                        </div>
                        <div style="font-size: 0.75rem; color: #0c4a6e; font-weight: 500;">Students</div>
                    </div>
                    
                    <div style="padding: 1rem; background: #f0fdf4; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #15803d; margin-bottom: 0.25rem;">
                            {{ $course->topics ? $course->topics->count() : 0 }}
                        </div>
                        <div style="font-size: 0.75rem; color: #166534; font-weight: 500;">Topics</div>
                    </div>
                    
                    <div style="padding: 1rem; background: #fef3c7; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #b45309; margin-bottom: 0.25rem;">
                            {{ $course->credits ?? 3 }}
                        </div>
                        <div style="font-size: 0.75rem; color: #92400e; font-weight: 500;">Credits</div>
                    </div>
                    
                    <div style="padding: 1rem; background: #f3f4f6; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #374151; margin-bottom: 0.25rem;">
                            {{ $course->created_at->diffInDays(now()) }}
                        </div>
                        <div style="font-size: 0.75rem; color: #4b5563; font-weight: 500;">Days Active</div>
                    </div>
                </div>
            </div>
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
                <!-- Topics will be loaded here via AJAX -->
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
                    <div style="color: #dc2626; font-weight: 500;">Error Loading Topics</div>
                    <div style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.5rem;">
                        ${error.message}
                    </div>
                    <button onclick="loadAvailableTopics()" 
                            style="margin-top: 1rem; padding: 0.5rem 1rem; background: #4f46e5; color: white; border-radius: 6px; border: none; cursor: pointer;">
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
                    <div style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.5rem;">
                        All topics are already added to this course or no topics exist.
                    </div>
                    <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">
                        <a href="{{ route('admin.topics.create') }}" style="color: #4f46e5; text-decoration: underline;">
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
                    <div style="font-size: 0.75rem; color: #9ca3af;">
                        <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>
                        Just added
                    </div>
                </div>
                <div class="action-dropdown">
                    <button class="action-btn" onclick="removeTopic(${topic.id}, '${(topic.title || 'Untitled Topic').replace(/'/g, "\\'")}')">
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
                            <div style="font-size: 1rem; font-weight: 500; color: #6b7280; margin-bottom: 0.5rem;">No Topics Yet</div>
                            <div style="font-size: 0.875rem; color: #9ca3af;">Start by adding topics to this course</div>
                            <button onclick="openAddTopicModal()" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.5rem; background: #4f46e5; color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;">
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

    function showNotification(message, type) {
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1001;
            animation: slideIn 0.3s ease;
        `;
        
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="margin-right: 0.5rem;"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
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