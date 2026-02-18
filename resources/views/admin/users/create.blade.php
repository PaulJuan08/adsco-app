@extends('layouts.admin')

@section('title', 'Add New User - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-form.css') }}">
@endpush

@section('content')
    <!-- Create User Form Card - Smaller Container -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-user-plus card-icon"></i>
                <h2 class="card-title">Add New User</h2>
            </div>
            <a href="{{ route('admin.users.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
        
        <div class="card-body">
            <!-- User Preview (Dynamic) -->
            <div class="user-preview" id="userPreview" style="display: none;">
                <div class="user-preview-avatar" id="previewAvatar">
                    <span id="previewInitials">JD</span>
                </div>
                <div class="user-preview-name" id="previewName">John Doe</div>
                <div class="user-preview-email" id="previewEmail">john.doe@example.com</div>
                <div class="user-preview-role" id="previewRole">Select Role</div>
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
            
            <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                @csrf
                
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
                                   value="{{ old('f_name') }}" 
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
                                   value="{{ old('l_name') }}" 
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
                                   value="{{ old('email') }}" 
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
                                   value="{{ old('age') }}"
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
                                <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
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
                                   value="{{ old('contact') }}"
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
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label required">
                            <i class="fas fa-user-tag"></i> User Role
                        </label>
                        <div class="role-grid">
                            @php
                                $roleOptions = [
                                    1 => ['name' => 'Admin', 'icon' => 'user-shield', 'color' => 'danger', 'id_required' => false, 'description' => 'Full system access', 'id_type' => null],
                                    2 => ['name' => 'Registrar', 'icon' => 'clipboard-list', 'color' => 'warning', 'id_required' => true, 'description' => 'Manage enrollments', 'id_type' => 'employee_id'],
                                    3 => ['name' => 'Teacher', 'icon' => 'chalkboard-teacher', 'color' => 'info', 'id_required' => true, 'description' => 'Manage classes', 'id_type' => 'employee_id'],
                                    4 => ['name' => 'Student', 'icon' => 'graduation-cap', 'color' => 'success', 'id_required' => true, 'description' => 'View courses', 'id_type' => 'student_id']
                                ];
                            @endphp
                            
                            @foreach($roleOptions as $key => $option)
                            <div class="role-option @if(old('role') == $key) active @endif" 
                                 onclick="selectRole({{ $key }})"
                                 data-role="{{ $key }}">
                                <div class="role-icon" style="background: linear-gradient(135deg, var(--{{ $option['color'] }}), var(--{{ $option['color'] }}-dark)); color: white;">
                                    <i class="fas fa-{{ $option['icon'] }}"></i>
                                </div>
                                <div class="role-content">
                                    <div class="role-title">{{ $option['name'] }}</div>
                                    <div class="role-description">{{ $option['description'] }}</div>
                                    <div class="role-id-required">
                                        @if($option['id_required'])
                                            <i class="fas fa-id-card"></i>
                                            Requires {{ $option['id_type'] == 'employee_id' ? 'Employee ID' : 'Student ID' }}
                                        @else
                                            <i class="fas fa-minus-circle"></i>
                                            No ID required
                                        @endif
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
                                       @if(old('role') == $key) checked @endif
                                       required>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="role" id="selectedRole" value="{{ old('role') }}">
                        @error('role')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- ID Fields (Conditional) -->
                    <div id="idFieldsSection" style="display: none;">
                        <div id="employeeIdGroup" style="display: none;" class="id-field-group">
                            <div class="form-group">
                                <label for="employee_id" class="form-label">
                                    <i class="fas fa-id-badge"></i> Employee ID
                                </label>
                                <input type="text" 
                                       id="employee_id" 
                                       name="employee_id" 
                                       value="{{ old('employee_id') }}"
                                       class="form-control @error('employee_id') is-invalid @enderror"
                                       placeholder="EMP-2024-XXXX">
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="employeeIdSuggestion" class="id-suggestion" style="display: none;">
                                    <i class="fas fa-lightbulb"></i>
                                    <span>Suggested: </span>
                                    <strong id="suggestedEmployeeId"></strong>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Required for Registrar/Teacher
                                </div>
                            </div>
                        </div>
                        
                        <div id="studentIdGroup" style="display: none;" class="id-field-group">
                            <div class="form-group">
                                <label for="student_id" class="form-label">
                                    <i class="fas fa-graduation-cap"></i> Student ID
                                </label>
                                <input type="text" 
                                       id="student_id" 
                                       name="student_id" 
                                       value="{{ old('student_id') }}"
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
                    
                    <!-- Password Fields -->
                    <div class="form-grid" style="margin-top: 1rem;">
                        <div class="form-group">
                            <label for="password" class="form-label required">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Enter password"
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
                                <i class="fas fa-info-circle"></i> Min 8 chars with letters & numbers
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label required">
                                <i class="fas fa-lock"></i> Confirm Password
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Confirm password"
                                   autocomplete="new-password">
                            <div id="passwordMatch" class="form-text"></div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="fas fa-user-plus"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize role selection
        const initialRole = document.querySelector('input[name="role"]:checked');
        if (initialRole) {
            selectRole(parseInt(initialRole.value));
        }
        
        // Initialize preview updates
        initializePreview();
    });

    // Role selection handler
    function selectRole(roleId) {
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
        
        // Show/hide ID fields based on role
        if ([2, 3].includes(parseInt(roleId))) { // Registrar or Teacher
            idFieldsSection.style.display = 'block';
            employeeIdGroup.style.display = 'block';
            studentIdGroup.style.display = 'none';
            
            // Set employee ID as required
            employeeIdInput.required = true;
            studentIdInput.required = false;
            studentIdInput.value = '';
            
            // Generate suggestion
            generateIdSuggestion('employee', roleId);
        } else if (roleId == 4) { // Student
            idFieldsSection.style.display = 'block';
            employeeIdGroup.style.display = 'none';
            studentIdGroup.style.display = 'block';
            
            // Set student ID as required
            employeeIdInput.required = false;
            studentIdInput.required = true;
            employeeIdInput.value = '';
            
            // Generate suggestion
            generateIdSuggestion('student', roleId);
        } else { // Admin
            idFieldsSection.style.display = 'none';
            employeeIdGroup.style.display = 'none';
            studentIdGroup.style.display = 'none';
            
            // Clear both IDs
            employeeIdInput.required = false;
            studentIdInput.required = false;
            employeeIdInput.value = '';
            studentIdInput.value = '';
        }
        
        // Update preview
        updatePreview();
    }

    // Generate ID suggestions
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
                const rolePrefix = selectedRole == 2 ? 'REG' : 'TEA';
                const suggestion = `${rolePrefix}-${currentYear}-${initials}${timestamp}`;
                
                const suggestionDiv = document.getElementById('employeeIdSuggestion');
                suggestionDiv.querySelector('#suggestedEmployeeId').textContent = suggestion;
                suggestionDiv.style.display = 'inline-flex';
                
                if (!document.getElementById('employee_id').value) {
                    document.getElementById('employee_id').value = suggestion;
                }
            } else if (type === 'student') {
                const suggestion = `STU-${currentYear}-${timestamp.padStart(4, '0')}`;
                
                const suggestionDiv = document.getElementById('studentIdSuggestion');
                suggestionDiv.querySelector('#suggestedStudentId').textContent = suggestion;
                suggestionDiv.style.display = 'inline-flex';
                
                if (!document.getElementById('student_id').value) {
                    document.getElementById('student_id').value = suggestion;
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

    // Preview functionality
    function initializePreview() {
        const inputs = ['f_name', 'l_name', 'email'];
        inputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', updatePreview);
            }
        });
    }

    function updatePreview() {
        const firstName = document.getElementById('f_name').value;
        const lastName = document.getElementById('l_name').value;
        const email = document.getElementById('email').value;
        const selectedRole = document.getElementById('selectedRole').value;
        const preview = document.getElementById('userPreview');
        
        if (firstName || lastName || email || selectedRole) {
            preview.style.display = 'block';
            
            // Update initials
            const initials = (firstName ? firstName.charAt(0).toUpperCase() : '') + 
                           (lastName ? lastName.charAt(0).toUpperCase() : '');
            document.getElementById('previewInitials').textContent = initials || 'NU';
            
            // Update name
            document.getElementById('previewName').textContent = 
                (firstName || 'New') + ' ' + (lastName || 'User');
            
            // Update email
            document.getElementById('previewEmail').textContent = email || 'email@example.com';
            
            // Update role
            const roleMap = {
                1: 'Admin',
                2: 'Registrar',
                3: 'Teacher',
                4: 'Student'
            };
            document.getElementById('previewRole').textContent = 
                roleMap[selectedRole] || 'Select Role';
        } else {
            preview.style.display = 'none';
        }
    }

    // Trigger ID suggestion when name fields change
    document.getElementById('f_name').addEventListener('input', function() {
        const role = parseInt(document.getElementById('selectedRole').value);
        if ([2, 3].includes(role)) {
            generateIdSuggestion('employee', role);
        } else if (role == 4) {
            generateIdSuggestion('student', role);
        }
        updatePreview();
    });

    document.getElementById('l_name').addEventListener('input', function() {
        const role = parseInt(document.getElementById('selectedRole').value);
        if ([2, 3].includes(role)) {
            generateIdSuggestion('employee', role);
        } else if (role == 4) {
            generateIdSuggestion('student', role);
        }
        updatePreview();
    });

    document.getElementById('email').addEventListener('input', updatePreview);

    // Form submission validation
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        const role = parseInt(document.getElementById('selectedRole').value);
        const employeeIdInput = document.getElementById('employee_id');
        const studentIdInput = document.getElementById('student_id');
        const password = document.getElementById('password').value;
        
        let isValid = true;
        
        // Validate role is selected
        if (!role) {
            e.preventDefault();
            showNotification('Please select a user role', 'error');
            isValid = false;
        }
        
        // Validate ID fields based on role
        if ([2, 3].includes(role) && employeeIdInput && !employeeIdInput.value.trim()) {
            employeeIdInput.classList.add('is-invalid');
            isValid = false;
        }
        
        if (role == 4 && studentIdInput && !studentIdInput.value.trim()) {
            studentIdInput.classList.add('is-invalid');
            isValid = false;
        }
        
        // Validate password strength
        if (password && password.length < 8) {
            e.preventDefault();
            showNotification('Password must be at least 8 characters', 'error');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            return;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitButton');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
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

    // Add CSS animations
    const style = document.createElement('style');
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
</script>
@endpush