@extends('layouts.admin')

@section('title', 'Create New College - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/colleges-form.css') }}">
@endpush

@section('content')
    <!-- Create College Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-university card-icon"></i>
                <h2 class="card-title">Create New College</h2>
            </div>
            <a href="{{ route('admin.colleges.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Colleges
            </a>
        </div>
        
        <div class="card-body">
            <!-- College Preview - Live Preview -->
            <div class="course-preview">
                <div class="course-preview-avatar" id="previewAvatar" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <span id="avatarLetter">📚</span>
                </div>
                <div class="course-preview-title" id="previewTitle">New College</div>
                <div class="course-preview-code" id="previewYears">---</div>
                <div class="course-preview-status" id="previewStatus">
                    <i class="fas fa-check-circle"></i> Active
                </div>
            </div>

            <!-- Display validation errors -->
            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            <!-- Two Column Layout - Form and Sidebar Inline -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <form action="{{ route('admin.colleges.store') }}" method="POST" id="collegeForm">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic College Information
                            </div>
                            
                            <div class="form-group">
                                <label for="college_name" class="form-label">
                                    <i class="fas fa-heading"></i> College Name
                                    <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="college_name" 
                                       name="college_name" 
                                       value="{{ old('college_name') }}" 
                                       required
                                       placeholder="e.g., College of Engineering, College of Education"
                                       class="form-input @error('college_name') error @enderror">
                                @error('college_name')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="college_year" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Available Year Levels
                                    <span class="required">*</span>
                                </label>
                                
                                <div class="year-input-group">
                                    <input type="text" 
                                           id="college_year" 
                                           name="college_year" 
                                           value="{{ old('college_year', '1st Year,2nd Year,3rd Year,4th Year') }}" 
                                           required
                                           placeholder="e.g., 1st Year,2nd Year,3rd Year,4th Year"
                                           class="form-input @error('college_year') error @enderror">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Separate years with commas (e.g., 1st Year,2nd Year,3rd Year,4th Year)
                                    </div>
                                </div>
                                
                                <!-- Year Chips for Quick Selection -->
                                <div class="year-chips">
                                    <span class="year-chip" onclick="addYear('1st Year')">+ 1st Year</span>
                                    <span class="year-chip" onclick="addYear('2nd Year')">+ 2nd Year</span>
                                    <span class="year-chip" onclick="addYear('3rd Year')">+ 3rd Year</span>
                                    <span class="year-chip" onclick="addYear('4th Year')">+ 4th Year</span>
                                    <span class="year-chip" onclick="addYear('5th Year')">+ 5th Year</span>
                                </div>
                                
                                @error('college_year')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> College Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Enter a detailed description of the college..."
                                          class="form-textarea @error('description') error @enderror">{{ old('description') }}</textarea>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Describe the college, its programs, and objectives
                                </div>
                                @error('description')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Status Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-cog"></i> College Status
                            </div>
                            
                            <div class="form-group">
                                <label for="status" class="form-label">
                                    <i class="fas fa-toggle-on"></i> Status
                                    <span class="required">*</span>
                                </label>
                                <select id="status" 
                                        name="status"
                                        class="form-select @error('status') error @enderror">
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Active colleges (1) can have enrolled students. Inactive colleges (0) won't appear in registration.
                                </div>
                                @error('status')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Status Notice -->
                        <div class="status-notice">
                            <i class="fas fa-info-circle"></i>
                            <div class="status-notice-content">
                                <div class="status-notice-title">College Status</div>
                                <div class="status-notice-text">
                                    New colleges are created as <strong>Active (1)</strong> by default. 
                                    They will be available for student registration immediately.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Right Column - Guidelines Sidebar -->
                <div class="sidebar-column">
                    <!-- Guidelines Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i> Guidelines
                        </div>
                        
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-asterisk"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Required Fields</div>
                                    <div class="guideline-text">Fields marked with * are mandatory</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">College Name</div>
                                    <div class="guideline-text">Use full official name of the college</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Year Levels</div>
                                    <div class="guideline-text">Comma-separated list of available years</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-toggle-on"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Status (1/0)</div>
                                    <div class="guideline-text">1 = Active, 0 = Inactive (inactive won't appear in registration)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <div class="quick-actions-grid">
                            <a href="{{ route('admin.colleges.index') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View All Colleges</div>
                                    <div class="action-subtitle">Browse existing colleges</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                            
                            <button type="button" onclick="resetForm()" class="action-card" style="width: 100%; border: none; background: #f8fafc; text-align: left; cursor: pointer;">
                                <div class="action-icon">
                                    <i class="fas fa-eraser"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Clear Form</div>
                                    <div class="action-subtitle">Reset all fields</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </button>
                            
                            <button type="button" onclick="setDefaultYears()" class="action-card" style="width: 100%; border: none; background: #f8fafc; text-align: left; cursor: pointer;">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Default Years</div>
                                    <div class="action-subtitle">Set standard 4-year program</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer-modern">
            <div class="form-actions">
                <a href="{{ route('admin.colleges.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" form="collegeForm" class="btn btn-primary" id="submitButton">
                    <i class="fas fa-save"></i> Create College
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('college_name');
        const yearsInput = document.getElementById('college_year');
        const statusSelect = document.getElementById('status');
        const previewTitle = document.getElementById('previewTitle');
        const previewYears = document.getElementById('previewYears');
        const avatarLetter = document.getElementById('avatarLetter');
        const previewStatus = document.getElementById('previewStatus');
        const submitButton = document.getElementById('submitButton');
        
        // Live preview update
        function updatePreview() {
            // Update title
            const name = nameInput.value.trim();
            previewTitle.textContent = name || 'New College';
            
            // Update years
            const years = yearsInput.value.trim();
            previewYears.textContent = years ? (years.length > 30 ? years.substring(0, 30) + '...' : years) : '---';
            
            // Update avatar
            if (name) {
                avatarLetter.textContent = name.charAt(0).toUpperCase();
            } else {
                avatarLetter.textContent = '📚';
            }
            
            // Update status (1 = active, 0 = inactive)
            const status = statusSelect.value;
            if (status === '1') {
                previewStatus.innerHTML = '<i class="fas fa-check-circle"></i> Active';
                previewStatus.className = 'course-preview-status status-published';
            } else {
                previewStatus.innerHTML = '<i class="fas fa-clock"></i> Inactive';
                previewStatus.className = 'course-preview-status status-draft';
            }
        }
        
        nameInput.addEventListener('input', updatePreview);
        yearsInput.addEventListener('input', updatePreview);
        statusSelect.addEventListener('change', updatePreview);
        
        // Initial preview update
        updatePreview();
        
        // Form validation and submission
        const collegeForm = document.getElementById('collegeForm');
        if (collegeForm) {
            collegeForm.addEventListener('submit', function(e) {
                const name = nameInput.value.trim();
                const years = yearsInput.value.trim();
                
                let isValid = true;
                
                if (!name) {
                    nameInput.classList.add('error');
                    isValid = false;
                } else {
                    nameInput.classList.remove('error');
                }
                
                if (!years) {
                    yearsInput.classList.add('error');
                    isValid = false;
                } else {
                    yearsInput.classList.remove('error');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    showToast('Please fill in all required fields.', 'error');
                    return;
                }
                
                // Show loading state
                if (submitButton) {
                    const originalHTML = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                    submitButton.disabled = true;
                    
                    // Revert after 5 seconds (in case submission fails)
                    setTimeout(() => {
                        submitButton.innerHTML = originalHTML;
                        submitButton.disabled = false;
                    }, 5000);
                }
            });
        }
        
        // Show validation errors if any
        @if($errors->any())
            showToast('Please fix the errors in the form.', 'error');
        @endif
        
        // Show success message if redirected with success
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
    });
    
    // Function to add year to input
    function addYear(year) {
        const yearsInput = document.getElementById('college_year');
        let currentYears = yearsInput.value.trim();
        
        if (currentYears === '') {
            yearsInput.value = year;
        } else {
            // Check if year already exists
            const years = currentYears.split(',').map(y => y.trim());
            if (!years.includes(year)) {
                yearsInput.value = currentYears + ', ' + year;
            } else {
                showToast(year + ' already exists in the list.', 'info');
            }
        }
        
        // Trigger preview update
        yearsInput.dispatchEvent(new Event('input'));
        
        // Highlight the chip
        const chips = document.querySelectorAll('.year-chip');
        chips.forEach(chip => {
            if (chip.textContent.includes(year)) {
                chip.classList.add('selected');
                chip.innerHTML = year + ' <i class="fas fa-check"></i>';
                setTimeout(() => {
                    chip.classList.remove('selected');
                    chip.innerHTML = '+ ' + year;
                }, 1000);
            }
        });
    }
    
    // Set default years (4-year program)
    function setDefaultYears() {
        document.getElementById('college_year').value = '1st Year, 2nd Year, 3rd Year, 4th Year';
        document.getElementById('college_year').dispatchEvent(new Event('input'));
        showToast('Default years set for 4-year program.', 'success');
    }
    
    // Reset form function
    function resetForm() {
        document.getElementById('collegeForm').reset();
        document.getElementById('previewTitle').textContent = 'New College';
        document.getElementById('previewYears').textContent = '---';
        document.getElementById('avatarLetter').textContent = '📚';
        
        // Reset status preview to Active (1)
        const previewStatus = document.getElementById('previewStatus');
        previewStatus.innerHTML = '<i class="fas fa-check-circle"></i> Active';
        previewStatus.className = 'course-preview-status status-published';
        
        // Clear error states
        document.querySelectorAll('.form-input, .form-textarea, .form-select').forEach(el => {
            el.classList.remove('error');
        });
        
        showToast('Form has been cleared.', 'info');
    }
    
    // Toast notification function
    window.showToast = function(message, type = 'info') {
        // Remove existing toast if any
        const existingToast = document.querySelector('.custom-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        
        toast.innerHTML = `
            <i class="fas fa-${icon}" style="font-size: 1.25rem;"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
</script>
@endpush