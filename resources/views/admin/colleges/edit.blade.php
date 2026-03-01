@extends('layouts.admin')

@section('title', 'Edit College - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/colleges-form.css') }}">
@endpush

@section('content')
    <!-- Edit College Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-university card-icon"></i>
                <h2 class="card-title">Edit College</h2>
            </div>
            <a href="{{ route('admin.colleges.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Colleges
            </a>
        </div>
        
        <div class="card-body">
            <!-- College Preview -->
            <div class="course-preview">
                <div class="course-preview-avatar" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    {{ strtoupper(substr($college->college_name, 0, 1)) }}
                </div>
                <div class="course-preview-title">{{ $college->college_name }}</div>
                <div class="course-preview-code">{{ Str::limit($college->college_year, 30) }}</div>
                <div class="course-preview-status {{ $college->status == 1 ? 'status-published' : 'status-draft' }}">
                    <i class="fas {{ $college->status == 1 ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $college->status == 1 ? 'Active' : 'Inactive' }}
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
                    <form action="{{ route('admin.colleges.update', urlencode(Crypt::encrypt($college->id))) }}" method="POST" id="updateForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic College Information
                            </div>
                            
                            <div class="form-group">
                                <label for="college_name" class="form-label required">
                                    <i class="fas fa-heading"></i> College Name
                                </label>
                                <input type="text" 
                                       id="college_name" 
                                       name="college_name" 
                                       value="{{ old('college_name', $college->college_name) }}" 
                                       required
                                       class="form-control @error('college_name') is-invalid @enderror"
                                       placeholder="e.g., College of Engineering">
                                @error('college_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="college_year" class="form-label required">
                                    <i class="fas fa-calendar-alt"></i> Available Year Levels
                                </label>
                                <input type="text" 
                                       id="college_year" 
                                       name="college_year" 
                                       value="{{ old('college_year', $college->college_year) }}" 
                                       required
                                       class="form-control @error('college_year') is-invalid @enderror"
                                       placeholder="e.g., 1st Year,2nd Year,3rd Year,4th Year">
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Separate years with commas
                                </div>
                                @error('college_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Year Management -->
                            <div class="year-management">
                                <div class="year-management-title">Manage Year Levels</div>
                                
                                <div class="year-add-form">
                                    <input type="text" id="newYearInput" class="year-add-input" placeholder="Enter new year (e.g., 1st Year)">
                                    <button type="button" class="year-add-btn" onclick="addNewYear()">Add Year</button>
                                </div>
                                
                                <div class="current-years" id="currentYears">
                                    @foreach(explode(',', $college->college_year) as $year)
                                        @if(trim($year))
                                        <span class="current-year">
                                            {{ trim($year) }}
                                            <span class="remove-year" onclick="removeYear('{{ trim($year) }}')">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </span>
                                        @endif
                                    @endforeach
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
                                          placeholder="Enter college description...">{{ old('description', $college->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Optional: Provide a detailed description of the college
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-cog"></i> College Status
                            </div>
                            
                            <div class="form-group">
                                <label for="status" class="form-label required">
                                    <i class="fas fa-toggle-on"></i> Status
                                </label>
                                <select id="status" 
                                        name="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', $college->status) == 1 ? 'selected' : '' }}>Active (1)</option>
                                    <option value="0" {{ old('status', $college->status) == 0 ? 'selected' : '' }}>Inactive (0)</option>
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
                                <div class="status-notice-title">College Status</div>
                                <div class="status-notice-text">
                                    This college is <strong>{{ $college->status == 1 ? 'Active (1)' : 'Inactive (0)' }}</strong>. 
                                    @if($college->status == 1)
                                        Active colleges are available for student registration.
                                    @else
                                        Inactive colleges won't appear in registration forms.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Right Column - College Information Sidebar -->
                <div class="sidebar-column">
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> College Information
                        </div>
                        
                        <!-- Statistics -->
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hashtag"></i> College ID</span>
                            <span class="info-value">#{{ $college->id }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Created</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $college->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $college->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $college->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $college->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-users"></i> Enrolled Students</span>
                            <span class="info-value">{{ $college->students_count ?? 0 }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-tag"></i> Year Levels</span>
                            <span class="info-value">{{ count(explode(',', $college->college_year)) }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-toggle-on"></i> Status Value</span>
                            <span class="info-value">{{ $college->status == 1 ? '1 (Active)' : '0 (Inactive)' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <div>
                    <form action="{{ route('admin.colleges.destroy', urlencode(Crypt::encrypt($college->id))) }}" method="POST" id="deleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" id="deleteButton">
                            <i class="fas fa-trash-alt"></i> Delete College
                        </button>
                    </form>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.colleges.show', urlencode(Crypt::encrypt($college->id))) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" form="updateForm" class="btn btn-primary" id="submitButton">
                        <i class="fas fa-save"></i> Update College
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
                    title: 'Delete College?',
                    text: 'This action cannot be undone. All college data will be permanently removed.',
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
                const name = document.getElementById('college_name').value.trim();
                const years = document.getElementById('college_year').value.trim();
                
                let isValid = true;
                
                if (!name) {
                    document.getElementById('college_name').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!years) {
                    document.getElementById('college_year').classList.add('is-invalid');
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
                
                // Re-enable after timeout (in case form doesn't redirect)
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

    // Year management functions
    function addNewYear() {
        const input = document.getElementById('newYearInput');
        const yearValue = input.value.trim();
        
        if (!yearValue) {
            showNotification('Please enter a year level', 'warning');
            return;
        }
        
        const yearsInput = document.getElementById('college_year');
        let currentYears = yearsInput.value;
        
        // Split by comma and clean up
        let years = currentYears.split(',').map(y => y.trim()).filter(y => y !== '');
        
        // Check if year already exists
        if (years.includes(yearValue)) {
            showNotification('This year already exists in the list', 'warning');
            input.value = '';
            return;
        }
        
        // Add new year
        years.push(yearValue);
        yearsInput.value = years.join(', ');
        
        // Update current years display
        updateYearsDisplay(years);
        
        // Clear input
        input.value = '';
        
        showNotification('Year added successfully', 'success');
    }

    function removeYear(yearToRemove) {
        const yearsInput = document.getElementById('college_year');
        let years = yearsInput.value.split(',').map(y => y.trim()).filter(y => y !== '');
        
        // Filter out the year to remove
        years = years.filter(y => y !== yearToRemove);
        
        if (years.length === 0) {
            showNotification('College must have at least one year level', 'warning');
            return;
        }
        
        yearsInput.value = years.join(', ');
        
        // Update current years display
        updateYearsDisplay(years);
        
        showNotification('Year removed', 'info');
    }

    function updateYearsDisplay(years) {
        const container = document.getElementById('currentYears');
        container.innerHTML = years.map(year => `
            <span class="current-year">
                ${year}
                <span class="remove-year" onclick="removeYear('${year}')">
                    <i class="fas fa-times"></i>
                </span>
            </span>
        `).join('');
    }

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