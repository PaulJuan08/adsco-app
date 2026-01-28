@extends('layouts.admin')

@section('title', 'Create New Course - Admin Dashboard')

@push('styles')
<style>
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
    }
    
    .form-control, .form-select {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--dark);
        background-color: white;
        background-clip: padding-box;
        border: 1px solid var(--border);
        border-radius: 8px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        outline: 0;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: var(--danger);
    }
    
    .form-control.is-invalid:focus, .form-select.is-invalid:focus {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--danger);
    }
    
    .text-muted {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.25rem;
    }
    
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
    }
    
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
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

<!-- Main Content -->
<div class="content-grid">
    <!-- Create Course Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Course Information</div>
            <a href="{{ route('admin.courses.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i>
                Back to Courses
            </a>
        </div>
        
        <div style="padding: 1.5rem;">
            <form action="{{ route('admin.courses.store') }}" method="POST">
                @csrf
                
                <!-- Display validation errors -->
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
                        <label for="title" class="form-label">Course Name *</label>
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
                        <label for="credits" class="form-label">Credits</label>
                        <input type="number" 
                               id="credits" 
                               name="credits" 
                               value="{{ old('credits', 3) }}" 
                               min="1" max="10" step="0.5"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('credits') border-color: var(--danger); @enderror">
                        @error('credits')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select id="status" 
                                name="status"
                                style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('status') border-color: var(--danger); @enderror">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="is_published" class="form-label">Publish Status</label>
                        <select id="is_published" 
                                name="is_published"
                                style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('is_published') border-color: var(--danger); @enderror">
                            <option value="0" {{ old('is_published') == '0' ? 'selected' : '' }}>Draft</option>
                            <option value="1" {{ old('is_published') == '1' ? 'selected' : 'selected' }}>Published</option>
                        </select>
                        @error('is_published')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" 
                               id="is_required" 
                               name="is_required" 
                               value="1" 
                               {{ old('is_required') ? 'checked' : '' }}
                               style="width: 16px; height: 16px;">
                        <label for="is_required" style="font-size: 0.875rem; color: var(--dark);">
                            Required Course
                        </label>
                    </div>
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Check if this course is mandatory for all students
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <a href="{{ route('admin.courses.index') }}" 
                       style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                        Cancel
                    </a>
                    <button type="submit" 
                            style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-save"></i>
                        Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Quick Tips Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Tips</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Course Codes</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Use unique course codes (e.g., CS101, MATH201)</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Teacher Assignment</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Assign teachers later if unsure</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Credits</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Standard courses: 3 credits, Lab courses: 1 credit</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Publish Status</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Published courses are visible to students</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Status Guide</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #dcfce7; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--success); font-size: 0.75rem;">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Active</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Currently ongoing, students can enroll</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #fee2e2; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--danger); font-size: 0.75rem;">
                            <i class="fas fa-pause-circle"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Inactive</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Not currently available for enrollment</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate course code suggestion
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const codeField = document.getElementById('course_code');
        
        if (title && !codeField.value) {
            // Generate a simple course code from the first 2-3 words
            const words = title.toUpperCase().split(' ');
            if (words.length >= 2) {
                let code = '';
                if (words[0].length >= 3) {
                    code = words[0].substring(0, 3);
                } else if (words.length >= 2) {
                    code = words[0].substring(0, 2) + words[1].charAt(0);
                }
                
                // Add a random 3-digit number
                if (code) {
                    const randomNum = Math.floor(Math.random() * 900) + 100;
                    codeField.placeholder = code + randomNum;
                }
            }
        }
    });
</script>
@endpush 
@endsection