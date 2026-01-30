@extends('layouts.teacher')

@section('title', 'Edit Course - Teacher Dashboard')

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
            <a href="{{ route('teacher.courses.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
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
            <form action="{{ route('teacher.courses.update', $course->id) }}" method="POST" id="update-form">
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
                    <label class="form-label">Course Status *</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 2px solid var(--border); border-radius: 8px; transition: all 0.2s;">
                            <input type="radio" 
                                   name="status" 
                                   value="active" 
                                   {{ old('status', $course->status ?? 'active') == 'active' ? 'checked' : '' }}
                                   style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500;">Active</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Currently running</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 2px solid var(--border); border-radius: 8px; transition: all 0.2s;">
                            <input type="radio" 
                                   name="status" 
                                   value="inactive" 
                                   {{ old('status', $course->status ?? 'active') == 'inactive' ? 'checked' : '' }}
                                   style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500;">Inactive</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Not currently active</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 2px solid var(--border); border-radius: 8px; transition: all 0.2s;">
                            <input type="radio" 
                                   name="status" 
                                   value="upcoming" 
                                   {{ old('status', $course->status ?? 'active') == 'upcoming' ? 'checked' : '' }}
                                   style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500;">Upcoming</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Starting soon</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 2px solid var(--border); border-radius: 8px; transition: all 0.2s;">
                            <input type="radio" 
                                   name="status" 
                                   value="archived" 
                                   {{ old('status', $course->status ?? 'active') == 'archived' ? 'checked' : '' }}
                                   style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500;">Archived</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Completed course</div>
                            </div>
                        </label>
                    </div>
                    @error('status')
                        <div class="invalid-feedback" style="display: block; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="thumbnail" class="form-label">Thumbnail URL (Optional)</label>
                    <input type="url" 
                           id="thumbnail" 
                           name="thumbnail" 
                           value="{{ old('thumbnail', $course->thumbnail) }}" 
                           placeholder="https://example.com/image.jpg"
                           class="form-control @error('thumbnail') is-invalid @enderror">
                    <div style="font-size: 0.75rem; color: var(--secondary); margin-top: 0.25rem;">
                        Enter a valid URL for the course thumbnail image
                    </div>
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label class="form-label">Publication Status</label>
                    <div style="display: flex; align-items: center; gap: 2rem;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 2px solid var(--border); border-radius: 8px; transition: all 0.2s;">
                            <input type="radio" 
                                   name="is_published" 
                                   value="1" 
                                   {{ old('is_published', $course->is_published) == 1 ? 'checked' : '' }}
                                   style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500;">Published</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Visible to students</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 2px solid var(--border); border-radius: 8px; transition: all 0.2s;">
                            <input type="radio" 
                                   name="is_published" 
                                   value="0" 
                                   {{ old('is_published', $course->is_published) == 0 ? 'checked' : '' }}
                                   style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500;">Draft</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">Only visible to you</div>
                            </div>
                        </label>
                    </div>
                    @error('is_published')
                        <div class="invalid-feedback" style="display: block; margin-top: 0.5rem;">{{ $message }}</div>
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
                        <a href="{{ route('teacher.courses.show', $course->id) }}" 
                           style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-eye"></i>
                            View Course
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
            <form id="delete-form" action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" style="display: none;">
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
                            // Calculate enrolled students count
                            $enrollmentCount = \App\Models\Enrollment::where('course_id', $course->id)
                                ->where('status', 'active')
                                ->count();
                        @endphp
                        {{ $enrollmentCount }}
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
                <div class="card-title">Course Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('teacher.courses.show', $course->id) }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
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
                <a href="{{ route('teacher.enrollments') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #166534;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">View Enrollments</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">See all students</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add visual feedback for radio selections
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        const parent = radio.closest('label');
        radio.addEventListener('change', function() {
            // Reset all borders in the group
            const groupName = this.name;
            document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                r.closest('label').style.borderColor = 'var(--border)';
                r.closest('label').style.backgroundColor = 'transparent';
            });
            
            // Highlight selected
            if (this.checked) {
                parent.style.borderColor = 'var(--primary)';
                parent.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
            }
        });
        
        // Initialize selected state
        if (radio.checked) {
            parent.style.borderColor = 'var(--primary)';
            parent.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
        }
    });
});
</script>
@endpush
@endsection