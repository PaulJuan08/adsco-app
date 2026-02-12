@extends('layouts.admin')

@section('title', 'Course Details - Admin Dashboard')

@push('styles')
<style>
    /* Modern Card Design - Smaller Container */
    .form-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin: 1.5rem auto;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        max-width: 1200px;
        width: 95%;
    }

    .form-container:hover {
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Action Buttons Container - At the Top */
    .top-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .top-action-btn {
        color: white;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .top-action-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        gap: 0.75rem;
    }

    .top-action-btn.delete-btn {
        background: rgba(245, 101, 101, 0.3);
    }

    .top-action-btn.delete-btn:hover {
        background: rgba(245, 101, 101, 0.5);
    }

    .card-body {
        padding: 1.5rem 1.75rem;
    }

    /* Course Avatar */
    .course-avatar-section {
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .course-details-avatar {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 auto 1rem;
        border: 4px solid white;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }

    .course-details-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
    }

    .course-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }

    .course-code {
        color: #4a5568;
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .course-status-container {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.8125rem;
        font-weight: 600;
        margin: 0.25rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .status-published {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }
    
    .status-draft {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
    }
    
    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    }
    
    /* Details Sections - Compact */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .detail-section {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .detail-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px 3px 0 0;
    }
    
    .detail-section:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }
    
    .detail-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
    }
    
    .detail-section-title i {
        color: #667eea;
        font-size: 1.125rem;
        width: 20px;
        text-align: center;
    }
    
    .detail-row {
        display: grid;
        grid-template-columns: 110px 1fr;
        align-items: start;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .detail-row:hover {
        background: #f7fafc;
    }
    
    .detail-label {
        font-size: 0.8125rem;
        color: #4a5568;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-value {
        font-size: 1rem;
        color: #1a202c;
        font-weight: 600;
        line-height: 1.5;
    }
    
    .detail-subvalue {
        font-size: 0.75rem;
        color: #718096;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* Description Box */
    .description-box {
        padding: 1rem 1.25rem;
        background: white;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.9375rem;
        line-height: 1.6;
        color: #2d3748;
    }
    
    /* Topics Section */
    .topics-section {
        margin-top: 1.5rem;
    }
    
    .topic-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }
    
    .topic-card:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
        border-color: #cbd5e0;
    }
    
    .topic-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #edf2f7;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    
    .topic-content {
        padding: 0.875rem 1.25rem;
    }
    
    .topic-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .topic-description {
        color: #718096;
        font-size: 0.8125rem;
        line-height: 1.5;
    }
    
    .action-btn-small {
        padding: 0.375rem 0.75rem;
        color: #718096;
        border: 1px solid #e2e8f0;
        background: white;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s;
        font-size: 0.75rem;
    }
    
    .action-btn-small:hover {
        background: #f56565;
        color: white;
        border-color: #f56565;
    }
    
    /* Search Container */
    .search-container {
        position: relative;
        margin-bottom: 1.25rem;
    }
    
    .search-input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.875rem;
        color: #2d3748;
        background: white;
        transition: all 0.3s;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 0.875rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: #a0aec0;
    }
    
    .empty-state i {
        font-size: 2.5rem;
        color: #cbd5e0;
        margin-bottom: 0.75rem;
    }
    
    .empty-state h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #718096;
        margin-bottom: 0.25rem;
    }
    
    .empty-state p {
        font-size: 0.8125rem;
        color: #a0aec0;
    }
    
    /* Modal - Compact */
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
        border-radius: 16px;
        width: 100%;
        max-width: 550px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
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
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    
    .modal-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #2d3748;
    }
    
    .modal-close {
        background: none;
        border: none;
        color: #718096;
        cursor: pointer;
        padding: 0.375rem;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .modal-close:hover {
        background: #edf2f7;
        color: #2d3748;
    }
    
    .modal-body {
        padding: 1.5rem;
        max-height: calc(80vh - 120px);
        overflow-y: auto;
    }
    
    .modal-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #f8fafc;
    }
    
    .btn {
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8125rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-secondary {
        background: white;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }
    
    .btn-secondary:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
    }
    
    /* Topic Items in Modal */
    .topics-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .topic-item {
        padding: 0.875rem 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }
    
    .topic-item:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
        transform: translateX(4px);
    }
    
    .topic-item.selected {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        border-width: 2px;
    }
    
    .topic-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.375rem;
    }
    
    .topic-item-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.9375rem;
    }
    
    .topic-item-description {
        color: #718096;
        font-size: 0.75rem;
        line-height: 1.5;
    }
    
    .add-btn {
        padding: 0.25rem 0.625rem;
        background: #48bb78;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.6875rem;
        font-weight: 600;
        cursor: pointer;
        opacity: 0;
        transition: all 0.2s;
    }
    
    .topic-item:hover .add-btn {
        opacity: 1;
    }
    
    .add-btn:hover {
        background: #38a169;
        transform: translateY(-1px);
    }
    
    /* Loading Spinner */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Success/Error Messages */
    .message-success {
        margin-top: 1.25rem;
        padding: 0.875rem 1.25rem;
        background: #f0fff4;
        color: #276749;
        border-radius: 10px;
        border-left: 4px solid #48bb78;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .message-error {
        margin-top: 1.25rem;
        padding: 0.875rem 1.25rem;
        background: #fff5f5;
        color: #c53030;
        border-radius: 10px;
        border-left: 4px solid #f56565;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Responsive Design */
    @media (max-width: 1280px) {
        .form-container {
            max-width: 1100px;
            width: 90%;
        }
    }

    @media (max-width: 768px) {
        .form-container {
            width: 95%;
            margin: 1rem auto;
        }

        .card-header {
            padding: 1rem 1.25rem;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .top-actions {
            align-self: stretch;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .top-action-btn {
            flex: 1;
            justify-content: center;
        }

        .card-body {
            padding: 1.25rem;
        }

        .details-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .detail-row {
            grid-template-columns: 1fr;
            gap: 0.25rem;
        }

        .course-details-avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        .course-title {
            font-size: 1.25rem;
        }

        .topic-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .form-container {
            width: 98%;
            margin: 0.75rem auto;
            border-radius: 16px;
        }

        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.125rem;
        }

        .detail-section {
            padding: 1rem 1.25rem;
        }

        .detail-section-title {
            font-size: 0.9375rem;
        }

        .course-details-avatar {
            width: 70px;
            height: 70px;
            font-size: 1.75rem;
        }

        .modal-container {
            width: 95%;
        }

        .modal-body {
            padding: 1.25rem;
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
                <h2 class="card-title">Course Details</h2>
            </div>
            <div class="top-actions">
                <!-- Edit Button -->
                <a href="{{ route('admin.courses.edit', Crypt::encrypt($course->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                <!-- Delete Button -->
                <form action="{{ route('admin.courses.destroy', urlencode(Crypt::encrypt($course->id))) }}" method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="top-action-btn delete-btn" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                
                <!-- Back Button -->
                <a href="{{ route('admin.courses.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Course Avatar & Basic Info -->
            <div class="course-avatar-section">
                <div class="course-details-avatar">
                    {{ strtoupper(substr($course->course_code, 0, 1)) }}
                </div>
                <h3 class="course-title">{{ $course->title }}</h3>
                <p class="course-code">{{ $course->course_code }}</p>
                
                <div class="course-status-container">
                    <div class="status-badge {{ $course->is_published ? 'status-published' : 'status-draft' }}">
                        <i class="fas {{ $course->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $course->is_published ? 'Published' : 'Draft' }}
                    </div>
                </div>
            </div>
            
            <!-- Detailed Information -->
            <div class="details-grid">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i>
                        Course Information
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Title</div>
                        <div class="detail-value">{{ $course->title }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Code</div>
                        <div class="detail-value">{{ $course->course_code }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Credits</div>
                        <div class="detail-value">{{ $course->credits ?? 3 }} units</div>
                    </div>
                    
                    @if($course->department)
                    <div class="detail-row">
                        <div class="detail-label">Department</div>
                        <div class="detail-value">{{ $course->department }}</div>
                    </div>
                    @endif
                    
                    @if($course->semester)
                    <div class="detail-row">
                        <div class="detail-label">Semester</div>
                        <div class="detail-value">{{ ucfirst($course->semester) }}</div>
                    </div>
                    @endif
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Instructor Information
                    </div>
                    
                    @if($course->teacher)
                    <div class="detail-row">
                        <div class="detail-label">Instructor</div>
                        <div class="detail-value">
                            {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                        </div>
                        <div class="detail-subvalue">
                            <i class="fas fa-envelope"></i> {{ $course->teacher->email }}
                        </div>
                    </div>
                    
                    @if($course->teacher->employee_id)
                    <div class="detail-row">
                        <div class="detail-label">Employee ID</div>
                        <div class="detail-value">{{ $course->teacher->employee_id }}</div>
                    </div>
                    @endif
                    @else
                    <div class="detail-row">
                        <div class="detail-label">Instructor</div>
                        <div class="detail-value" style="color: #ed8936;">
                            <i class="fas fa-exclamation-triangle"></i> Not Assigned
                        </div>
                    </div>
                    @endif
                    
                    <div class="detail-row">
                        <div class="detail-label">Course ID</div>
                        <div class="detail-value">#{{ $course->id }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Created</div>
                        <div class="detail-value">
                            {{ $course->created_at->format('M d, Y') }}
                            <div class="detail-subvalue">
                                <i class="fas fa-clock"></i> {{ $course->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Updated</div>
                        <div class="detail-value">
                            {{ $course->updated_at->format('M d, Y') }}
                            <div class="detail-subvalue">
                                <i class="fas fa-clock"></i> {{ $course->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Course Description -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-align-left"></i>
                    Course Description
                </div>
                
                <div class="description-box">
                    {{ $course->description ?: 'No description provided for this course.' }}
                </div>
            </div>

            <!-- Publish Button for Draft Courses -->
            @if(!$course->is_published)
            <div style="margin-top: 1.5rem; text-align: center;">
                <form action="{{ route('admin.courses.publish', Crypt::encrypt($course->id)) }}" method="POST" id="publishForm" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="top-action-btn" id="publishButton" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); border: none; padding: 0.75rem 2rem;">
                        <i class="fas fa-upload"></i> Publish Course
                    </button>
                </form>
            </div>
            @endif
            
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="message-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="message-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Topics Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-list card-icon"></i>
                <h2 class="card-title">Course Topics</h2>
            </div>
            <div class="top-actions">
                <button onclick="openAddTopicModal()" class="top-action-btn" style="background: rgba(255, 255, 255, 0.15); border: none;">
                    <i class="fas fa-plus"></i> Add Topics
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Search Bar -->
            @if($course->topics && $course->topics->count() > 0)
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search topics..." id="topicSearch">
            </div>
            @endif
            
            <!-- Topics List -->
            <div class="topics-section" id="topicsList">
                @if($course->topics && $course->topics->count() > 0)
                    @foreach($course->topics as $topic)
                    <div class="topic-card" id="topic-{{ $topic->id }}">
                        <div class="topic-header">
                            <div>
                                <div class="topic-title">{{ $topic->title }}</div>
                                <div style="font-size: 0.6875rem; color: #a0aec0;">
                                    <i class="fas fa-clock"></i>
                                    Added {{ $topic->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="action-dropdown">
                                <button class="action-btn-small" onclick="removeTopic({{ $topic->id }}, '{{ addslashes($topic->title) }}')">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                        <div class="topic-content">
                            <div class="topic-description">
                                {{ $topic->description ?? 'No description provided.' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Topics Yet</h3>
                    <p>Start by adding topics to this course</p>
                    <button onclick="openAddTopicModal()" style="margin-top: 1rem; padding: 0.5rem 1.25rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-plus" style="margin-right: 0.375rem;"></i>Add First Topic
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Topic Modal -->
    <div class="modal-overlay" id="addTopicModal">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fas fa-plus-circle" style="margin-right: 0.5rem; color: #667eea;"></i>
                    Add Topics to Course
                </div>
                <button class="modal-close" onclick="closeAddTopicModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="search-container" style="margin-bottom: 1rem;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search available topics..." id="modalTopicSearch" onkeyup="searchTopics()">
                </div>
                
                <div id="availableTopicsList" class="topics-list">
                    <div class="loading" style="text-align: center; padding: 2rem;">
                        <div class="spinner" style="width: 32px; height: 32px; border: 3px solid #e2e8f0; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 0.75rem;"></div>
                        <div style="color: #718096; font-size: 0.875rem;">Loading topics...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAddTopicModal()">Cancel</button>
                <button class="btn btn-primary" onclick="addSelectedTopics()">
                    <i class="fas fa-check" style="margin-right: 0.375rem;"></i>
                    Add Selected
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle publish button click
        const publishButton = document.getElementById('publishButton');
        if (publishButton) {
            publishButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Publish Course?',
                    text: 'This course will be visible to enrolled students.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#48bb78',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Publish',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        publishButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';
                        publishButton.disabled = true;
                        document.getElementById('publishForm').submit();
                    }
                });
            });
        }
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Course?',
                    text: 'This action cannot be undone. All course data will be permanently removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        deleteButton.disabled = true;
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        }
        
        // Show notifications from session
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

    // Topics management
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
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.message || data.error);
            
            allAvailableTopics = Array.isArray(data) ? data : [];
            renderAvailableTopics(allAvailableTopics);
        })
        .catch(error => {
            console.error('Error loading topics:', error);
            
            document.getElementById('availableTopicsList').innerHTML = `
                <div class="no-topics" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #f56565; margin-bottom: 0.75rem;"></i>
                    <div style="color: #c53030; font-weight: 600;">Error Loading Topics</div>
                    <div style="font-size: 0.8125rem; color: #718096; margin-top: 0.5rem;">
                        ${error.message}
                    </div>
                    <button onclick="loadAvailableTopics()" 
                            style="margin-top: 1rem; padding: 0.5rem 1rem; background: #667eea; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                </div>
            `;
        });
    }

    function renderAvailableTopics(topics) {
        const container = document.getElementById('availableTopicsList');
        
        if (!Array.isArray(topics) || topics.length === 0) {
            container.innerHTML = `
                <div class="empty-state" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-folder-open" style="font-size: 2.5rem; color: #cbd5e0;"></i>
                    <h3 style="font-size: 1rem; color: #718096; margin-top: 0.75rem;">No Topics Available</h3>
                    <p style="font-size: 0.8125rem; color: #a0aec0; margin-top: 0.25rem;">
                        All topics are already added to this course.
                    </p>
                    <a href="{{ route('admin.topics.create') }}" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.25rem; background: #667eea; color: white; border-radius: 8px; text-decoration: none; font-size: 0.8125rem;">
                        <i class="fas fa-plus" style="margin-right: 0.375rem;"></i>Create New Topic
                    </a>
                </div>
            `;
            return;
        }

        container.innerHTML = topics.map(topic => {
            const description = topic.description || topic.content || 'No description provided.';
            const truncatedDesc = description.length > 120 ? 
                description.substring(0, 120) + '...' : 
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
        
        const topicItem = document.querySelector(`.topic-item[onclick*="toggleTopic(${topicId})"]`);
        if (topicItem) {
            topicItem.classList.toggle('selected');
        }
    }

    function addSingleTopic(topicId) {
        if (currentCourseTopics.includes(topicId)) {
            showNotification('This topic is already added to the course.', 'warning');
            return;
        }

        fetch(`/admin/courses/${encryptedCourseId}/add-topic`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ topic_id: topicId })
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
            showNotification('Please select at least one topic to add.', 'warning');
            return;
        }

        fetch(`/admin/courses/${encryptedCourseId}/add-topics`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ topic_ids: selectedTopics })
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
                    data.topics.forEach(topic => addTopicToDisplay(topic));
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
                    <div style="font-size: 0.6875rem; color: #a0aec0;">
                        <i class="fas fa-clock"></i>
                        Just added
                    </div>
                </div>
                <div class="action-dropdown">
                    <button class="action-btn-small" onclick="removeTopic(${topic.id}, '${(topic.title || 'Untitled Topic').replace(/'/g, "\\'")}')">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
            <div class="topic-content">
                <div class="topic-description">
                    ${topic.description || topic.content || 'No description provided.'}
                </div>
            </div>
        `;
        
        topicsList.appendChild(topicElement);
    }

    function removeTopic(topicId, topicTitle) {
        Swal.fire({
            title: 'Remove Topic?',
            text: `Are you sure you want to remove "${topicTitle}" from this course?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/courses/${encryptedCourseId}/remove-topic`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ topic_id: topicId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentCourseTopics = currentCourseTopics.filter(id => id !== topicId);
                        
                        const topicElement = document.getElementById(`topic-${topicId}`);
                        if (topicElement) topicElement.remove();
                        
                        if (data.topic) {
                            allAvailableTopics.push(data.topic);
                            renderAvailableTopics(allAvailableTopics);
                        }
                        
                        const topicsList = document.getElementById('topicsList');
                        if (topicsList.children.length === 0) {
                            topicsList.innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <h3>No Topics Yet</h3>
                                    <p>Start by adding topics to this course</p>
                                    <button onclick="openAddTopicModal()" style="margin-top: 1rem; padding: 0.5rem 1.25rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                                        <i class="fas fa-plus" style="margin-right: 0.375rem;"></i>Add First Topic
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

    // Search functionality for main topics list
    const topicSearch = document.getElementById('topicSearch');
    if (topicSearch) {
        topicSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const topicCards = document.querySelectorAll('.topic-card');
            
            topicCards.forEach(card => {
                const title = card.querySelector('.topic-title').textContent.toLowerCase();
                const description = card.querySelector('.topic-description').textContent.toLowerCase();
                
                card.style.display = title.includes(searchTerm) || description.includes(searchTerm) ? 'block' : 'none';
            });
        });
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

    function showNotification(message, type = 'info') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            icon: type,
            title: message,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
</script>
@endpush