@extends('layouts.admin')

@section('title', 'Add New User - Admin Dashboard')

@push('styles')
<style>
    /* Additional styles for create user form */
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
    }
    
    .form-control {
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
    
    .form-control:focus {
        border-color: var(--primary);
        outline: 0;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-control.is-invalid {
        border-color: var(--danger);
    }
    
    .form-control.is-invalid:focus {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: var(--danger);
    }
    
    .text-muted {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.25rem;
    }
    
    .form-select {
        display: block;
        width: 100%;
        padding: 0.75rem 2.25rem 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--dark);
        background-color: white;
        border: 1px solid var(--border);
        border-radius: 8px;
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
    }
    
    .form-select.is-invalid:focus {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Add New User</h1>
        <p>Create a new user account in the system</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Create User Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">User Information</div>
            <a href="{{ route('admin.users.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i>
                Back to Users
            </a>
        </div>
        
        <div style="padding: 1.5rem;">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                
                <!-- Display validation errors -->
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
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="f_name" class="form-label">First Name *</label>
                        <input type="text" 
                               id="f_name" 
                               name="f_name" 
                               value="{{ old('f_name') }}" 
                               required
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('f_name') border-color: var(--danger); @enderror">
                        @error('f_name')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="l_name" class="form-label">Last Name *</label>
                        <input type="text" 
                               id="l_name" 
                               name="l_name" 
                               value="{{ old('l_name') }}" 
                               required
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('l_name') border-color: var(--danger); @enderror">
                        @error('l_name')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('email') border-color: var(--danger); @enderror">
                    @error('email')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('password') border-color: var(--danger); @enderror">
                        @error('password')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                        <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">Minimum 8 characters</div>
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="form-label">Confirm Password *</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="role" class="form-label">User Role *</label>
                    <select id="role" 
                            name="role" 
                            required
                            onchange="toggleIdFields()"
                            style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('role') border-color: var(--danger); @enderror">
                        <option value="">Select Role</option>
                        <option value="1" {{ old('role') == 1 ? 'selected' : '' }}>Admin</option>
                        <option value="2" {{ old('role') == 2 ? 'selected' : '' }}>Registrar</option>
                        <option value="3" {{ old('role') == 3 ? 'selected' : '' }}>Teacher</option>
                        <option value="4" {{ old('role') == 4 ? 'selected' : '' }}>Student</option>
                    </select>
                    @error('role')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Conditional ID fields based on role -->
                <div id="employeeIdField" style="display: none; margin-bottom: 1.5rem;">
                    <label for="employee_id" class="form-label">Employee ID *</label>
                    <input type="text" 
                           id="employee_id" 
                           name="employee_id" 
                           value="{{ old('employee_id') }}"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('employee_id') border-color: var(--danger); @enderror">
                    @error('employee_id')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Required for: Registrar & Teacher roles
                    </div>
                </div>
                
                <div id="studentIdField" style="display: none; margin-bottom: 1.5rem;">
                    <label for="student_id" class="form-label">Student ID *</label>
                    <input type="text" 
                           id="student_id" 
                           name="student_id" 
                           value="{{ old('student_id') }}"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('student_id') border-color: var(--danger); @enderror">
                    @error('student_id')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Required for: Student role only
                    </div>
                </div>
                
                <!-- Additional fields for user details -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="age" class="form-label">Age</label>
                        <input type="number" 
                               id="age" 
                               name="age" 
                               value="{{ old('age') }}"
                               min="15"
                               max="100"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('age') border-color: var(--danger); @enderror">
                        @error('age')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="sex" class="form-label">Gender</label>
                        <select id="sex" 
                                name="sex"
                                style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('sex') border-color: var(--danger); @enderror">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('sex')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input type="text" 
                           id="contact" 
                           name="contact" 
                           value="{{ old('contact') }}"
                           placeholder="e.g., +63 912 345 6789"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('contact') border-color: var(--danger); @enderror">
                    @error('contact')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <a href="{{ route('admin.users.index') }}" 
                       style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                        Cancel
                    </a>
                    <button type="submit" 
                            style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-user-plus"></i>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Quick Tips Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Tips</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">ID Requirements</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Admins need no ID, Registrars/Teachers need Employee ID, Students need Student ID</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Role Selection</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Choose appropriate role based on user needs</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Email Verification</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">User will need to verify their email</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Auto-Approval</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Users created by admin are auto-approved</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Role Descriptions</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #fee2e2; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--danger); font-size: 0.75rem;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Admin</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Full system access and management</div>
                            <div style="font-size: 0.7rem; color: var(--secondary); margin-top: 2px;">No ID required</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Registrar</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Manage student registrations and records</div>
                            <div style="font-size: 0.7rem; color: var(--secondary); margin-top: 2px;">Requires Employee ID</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #dcfce7; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--success); font-size: 0.75rem;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Teacher</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Create courses and manage students</div>
                            <div style="font-size: 0.7rem; color: var(--secondary); margin-top: 2px;">Requires Employee ID</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0f2fe; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #0ea5e9; font-size: 0.75rem;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Student</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Enroll in courses and view materials</div>
                            <div style="font-size: 0.7rem; color: var(--secondary); margin-top: 2px;">Requires Student ID</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide ID fields based on role selection
    function toggleIdFields() {
        const roleSelect = document.getElementById('role');
        const selectedRole = roleSelect.value;
        const employeeIdField = document.getElementById('employeeIdField');
        const studentIdField = document.getElementById('studentIdField');
        const employeeIdInput = document.getElementById('employee_id');
        const studentIdInput = document.getElementById('student_id');
        
        // Hide both fields initially
        employeeIdField.style.display = 'none';
        studentIdField.style.display = 'none';
        
        // Remove required attribute
        employeeIdInput.required = false;
        studentIdInput.required = false;
        
        // Clear values if switching roles
        if (selectedRole !== '2' && selectedRole !== '3') {
            employeeIdInput.value = '';
        }
        if (selectedRole !== '4') {
            studentIdInput.value = '';
        }
        
        // Show appropriate field based on role
        if (selectedRole === '2' || selectedRole === '3') { // Registrar or Teacher
            employeeIdField.style.display = 'block';
            employeeIdInput.required = true;
        } else if (selectedRole === '4') { // Student
            studentIdField.style.display = 'block';
            studentIdInput.required = true;
        }
        // Admin (role 1) shows no ID field
        
        // Generate ID suggestions
        generateIdSuggestion();
    }

    // Auto-generate ID suggestions
    function generateIdSuggestion() {
        const roleSelect = document.getElementById('role');
        const firstName = document.getElementById('f_name').value;
        const lastName = document.getElementById('l_name').value;
        const selectedRole = roleSelect.value;
        
        if (firstName && lastName) {
            const initials = firstName.charAt(0).toUpperCase() + lastName.charAt(0).toUpperCase();
            const timestamp = Date.now().toString().slice(-4);
            
            if (selectedRole === '2') { // Registrar
                document.getElementById('employee_id').placeholder = `e.g., REG-${initials}${timestamp}`;
            } else if (selectedRole === '3') { // Teacher
                document.getElementById('employee_id').placeholder = `e.g., TEA-${initials}${timestamp}`;
            } else if (selectedRole === '4') { // Student
                const year = new Date().getFullYear();
                document.getElementById('student_id').placeholder = `e.g., STU-${year}-${timestamp}`;
            }
        }
    }

    // Trigger ID suggestion when name fields change
    document.getElementById('f_name').addEventListener('input', generateIdSuggestion);
    document.getElementById('l_name').addEventListener('input', generateIdSuggestion);

    // Real-time password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordFeedback = document.createElement('div');
    passwordFeedback.style.fontSize = '0.75rem';
    passwordFeedback.style.marginTop = '0.25rem';
    
    passwordInput.parentNode.appendChild(passwordFeedback);
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 'Weak';
        let color = '#ef4444';
        
        if (password.length >= 12) {
            strength = 'Strong';
            color = '#10b981';
        } else if (password.length >= 8) {
            strength = 'Medium';
            color = '#f59e0b';
        }
        
        passwordFeedback.textContent = `Password strength: ${strength}`;
        passwordFeedback.style.color = color;
    });
    
    // Confirm password validation
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.style.borderColor = '#ef4444';
        } else {
            this.style.borderColor = '#e5e7eb';
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial state based on previously selected role
        const roleSelect = document.getElementById('role');
        const selectedRole = roleSelect.value;
        
        if (selectedRole === '2' || selectedRole === '3') {
            document.getElementById('employeeIdField').style.display = 'block';
            document.getElementById('employee_id').required = true;
        } else if (selectedRole === '4') {
            document.getElementById('studentIdField').style.display = 'block';
            document.getElementById('student_id').required = true;
        }
        
        // Also generate suggestions if form was submitted with errors
        generateIdSuggestion();
    });
</script>
@endpush
@endsection