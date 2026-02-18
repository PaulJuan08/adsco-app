@extends('layouts.registrar')

@section('title', 'Edit User - Registrar Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-form.css') }}">
@endpush

@section('content')
    <!-- Edit User Form Card - Smaller Container with Better Spacing -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-user-edit card-icon"></i>
                <h2 class="card-title">Edit User Profile</h2>
            </div>
            <a href="{{ route('registrar.users.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
        
        <div class="card-body">
            <!-- User Preview - More Compact -->
            <div class="user-preview">
                <div class="user-preview-avatar">
                    {{ strtoupper(substr($user->f_name, 0, 1)) }}{{ strtoupper(substr($user->l_name, 0, 1)) }}
                </div>
                <div class="user-preview-name">{{ $user->f_name }} {{ $user->l_name }}</div>
                <div class="user-preview-email">{{ $user->email }}</div>
                <div class="user-preview-role">
                    <i class="fas fa-user-tag"></i>
                    @php
                        $roleDisplay = match($user->role) {
                            3 => 'Teacher',
                            4 => 'Student',
                            default => 'Unknown'
                        };
                    @endphp
                    {{ $roleDisplay }}
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
            
            <!-- Form with encrypted ID -->
            <form action="{{ route('registrar.users.update', $encryptedId) }}" method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                
                <!-- Personal Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-id-card"></i> Personal Information
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="f_name" class="form-label required">
                                <i class="fas fa-user"></i> First Name
                            </label>
                            <input type="text" 
                                   id="f_name" 
                                   name="f_name" 
                                   value="{{ old('f_name', $user->f_name) }}" 
                                   required
                                   class="form-control @error('f_name') is-invalid @enderror"
                                   placeholder="John">
                            @error('f_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="l_name" class="form-label required">
                                <i class="fas fa-user"></i> Last Name
                            </label>
                            <input type="text" 
                                   id="l_name" 
                                   name="l_name" 
                                   value="{{ old('l_name', $user->l_name) }}" 
                                   required
                                   class="form-control @error('l_name') is-invalid @enderror"
                                   placeholder="Doe">
                            @error('l_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label required">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="john.doe@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Used for login
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="age" class="form-label">
                                <i class="fas fa-birthday-cake"></i> Age
                            </label>
                            <input type="number" 
                                   id="age" 
                                   name="age" 
                                   value="{{ old('age', $user->age) }}"
                                   min="15"
                                   max="100"
                                   class="form-control @error('age') is-invalid @enderror"
                                   placeholder="25">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="sex" class="form-label">
                                <i class="fas fa-venus-mars"></i> Gender
                            </label>
                            <select id="sex" 
                                    name="sex"
                                    class="form-select @error('sex') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('sex', $user->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="contact" class="form-label">
                                <i class="fas fa-phone"></i> Contact Number
                            </label>
                            <input type="text" 
                                   id="contact" 
                                   name="contact" 
                                   value="{{ old('contact', $user->contact) }}"
                                   class="form-control @error('contact') is-invalid @enderror"
                                   placeholder="+63 912 345 6789">
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Account Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user-cog"></i> Account Settings
                    </div>
                    
                    <!-- Role Selection -->
                    <div class="form-group">
                        <label class="form-label required">
                            <i class="fas fa-user-tag"></i> User Role
                        </label>
                        <div class="role-grid">
                            @php
                                $roleOptions = [
                                    3 => ['name' => 'Teacher', 'icon' => 'chalkboard-teacher', 'color' => 'info', 'id_required' => true, 'description' => 'Manage classes and students', 'id_type' => 'employee_id'],
                                    4 => ['name' => 'Student', 'icon' => 'graduation-cap', 'color' => 'success', 'id_required' => true, 'description' => 'Enroll in courses and view materials', 'id_type' => 'student_id']
                                ];
                            @endphp
                            
                            @foreach($roleOptions as $key => $option)
                            <div class="role-option @if(old('role', $user->role) == $key) active @endif" 
                                 onclick="selectRole({{ $key }})"
                                 data-role="{{ $key }}"
                                 @if(in_array($user->role, [1,2]) && $user->role != $key) style="opacity: 0.5; pointer-events: none;" @endif>
                                <div class="role-icon" style="background: linear-gradient(135deg, var(--{{ $option['color'] }}), var(--{{ $option['color'] }}-dark)); color: white;">
                                    <i class="fas fa-{{ $option['icon'] }}"></i>
                                </div>
                                <div class="role-content">
                                    <div class="role-title">{{ $option['name'] }}</div>
                                    <div class="role-description">{{ $option['description'] }}</div>
                                    <div class="role-id-required">
                                        <i class="fas fa-id-card"></i>
                                        Requires {{ $option['id_type'] == 'employee_id' ? 'Employee ID' : 'Student ID' }}
                                    </div>
                                </div>
                                <div class="role-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <input type="radio" 
                                       name="role" 
                                       value="{{ $key }}" 
                                       id="role_{{ $key }}"
                                       class="d-none"
                                       @if(old('role', $user->role) == $key) checked @endif
                                       @if(in_array($user->role, [1,2]) && $user->role != $key) disabled @endif
                                       required>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="role" id="selectedRole" value="{{ old('role', $user->role) }}">
                        @error('role')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        @if(in_array($user->role, [1,2]))
                        <div class="form-text" style="color: var(--warning); margin-top: 0.5rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                            This user has a higher role ({{ $user->role == 1 ? 'Admin' : 'Registrar' }}) that cannot be changed by registrar.
                        </div>
                        @endif
                    </div>
                    
                    <!-- ID Fields (Conditional) -->
                    <div id="idFieldsSection" class="{{ in_array($user->role, [3,4]) ? '' : 'd-none' }}" style="{{ in_array($user->role, [3,4]) ? '' : 'display: none;' }}">
                        <div id="employeeIdGroup" class="id-field-group {{ $user->role == 3 ? '' : 'd-none' }}" style="{{ $user->role == 3 ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="employee_id" class="form-label {{ $user->role == 3 ? 'required' : '' }}">
                                    <i class="fas fa-id-badge"></i> Employee ID
                                </label>
                                <input type="text" 
                                       id="employee_id" 
                                       name="employee_id" 
                                       value="{{ old('employee_id', $user->employee_id) }}"
                                       {{ $user->role == 3 ? 'required' : '' }}
                                       class="form-control @error('employee_id') is-invalid @enderror"
                                       placeholder="TEA-2024-XXXX">
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="employeeIdSuggestion" class="id-suggestion" style="display: none;">
                                    <i class="fas fa-lightbulb"></i>
                                    <span>Suggested: </span>
                                    <strong id="suggestedEmployeeId"></strong>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Required for Teacher role
                                </div>
                            </div>
                        </div>
                        
                        <div id="studentIdGroup" class="id-field-group {{ $user->role == 4 ? '' : 'd-none' }}" style="{{ $user->role == 4 ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="student_id" class="form-label {{ $user->role == 4 ? 'required' : '' }}">
                                    <i class="fas fa-graduation-cap"></i> Student ID
                                </label>
                                <input type="text" 
                                       id="student_id" 
                                       name="student_id" 
                                       value="{{ old('student_id', $user->student_id) }}"
                                       {{ $user->role == 4 ? 'required' : '' }}
                                       class="form-control @error('student_id') is-invalid @enderror"
                                       placeholder="STU-2024-001">
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="studentIdSuggestion" class="id-suggestion" style="display: none;">
                                    <i class="fas fa-lightbulb"></i>
                                    <span>Suggested: </span>
                                    <strong id="suggestedStudentId"></strong>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Required for Student role
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Security Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-shield-alt"></i> Security Settings
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> New Password
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Leave blank to keep current"
                                   autocomplete="new-password">
                            <div class="password-strength-container">
                                <div class="password-strength-meter">
                                    <div class="password-strength-fill" id="passwordStrength"></div>
                                </div>
                                <div class="strength-text" id="strengthText"></div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Leave blank to keep current password
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock"></i> Confirm Password
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Confirm new password"
                                   autocomplete="new-password">
                            <div id="passwordMatch" class="form-text"></div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions - Like Admin Version -->
                <div class="form-actions" style="justify-content: space-between;">
                    <div>
                        <span class="creation-badge">
                            <i class="fas fa-calendar-alt"></i> Created: {{ $user->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="{{ route('registrar.users.show', $encryptedId) }}" class="btn btn-outline">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('registrar.users.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitButton">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Minimal additional styles */
    .d-none {
        display: none !important;
    }
    
    .creation-badge {
        font-size: 0.75rem;
        color: var(--secondary);
        background: #f7fafc;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .form-actions {
        margin-top: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize role selection
        const initialRole = document.querySelector('input[name="role"]:checked');
        if (initialRole) {
            selectRole(parseInt(initialRole.value));
        }
    });

    // Role selection handler
    function selectRole(roleId) {
        // Prevent role change if user is Admin or Registrar
        const currentUserRole = {{ $user->role }};
        if ([1, 2].includes(currentUserRole)) {
            return;
        }
        
        const roleRadio = document.getElementById('role_' + roleId);
        const selectedRoleInput = document.getElementById('selectedRole');
        const idFieldsSection = document.getElementById('idFieldsSection');
        const employeeIdGroup = document.getElementById('employeeIdGroup');
        const studentIdGroup = document.getElementById('studentIdGroup');
        const employeeIdInput = document.getElementById('employee_id');
        const studentIdInput = document.getElementById('student_id');
        
        // Update selected radio
        if (roleRadio) {
            roleRadio.checked = true;
            selectedRoleInput.value = roleId;
            
            // Update visual selection
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('active');
            });
            roleRadio.closest('.role-option').classList.add('active');
        }
        
        // Show/hide ID fields section based on role
        const shouldShowIdFields = [3, 4].includes(parseInt(roleId));
        
        if (shouldShowIdFields) {
            idFieldsSection.style.display = 'block';
            idFieldsSection.classList.remove('d-none');
        } else {
            idFieldsSection.style.display = 'none';
            idFieldsSection.classList.add('d-none');
        }
        
        // Show/hide specific ID fields
        if (roleId == 3) { // Teacher
            employeeIdGroup.style.display = 'block';
            employeeIdGroup.classList.remove('d-none');
            studentIdGroup.style.display = 'none';
            studentIdGroup.classList.add('d-none');
            
            // Set employee ID as required
            employeeIdInput.required = true;
            studentIdInput.required = false;
            
            // Generate suggestion if empty
            if (!employeeIdInput.value) {
                generateIdSuggestion('employee', roleId);
            }
        } else if (roleId == 4) { // Student
            employeeIdGroup.style.display = 'none';
            employeeIdGroup.classList.add('d-none');
            studentIdGroup.style.display = 'block';
            studentIdGroup.classList.remove('d-none');
            
            // Set student ID as required
            employeeIdInput.required = false;
            studentIdInput.required = true;
            
            // Generate suggestion if empty
            if (!studentIdInput.value) {
                generateIdSuggestion('student', roleId);
            }
        }
    }

    // Generate ID suggestion
    function generateIdSuggestion(type, roleId = null) {
        const firstName = document.getElementById('f_name').value;
        const lastName = document.getElementById('l_name').value;
        const selectedRole = roleId || parseInt(document.getElementById('selectedRole').value);
        
        if (firstName || lastName) {
            const initials = (firstName ? firstName.charAt(0).toUpperCase() : 'X') + 
                           (lastName ? lastName.charAt(0).toUpperCase() : 'X');
            const timestamp = Date.now().toString().slice(-4);
            const currentYear = new Date().getFullYear();
            
            if (type === 'employee') {
                const suggestion = `TEA-${currentYear}-${initials}${timestamp}`;
                
                const suggestionDiv = document.getElementById('employeeIdSuggestion');
                if (suggestionDiv) {
                    suggestionDiv.querySelector('#suggestedEmployeeId').textContent = suggestion;
                    suggestionDiv.style.display = 'inline-flex';
                }
            } else if (type === 'student') {
                const suggestion = `STU-${currentYear}-${timestamp.padStart(4, '0')}`;
                
                const suggestionDiv = document.getElementById('studentIdSuggestion');
                if (suggestionDiv) {
                    suggestionDiv.querySelector('#suggestedStudentId').textContent = suggestion;
                    suggestionDiv.style.display = 'inline-flex';
                }
            }
        }
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    
    if (passwordInput && passwordStrength && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            passwordStrength.className = 'password-strength-fill';
            
            if (password.length === 0) {
                strengthText.textContent = '';
            } else if (strength <= 2) {
                passwordStrength.classList.add('strength-weak');
                strengthText.innerHTML = '<i class="fas fa-exclamation-circle"></i> Weak';
                strengthText.style.color = '#f56565';
            } else if (strength <= 4) {
                passwordStrength.classList.add('strength-medium');
                strengthText.innerHTML = '<i class="fas fa-info-circle"></i> Medium';
                strengthText.style.color = '#ed8936';
            } else if (strength <= 5) {
                passwordStrength.classList.add('strength-good');
                strengthText.innerHTML = '<i class="fas fa-check-circle"></i> Good';
                strengthText.style.color = '#ecc94b';
            } else {
                passwordStrength.classList.add('strength-strong');
                strengthText.innerHTML = '<i class="fas fa-shield-alt"></i> Strong';
                strengthText.style.color = '#48bb78';
            }
        });
    }

    // Password confirmation check
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatch = document.getElementById('passwordMatch');
    
    if (confirmPasswordInput && passwordMatch) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword) {
                if (password === confirmPassword) {
                    passwordMatch.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                    passwordMatch.style.color = '#48bb78';
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    passwordMatch.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
                    passwordMatch.style.color = '#f56565';
                    this.setCustomValidity('Passwords do not match');
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            } else {
                passwordMatch.innerHTML = '';
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }

    // Auto-fill ID when name changes
    document.getElementById('f_name')?.addEventListener('input', function() {
        updateIdSuggestions();
    });

    document.getElementById('l_name')?.addEventListener('input', function() {
        updateIdSuggestions();
    });

    function updateIdSuggestions() {
        const role = parseInt(document.getElementById('selectedRole').value);
        if (role == 3) {
            generateIdSuggestion('employee', role);
        } else if (role == 4) {
            generateIdSuggestion('student', role);
        }
    }

    // Form submission validation
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const role = parseInt(document.getElementById('selectedRole').value);
        const employeeIdInput = document.getElementById('employee_id');
        const studentIdInput = document.getElementById('student_id');
        const password = document.getElementById('password').value;
        
        let isValid = true;
        let errorMessage = '';
        
        // Validate ID fields based on role
        if (role == 3 && employeeIdInput && !employeeIdInput.value.trim()) {
            employeeIdInput.classList.add('is-invalid');
            errorMessage = 'Employee ID is required for Teacher role';
            isValid = false;
        }
        
        if (role == 4 && studentIdInput && !studentIdInput.value.trim()) {
            studentIdInput.classList.add('is-invalid');
            errorMessage = 'Student ID is required for Student role';
            isValid = false;
        }
        
        // Validate password if entered
        if (password && password.length < 8) {
            errorMessage = 'Password must be at least 8 characters';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            showNotification(errorMessage, 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitButton');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });

    // Show notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            background: ${type === 'error' ? '#f56565' : type === 'success' ? '#48bb78' : '#4299e1'};
            color: white;
            z-index: 9999;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            max-width: 350px;
            font-size: 0.875rem;
            font-weight: 500;
        `;
        
        const icon = type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle';
        
        notification.innerHTML = `<i class="fas fa-${icon}"></i><span>${message}</span>`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    // Add CSS animations if not already present
    if (!document.querySelector('#notification-animations')) {
        const style = document.createElement('style');
        style.id = 'notification-animations';
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
</script>
@endpush