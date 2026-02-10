@extends('layouts.admin')

@section('title', 'Add New User - Admin Dashboard')

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

    /* Form Sections */
    .form-section {
        background: white;
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-section-title i {
        color: var(--primary);
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--gray-900);
        font-size: 0.875rem;
    }
    
    .form-label.required::after {
        content: " *";
        color: var(--danger);
    }
    
    .form-control {
        display: block;
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--gray-900);
        background-color: white;
        background-clip: padding-box;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        outline: 0;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-control.is-invalid {
        border-color: var(--danger);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef4444' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        padding-right: calc(1.5em + 0.75rem);
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--danger);
    }
    
    .form-text {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
    }
    
    .form-select {
        display: block;
        width: 100%;
        padding: 0.625rem 2.25rem 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--gray-900);
        background-color: white;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
    }
    
    .form-select:focus {
        border-color: var(--primary);
        outline: 0;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-select.is-invalid {
        border-color: var(--danger);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef4444' stroke='none'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-position: right calc(0.375em + 0.1875rem) center, right 0.75rem center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem), 16px 12px;
        padding-right: calc(2.25rem + 1.125rem);
    }
    
    .password-strength-meter {
        height: 4px;
        background: var(--gray-200);
        border-radius: 2px;
        margin-top: 0.5rem;
        overflow: hidden;
    }
    
    .password-strength-fill {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease, background-color 0.3s ease;
        border-radius: 2px;
    }
    
    .strength-weak {
        background: var(--danger);
        width: 33%;
    }
    
    .strength-medium {
        background: var(--warning);
        width: 66%;
    }
    
    .strength-strong {
        background: var(--success);
        width: 100%;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--gray-200);
        margin-top: 1.5rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .btn-outline {
        background: white;
        border-color: var(--gray-300);
        color: var(--gray-700);
    }
    
    .btn-outline:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    /* Validation alert */
    .validation-alert {
        background: var(--danger-light);
        color: var(--danger-dark);
        border: 1px solid var(--danger);
        border-radius: var(--radius-sm);
        padding: 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }
    
    .validation-alert i {
        margin-right: 0.5rem;
    }
    
    .validation-alert ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }
    
    /* Role options */
    .role-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: var(--radius-sm);
        border: 2px solid var(--gray-200);
        background: white;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .role-option:hover {
        border-color: var(--primary-light);
        background: var(--primary-light);
    }
    
    .role-option.active {
        border-color: var(--primary);
        background: var(--primary-light);
    }
    
    .role-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .role-content {
        flex: 1;
    }
    
    .role-title {
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray-900);
        margin-bottom: 0.125rem;
    }
    
    .role-description {
        font-size: 0.75rem;
        color: var(--gray-600);
    }
    
    .role-id-required {
        font-size: 0.7rem;
        color: var(--gray-500);
        margin-top: 0.125rem;
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
    }
</style>
@endpush

