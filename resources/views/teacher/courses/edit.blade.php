@extends('layouts.teacher')

@section('title', 'Edit Course - ' . $course->title)

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Edit Course</h1>
        <p>Update course information</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Edit Course Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Edit Course - {{ $course->title }}</div>
            <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i>
                Back to View
            </a>
        </div>
        
        <div style="padding: 1.5rem;">
            @if(session('success'))
            <div style="margin: 0 0 1.5rem; padding: 12px; background: #dcfce7; color: #065f46; border-radius: 8px; font-size: 0.875rem;">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div style="margin: 0 0 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.875rem;">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                {{ session('error') }}
            </div>
            @endif
            
            <!-- Update Form -->
            <form action="{{ route('teacher.courses.update', Crypt::encrypt($course->id)) }}" method="POST" id="update-form">
                @csrf
                @method('PUT')
                
                @if($errors->any())
                <div style="margin: 0 0 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.875rem;">
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
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="title" class="form-label">Course Title *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $course->title) }}" 
                               required
                               class="form-control"
                               placeholder="e.g., Introduction to Programming">
                    </div>
                    
                    <div>
                        <label for="course_code" class="form-label">Course Code *</label>
                        <input type="text" 
                               id="course_code" 
                               name="course_code" 
                               value="{{ old('course_code', $course->course_code) }}" 
                               required
                               class="form-control"
                               placeholder="e.g., CS101">
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="form-control"
                              placeholder="Enter course description...">{{ old('description', $course->description) }}</textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="credits" class="form-label">Credits *</label>
                        <input type="number" 
                               id="credits" 
                               name="credits" 
                               value="{{ old('credits', $course->credits ?? 3) }}" 
                               min="0" 
                               step="0.5"
                               required
                               class="form-control">
                    </div>
                    
                    <div>
                        <label class="form-label">Publish Status</label>
                        <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" 
                                       name="is_published" 
                                       value="1" 
                                       {{ old('is_published', $course->is_published) == 1 ? 'checked' : '' }}>
                                <span>Published</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" 
                                       name="is_published" 
                                       value="0" 
                                       {{ old('is_published', $course->is_published) == 0 ? 'checked' : '' }}>
                                <span>Draft</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Learning Outcomes -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="learning_outcomes" class="form-label">Learning Outcomes (Optional)</label>
                    <textarea id="learning_outcomes" 
                              name="learning_outcomes" 
                              rows="2"
                              class="form-control"
                              placeholder="What will students learn in this course?">{{ old('learning_outcomes', $course->learning_outcomes ?? '') }}</textarea>
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Optional: Describe what students will achieve
                    </div>
                </div>
                
                <!-- Thumbnail URL -->
                <div style="margin-bottom: 2rem;">
                    <label for="thumbnail" class="form-label">Thumbnail URL (Optional)</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="url" 
                               id="thumbnail" 
                               name="thumbnail" 
                               value="{{ old('thumbnail', $course->thumbnail ?? '') }}"
                               placeholder="https://example.com/image.jpg"
                               class="form-control"
                               style="flex: 1;">
                        <button type="button" 
                                id="preview-thumbnail" 
                                style="padding: 12px 20px; background: #f3f4f6; color: var(--dark); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; font-weight: 500;">
                            Preview
                        </button>
                    </div>
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        <i class="fas fa-info-circle"></i> Optional: URL to course thumbnail image
                    </div>
                    
                    <!-- Thumbnail Preview -->
                    <div id="thumbnail-preview" style="margin-top: 0.75rem; display: none;">
                        <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Preview:</div>
                        <div style="width: 100%; max-width: 400px; height: 225px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                            <img id="preview-image" src="" alt="Thumbnail preview" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            <div id="no-preview" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f8fafc; color: var(--secondary);">
                                No preview available
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Teacher is automatically assigned (hidden field) -->
                <input type="hidden" name="teacher_id" value="{{ Auth::id() }}">
                
                <!-- Form Actions -->
                <div style="display: flex; justify-content: space-between; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <div style="display: flex; gap: 1rem;">
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to delete this course? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }"
                                style="padding: 10px 20px; background: #fee2e2; color: var(--danger); border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-trash"></i>
                            Delete Course
                        </button>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" 
                           style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                            Cancel
                        </a>
                        <button type="submit" 
                                style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-save"></i>
                            Update Course
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Hidden Delete Form (OUTSIDE the update form) -->
            <form id="delete-form" action="{{ route('teacher.courses.destroy', Crypt::encrypt($course->id)) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    
    <!-- Course Details Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Course Details</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Course ID</div>
                    <div style="font-weight: 600;">#{{ $course->id }}</div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Created</div>
                    <div style="font-weight: 600;">{{ $course->created_at->format('M d, Y') }}</div>
                    <div style="color: var(--secondary); font-size: 0.75rem;">{{ $course->created_at->diffForHumans() }}</div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Last Updated</div>
                    <div style="font-weight: 600;">{{ $course->updated_at->format('M d, Y') }}</div>
                    <div style="color: var(--secondary); font-size: 0.75rem;">{{ $course->updated_at->diffForHumans() }}</div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Enrolled Students</div>
                    <div style="font-weight: 600;">
                        @php
                            $studentCount = $course->students ? $course->students->count() : 0;
                        @endphp
                        {{ $studentCount }}
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Status</div>
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
        
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">View Course</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">See full details</div>
                    </div>
                </a>
                <a href="{{ route('teacher.courses.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">All Courses</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Back to list</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

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
                noPreview.textContent = 'Image failed to load';
                noPreview.style.color = '#ef4444';
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
@endsection