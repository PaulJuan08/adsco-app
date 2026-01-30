@extends('layouts.teacher')

@section('title', 'Create New Course')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Create New Course</h1>
        <p>Add a new course to your teaching schedule</p>
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
        <a href="{{ route('teacher.courses.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('teacher.courses.store') }}" method="POST">
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
                
                <div>
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" 
                            name="status"
                            required
                            style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('status') border-color: var(--danger); @enderror">
                        <option value="">-- Select Status --</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="thumbnail" class="form-label">Thumbnail URL (Optional)</label>
                <input type="url" 
                       id="thumbnail" 
                       name="thumbnail" 
                       value="{{ old('thumbnail') }}"
                       placeholder="https://example.com/image.jpg"
                       style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('thumbnail') border-color: var(--danger); @enderror">
                <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                    URL to course thumbnail image
                </div>
                @error('thumbnail')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" 
                           id="is_published" 
                           name="is_published" 
                           value="1" 
                           {{ old('is_published') ? 'checked' : '' }}
                           style="width: 16px; height: 16px;">
                    <label for="is_published" style="font-size: 0.875rem; color: var(--dark);">
                        Publish course immediately
                    </label>
                </div>
                @error('is_published')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Teacher ID is automatically set from auth -->
            <input type="hidden" name="teacher_id" value="{{ Auth::id() }}">
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('teacher.courses.index') }}" 
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
</style>

<script>
    // Auto-generate course code suggestion
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const codeField = document.getElementById('course_code');
        
        if (title && !codeField.value) {
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
                    codeField.value = code + randomNum;
                }
            }
        }
    });
    
    // Character counter for description
    document.getElementById('description').addEventListener('input', function() {
        const maxLength = 255;
        const currentLength = this.value.length;
        const charCounter = document.getElementById('char-counter');
        
        if (!charCounter) {
            const counter = document.createElement('div');
            counter.id = 'char-counter';
            counter.style.cssText = 'color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;';
            this.parentNode.appendChild(counter);
        }
        
        document.getElementById('char-counter').textContent = `${currentLength}/${maxLength} characters`;
        
        if (currentLength > maxLength) {
            this.style.borderColor = 'var(--danger)';
            document.getElementById('char-counter').style.color = 'var(--danger)';
        } else {
            this.style.borderColor = 'var(--border)';
            document.getElementById('char-counter').style.color = 'var(--secondary)';
        }
    });
</script>
@endsection