@section('content')
    <!-- Create User Form Card -->
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
            <!-- Display validation errors -->
            @if($errors->any())
            <div class="validation-alert">
                <div>
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
                        <i class="fas fa-user"></i> Personal Information
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="f_name" class="form-label required">First Name</label>
                            <input type="text" 
                                   id="f_name" 
                                   name="f_name" 
                                   value="{{ old('f_name') }}" 
                                   required
                                   class="form-control @error('f_name') is-invalid @enderror"
                                   placeholder="Enter first name">
                            @error('f_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="l_name" class="form-label required">Last Name</label>
                            <input type="text" 
                                   id="l_name" 
                                   name="l_name" 
                                   value="{{ old('l_name') }}" 
                                   required
                                   class="form-control @error('l_name') is-invalid @enderror"
                                   placeholder="Enter last name">
                            @error('l_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label required">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Enter email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">User will receive verification email</div>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" 
                                   id="age" 
                                   name="age" 
                                   value="{{ old('age') }}"
                                   min="15"
                                   max="100"
                                   class="form-control @error('age') is-invalid @enderror"
                                   placeholder="Enter age">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="sex" class="form-label">Gender</label>
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
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="text" 
                                   id="contact" 
                                   name="contact" 
                                   value="{{ old('contact') }}"
                                   class="form-control @error('contact') is-invalid @enderror"
                                   placeholder="e.g., +63 912 345 6789">
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Account Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user-cog"></i> Account Information
                    </div>
                    
                    <!-- Role Selection -->
                    <div style="margin-bottom: 1.5rem;">
                        <label class="form-label required">User Role</label>
                        <div class="form-grid">
                            @if(isset($roleOptions))
                                @foreach($roleOptions as $key => $option)
                                <div class="role-option @if(old('role') == $key) active @endif" onclick="selectRole({{ $key }})">
                                    <div class="role-icon" style="background: var(--{{ $option['color'] }}-light); color: var(--{{ $option['color'] }});">
                                        <i class="fas fa-{{ $option['icon'] }}"></i>
                                    </div>
                                    <div class="role-content">
                                        <div class="role-title">{{ $option['name'] }}</div>
                                        <div class="role-description">{{ $option['description'] }}</div>
                                        <div class="role-id-required">
                                            @if($option['id_required'])
                                                Requires {{ $option['id_type'] == 'employee_id' ? 'Employee ID' : 'Student ID' }}
                                            @else
                                                No ID required
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <input type="radio" 
                                               name="role" 
                                               value="{{ $key }}" 
                                               id="role_{{ $key }}"
                                               class="d-none"
                                               @if(old('role') == $key) checked @endif
                                               required>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <input type="hidden" name="role" id="selectedRole" value="{{ old('role') }}">
                        @error('role')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- ID Fields (Conditional) -->
                    <div id="idFieldsSection" style="display: none;">
                        <div class="form-grid">
                            <div class="form-group" id="employeeIdGroup" style="display: none;">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input type="text" 
                                       id="employee_id" 
                                       name="employee_id" 
                                       value="{{ old('employee_id') }}"
                                       class="form-control @error('employee_id') is-invalid @enderror"
                                       placeholder="Enter employee ID">
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Required for Registrar and Teacher roles</div>
                            </div>
                            
                            <div class="form-group" id="studentIdGroup" style="display: none;">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" 
                                       id="student_id" 
                                       name="student_id" 
                                       value="{{ old('student_id') }}"
                                       class="form-control @error('student_id') is-invalid @enderror"
                                       placeholder="Enter student ID">
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Required for Student role only</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Fields -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="password" class="form-label required">Password</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Enter password">
                            <div class="password-strength-meter">
                                <div class="password-strength-fill" id="passwordStrength"></div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 8 characters with letters and numbers</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label required">Confirm Password</label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Confirm password">
                            <div id="passwordMatch" class="form-text"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
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
            selectRole(initialRole.value);
        }
        
        // Generate ID suggestions based on names
        generateIdSuggestions();
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
        } else if (roleId == 4) { // Student
            idFieldsSection.style.display = 'block';
            employeeIdGroup.style.display = 'none';
            studentIdGroup.style.display = 'block';
            
            // Set student ID as required
            employeeIdInput.required = false;
            studentIdInput.required = true;
            employeeIdInput.value = '';
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
        
        // Generate ID suggestions
        generateIdSuggestions();
    }

    // Generate ID suggestions based on names
    function generateIdSuggestions() {
        const firstName = document.getElementById('f_name').value;
        const lastName = document.getElementById('l_name').value;
        const selectedRole = document.getElementById('selectedRole').value;
        
        if (firstName && lastName) {
            const initials = firstName.charAt(0).toUpperCase() + lastName.charAt(0).toUpperCase();
            const timestamp = Date.now().toString().slice(-4);
            const currentYear = new Date().getFullYear();
            
            if ([2, 3].includes(parseInt(selectedRole))) { // Registrar or Teacher
                const rolePrefix = selectedRole == 2 ? 'REG' : 'TEA';
                const suggestion = `${rolePrefix}-${currentYear}-${initials}${timestamp}`;
                document.getElementById('employee_id').placeholder = suggestion;
                
                // Auto-fill if empty
                if (!document.getElementById('employee_id').value) {
                    document.getElementById('employee_id').value = suggestion;
                }
            } else if (selectedRole == 4) { // Student
                const suggestion = `STU-${currentYear}-${timestamp}`;
                document.getElementById('student_id').placeholder = suggestion;
                
                // Auto-fill if empty
                if (!document.getElementById('student_id').value) {
                    document.getElementById('student_id').value = suggestion;
                }
            }
        }
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    
    if (passwordInput && passwordStrength) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Complexity checks
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update strength meter
            passwordStrength.className = 'password-strength-fill';
            
            if (strength <= 2) {
                passwordStrength.classList.add('strength-weak');
            } else if (strength <= 4) {
                passwordStrength.classList.add('strength-medium');
            } else {
                passwordStrength.classList.add('strength-strong');
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
                    passwordMatch.textContent = 'Passwords match';
                    passwordMatch.style.color = 'var(--success)';
                    this.setCustomValidity('');
                } else {
                    passwordMatch.textContent = 'Passwords do not match';
                    passwordMatch.style.color = 'var(--danger)';
                    this.setCustomValidity('Passwords do not match');
                }
            } else {
                passwordMatch.textContent = '';
            }
        });
    }

    // Trigger ID suggestion when name fields change
    document.getElementById('f_name').addEventListener('input', generateIdSuggestions);
    document.getElementById('l_name').addEventListener('input', generateIdSuggestions);

    // Form submission validation
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating User...';
        submitBtn.disabled = true;
        
        // Revert after 3 seconds (in case of error)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
</script>
@endpush