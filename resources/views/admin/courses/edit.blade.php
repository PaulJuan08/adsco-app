@extends('layouts.admin')

@section('title', 'Edit Course - Admin Dashboard')

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

    /* Form Elements */
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--gray-900);
        font-size: 0.875rem;
    }
    
    .form-control, .form-select, textarea {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--gray-900);
        background-color: white;
        background-clip: padding-box;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus, .form-select:focus, textarea:focus {
        border-color: var(--primary);
        outline: 0;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: var(--danger);
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--danger);
    }

    .form-hint {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    /* Form Sections */
    .form-section {
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .form-section-title i {
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }

    /* Thumbnail Preview */
    .input-with-button {
        display: flex;
        gap: 0.5rem;
    }
    
    .input-with-button .form-control {
        flex: 1;
    }
    
    .preview-button {
        padding: 0.75rem 1rem;
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .preview-button:hover {
        background: var(--gray-200);
    }
    
    .thumbnail-preview {
        margin-top: 1rem;
        display: none;
    }
    
    .preview-container {
        width: 100%;
        max-width: 400px;
        height: 225px;
        border-radius: var(--radius-sm);
        overflow: hidden;
        border: 1px solid var(--gray-300);
        background: var(--gray-50);
    }
    
    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    
    .no-preview {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--gray-400);
        text-align: center;
        padding: 1rem;
    }
    
    .no-preview i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .no-preview span {
        font-size: 0.875rem;
    }

    /* Course Status */
    .status-notice {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #075985;
        padding: 1rem;
        border-radius: var(--radius-sm);
        margin-bottom: 1.5rem;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }
    
    .btn-secondary:hover {
        background: var(--gray-200);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }
    
    .btn-danger {
        background: #fee2e2;
        color: var(--danger);
        border: 1px solid #fecaca;
    }
    
    .btn-danger:hover {
        background: #fecaca;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .input-with-button {
            flex-direction: column;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Edit Course Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-edit card-icon"></i>
                <h2 class="card-title">Edit Course: {{ $course->title }}</h2>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>
        
        <div class="card-body">
            @if(session('success'))
            <div style="margin-bottom: 1.5rem; padding: 12px; background: #dcfce7; color: #065f46; border-radius: var(--radius-sm); font-size: 0.875rem;">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div style="margin-bottom: 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: var(--radius-sm); font-size: 0.875rem;">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                {{ session('error') }}
            </div>
            @endif
            
            <!-- Update Form -->
            <form action="{{ route('admin.courses.update', Crypt::encrypt($course->id)) }}" method="POST" id="update-form">
                @csrf
                @method('PUT')
                
                @if($errors->any())
                <div style="margin-bottom: 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: var(--radius-sm); font-size: 0.875rem;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                        <strong>Please fix the following errors:</strong>
                    </div>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Basic Information -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Course Information
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title" class="form-label">Course Title *</label>
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
                            <label for="course_code" class="form-label">Course Code *</label>
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
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter course description...">{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Course Details -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-cog"></i>
                        Course Details
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="teacher_id" class="form-label">Assign Teacher (Optional)</label>
                            <select id="teacher_id" 
                                    name="teacher_id"
                                    class="form-select @error('teacher_id') is-invalid @enderror">
                                <option value="">-- Select Teacher --</option>
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
                            <label for="credits" class="form-label">Credits *</label>
                            <input type="number" 
                                   id="credits" 
                                   name="credits" 
                                   value="{{ old('credits', $course->credits ?? 3) }}" 
                                   min="0" 
                                   step="0.5"
                                   required
                                   class="form-control @error('credits') is-invalid @enderror">
                            @error('credits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Learning Outcomes -->
                    <div class="form-group">
                        <label for="learning_outcomes" class="form-label">Learning Outcomes (Optional)</label>
                        <textarea id="learning_outcomes" 
                                  name="learning_outcomes" 
                                  rows="2"
                                  class="form-control @error('learning_outcomes') is-invalid @enderror"
                                  placeholder="What will students learn in this course?">{{ old('learning_outcomes', $course->learning_outcomes ?? '') }}</textarea>
                        <div class="form-hint">
                            <i class="fas fa-graduation-cap"></i> Optional: Describe what students will achieve
                        </div>
                        @error('learning_outcomes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Thumbnail URL -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-image"></i>
                        Course Thumbnail (Optional)
                    </div>
                    
                    <div class="form-group">
                        <label for="thumbnail" class="form-label">Thumbnail URL</label>
                        <div class="input-with-button">
                            <input type="url" 
                                   id="thumbnail" 
                                   name="thumbnail" 
                                   value="{{ old('thumbnail', $course->thumbnail ?? '') }}"
                                   placeholder="https://example.com/image.jpg"
                                   class="form-control @error('thumbnail') is-invalid @enderror">
                            <button type="button" 
                                    id="preview-thumbnail" 
                                    class="preview-button">
                                Preview
                            </button>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i> Optional: URL to course thumbnail image
                        </div>
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Thumbnail Preview -->
                        <div id="thumbnail-preview" class="thumbnail-preview">
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--gray-900); margin-bottom: 0.5rem;">Preview:</div>
                            <div class="preview-container">
                                <img id="preview-image" src="" alt="Thumbnail preview" 
                                     class="preview-image">
                                <div id="no-preview" class="no-preview">
                                    <i class="fas fa-image"></i>
                                    <span>No preview available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Course Status -->
                <div class="status-notice">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 0.5rem;">
                        <div style="width: 24px; height: 24px; background: var(--primary); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-info-circle" style="font-size: 12px;"></i>
                        </div>
                        <div style="font-weight: 500; color: var(--gray-900);">Course Status</div>
                    </div>
                    <div style="color: #075985; font-size: 0.875rem; line-height: 1.5;">
                        <p style="margin: 0 0 0.5rem 0;">This course is <strong>Active</strong> and <strong>Published</strong>.</p>
                        <p style="margin: 0; font-size: 0.75rem;">Courses are always created as active and published. To unpublish or deactivate, use the course management panel.</p>
                    </div>
                </div>
                
                <!-- Hidden fields - Always Active and Published -->
                <input type="hidden" name="status" value="active">
                <input type="hidden" name="is_published" value="1">
            </form>
        </div>
        
        <div class="card-footer-modern">
            <div style="display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; gap: 1rem;">
                    <form action="{{ route('admin.courses.destroy', Crypt::encrypt($course->id)) }}" method="POST" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to delete this course? This action cannot be undone.')) { this.form.submit(); }"
                                class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete Course
                        </button>
                    </form>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" form="update-form" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Course
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Details Sidebar -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-info-circle card-icon"></i>
                <h2 class="card-title">Course Details</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div style="display: grid; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-sm);">
                    <div style="color: var(--gray-600); font-size: 0.875rem;">Course ID</div>
                    <div style="font-weight: 600;">#{{ $course->id }}</div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-sm);">
                    <div>
                        <div style="color: var(--gray-600); font-size: 0.875rem;">Created</div>
                        <div style="color: var(--gray-500); font-size: 0.75rem;">{{ $course->created_at->diffForHumans() }}</div>
                    </div>
                    <div style="font-weight: 600;">{{ $course->created_at->format('M d, Y') }}</div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-sm);">
                    <div>
                        <div style="color: var(--gray-600); font-size: 0.875rem;">Last Updated</div>
                        <div style="color: var(--gray-500); font-size: 0.75rem;">{{ $course->updated_at->diffForHumans() }}</div>
                    </div>
                    <div style="font-weight: 600;">{{ $course->updated_at->format('M d, Y') }}</div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-sm);">
                    <div style="color: var(--gray-600); font-size: 0.875rem;">Enrolled Students</div>
                    <div style="font-weight: 600;">
                        @php
                            $studentCount = $course->students ? $course->students->count() : 0;
                        @endphp
                        {{ $studentCount }}
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-sm);">
                    <div style="color: var(--gray-600); font-size: 0.875rem;">Status</div>
                    <div>
                        @if($course->is_published)
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                Published
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                <i class="fas fa-clock" style="font-size: 10px;"></i>
                                Draft
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($course->teacher)
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-chalkboard-teacher card-icon"></i>
                <h2 class="card-title">Current Instructor</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-sm);">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                    {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: var(--gray-900);">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                    <div style="color: var(--gray-600); font-size: 0.75rem; margin-top: 0.25rem;">
                        {{ $course->teacher->email }}
                        @if($course->teacher->employee_id)
                            • {{ $course->teacher->employee_id }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-bolt card-icon"></i>
                <h2 class="card-title">Quick Actions</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div style="display: grid; gap: 1rem;">
                <a href="{{ route('admin.courses.show', Crypt::encrypt($course->id)) }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); text-decoration: none; color: var(--primary-dark); transition: all 0.2s ease;">
                    <div style="width: 40px; height: 40px; background: white; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">View Course</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">See full details</div>
                    </div>
                </a>
                
                <a href="{{ route('admin.courses.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--gray-100); border-radius: var(--radius-sm); text-decoration: none; color: var(--gray-700); transition: all 0.2s ease;">
                    <div style="width: 40px; height: 40px; background: white; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: var(--gray-600);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">All Courses</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Back to list</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const thumbnailInput = document.getElementById('thumbnail');
    const previewBtn = document.getElementById('preview-thumbnail');
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    const previewImage = document.getElementById('preview-image');
    const noPreview = document.getElementById('no-preview');
    
    // Thumbnail preview functionality
    function updateThumbnailPreview() {
        const url = thumbnailInput.value.trim();
        
        if (url) {
            thumbnailPreview.style.display = 'block';
            previewImage.src = url;
            previewImage.style.display = 'block';
            noPreview.style.display = 'none';
            
            // Check if image loads successfully
            previewImage.onload = function() {
                previewImage.style.display = 'block';
                noPreview.style.display = 'none';
            };
            
            previewImage.onerror = function() {
                previewImage.style.display = 'none';
                noPreview.style.display = 'flex';
                noPreview.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Image failed to load</span>';
                noPreview.style.color = 'var(--danger)';
            };
        } else {
            thumbnailPreview.style.display = 'none';
        }
    }
    
    previewBtn.addEventListener('click', updateThumbnailPreview);
    thumbnailInput.addEventListener('change', updateThumbnailPreview);
    thumbnailInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            updateThumbnailPreview();
        }
    });
    
    // Initialize preview if thumbnail already exists
    if (thumbnailInput.value) {
        updateThumbnailPreview();
    }
    
    // Form validation
    document.getElementById('update-form').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const code = document.getElementById('course_code').value.trim();
        const credits = document.getElementById('credits').value;
        
        if (!title) {
            e.preventDefault();
            alert('Please enter a course title.');
            document.getElementById('title').focus();
            return;
        }
        
        if (!code) {
            e.preventDefault();
            alert('Please enter a course code.');
            document.getElementById('course_code').focus();
            return;
        }
        
        if (!credits || parseFloat(credits) <= 0) {
            e.preventDefault();
            alert('Please enter valid credits (greater than 0).');
            document.getElementById('credits').focus();
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});
</script>
@endpush