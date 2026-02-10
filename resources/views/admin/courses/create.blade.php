@extends('layouts.admin')

@section('title', 'Create New Course - Admin Dashboard')

@push('styles')
<style>
    /* Form Container */
    .form-container {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.125rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }

    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer-modern {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    /* Form Sections */
    .form-section {
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .form-section-title i {
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.875rem;
    }
    
    .required {
        color: var(--danger);
        margin-left: 0.25rem;
    }
    
    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: white;
    }
    
    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }
    
    .form-input.error,
    .form-textarea.error,
    .form-select.error {
        border-color: var(--danger);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    .form-hint {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .form-error {
        font-size: 0.75rem;
        color: var(--danger);
        margin-top: 0.25rem;
    }
    
    .input-with-unit {
        position: relative;
    }
    
    .input-with-unit .form-input {
        padding-right: 4rem;
    }
    
    .input-unit {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-500);
        font-size: 0.875rem;
    }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }
    
    .btn-secondary:hover {
        background: var(--gray-200);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }
    
    /* Alerts */
    .alert {
        padding: 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .alert i {
        font-size: 1rem;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }
    
    .alert-success {
        background: var(--success-light);
        color: var(--success-dark);
        border: 1px solid var(--success);
    }
    
    .alert-error {
        background: var(--danger-light);
        color: var(--danger-dark);
        border: 1px solid var(--danger);
    }
    
    .alert-error ul {
        margin: 0.5rem 0 0 1rem;
        padding: 0;
    }
    
    .alert-error li {
        margin-bottom: 0.25rem;
    }
    
    .alert-error li:last-child {
        margin-bottom: 0;
    }

    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        gap: 1rem;
    }

    .action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--primary-dark);
        border: 1px solid var(--primary);
        transition: all 0.2s ease;
        cursor: pointer;
        width: 100%;
        text-align: left;
        border: none;
    }

    .action-card:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .action-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-sm);
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        color: var(--primary);
        transition: all 0.2s ease;
    }

    .action-card:hover .action-icon {
        background: white;
    }

    .action-content {
        flex: 1;
        min-width: 0;
    }

    .action-title {
        font-weight: 600;
        color: inherit;
        font-size: 0.9375rem;
        margin-bottom: 0.125rem;
    }

    .action-subtitle {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .action-arrow {
        color: inherit;
        font-size: 0.875rem;
        opacity: 0.7;
    }

    /* Guidelines */
    .guidelines-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .guideline-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
    }
    
    .guideline-icon {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-sm);
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    
    .guideline-content {
        flex: 1;
        min-width: 0;
    }
    
    .guideline-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }
    
    .guideline-text {
        font-size: 0.75rem;
        color: var(--gray-600);
        line-height: 1.4;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Course Creation Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-book-medical card-icon"></i>
                <h2 class="card-title">Create New Course</h2>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>
        
        <div class="card-body">
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
            
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            <form action="{{ route('admin.courses.store') }}" method="POST" id="course-form">
                @csrf
                
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Course Information
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title" class="form-label">
                                Course Title <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required
                                   placeholder="e.g., Introduction to Programming"
                                   class="form-input @error('title') error @enderror">
                            @error('title')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="course_code" class="form-label">
                                Course Code <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   id="course_code" 
                                   name="course_code" 
                                   value="{{ old('course_code') }}" 
                                   required
                                   placeholder="e.g., CS101"
                                   class="form-input @error('course_code') error @enderror">
                            <div class="form-hint">
                                <i class="fas fa-lightbulb"></i> Will auto-generate based on title
                            </div>
                            @error('course_code')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">
                            Course Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Enter a detailed description of the course..."
                                  class="form-textarea @error('description') error @enderror">{{ old('description') }}</textarea>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i> Describe what students will learn
                        </div>
                        @error('description')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-cog"></i>
                        Course Details
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="teacher_id" class="form-label">
                                Assign Teacher
                            </label>
                            <select id="teacher_id" 
                                    name="teacher_id"
                                    class="form-select @error('teacher_id') error @enderror">
                                <option value="">-- Select Teacher (Optional) --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->f_name }} {{ $teacher->l_name }} 
                                        @if($teacher->email)
                                            ({{ $teacher->email }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">
                                <i class="fas fa-user-tie"></i> Can be assigned later
                            </div>
                            @error('teacher_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="credits" class="form-label">
                                Credits <span class="required">*</span>
                            </label>
                            <div class="input-with-unit">
                                <input type="number" 
                                       id="credits" 
                                       name="credits" 
                                       value="{{ old('credits', 3) }}" 
                                       min="0" 
                                       step="0.5"
                                       required
                                       class="form-input @error('credits') error @enderror">
                                <span class="input-unit">credits</span>
                            </div>
                            @error('credits')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="learning_outcomes" class="form-label">
                            Learning Outcomes
                        </label>
                        <textarea id="learning_outcomes" 
                                  name="learning_outcomes" 
                                  rows="2"
                                  placeholder="What specific skills or knowledge will students gain?"
                                  class="form-textarea @error('learning_outcomes') error @enderror">{{ old('learning_outcomes') }}</textarea>
                        <div class="form-hint">
                            <i class="fas fa-graduation-cap"></i> Optional: List key learning objectives
                        </div>
                        @error('learning_outcomes')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Hidden fields -->
                <input type="hidden" name="status" value="active">
                <input type="hidden" name="is_published" value="1">
            </form>
        </div>
        
        <div class="card-footer-modern">
            <div class="form-actions">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
                <button type="submit" form="course-form" class="btn btn-primary" id="submit-button">
                    <i class="fas fa-save"></i>
                    Create Course
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-bolt card-icon"></i>
                <h2 class="card-title">Quick Actions</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div class="quick-actions-grid">
                <a href="{{ route('admin.courses.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">View All Courses</div>
                        <div class="action-subtitle">Browse existing courses</div>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
                
                <button onclick="document.getElementById('course-form').reset()" class="action-card">
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
                
                <a href="{{ route('admin.courses.create') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Refresh Data</div>
                        <div class="action-subtitle">Reload teacher list</div>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Guidelines Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-clipboard-check card-icon"></i>
                <h2 class="card-title">Guidelines</h2>
            </div>
        </div>
        
        <div class="card-body">
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
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="guideline-content">
                        <div class="guideline-title">Course Code</div>
                        <div class="guideline-text">Use standard format like CS101, MATH201</div>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="guideline-content">
                        <div class="guideline-title">Teacher Assignment</div>
                        <div class="guideline-text">Can be assigned now or later</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const codeInput = document.getElementById('course_code');
        const submitButton = document.getElementById('submit-button');
        
        // Auto-generate course code suggestion
        titleInput.addEventListener('input', function() {
            const title = this.value.trim();
            
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
        
        // Form validation and submission
        const courseForm = document.getElementById('course-form');
        if (courseForm) {
            courseForm.addEventListener('submit', function(e) {
                const title = titleInput.value.trim();
                const code = codeInput.value.trim();
                const credits = document.getElementById('credits').value;
                
                // Basic validation
                if (!title) {
                    e.preventDefault();
                    showToast('Please enter a course title.', 'error');
                    titleInput.focus();
                    return;
                }
                
                if (!code) {
                    e.preventDefault();
                    showToast('Please enter a course code.', 'error');
                    codeInput.focus();
                    return;
                }
                
                if (!credits || parseFloat(credits) <= 0) {
                    e.preventDefault();
                    showToast('Please enter valid credits (greater than 0).', 'error');
                    document.getElementById('credits').focus();
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
        
        // Toast notification function
        function showToast(message, type = 'info') {
            // Remove existing toast if any
            const existingToast = document.querySelector('.custom-toast');
            if (existingToast) {
                existingToast.remove();
            }
            
            const toast = document.createElement('div');
            toast.className = 'custom-toast';
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: var(--radius-sm);
                background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--warning)'};
                color: white;
                z-index: 10000;
                box-shadow: var(--shadow-lg);
                animation: slideIn 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                max-width: 400px;
            `;
            
            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle';
            
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
        
        // Add CSS animations for toast
        if (!document.querySelector('#toast-animations')) {
            const style = document.createElement('style');
            style.id = 'toast-animations';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
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
</script>
@endpush