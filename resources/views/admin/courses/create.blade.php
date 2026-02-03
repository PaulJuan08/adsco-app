@extends('layouts.admin')

@section('title', 'Create New Course')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Create New Course</h1>
        <p>Add a new course to the system</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Course Information</h2>
        <a href="{{ route('admin.courses.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf
            
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
                           value="{{ old('title') }}" 
                           required
                           placeholder="e.g., Introduction to Programming"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('title') border-color: var(--danger); @enderror">
                    @error('title')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <label for="course_code" class="form-label">Course Code *</label>
                    <input type="text" 
                           id="course_code" 
                           name="course_code" 
                           value="{{ old('course_code') }}" 
                           required
                           placeholder="e.g., CS101"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('course_code') border-color: var(--danger); @enderror">
                    @error('course_code')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          placeholder="Enter course description..."
                          style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('description') border-color: var(--danger); @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="teacher_id" class="form-label">Assign Teacher</label>
                    <select id="teacher_id" 
                            name="teacher_id"
                            style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('teacher_id') border-color: var(--danger); @enderror">
                        <option value="">-- Select Teacher --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->f_name }} {{ $teacher->l_name }} ({{ $teacher->email }})
                            </option>
                        @endforeach
                    </select>
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Leave blank to assign later
                    </div>
                    @error('teacher_id')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <label for="credits" class="form-label">Credits *</label>
                    <input type="number" 
                           id="credits" 
                           name="credits" 
                           value="{{ old('credits', 3) }}" 
                           min="0" 
                           step="0.5"
                           required
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('credits') border-color: var(--danger); @enderror">
                    @error('credits')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Learning Outcomes -->
            <div style="margin-bottom: 1.5rem;">
                <label for="learning_outcomes" class="form-label">Learning Outcomes (Optional)</label>
                <textarea id="learning_outcomes" 
                          name="learning_outcomes" 
                          rows="2"
                          placeholder="What will students learn in this course?"
                          style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical; @error('learning_outcomes') border-color: var(--danger); @enderror">{{ old('learning_outcomes') }}</textarea>
                <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                    Optional: Describe what students will achieve
                </div>
                @error('learning_outcomes')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="thumbnail" class="form-label">Thumbnail URL (Optional)</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="url" 
                           id="thumbnail" 
                           name="thumbnail" 
                           value="{{ old('thumbnail') }}"
                           placeholder="https://example.com/image.jpg"
                           style="flex: 1; padding: 12px; border: 1px solid var(--border); border-radius: 8px; @error('thumbnail') border-color: var(--danger); @enderror">
                    <button type="button" 
                            id="preview-thumbnail" 
                            style="padding: 12px 20px; background: #f3f4f6; color: var(--dark); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; font-weight: 500;">
                        Preview
                    </button>
                </div>
                <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                    <i class="fas fa-info-circle"></i> Optional: URL to course thumbnail image
                </div>
                @error('thumbnail')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
                
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
            
            <!-- Hidden fields - Status always active and published -->
            <input type="hidden" name="status" value="active">
            <input type="hidden" name="is_published" value="1">
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('admin.courses.index') }}" 
                   style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                    Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-save"></i> Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }
    
    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    
    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const codeInput = document.getElementById('course_code');
        const thumbnailInput = document.getElementById('thumbnail');
        const previewBtn = document.getElementById('preview-thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const previewImage = document.getElementById('preview-image');
        const noPreview = document.getElementById('no-preview');
        
        // Auto-generate course code suggestion
        titleInput.addEventListener('input', function() {
            const title = this.value;
            
            if (title && !codeInput.value) {
                const words = title.toUpperCase().split(' ');
                if (words.length >= 2) {
                    let code = '';
                    if (words[0].length >= 3) {
                        code = words[0].substring(0, 3);
                    } else if (words.length >= 2) {
                        code = words[0].substring(0, 2) + words[1].charAt(0);
                    }
                    
                    if (code) {
                        const randomNum = Math.floor(Math.random() * 900) + 100;
                        codeInput.value = code + randomNum;
                    }
                }
            }
        });
        
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
        
        // Form validation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = titleInput.value.trim();
            const code = codeInput.value.trim();
            const credits = document.getElementById('credits').value;
            
            if (!title) {
                e.preventDefault();
                alert('Please enter a course title.');
                titleInput.focus();
                return;
            }
            
            if (!code) {
                e.preventDefault();
                alert('Please enter a course code.');
                codeInput.focus();
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    });
</script>
@endsection