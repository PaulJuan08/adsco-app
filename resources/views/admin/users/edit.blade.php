@extends('layouts.admin')

@section('title', 'Edit User - Admin Dashboard')

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
            <a href="{{ route('admin.users.index') }}" class="view-all-link">
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
                            1 => 'Admin',
                            2 => 'Registrar',
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
            <form action="{{ route('admin.users.update', $encryptedId) }}" method="POST" id="editUserForm">
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
                            <input type="text" id="f_name" name="f_name" value="{{ old('f_name', $user->f_name) }}" required
                                   class="form-control @error('f_name') is-invalid @enderror" placeholder="John">
                            @error('f_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="l_name" class="form-label required">
                                <i class="fas fa-user"></i> Last Name
                            </label>
                            <input type="text" id="l_name" name="l_name" value="{{ old('l_name', $user->l_name) }}" required
                                   class="form-control @error('l_name') is-invalid @enderror" placeholder="Doe">
                            @error('l_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label required">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="form-control @error('email') is-invalid @enderror" placeholder="john.doe@example.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text"><i class="fas fa-info-circle"></i> Used for login</div>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="age" class="form-label">
                                <i class="fas fa-birthday-cake"></i> Age
                            </label>
                            <input type="number" id="age" name="age" value="{{ old('age', $user->age) }}" min="15" max="100"
                                   class="form-control @error('age') is-invalid @enderror" placeholder="25">
                            @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="sex" class="form-label">
                                <i class="fas fa-venus-mars"></i> Gender
                            </label>
                            <select id="sex" name="sex" class="form-select @error('sex') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="male"   {{ old('sex', $user->sex) == 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="contact" class="form-label">
                                <i class="fas fa-phone"></i> Contact Number
                            </label>
                            <input type="text" id="contact" name="contact" value="{{ old('contact', $user->contact) }}"
                                   class="form-control @error('contact') is-invalid @enderror" placeholder="+63 912 345 6789">
                            @error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                    1 => ['name' => 'Admin',     'icon' => 'user-shield',       'color' => 'danger',  'id_required' => false, 'description' => 'Full system access',  'id_type' => null],
                                    2 => ['name' => 'Registrar', 'icon' => 'clipboard-list',     'color' => 'warning', 'id_required' => true,  'description' => 'Manage enrollments',   'id_type' => 'employee_id'],
                                    3 => ['name' => 'Teacher',   'icon' => 'chalkboard-teacher', 'color' => 'info',    'id_required' => true,  'description' => 'Manage classes',       'id_type' => 'employee_id'],
                                    4 => ['name' => 'Student',   'icon' => 'graduation-cap',     'color' => 'success', 'id_required' => true,  'description' => 'View courses',         'id_type' => 'student_id']
                                ];
                            @endphp
                            
                            @foreach($roleOptions as $key => $option)
                            <div class="role-option role-{{ strtolower($option['name']) }} @if(old('role', $user->role) == $key) active @endif" 
                                 onclick="selectRole({{ $key }})" data-role="{{ $key }}">
                                <div class="role-icon">
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
                                <div class="role-check"><i class="fas fa-check-circle"></i></div>
                                <input type="radio" name="role" value="{{ $key }}" id="role_{{ $key }}"
                                       class="d-none" @if(old('role', $user->role) == $key) checked @endif required>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="role" id="selectedRole" value="{{ old('role', $user->role) }}">
                        @error('role')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- ID Fields (Conditional) -->
                    <div id="idFieldsSection" class="{{ in_array($user->role, [2,3,4]) ? 'show' : '' }}"
                         style="{{ in_array($user->role, [2,3,4]) ? '' : 'display: none;' }}">
                        <div id="employeeIdGroup" style="{{ in_array($user->role, [2,3]) ? '' : 'display: none;' }}" class="id-field-group">
                            <div class="form-group">
                                <label for="employee_id" class="form-label {{ in_array($user->role, [2,3]) ? 'required' : '' }}">
                                    <i class="fas fa-id-badge"></i> Employee ID
                                </label>
                                <input type="text" id="employee_id" name="employee_id"
                                       value="{{ old('employee_id', $user->employee_id) }}"
                                       {{ in_array($user->role, [2,3]) ? 'required' : '' }}
                                       class="form-control @error('employee_id') is-invalid @enderror"
                                       placeholder="e.g., EMP-2024-001">
                                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div id="employeeIdSuggestion" class="id-suggestion" style="display: none;">
                                    <i class="fas fa-lightbulb"></i>
                                    <span>Suggested: </span><strong id="suggestedEmployeeId"></strong>
                                </div>
                            </div>
                        </div>
                        
                        <div id="studentIdGroup" style="{{ $user->role == 4 ? '' : 'display: none;' }}" class="id-field-group">
                            <div class="form-group">
                                <label for="student_id" class="form-label {{ $user->role == 4 ? 'required' : '' }}">
                                    <i class="fas fa-graduation-cap"></i> Student ID
                                </label>
                                <input type="text" id="student_id" name="student_id"
                                       value="{{ old('student_id', $user->student_id) }}"
                                       {{ $user->role == 4 ? 'required' : '' }}
                                       class="form-control @error('student_id') is-invalid @enderror"
                                       placeholder="e.g., STU-2024-001">
                                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div id="studentIdSuggestion" class="id-suggestion" style="display: none;">
                                    <i class="fas fa-lightbulb"></i>
                                    <span>Suggested: </span><strong id="suggestedStudentId"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Academic Fields (students only) ── -->
                    <div id="academicFieldsSection" style="{{ $user->role == 4 ? '' : 'display: none;' }}margin-top: 1rem;">
                        <div style="padding: 1rem; background: #f8f7ff; border: 1px solid #e0e7ff; border-radius: 10px;">
                            <div style="font-size: 0.875rem; font-weight: 600; color: #4f46e5; margin-bottom: 1rem;">
                                <i class="fas fa-university"></i> Academic Assignment
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="college_id" class="form-label">
                                        <i class="fas fa-university"></i> College
                                    </label>
                                    <select id="college_id" name="college_id"
                                            class="form-select @error('college_id') is-invalid @enderror"
                                            onchange="onCollegeChange(this.value)">
                                        <option value="">-- Select College --</option>
                                        @foreach($colleges as $college)
                                            <option value="{{ $college->id }}"
                                                    data-years="{{ $college->college_year }}"
                                                    {{ old('college_id', $user->college_id) == $college->id ? 'selected' : '' }}>
                                                {{ $college->college_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('college_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="program_id" class="form-label">
                                        <i class="fas fa-graduation-cap"></i> Program
                                    </label>
                                    <select id="program_id" name="program_id"
                                            class="form-select @error('program_id') is-invalid @enderror">
                                        <option value="">-- Select College First --</option>
                                    </select>
                                    @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="college_year" class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Year Level
                                    </label>
                                    <select id="college_year" name="college_year"
                                            class="form-select @error('college_year') is-invalid @enderror">
                                        <option value="">-- Select College First --</option>
                                    </select>
                                    @error('college_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div style="font-size: 0.8125rem; color: #6b7280; margin-top: 0.25rem;">
                                <i class="fas fa-info-circle"></i> Changing the program will automatically update the college.
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
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Leave blank to keep current" autocomplete="new-password">
                            <div class="password-strength-container">
                                <div class="password-strength-meter">
                                    <div class="password-strength-fill" id="passwordStrength"></div>
                                </div>
                                <div class="strength-text" id="strengthText"></div>
                            </div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock"></i> Confirm Password
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Confirm new password" autocomplete="new-password">
                            <div id="passwordMatch" class="form-text"></div>
                            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions - More Compact -->
                <div class="form-actions">
                    <a href="{{ route('admin.users.show', $encryptedId) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

    // Current student's saved values — used to pre-populate dropdowns on load
    const CURRENT_COLLEGE_ID = {{ $user->college_id ?? 'null' }};
    const CURRENT_PROGRAM_ID = {{ $user->program_id ?? 'null' }};
    const CURRENT_YEAR       = '{{ old('college_year', $user->college_year ?? '') }}';

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize role selection (isInit = true so we don't clear existing values)
        const initialRole = document.querySelector('input[name="role"]:checked');
        if (initialRole) {
            selectRole(parseInt(initialRole.value), true);
        }
    });

    // ── Role selection ──────────────────────────────────────────────────────
    function selectRole(roleId, isInit = false) {
        const roleRadio        = document.getElementById('role_' + roleId);
        const selectedRoleInput = document.getElementById('selectedRole');
        const idFieldsSection  = document.getElementById('idFieldsSection');
        const employeeIdGroup  = document.getElementById('employeeIdGroup');
        const studentIdGroup   = document.getElementById('studentIdGroup');
        const academicSection  = document.getElementById('academicFieldsSection');
        const employeeIdInput  = document.getElementById('employee_id');
        const studentIdInput   = document.getElementById('student_id');
        
        if (roleRadio) {
            roleRadio.checked = true;
            selectedRoleInput.value = roleId;
            document.querySelectorAll('.role-option').forEach(o => o.classList.remove('active'));
            roleRadio.closest('.role-option').classList.add('active');
        }
        
        // Reset required
        employeeIdInput.required = false;
        studentIdInput.required  = false;
        
        if ([2, 3].includes(parseInt(roleId))) {
            idFieldsSection.style.display  = 'block';
            idFieldsSection.classList.add('show');
            employeeIdGroup.style.display  = 'block';
            studentIdGroup.style.display   = 'none';
            academicSection.style.display  = 'none';
            employeeIdInput.required = true;
            if (!isInit) { studentIdInput.value = ''; clearAcademic(); }
            if (!employeeIdInput.value) generateIdSuggestion('employee', roleId);

        } else if (roleId == 4) {
            idFieldsSection.style.display  = 'block';
            idFieldsSection.classList.add('show');
            employeeIdGroup.style.display  = 'none';
            studentIdGroup.style.display   = 'block';
            academicSection.style.display  = 'block';
            studentIdInput.required = true;
            if (!isInit) { employeeIdInput.value = ''; }
            // On init: load existing college/program/year into the dropdowns
            if (isInit && CURRENT_COLLEGE_ID) {
                document.getElementById('college_id').value = CURRENT_COLLEGE_ID;
                onCollegeChange(CURRENT_COLLEGE_ID, CURRENT_PROGRAM_ID, CURRENT_YEAR);
            }
            if (!studentIdInput.value) generateIdSuggestion('student', roleId);

        } else {
            idFieldsSection.classList.remove('show');
            setTimeout(() => { idFieldsSection.style.display = 'none'; }, 300);
            academicSection.style.display = 'none';
            if (!isInit) { employeeIdInput.value = ''; studentIdInput.value = ''; clearAcademic(); }
        }
    }

    function clearAcademic() {
        document.getElementById('college_id').value    = '';
        document.getElementById('program_id').innerHTML  = '<option value="">-- Select College First --</option>';
        document.getElementById('college_year').innerHTML = '<option value="">-- Select College First --</option>';
    }

    // ── College → load programs & year levels ───────────────────────────────
    function onCollegeChange(collegeId, preselectProgramId = null, preselectYear = '') {
        const programSelect = document.getElementById('program_id');
        const yearSelect    = document.getElementById('college_year');

        if (!collegeId) {
            programSelect.innerHTML = '<option value="">-- Select College First --</option>';
            yearSelect.innerHTML    = '<option value="">-- Select College First --</option>';
            return;
        }

        // Load programs
        fetch(`/api/registration/colleges/${collegeId}/programs`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(programs => {
            programSelect.innerHTML = '<option value="">-- Select Program --</option>';
            programs.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.program_name + (p.program_code ? ` (${p.program_code})` : '');
                if (preselectProgramId && p.id == preselectProgramId) opt.selected = true;
                programSelect.appendChild(opt);
            });
        });

        // Populate year levels from data attribute
        const collegeOption = document.querySelector(`#college_id option[value="${collegeId}"]`);
        const yearsRaw      = collegeOption ? collegeOption.dataset.years : '';
        yearSelect.innerHTML = '<option value="">-- Select Year Level --</option>';
        if (yearsRaw) {
            yearsRaw.split(',').map(y => y.trim()).filter(Boolean).forEach(y => {
                const opt = document.createElement('option');
                opt.value = y; opt.textContent = y;
                if (preselectYear === y) opt.selected = true;
                yearSelect.appendChild(opt);
            });
        }
    }

    // ── ID suggestions ──────────────────────────────────────────────────────
    function generateIdSuggestion(type, roleId = null) {
        const firstName    = document.getElementById('f_name').value;
        const lastName     = document.getElementById('l_name').value;
        const selectedRole = roleId || parseInt(document.getElementById('selectedRole').value);
        if (!firstName && !lastName) return;

        const initials    = (firstName ? firstName[0].toUpperCase() : 'X') + (lastName ? lastName[0].toUpperCase() : 'X');
        const timestamp   = Date.now().toString().slice(-4);
        const currentYear = new Date().getFullYear();

        if (type === 'employee') {
            const prefix     = selectedRole == 2 ? 'REG' : 'TEA';
            const suggestion = `${prefix}-${currentYear}-${initials}${timestamp}`;
            document.getElementById('suggestedEmployeeId').textContent = suggestion;
            document.getElementById('employeeIdSuggestion').style.display = 'inline-flex';
            if (!document.getElementById('employee_id').value) document.getElementById('employee_id').value = suggestion;
        } else {
            const suggestion = `STU-${currentYear}-${timestamp.padStart(4,'0')}`;
            document.getElementById('suggestedStudentId').textContent = suggestion;
            document.getElementById('studentIdSuggestion').style.display = 'inline-flex';
            if (!document.getElementById('student_id').value) document.getElementById('student_id').value = suggestion;
        }
    }

    // ── Password strength ───────────────────────────────────────────────────
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    
    if (passwordInput && passwordStrength && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            if (password.length >= 8) strength++; if (password.length >= 12) strength++;
            if (/[A-Z]/.test(password)) strength++; if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++; if (/[^A-Za-z0-9]/.test(password)) strength++;
            passwordStrength.className = 'password-strength-fill';
            if (password.length === 0) { strengthText.textContent = ''; return; }
            if (strength <= 2) { passwordStrength.classList.add('strength-weak'); strengthText.innerHTML = '<i class="fas fa-exclamation-circle"></i> Weak'; strengthText.style.color = '#f56565'; }
            else if (strength <= 4) { passwordStrength.classList.add('strength-fair'); strengthText.innerHTML = '<i class="fas fa-info-circle"></i> Fair'; strengthText.style.color = '#ed8936'; }
            else if (strength <= 5) { passwordStrength.classList.add('strength-good'); strengthText.innerHTML = '<i class="fas fa-check-circle"></i> Good'; strengthText.style.color = '#ecc94b'; }
            else { passwordStrength.classList.add('strength-strong'); strengthText.innerHTML = '<i class="fas fa-shield-alt"></i> Strong'; strengthText.style.color = '#48bb78'; }
        });
    }

    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatch = document.getElementById('passwordMatch');
    if (confirmPasswordInput && passwordMatch) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            if (this.value) {
                if (password === this.value) {
                    passwordMatch.innerHTML = '<i class="fas fa-check-circle"></i> Match'; passwordMatch.style.color = '#48bb78';
                    this.setCustomValidity(''); this.classList.remove('is-invalid'); this.classList.add('is-valid');
                } else {
                    passwordMatch.innerHTML = '<i class="fas fa-times-circle"></i> No match'; passwordMatch.style.color = '#f56565';
                    this.setCustomValidity('Passwords do not match'); this.classList.add('is-invalid'); this.classList.remove('is-valid');
                }
            } else { passwordMatch.innerHTML = ''; this.classList.remove('is-invalid', 'is-valid'); }
        });
    }

    // ── Auto-fill ID when name changes ──────────────────────────────────────
    ['f_name', 'l_name'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function() {
            const role = parseInt(document.getElementById('selectedRole').value);
            if ([2, 3].includes(role)) generateIdSuggestion('employee', role);
            else if (role == 4) generateIdSuggestion('student', role);
        });
    });

    // ── Form submit validation ──────────────────────────────────────────────
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const role           = parseInt(document.getElementById('selectedRole').value);
        const employeeIdInput = document.getElementById('employee_id');
        const studentIdInput  = document.getElementById('student_id');
        let isValid = true;

        if ([2, 3].includes(role) && employeeIdInput && !employeeIdInput.value.trim()) { employeeIdInput.classList.add('is-invalid'); isValid = false; }
        if (role == 4 && studentIdInput && !studentIdInput.value.trim()) { studentIdInput.classList.add('is-invalid'); isValid = false; }
        if (!isValid) { e.preventDefault(); showNotification('Please fill in all required fields', 'error'); return; }

        const submitBtn = document.getElementById('submitButton');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
        setTimeout(() => { submitBtn.innerHTML = '<i class="fas fa-save"></i> Save'; submitBtn.disabled = false; }, 5000);
    });

    function showNotification(message, type = 'info') {
        const n = document.createElement('div');
        n.style.cssText = `position:fixed;top:20px;right:20px;padding:0.75rem 1.25rem;border-radius:8px;background:${type==='error'?'#f56565':type==='success'?'#48bb78':'#4299e1'};color:white;z-index:9999;box-shadow:0 8px 20px rgba(0,0,0,0.15);animation:slideIn 0.3s ease;display:flex;align-items:center;gap:0.5rem;max-width:350px;font-size:0.875rem;font-weight:500;`;
        n.innerHTML = `<i class="fas fa-${type==='error'?'exclamation-circle':'check-circle'}"></i><span>${message}</span>`;
        document.body.appendChild(n);
        setTimeout(() => { n.style.animation = 'slideOut 0.3s ease'; setTimeout(() => n.remove(), 300); }, 4000);
    }

    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes slideOut{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}`;
    document.head.appendChild(style);
</script>
@endpush