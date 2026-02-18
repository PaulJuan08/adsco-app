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
                    <form action="{{ route('teacher.courses.update', Crypt::encrypt($course->id)) }}" method="POST" id="updateForm">
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
                                
                                <div class="form-group">
                                    <label for="department" class="form-label">
                                        <i class="fas fa-building"></i> Department
                                    </label>
                                    <input type="text" 
                                           id="department" 
                                           name="department" 
                                           value="{{ old('department', $course->department ?? '') }}" 
                                           class="form-control @error('department') is-invalid @enderror"
                                           placeholder="e.g., Computer Science">
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Optional: Department offering the course
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="learning_outcomes" class="form-label">
                                        <i class="fas fa-tasks"></i> Learning Outcomes
                                    </label>
                                    <textarea id="learning_outcomes" 
                                              name="learning_outcomes" 
                                              rows="3"
                                              class="form-control @error('learning_outcomes') is-invalid @enderror"
                                              placeholder="What will students learn in this course?">{{ old('learning_outcomes', $course->learning_outcomes ?? '') }}</textarea>
                                    @error('learning_outcomes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Optional: Describe the key learning objectives
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="semester" class="form-label">
                                        <i class="fas fa-calendar"></i> Semester
                                    </label>
                                    <select id="semester" 
                                            name="semester"
                                            class="form-select @error('semester') is-invalid @enderror">
                                        <option value="">-- Select Semester (Optional) --</option>
                                        <option value="fall" {{ old('semester', $course->semester ?? '') == 'fall' ? 'selected' : '' }}>Fall</option>
                                        <option value="spring" {{ old('semester', $course->semester ?? '') == 'spring' ? 'selected' : '' }}>Spring</option>
                                        <option value="summer" {{ old('semester', $course->semester ?? '') == 'summer' ? 'selected' : '' }}>Summer</option>
                                        <option value="winter" {{ old('semester', $course->semester ?? '') == 'winter' ? 'selected' : '' }}>Winter</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="thumbnail" class="form-label">
                                    <i class="fas fa-image"></i> Thumbnail URL
                                </label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="url" 
                                           id="thumbnail" 
                                           name="thumbnail" 
                                           value="{{ old('thumbnail', $course->thumbnail ?? '') }}"
                                           placeholder="https://example.com/image.jpg"
                                           class="form-control @error('thumbnail') is-invalid @enderror"
                                           style="flex: 1;">
                                    <button type="button" 
                                            id="preview-thumbnail"
                                            class="btn btn-outline"
                                            style="white-space: nowrap;">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                </div>
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Optional: URL to course thumbnail image
                                </div>
                                
                                <!-- Thumbnail Preview -->
                                <div id="thumbnail-preview" style="margin-top: 0.75rem; display: none;">
                                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Preview:</div>
                                    <div style="width: 100%; max-width: 400px; height: 225px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                                        <img id="preview-image" src="" alt="Thumbnail preview" 
                                             style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                        <div id="no-preview" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f8fafc; color: var(--gray-500);">
                                            No preview available
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Publishing Options Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-globe"></i> Publishing Options
                            </div>
                            
                            <div class="publishing-options">
                                <div style="display: flex; align-items: center; gap: 2rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="radio" 
                                               name="is_published" 
                                               value="1" 
                                               {{ old('is_published', $course->is_published) == 1 ? 'checked' : '' }}
                                               style="width: 16px; height: 16px;">
                                        <span style="font-weight: 500; color: var(--dark);">
                                            <i class="fas fa-check-circle" style="color: var(--success); margin-right: 0.25rem;"></i>
                                            Published
                                        </span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="radio" 
                                               name="is_published" 
                                               value="0" 
                                               {{ old('is_published', $course->is_published) == 0 ? 'checked' : '' }}
                                               style="width: 16px; height: 16px;">
                                        <span style="font-weight: 500; color: var(--dark);">
                                            <i class="fas fa-clock" style="color: var(--warning); margin-right: 0.25rem;"></i>
                                            Draft
                                        </span>
                                    </label>
                                </div>
                                <div class="form-hint" style="margin-top: 0.5rem;">
                                    <i class="fas fa-info-circle"></i> 
                                    Published courses are visible to enrolled students. Draft courses are only visible to you.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
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
                            
                            <div class="instructor-card">
                                <div class="instructor-avatar">
                                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->l_name, 0, 1)) }}
                                </div>
                                <div class="instructor-info">
                                    <div class="instructor-name">{{ Auth::user()->f_name }} {{ Auth::user()->l_name }}</div>
                                    <div class="instructor-details">
                                        <i class="fas fa-envelope"></i> {{ Auth::user()->email }}<br>
                                        @if(Auth::user()->employee_id)
                                            <i class="fas fa-id-badge"></i> {{ Auth::user()->employee_id }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                            <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" class="sidebar-action">
                                <i class="fas fa-eye"></i>
                                <span>View Course</span>
                                <i class="fas fa-chevron-right" style="margin-left: auto; font-size: 0.75rem;"></i>
                            </a>
                            <a href="{{ route('teacher.topics.create') }}" class="sidebar-action">
                                <i class="fas fa-plus-circle"></i>
                                <span>Create New Topic</span>
                                <i class="fas fa-chevron-right" style="margin-left: auto; font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Status Notice -->
                    <div class="status-notice" style="margin-top: 1rem;">
                        <i class="fas fa-info-circle"></i>
                        <div class="status-notice-content">
                            <div class="status-notice-title">Course Status</div>
                            <div class="status-notice-text">
                                This course is <strong>{{ $course->is_published ? 'Published' : 'Draft' }}</strong>. 
                                @if($course->is_published)
                                    Published courses are visible to enrolled students.
                                @else
                                    Draft courses are only visible to you.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions (Outside Two Column Layout) -->
            <div class="form-actions">
                <div>
                    <form action="{{ route('teacher.courses.destroy', Crypt::encrypt($course->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" id="deleteButton">
                            <i class="fas fa-trash-alt"></i> Delete Course
                        </button>
                    </form>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" class="btn btn-outline">
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
                } else {
                    document.getElementById('title').classList.remove('is-invalid');
                }
                
                if (!code) {
                    document.getElementById('course_code').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('course_code').classList.remove('is-invalid');
                }
                
                if (!credits || parseFloat(credits) <= 0) {
                    document.getElementById('credits').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('credits').classList.remove('is-invalid');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please fill in all required fields.',
                        icon: 'error',
                        confirmButtonColor: 'var(--primary)'
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

        // Thumbnail preview functionality
        const thumbnailInput = document.getElementById('thumbnail');
        const previewBtn = document.getElementById('preview-thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const previewImage = document.getElementById('preview-image');
        const noPreview = document.getElementById('no-preview');
        
        function updateThumbnailPreview() {
            const url = thumbnailInput.value.trim();
            
            if (url) {
                thumbnailPreview.style.display = 'block';
                previewImage.src = url;
                previewImage.style.display = 'block';
                noPreview.style.display = 'none';
                
                previewImage.onload = function() {
                    previewImage.style.display = 'block';
                    noPreview.style.display = 'none';
                    noPreview.textContent = 'No preview available';
                    noPreview.style.color = 'var(--gray-500)';
                };
                
                previewImage.onerror = function() {
                    previewImage.style.display = 'none';
                    noPreview.style.display = 'flex';
                    noPreview.textContent = 'Image failed to load';
                    noPreview.style.color = '#ef4444';
                };
            } else {
                thumbnailPreview.style.display = 'none';
            }
        }
        
        if (previewBtn) {
            previewBtn.addEventListener('click', updateThumbnailPreview);
        }
        
        if (thumbnailInput) {
            thumbnailInput.addEventListener('change', updateThumbnailPreview);
            thumbnailInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    updateThumbnailPreview();
                }
            });
        }
        
        // Initialize preview if thumbnail already exists
        if (thumbnailInput && thumbnailInput.value) {
            updateThumbnailPreview();
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

        // Auto-dismiss validation alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.validation-alert, .success-alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>
@endpush