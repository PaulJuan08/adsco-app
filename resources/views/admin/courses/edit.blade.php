@extends('layouts.admin')

@section('title', 'Edit Course - Admin Dashboard')

@push('styles')
<style>
    /* Modern Form Container - Matching User Edit */
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

    .view-all-link {
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
        position: relative;
        z-index: 1;
    }

    .view-all-link:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        gap: 0.75rem;
    }

    .card-body {
        padding: 1.5rem 1.75rem;
    }

    /* Two Column Layout for Form and Sidebar */
    .two-column-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .two-column-layout {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* Form Column */
    .form-column {
        min-width: 0;
    }

    /* Sidebar Column */
    .sidebar-column {
        min-width: 0;
    }

    /* Course Preview */
    .course-preview {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .course-preview-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 0.75rem;
        border: 4px solid white;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .course-preview-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.125rem;
    }
    
    .course-preview-code {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.5rem;
    }
    
    .course-preview-status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.6875rem;
        font-weight: 600;
        background: #edf2f7;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }

    .status-published {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }
    
    .status-draft {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }

    /* Modern Form Sections */
    .form-section {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .form-section:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }
    
    .form-section-title {
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
    
    .form-section-title i {
        color: #667eea;
        font-size: 1.125rem;
        width: 20px;
        text-align: center;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
        position: relative;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.375rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8125rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .form-label.required::after {
        content: " *";
        color: #f56565;
    }
    
    .form-control, .form-select, textarea {
        display: block;
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #1a202c;
        background-color: white;
        background-clip: padding-box;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    
    .form-control:focus, .form-select:focus, textarea:focus {
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .form-control.is-invalid, .form-select.is-invalid, textarea.is-invalid {
        border-color: #f56565;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23f56565' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23f56565' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.875rem center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        padding-right: calc(1.5em + 0.875rem);
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #f56565;
        font-weight: 500;
    }
    
    .form-hint {
        font-size: 0.6875rem;
        color: #718096;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Sidebar Card */
    .sidebar-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .sidebar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px 3px 0 0;
    }

    .sidebar-card-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
    }

    .sidebar-card-title i {
        color: #667eea;
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    /* Info Rows */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .info-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
    }

    .info-subvalue {
        font-size: 0.625rem;
        color: #a0aec0;
        margin-top: 0.125rem;
    }

    /* Instructor Section */
    .instructor-section {
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid #edf2f7;
    }

    .instructor-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .instructor-header i {
        color: #667eea;
        font-size: 0.875rem;
    }

    .instructor-header span {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8125rem;
        letter-spacing: 0.5px;
    }

    .instructor-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 12px;
    }

    .instructor-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.125rem;
        flex-shrink: 0;
    }

    .instructor-info {
        flex: 1;
        min-width: 0;
    }

    .instructor-name {
        font-weight: 700;
        color: #2d3748;
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }

    .instructor-details {
        font-size: 0.6875rem;
        color: #718096;
        line-height: 1.4;
    }

    .instructor-details i {
        margin-right: 0.25rem;
    }

    .no-instructor {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        color: #a0aec0;
        font-size: 0.8125rem;
        text-align: center;
    }

    .no-instructor i {
        margin-right: 0.375rem;
    }

    /* Status Notice */
    .status-notice {
        background: linear-gradient(135deg, #f0f9ff 0%, #e6f7ff 100%);
        border: 1px solid #bae6fd;
        color: #075985;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .status-notice i {
        color: #0284c7;
        font-size: 1rem;
        margin-top: 0.125rem;
    }

    .status-notice-content {
        flex: 1;
    }

    .status-notice-title {
        font-weight: 700;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }

    .status-notice-text {
        font-size: 0.8125rem;
        opacity: 0.9;
        line-height: 1.5;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 1.25rem 0 0.5rem;
        border-top: 1px solid #e2e8f0;
        margin-top: 0.5rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 600;
        font-size: 0.8125rem;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1.5px solid transparent;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.3px;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.7s ease;
    }

    .btn:hover::before {
        left: 100%;
    }
    
    .btn-outline {
        background: white;
        border-color: #cbd5e0;
        color: #4a5568;
    }
    
    .btn-outline:hover {
        background: #f7fafc;
        border-color: #a0aec0;
        color: #2d3748;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(245, 101, 101, 0.3);
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(245, 101, 101, 0.4);
    }

    /* Validation alerts */
    .validation-alert {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        color: #c53030;
        border: 1px solid #fc8181;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        font-size: 0.8125rem;
        box-shadow: 0 2px 8px rgba(245, 101, 101, 0.1);
        animation: slideIn 0.3s ease;
    }
    
    .validation-alert i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    .validation-alert ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }
    
    .success-alert {
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        color: #276749;
        border: 1px solid #9ae6b4;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        font-size: 0.8125rem;
        box-shadow: 0 2px 8px rgba(72, 187, 120, 0.1);
        animation: slideIn 0.3s ease;
    }
    
    .success-alert i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-container {
            width: 98%;
            margin: 1rem auto;
        }
        
        .card-header {
            padding: 1rem 1.25rem;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .view-all-link {
            align-self: stretch;
            justify-content: center;
        }

        .card-body {
            padding: 1.25rem;
        }

        .two-column-layout {
            grid-template-columns: 1fr;
        }

        .form-section {
            padding: 1rem 1.25rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
    
    @media (max-width: 480px) {
        .form-container {
            width: 100%;
            margin: 0.5rem auto;
            border-radius: 16px;
        }
        
        .card-body {
            padding: 1rem;
        }

        .course-preview-avatar {
            width: 70px;
            height: 70px;
            font-size: 1.75rem;
        }

        .instructor-card {
            flex-direction: column;
            text-align: center;
        }

        .instructor-avatar {
            margin: 0 auto;
        }
    }
</style>
@endpush

@section('content')
    <!-- Edit Course Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-book-open card-icon"></i>
                <h2 class="card-title">Edit Course</h2>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>
        
        <div class="card-body">
            <!-- Course Preview -->
            <div class="course-preview">
                <div class="course-preview-avatar">
                    {{ strtoupper(substr($course->course_code, 0, 1)) }}
                </div>
                <div class="course-preview-title">{{ $course->title }}</div>
                <div class="course-preview-code">{{ $course->course_code }}</div>
                <div class="course-preview-status {{ $course->is_published ? 'status-published' : 'status-draft' }}">
                    <i class="fas {{ $course->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $course->is_published ? 'Published' : 'Draft' }}
                </div>
            </div>

            <!-- Display validation errors -->
            @if($errors->any())
            <div class="validation-alert">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Display success message if any -->
            @if(session('success'))
            <div class="success-alert">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-check-circle"></i>
                    <strong>{{ session('success') }}</strong>
                </div>
            </div>
            @endif
            
            @if(session('error'))
            <div class="validation-alert">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>{{ session('error') }}</strong>
                </div>
            </div>
            @endif
            
            <!-- Two Column Layout - Form and Sidebar Inline -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <!-- Update Form -->
                    <form action="{{ route('admin.courses.update', Crypt::encrypt($course->id)) }}" method="POST" id="updateForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Course Information
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title" class="form-label required">
                                        <i class="fas fa-heading"></i> Course Title
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $course->title) }}" 
                                           required
                                           class="form-control @error('title') is-invalid @enderror"
                                           placeholder="e.g., Introduction to Programming">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="course_code" class="form-label required">
                                        <i class="fas fa-code"></i> Course Code
                                    </label>
                                    <input type="text" 
                                           id="course_code" 
                                           name="course_code" 
                                           value="{{ old('course_code', $course->course_code) }}" 
                                           required
                                           class="form-control @error('course_code') is-invalid @enderror"
                                           placeholder="e.g., CS101">
                                    @error('course_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Enter course description...">{{ old('description', $course->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Optional: Provide a detailed description of the course
                                </div>
                            </div>
                        </div>
                        
                        <!-- Course Details Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-cog"></i> Course Details
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="teacher_id" class="form-label">
                                        <i class="fas fa-chalkboard-teacher"></i> Assign Teacher
                                    </label>
                                    <select id="teacher_id" 
                                            name="teacher_id"
                                            class="form-select @error('teacher_id') is-invalid @enderror">
                                        <option value="">-- Select Teacher (Optional) --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->f_name }} {{ $teacher->l_name }} 
                                                @if($teacher->employee_id)
                                                    ({{ $teacher->employee_id }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint">
                                        <i class="fas fa-user-tie"></i> Leave blank to assign later
                                    </div>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="credits" class="form-label required">
                                        <i class="fas fa-cubes"></i> Credits
                                    </label>
                                    <input type="number" 
                                           id="credits" 
                                           name="credits" 
                                           value="{{ old('credits', $course->credits ?? 3) }}" 
                                           min="0.5"
                                           max="10"
                                           step="0.5"
                                           required
                                           class="form-control @error('credits') is-invalid @enderror">
                                    @error('credits')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Enter between 0.5 and 10 credits
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Course Status Notice -->
                        <div class="status-notice">
                            <i class="fas fa-info-circle"></i>
                            <div class="status-notice-content">
                                <div class="status-notice-title">Course Status</div>
                                <div class="status-notice-text">
                                    This course is <strong>{{ $course->is_published ? 'Published' : 'Draft' }}</strong>. 
                                    @if($course->is_published)
                                        Published courses are visible to enrolled students.
                                    @else
                                        Draft courses are only visible to instructors and administrators.
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="status" value="active">
                        <input type="hidden" name="is_published" value="{{ $course->is_published ? '1' : '0' }}">
                    </form>
                </div>
                
                <!-- Right Column - Course Information Sidebar (Inline with Basic Course Information) -->
                <div class="sidebar-column">
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> Course Information
                        </div>
                        
                        <!-- Statistics -->
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hashtag"></i> Course ID</span>
                            <span class="info-value">#{{ $course->id }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Created</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $course->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $course->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $course->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $course->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-users"></i> Enrolled Students</span>
                            <span class="info-value">{{ $course->students ? $course->students->count() : 0 }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-layer-group"></i> Topics</span>
                            <span class="info-value">{{ $course->topics ? $course->topics->count() : 0 }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-tag"></i> Credits</span>
                            <span class="info-value">{{ $course->credits ?? 3 }}</span>
                        </div>
                        
                        <!-- Instructor Section -->
                        <div class="instructor-section">
                            <div class="instructor-header">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>INSTRUCTOR</span>
                            </div>
                            
                            @if($course->teacher)
                                <div class="instructor-card">
                                    <div class="instructor-avatar">
                                        {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}{{ strtoupper(substr($course->teacher->l_name, 0, 1)) }}
                                    </div>
                                    <div class="instructor-info">
                                        <div class="instructor-name">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                                        <div class="instructor-details">
                                            <i class="fas fa-envelope"></i> {{ $course->teacher->email }}<br>
                                            @if($course->teacher->employee_id)
                                                <i class="fas fa-id-badge"></i> {{ $course->teacher->employee_id }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="no-instructor">
                                    <i class="fas fa-user-slash"></i> No instructor assigned
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions (Outside Two Column Layout) -->
            <div class="form-actions">
                <div>
                    <form action="{{ route('admin.courses.destroy', Crypt::encrypt($course->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" id="deleteButton">
                            <i class="fas fa-trash-alt"></i> Delete Course
                        </button>
                    </form>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.courses.show', Crypt::encrypt($course->id)) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" form="updateForm" class="btn btn-primary" id="submitButton">
                        <i class="fas fa-save"></i> Update Course
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete button click with SweetAlert2
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

        // Form validation
        const updateForm = document.getElementById('updateForm');
        if (updateForm) {
            updateForm.addEventListener('submit', function(e) {
                const title = document.getElementById('title').value.trim();
                const code = document.getElementById('course_code').value.trim();
                const credits = document.getElementById('credits').value;
                
                let isValid = true;
                
                if (!title) {
                    document.getElementById('title').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!code) {
                    document.getElementById('course_code').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!credits || parseFloat(credits) <= 0) {
                    document.getElementById('credits').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please fill in all required fields.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                
                // Show loading state
                const submitBtn = document.getElementById('submitButton');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                submitBtn.disabled = true;
                
                // Re-enable after timeout (in case form doesn't redirect)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        }

        // Show notifications from session
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'success',
                title: '{{ session('success') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: '{{ session('error') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
</script>
@endpush