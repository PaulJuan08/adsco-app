@extends('layouts.admin')

@section('title', 'Course Details - Admin Dashboard')

@push('styles')
<style>
    .avatar-lg {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.5rem;
    }
    
    .stat-card {
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 1px solid var(--border);
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark);
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: var(--secondary);
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Course Details</h1>
        <p>View detailed information about this course</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Course Details Card -->
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
            
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem; text-align: center;">
                <div class="avatar-lg" style="background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); color: white; margin-bottom: 1rem;">
                    {{ strtoupper(substr($course->title, 0, 1)) }}
                </div>
                <h2 style="margin: 0.5rem 0; color: var(--dark);">{{ $course->title }}</h2>
                <p style="color: var(--secondary); margin-bottom: 1rem;">{{ $course->course_code }}</p>
                
                @if($course->is_published)
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 16px; background: #dcfce7; color: #166534; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-check-circle"></i>
                        Published
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 16px; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-clock"></i>
                        Draft
                    </span>
                @endif
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <h3 style="font-size: 1rem; color: var(--dark); margin-bottom: 1rem;">Course Information</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Course Title</div>
                        <div style="font-weight: 500; color: var(--dark);">{{ $course->title }}</div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Course Code</div>
                        <div style="font-weight: 500; color: var(--dark);">{{ $course->course_code }}</div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Description</div>
                        <div style="font-weight: 500; color: var(--dark);">
                            {{ $course->description ?: 'No description provided' }}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Credits</div>
                        <div style="font-weight: 500; color: var(--dark);">{{ $course->credits ?? 3 }}</div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div>
                    <h3 style="font-size: 1rem; color: var(--dark); margin-bottom: 1rem;">Course Details</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Course Status</div>
                        <div>
                            @if($course->is_published)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                    Published
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-clock" style="font-size: 10px;"></i>
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Course ID</div>
                        <div style="font-weight: 500; color: var(--dark);">#{{ $course->id }}</div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Created</div>
                        <div style="font-weight: 500; color: var(--dark);">
                            {{ $course->created_at->format('F d, Y \a\t h:i A') }}
                        </div>
                        <div style="color: var(--secondary); font-size: 0.75rem;">
                            {{ $course->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Last Updated</div>
                        <div style="font-weight: 500; color: var(--dark);">
                            {{ $course->updated_at->format('F d, Y \a\t h:i A') }}
                        </div>
                        <div style="color: var(--secondary); font-size: 0.75rem;">
                            {{ $course->updated_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    @if($course->teacher)
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 4px;">Instructor</div>
                        <div style="font-weight: 500; color: var(--dark);">
                            {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                        </div>
                        <div style="color: var(--secondary); font-size: 0.75rem;">
                            {{ $course->teacher->email }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Course Actions -->
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <div style="color: var(--secondary); font-size: 0.875rem; margin-bottom: 1rem;">Course Actions</div>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="{{ route('admin.courses.edit', $course->id) }}" 
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #e0e7ff; color: var(--primary); border-radius: 6px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-edit"></i>
                        Edit Course
                    </a>
                    
                    <a href="{{ route('admin.courses.index') }}" 
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </a>
                    
                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to delete this course?')) { this.parentElement.submit(); }"
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #fee2e2; color: var(--danger); border-radius: 6px; border: none; font-weight: 500; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                            Delete Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Stats -->
    <div>
        <!-- Quick Stats -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Stats</div>
            </div>
            <div style="padding: 0.5rem;">
                <div class="stat-card" style="margin-bottom: 1rem;">
                    <div class="stat-number">
                        @php
                            $studentCount = $course->students ? $course->students->count() : 0;
                        @endphp
                        {{ $studentCount }}
                    </div>
                    <div class="stat-label">Enrolled Students</div>
                </div>
                
                <div class="stat-card" style="margin-bottom: 1rem;">
                    <div class="stat-number">{{ $course->credits ?? 3 }}</div>
                    <div class="stat-label">Course Credits</div>
                </div>
                
                <div class="stat-card" style="margin-bottom: 1rem;">
                    <div class="stat-number">
                        @if($course->is_published)
                            <span style="color: #10b981;">Published</span>
                        @else
                            <span style="color: #f59e0b;">Draft</span>
                        @endif
                    </div>
                    <div class="stat-label">Status</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        {{ $course->created_at->diffForHumans(null, true) }}
                    </div>
                    <div class="stat-label">Course Age</div>
                </div>
            </div>
        </div>
        
        <!-- Instructor Card -->
        @if($course->teacher)
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Instructor</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.25rem;">
                            {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--dark);">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                            <div style="color: var(--secondary); font-size: 0.75rem; margin-bottom: 4px;">{{ $course->teacher->email }}</div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($course->teacher->employee_id)
                                <span style="padding: 2px 8px; background: #e0e7ff; color: var(--primary); border-radius: 4px; font-size: 0.7rem; font-weight: 500;">
                                    {{ $course->teacher->employee_id }}
                                </span>
                                @endif
                                <span style="padding: 2px 8px; background: #dcfce7; color: #166534; border-radius: 4px; font-size: 0.7rem; font-weight: 500;">
                                    Teacher
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Quick Links -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick Links</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('admin.courses.edit', $course->id) }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Edit Course</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Update course details</div>
                    </div>
                </a>
                <a href="{{ route('admin.courses.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">All Courses</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">View all courses</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection