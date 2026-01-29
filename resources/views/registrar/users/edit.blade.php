<!-- resources/views/registrar/users/edit.blade.php -->
@extends('layouts.registrar')

@section('title', 'Edit User - Registrar Dashboard')

@push('styles')
<style>
    /* Additional styles for edit user form */
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
        <h1>Edit User</h1>
        <p>Update user information (Teachers and Students only)</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Edit User Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Edit User Information</div>
            <a href="{{ route('registrar.users.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i>
                Back to Users
            </a>
        </div>
        
        <div style="padding: 1.5rem;">
            <!-- Use $encryptedId passed from controller -->
            <form action="{{ route('registrar.users.update', $encryptedId) }}" method="POST">
                @csrf
                @method('PUT')
                
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
                               value="{{ old('f_name', $user->f_name) }}" 
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
                               value="{{ old('l_name', $user->l_name) }}" 
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
                           value="{{ old('email', $user->email) }}" 
                           required
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('email') border-color: var(--danger); @enderror">
                    @error('email')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Changing email may require verification
                    </div>
                </div>
                
                <!-- Password Update (Optional) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('password') border-color: var(--danger); @enderror">
                        @error('password')
                            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                        <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                            Leave blank to keep current password
                        </div>
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
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
                        <!-- Registrar can only edit Teacher and Student roles -->
                        <option value="3" {{ old('role', $user->role) == '3' ? 'selected' : '' }} {{ $user->role == 1 || $user->role == 2 ? 'disabled' : '' }}>Teacher</option>
                        <option value="4" {{ old('role', $user->role) == '4' ? 'selected' : '' }} {{ $user->role == 1 || $user->role == 2 ? 'disabled' : '' }}>Student</option>
                    </select>
                    @error('role')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    @if($user->role == 1 || $user->role == 2)
                        <div style="color: var(--warning); font-size: 0.75rem; margin-top: 0.25rem;">
                            Note: This user has a higher role ({{ $user->role_name }}) that cannot be changed by registrar
                        </div>
                    @endif
                </div>
                
                <!-- Conditional ID fields based on role -->
                <div id="employeeIdField" style="display: none; margin-bottom: 1.5rem;">
                    <label for="employee_id" class="form-label">Employee ID *</label>
                    <input type="text" 
                           id="employee_id" 
                           name="employee_id" 
                           value="{{ old('employee_id', $user->employee_id) }}"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('employee_id') border-color: var(--danger); @enderror">
                    @error('employee_id')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Required for: Teacher role
                    </div>
                </div>
                
                <div id="studentIdField" style="display: none; margin-bottom: 1.5rem;">
                    <label for="student_id" class="form-label">Student ID *</label>
                    <input type="text" 
                           id="student_id" 
                           name="student_id" 
                           value="{{ old('student_id', $user->student_id) }}"
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
                               value="{{ old('age', $user->age) }}"
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
                            <option value="male" {{ old('sex', $user->sex) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
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
                           value="{{ old('contact', $user->contact) }}"
                           placeholder="e.g., +63 912 345 6789"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('contact') border-color: var(--danger); @enderror">
                    @error('contact')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Approval Status Section (Registrar can approve users) -->
                @if($user->role == 3 || $user->role == 4)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.5rem;">
                        <i class="fas fa-user-check" style="color: #0369a1;"></i>
                        <h3 style="font-size: 1rem; font-weight: 600; color: #0369a1; margin: 0;">Approval Status</h3>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="radio" 
                                   id="approve" 
                                   name="is_approved" 
                                   value="1"
                                   {{ $user->is_approved ? 'checked' : '' }}
                                   style="width: 16px; height: 16px;">
                            <label for="approve" style="font-size: 0.875rem; color: var(--dark); cursor: pointer;">
                                Approved
                            </label>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="radio" 
                                   id="pending" 
                                   name="is_approved" 
                                   value="0"
                                   {{ !$user->is_approved ? 'checked' : '' }}
                                   style="width: 16px; height: 16px;">
                            <label for="pending" style="font-size: 0.875rem; color: var(--dark); cursor: pointer;">
                                Pending Approval
                            </label>
                        </div>
                    </div>
                    
                    <div style="font-size: 0.75rem; color: #0369a1; margin-top: 0.5rem;">
                        <i class="fas fa-info-circle"></i>
                        Only approved users can access the system
                    </div>
                </div>
                @endif
                
                <!-- Form Actions -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <div>
                        <span style="font-size: 0.875rem; color: var(--secondary);">
                            Created: {{ $user->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('registrar.users.show', $encryptedId) }}" 
                           style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-eye"></i>
                            View
                        </a>
                        <a href="{{ route('registrar.users.index') }}" 
                           style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                            Cancel
                        </a>
                        <button type="submit" 
                                style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-save"></i>
                            Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Quick Tips Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Registrar Update Notes</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Role Restrictions</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Can only manage Teachers (3) and Students (4)</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Approval Control</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Can approve or pending users for system access</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">ID Management</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Teachers require Employee ID, Students require Student ID</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <div style="width: 20px; height: 20px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.75rem;">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">Password Reset</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">Can reset passwords for teachers and students</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">User Details</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.875rem; color: var(--secondary);">User ID</div>
                        <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">
                            {{ $user->id }}
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.875rem; color: var(--secondary);">Current Role</div>
                        <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">
                            {{ $user->role_name }}
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.875rem; color: var(--secondary);">Approval Status</div>
                        <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">
                            @if($user->is_approved)
                                <span style="color: #10b981; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-check-circle"></i> Approved
                                </span>
                            @else
                                <span style="color: #ef4444; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.875rem; color: var(--secondary);">Email Verified</div>
                        <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">
                            @if($user->email_verified_at)
                                <span style="color: #10b981;">Yes</span>
                            @else
                                <span style="color: #ef4444;">No</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.875rem; color: var(--secondary);">Last Updated</div>
                        <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark);">
                            {{ $user->updated_at->format('M d, Y h:i A') }}
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
        
        // Show appropriate field based on role
        if (selectedRole === '3') { // Teacher
            employeeIdField.style.display = 'block';
            employeeIdInput.required = true;
        } else if (selectedRole === '4') { // Student
            studentIdField.style.display = 'block';
            studentIdInput.required = true;
        }
        // Note: Registrar (role 2) and Admin (role 1) not available for registrar to edit
    }

    // Real-time password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordFeedback = document.createElement('div');
    passwordFeedback.style.fontSize = '0.75rem';
    passwordFeedback.style.marginTop = '0.25rem';
    
    if (passwordInput) {
        passwordInput.parentNode.appendChild(passwordFeedback);
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            if (password.length === 0) {
                passwordFeedback.textContent = '';
                return;
            }
            
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
    }

    // Confirm password validation
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#ef4444';
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial state based on user's current role
        const roleSelect = document.getElementById('role');
        const selectedRole = roleSelect.value;
        
        if (selectedRole === '3') {
            document.getElementById('employeeIdField').style.display = 'block';
            document.getElementById('employee_id').required = true;
        } else if (selectedRole === '4') {
            document.getElementById('studentIdField').style.display = 'block';
            document.getElementById('student_id').required = true;
        }
        
        // If user is Admin or Registrar (roles 1 or 2), disable role selection
        if (selectedRole === '1' || selectedRole === '2') {
            roleSelect.disabled = true;
        }
    });
</script>
@endpush
@endsection