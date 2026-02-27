@extends('layouts.teacher')

@section('title', 'Edit Course - Teacher Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-form.css') }}">
@endpush

@section('content')
    <!-- Edit Course Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-book-open card-icon"></i>
                <h2 class="card-title">Edit Course</h2>
            </div>
            <a href="{{ route('teacher.courses.index') }}" class="view-all-link">
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

            <!-- Publish Toggle -->
            <div class="publish-toggle-container">
                <div class="publish-info">
                    <div class="publish-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="publish-text">
                        <h4>Course Visibility</h4>
                        <p>Toggle to change course publication status</p>
                    </div>
                </div>
                <div class="toggle-wrapper">
                    <div class="toggle-status" id="toggleStatusText">
                        @if($course->is_published)
                            <span class="status-published"><i class="fas fa-check-circle"></i> Published</span>
                        @else
                            <span class="status-draft"><i class="fas fa-clock"></i> Draft</span>
                        @endif
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="publishToggle" name="is_published" value="1" form="updateForm" {{ $course->is_published ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Publish Info Card -->
            <div class="publish-info-card">
                <div class="publish-info-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="publish-info-content">
                    <div class="publish-info-label">Current Status</div>
                    <div class="publish-info-value">
                        @if($course->is_published)
                            <span class="publish-badge published"><i class="fas fa-check-circle"></i> Published</span>
                            <span style="font-size: 0.75rem; color: #718096;">Visible to all enrolled students</span>
                        @else
                            <span class="publish-badge draft"><i class="fas fa-clock"></i> Draft</span>
                            <span style="font-size: 0.75rem; color: #718096;">Only visible to instructors</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Display validation errors -->
            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            <!-- Display success message if any -->
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
            
            <!-- Two Column Layout - Form and Sidebar Inline -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <!-- Update Form -->
                    <form action="{{ route('teacher.courses.update', $encryptedId) }}" method="POST" id="updateForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Course Information
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading"></i> Course Title
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $course->title) }}" 
                                           required
                                           class="form-input @error('title') error @enderror"
                                           placeholder="e.g., Introduction to Programming">
                                    @error('title')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="course_code" class="form-label">
                                        <i class="fas fa-code"></i> Course Code
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="course_code" 
                                           name="course_code" 
                                           value="{{ old('course_code', $course->course_code) }}" 
                                           required
                                           class="form-input @error('course_code') error @enderror"
                                           placeholder="e.g., CS101">
                                    @error('course_code')
                                        <div class="form-error">{{ $message }}</div>
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
                                          class="form-textarea @error('description') error @enderror"
                                          placeholder="Enter course description...">{{ old('description', $course->description) }}</textarea>
                                @error('description')
                                    <div class="form-error">{{ $message }}</div>
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
                                    <label for="credits" class="form-label">
                                        <i class="fas fa-cubes"></i> Credits
                                        <span class="required">*</span>
                                    </label>
                                    <input type="number" 
                                           id="credits" 
                                           name="credits" 
                                           value="{{ old('credits', $course->credits ?? 3) }}" 
                                           min="0.5"
                                           max="10"
                                           step="0.5"
                                           required
                                           class="form-input @error('credits') error @enderror">
                                    @error('credits')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Enter between 0.5 and 10 credits
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="status" value="active">
                        <input type="hidden" name="teacher_id" value="{{ Auth::id() }}">
                    </form>
                </div>
                
                <!-- Right Column - Course Information Sidebar -->
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
                            <span class="info-value">{{ $course->students_count ?? 0 }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-layer-group"></i> Topics</span>
                            <span class="info-value">{{ $course->topics_count ?? 0 }}</span>
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
            
            <!-- Form Actions -->
            <div class="form-actions" style="margin-top: 1.5rem;">
                <div>
                    <form action="{{ route('teacher.courses.destroy', $encryptedId) }}" method="POST" id="deleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" id="deleteButton">
                            <i class="fas fa-trash-alt"></i> Delete Course
                        </button>
                    </form>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('teacher.courses.show', $encryptedId) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline">
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
        const publishToggle = document.getElementById('publishToggle');
        const toggleStatusText = document.getElementById('toggleStatusText');
        const previewStatus = document.querySelector('.course-preview-status');
        const submitButton = document.getElementById('submitButton');
        
        // Update publish status display
        function updatePublishStatus() {
            const isPublished = publishToggle.checked;
            
            if (isPublished) {
                toggleStatusText.innerHTML = '<span class="status-published"><i class="fas fa-check-circle"></i> Published</span>';
                if (previewStatus) {
                    previewStatus.innerHTML = '<i class="fas fa-check-circle"></i> Published';
                    previewStatus.className = 'course-preview-status status-published';
                }
            } else {
                toggleStatusText.innerHTML = '<span class="status-draft"><i class="fas fa-clock"></i> Draft</span>';
                if (previewStatus) {
                    previewStatus.innerHTML = '<i class="fas fa-clock"></i> Draft';
                    previewStatus.className = 'course-preview-status status-draft';
                }
            }
        }
        
        if (publishToggle) {
            publishToggle.addEventListener('change', updatePublishStatus);
        }
        
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
                    document.getElementById('title').classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById('title').classList.remove('error');
                }
                
                if (!code) {
                    document.getElementById('course_code').classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById('course_code').classList.remove('error');
                }
                
                if (!credits || parseFloat(credits) < 0.5 || parseFloat(credits) > 10) {
                    document.getElementById('credits').classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById('credits').classList.remove('error');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please fill in all required fields correctly.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                
                // Show loading state
                if (submitButton) {
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                    submitButton.disabled = true;
                    
                    // Re-enable after timeout (in case form doesn't redirect)
                    setTimeout(() => {
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    }, 5000);
                }
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