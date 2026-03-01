@extends('layouts.admin')

@section('title', 'Edit Program - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/programs-form.css') }}">
@endpush

@section('content')
    <!-- Edit Program Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-graduation-cap card-icon"></i>
                <h2 class="card-title">Edit Program</h2>
            </div>
            <a href="{{ route('admin.programs.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Programs
            </a>
        </div>
        
        <div class="card-body">
            <!-- Program Preview -->
            <div class="course-preview">
                <div class="course-preview-avatar" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    {{ strtoupper(substr($program->program_name, 0, 1)) }}
                </div>
                <div class="course-preview-title">{{ $program->program_name }}</div>
                <div class="course-preview-code">{{ $program->program_code ?? '—' }}</div>
                <div class="course-preview-status {{ $program->status == 1 ? 'status-published' : 'status-draft' }}">
                    <i class="fas {{ $program->status == 1 ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $program->status == 1 ? 'Active' : 'Inactive' }}
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
                    <form action="{{ route('admin.programs.update', Crypt::encrypt($program->id)) }}" method="POST" id="updateForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Program Information
                            </div>
                            
                            <div class="form-group">
                                <label for="college_id" class="form-label required">
                                    <i class="fas fa-university"></i> College
                                </label>
                                <select id="college_id" 
                                        name="college_id"
                                        class="form-select @error('college_id') is-invalid @enderror"
                                        required>
                                    <option value="">Select College</option>
                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}" {{ old('college_id', $program->college_id) == $college->id ? 'selected' : '' }}>
                                            {{ $college->college_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('college_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="program_name" class="form-label required">
                                    <i class="fas fa-heading"></i> Program Name
                                </label>
                                <input type="text" 
                                       id="program_name" 
                                       name="program_name" 
                                       value="{{ old('program_name', $program->program_name) }}" 
                                       required
                                       class="form-control @error('program_name') is-invalid @enderror"
                                       placeholder="e.g., Bachelor of Science in Computer Science">
                                @error('program_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="program_code" class="form-label">
                                    <i class="fas fa-code"></i> Program Code
                                </label>
                                <input type="text" 
                                       id="program_code" 
                                       name="program_code" 
                                       value="{{ old('program_code', $program->program_code) }}" 
                                       class="form-control @error('program_code') is-invalid @enderror"
                                       placeholder="e.g., BSCS, BSIT, BSCE">
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Optional: Short code for the program
                                </div>
                                @error('program_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Program Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Enter program description...">{{ old('description', $program->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Optional: Provide a detailed description of the program
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-cog"></i> Program Status
                            </div>
                            
                            <div class="form-group">
                                <label for="status" class="form-label required">
                                    <i class="fas fa-toggle-on"></i> Status
                                </label>
                                <select id="status" 
                                        name="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', $program->status) == 1 ? 'selected' : '' }}>Active (1)</option>
                                    <option value="0" {{ old('status', $program->status) == 0 ? 'selected' : '' }}>Inactive (0)</option>
                                </select>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> 1 = Active, 0 = Inactive
                                </div>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Status Notice -->
                        <div class="status-notice">
                            <i class="fas fa-info-circle"></i>
                            <div class="status-notice-content">
                                <div class="status-notice-title">Program Status</div>
                                <div class="status-notice-text">
                                    This program is <strong>{{ $program->status == 1 ? 'Active (1)' : 'Inactive (0)' }}</strong>. 
                                    @if($program->status == 1)
                                        Active programs are available for student registration.
                                    @else
                                        Inactive programs won't appear in registration forms.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Right Column - Program Information Sidebar -->
                <div class="sidebar-column">
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> Program Information
                        </div>
                        
                        <!-- Statistics -->
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hashtag"></i> Program ID</span>
                            <span class="info-value">#{{ $program->id }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Created</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $program->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $program->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $program->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $program->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-users"></i> Enrolled Students</span>
                            <span class="info-value">{{ $program->students_count ?? 0 }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-university"></i> College</span>
                            <span class="info-value">{{ $program->college->college_name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <div class="quick-actions-grid">
                            <a href="{{ route('admin.programs.show', Crypt::encrypt($program->id)) }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View Program</div>
                                    <div class="action-subtitle">See program details</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.colleges.show', Crypt::encrypt($program->college_id)) }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View College</div>
                                    <div class="action-subtitle">See parent college</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <div>
                    <form action="{{ route('admin.programs.destroy', Crypt::encrypt($program->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" id="deleteButton">
                            <i class="fas fa-trash-alt"></i> Delete Program
                        </button>
                    </form>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.programs.show', Crypt::encrypt($program->id)) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" form="updateForm" class="btn btn-primary" id="submitButton">
                        <i class="fas fa-save"></i> Update Program
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
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Program?',
                    text: 'This action cannot be undone. All program data will be permanently removed.',
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
                const name = document.getElementById('program_name').value.trim();
                const collegeId = document.getElementById('college_id').value;
                
                let isValid = true;
                
                if (!name) {
                    document.getElementById('program_name').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!collegeId) {
                    document.getElementById('college_id').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please fill in all required fields.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                
                // Show loading state
                const submitBtn = document.getElementById('submitButton');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
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

    function showNotification(message, type = 'info') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: type,
            title: message
        });
    }
</script>
@endpush