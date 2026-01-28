@extends('layouts.admin')

@section('title', 'Edit Course - Admin Dashboard')

@push('styles')
<style>
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
    }
    
    .form-control, .form-select, textarea {
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
    
    input[type="radio"] {
        margin-right: 0.5rem;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Edit Course</h1>
        <p>Update course information</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Edit Course Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Course Information</div>
            <a href="{{ route('admin.courses.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i>
                Back to Courses
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
            <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" id="update-form">
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
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="title" class="form-label">Course Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $course->title) }}" 
                           required
                           class="form-control @error('title') is-invalid @enderror">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="course_code" class="form-label">Course Code *</label>
                    <input type="text" 
                           id="course_code" 
                           name="course_code" 
                           value="{{ old('course_code', $course->course_code) }}" 
                           required
                           class="form-control @error('course_code') is-invalid @enderror">
                    @error('course_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="teacher_id" class="form-label">Instructor</label>
                    <select id="teacher_id" 
                            name="teacher_id"
                            class="form-select @error('teacher_id') is-invalid @enderror">
                        <option value="">Select Instructor (Optional)</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->f_name }} {{ $teacher->l_name }} 
                                @if($teacher->employee_id)
                                    ({{ $teacher->employee_id }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="credits" class="form-label">Credits</label>
                    <input type="number" 
                           id="credits" 
                           name="credits" 
                           value="{{ old('credits', $course->credits ?? 3) }}" 
                           min="1"
                           max="10"
                           class="form-control @error('credits') is-invalid @enderror">
                    @error('credits')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Publication Status</label>
                    <div style="display: flex; align-items: center; gap: 2rem;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" 
                                   name="is_published" 
                                   value="1" 
                                   {{ old('is_published', $course->is_published) == 1 ? 'checked' : '' }}>
                            <span style="margin-left: 0.5rem;">Published</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" 
                                   name="is_published" 
                                   value="0" 
                                   {{ old('is_published', $course->is_published) == 0 ? 'checked' : '' }}>
                            <span style="margin-left: 0.5rem;">Draft</span>
                        </label>
                    </div>
                    @error('is_published')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label class="form-label">Course Status</label>
                    <div style="display: flex; align-items: center; gap: 2rem;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" 
                                   name="status" 
                                   value="active" 
                                   {{ old('status', $course->status ?? 'active') == 'active' ? 'checked' : '' }}>
                            <span style="margin-left: 0.5rem;">Active</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" 
                                   name="status" 
                                   value="inactive" 
                                   {{ old('status', $course->status ?? 'active') == 'inactive' ? 'checked' : '' }}>
                            <span style="margin-left: 0.5rem;">Inactive</span>
                        </label>
                    </div>
                    @error('status')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; justify-content: space-between; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <div style="display: flex; gap: 1rem;">
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to delete this course?')) { document.getElementById('delete-form').submit(); }"
                                style="padding: 10px 20px; background: #fee2e2; color: var(--danger); border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-trash"></i>
                            Delete Course
                        </button>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('admin.courses.index') }}" 
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
            <form id="delete-form" action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" style="display: none;">
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
        
        @if($course->teacher)
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Current Instructor</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                            {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--dark);">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                            <div style="color: var(--secondary); font-size: 0.75rem;">
                                {{ $course->teacher->email }}
                                @if($course->teacher->employee_id)
                                    • {{ $course->teacher->employee_id }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('admin.courses.show', $course->id) }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">View Course</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">See full details</div>
                    </div>
                </a>
                <a href="{{ route('admin.courses.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
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
// Optional: Add form validation or other JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    // You can add any additional JavaScript functionality here
    console.log('Course edit page loaded');
});
</script>
@endpush
@endsection