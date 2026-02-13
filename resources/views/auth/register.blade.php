<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ADSCO Learning Management System - Register for a new account">
    <title>Register - ADSCO LMS</title>
    
    <!-- Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Logo -->
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}" type="image/png">
</head>
<body>
    <!-- Floating particles for background decoration -->
    <div class="auth-particle auth-particle-1"></div>
    <div class="auth-particle auth-particle-2"></div>
    <div class="auth-particle auth-particle-3"></div>

    <!-- Navigation -->
    <nav class="auth-navbar">
        <div class="container">
            <a href="/" class="brand">
                <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="brand-logo">
                <span class="brand-text">ADS<span class="accent">CO</span></span>
            </a>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-links" id="navLinks">
                <a href="/" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="nav-link active">
                    <i class="fas fa-user-plus"></i>
                    <span>Register</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="auth-container">
        <div class="auth-card auth-card-wide">
            <!-- Card Header -->
            <div class="auth-card-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="logo">
                </div>
                <h1>Create Your Account</h1>
                <p class="subtitle">Join ADSCO Learning Management System</p>
            </div>

            <!-- Card Body -->
            <div class="auth-card-body">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Registration Form -->
                <form id="registrationForm" method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    <!-- Personal Information -->
                    <h3 style="color: var(--color-adsco-primary); margin-bottom: 1.5rem; font-size: 1.1rem; border-bottom: 2px solid var(--color-adsco-accent); padding-bottom: 0.5rem;">
                        <i class="fas fa-user-circle"></i> Personal Information
                    </h3>

                    <div class="row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="f_name" class="form-label">
                                    First Name <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="f_name" 
                                    name="f_name" 
                                    value="{{ old('f_name') }}"
                                    placeholder="Enter first name"
                                    required>
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="l_name" class="form-label">
                                    Last Name <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="l_name" 
                                    name="l_name" 
                                    value="{{ old('l_name') }}"
                                    placeholder="Enter last name"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-md-4">
                            <div class="form-group">
                                <label for="age" class="form-label">
                                    Age <span class="required">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="age" 
                                    name="age" 
                                    value="{{ old('age') }}"
                                    min="15" 
                                    max="100"
                                    placeholder="Age"
                                    required>
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <div class="form-group">
                                <label for="sex" class="form-label">
                                    Sex <span class="required">*</span>
                                </label>
                                <select class="form-select" id="sex" name="sex" required>
                                    <option value="">Select Sex</option>
                                    <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <div class="form-group">
                                <label for="contact" class="form-label">
                                    Contact Number <span class="required">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="contact" 
                                    name="contact" 
                                    value="{{ old('contact') }}"
                                    placeholder="09XX XXX XXXX"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <h3 style="color: var(--color-adsco-primary); margin: 2rem 0 1.5rem; font-size: 1.1rem; border-bottom: 2px solid var(--color-adsco-accent); padding-bottom: 0.5rem;">
                        <i class="fas fa-id-card"></i> Account Information
                    </h3>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                placeholder="your.email@example.com"
                                required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="role" class="form-label">
                                    Account Type <span class="required">*</span>
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <!-- <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>Admin</option> -->
                                    <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>Registrar</option>
                                    <option value="3" {{ old('role') == '3' ? 'selected' : '' }}>Teacher</option>
                                    <option value="4" {{ old('role') == '4' ? 'selected' : '' }}>Student</option>
                                </select>
                            </div>
                        </div>

                        <!-- Employee ID Field (hidden by default) -->
                        <div class="col col-md-6" id="employeeIdField" style="display: none;">
                            <div class="form-group">
                                <label for="employee_id" class="form-label">
                                    Employee ID <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="employee_id" 
                                    name="employee_id" 
                                    value="{{ old('employee_id') }}"
                                    placeholder="Enter employee ID">
                            </div>
                        </div>

                        <!-- Student ID Field (hidden by default) -->
                        <div class="col col-md-6" id="studentIdField" style="display: none;">
                            <div class="form-group">
                                <label for="student_id" class="form-label">
                                    Student ID <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="student_id" 
                                    name="student_id" 
                                    value="{{ old('student_id') }}"
                                    placeholder="Enter student ID">
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <h3 style="color: var(--color-adsco-primary); margin: 2rem 0 1.5rem; font-size: 1.1rem; border-bottom: 2px solid var(--color-adsco-accent); padding-bottom: 0.5rem;">
                        <i class="fas fa-lock"></i> Security
                    </h3>

                    <div class="row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    Password <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="password" 
                                        name="password"
                                        placeholder="Minimum 8 characters"
                                        required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password', 'passwordIcon1')">
                                        <i class="fas fa-eye" id="passwordIcon1"></i>
                                    </button>
                                </div>
                                <span class="form-text">Minimum 8 characters</span>
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    Confirm Password <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="password_confirmation" 
                                        name="password_confirmation"
                                        placeholder="Re-enter password"
                                        required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'passwordIcon2')">
                                        <i class="fas fa-eye" id="passwordIcon2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div style="margin-top: 2rem;">
                        <button type="button" class="btn btn-primary btn-block btn-lg" id="reviewBtn">
                            <i class="fas fa-eye"></i>
                            <span>Review Registration</span>
                        </button>
                    </div>
                </form>

                <!-- Login Link -->
                <div class="divider">
                    <span>Already have an account?</span>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="auth-link" style="font-size: 1rem;">
                        <i class="fas fa-sign-in-alt"></i> Login here
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal" id="reviewModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check"></i>
                    Review Registration Details
                </h5>
                <button type="button" class="btn-close" onclick="closeModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th width="35%"><i class="fas fa-user"></i> First Name</th>
                            <td id="review_f_name"></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user"></i> Last Name</th>
                            <td id="review_l_name"></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Age</th>
                            <td id="review_age"></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-venus-mars"></i> Sex</th>
                            <td id="review_sex"></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone"></i> Contact</th>
                            <td id="review_contact"></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <td id="review_email"></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user-tag"></i> Role</th>
                            <td id="review_role"></td>
                        </tr>
                        <tr id="review_employee_id_row" style="display: none;">
                            <th><i class="fas fa-id-badge"></i> Employee ID</th>
                            <td id="review_employee_id"></td>
                        </tr>
                        <tr id="review_student_id_row" style="display: none;">
                            <th><i class="fas fa-id-badge"></i> Student ID</th>
                            <td id="review_student_id"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-edit"></i>
                    Edit Details
                </button>
                <button type="button" class="btn btn-primary" id="submitRegistrationBtn">
                    <i class="fas fa-check"></i>
                    Submit Registration
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="auth-footer">
        <p>
            <i class="far fa-copyright"></i> {{ date('Y') }} Agusan Del Sur College. All rights reserved.
        </p>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('show');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            });
        }

        // Show/hide ID fields based on role selection
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const employeeField = document.getElementById('employeeIdField');
            const studentField = document.getElementById('studentIdField');
            const employeeInput = document.getElementById('employee_id');
            const studentInput = document.getElementById('student_id');

            if (role == '1' || role == '2' || role == '3') {
                // Admin, Registrar, or Teacher - show Employee ID
                employeeField.style.display = 'block';
                employeeInput.required = true;
                studentField.style.display = 'none';
                studentInput.required = false;
            } else if (role == '4') {
                // Student - show Student ID
                studentField.style.display = 'block';
                studentInput.required = true;
                employeeField.style.display = 'none';
                employeeInput.required = false;
            } else {
                // No role selected
                employeeField.style.display = 'none';
                studentField.style.display = 'none';
                employeeInput.required = false;
                studentInput.required = false;
            }
        });

        // Trigger change on page load if role is already selected
        document.getElementById('role').dispatchEvent(new Event('change'));

        // Password toggle function
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Review button click handler
        document.getElementById('reviewBtn').addEventListener('click', function() {
            reviewRegistration();
        });

        // Submit button in modal
        document.getElementById('submitRegistrationBtn').addEventListener('click', function() {
            submitRegistration();
        });

        function reviewRegistration() {
            // Collect form data
            const formData = {
                f_name: document.getElementById('f_name').value,
                l_name: document.getElementById('l_name').value,
                age: document.getElementById('age').value,
                sex: document.getElementById('sex').value,
                contact: document.getElementById('contact').value,
                email: document.getElementById('email').value,
                role: document.getElementById('role').value,
                employee_id: document.getElementById('employee_id').value,
                student_id: document.getElementById('student_id').value,
                role_name: document.getElementById('role').options[document.getElementById('role').selectedIndex].text
            };

            // Validate required fields
            const requiredFields = ['f_name', 'l_name', 'age', 'sex', 'contact', 'email', 'role'];
            for (const field of requiredFields) {
                if (!formData[field]) {
                    alert(`Please fill in the ${field.replace('_', ' ')} field`);
                    return;
                }
            }

            // Validate ID based on role
            if ((formData.role == '1' || formData.role == '2' || formData.role == '3') && !formData.employee_id) {
                alert('Please enter Employee ID');
                return;
            }

            if (formData.role == '4' && !formData.student_id) {
                alert('Please enter Student ID');
                return;
            }

            // Validate password
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                return;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            // Populate review modal
            document.getElementById('review_f_name').textContent = formData.f_name;
            document.getElementById('review_l_name').textContent = formData.l_name;
            document.getElementById('review_age').textContent = formData.age;
            document.getElementById('review_sex').textContent = formData.sex === 'male' ? 'Male' : 'Female';
            document.getElementById('review_contact').textContent = formData.contact;
            document.getElementById('review_email').textContent = formData.email;
            document.getElementById('review_role').textContent = formData.role_name;

            // Show/hide ID fields in review
            if (formData.role == '1' || formData.role == '2' || formData.role == '3') {
                document.getElementById('review_employee_id').textContent = formData.employee_id;
                document.getElementById('review_employee_id_row').style.display = '';
                document.getElementById('review_student_id_row').style.display = 'none';
            } else if (formData.role == '4') {
                document.getElementById('review_student_id').textContent = formData.student_id;
                document.getElementById('review_student_id_row').style.display = '';
                document.getElementById('review_employee_id_row').style.display = 'none';
            } else {
                document.getElementById('review_employee_id_row').style.display = 'none';
                document.getElementById('review_student_id_row').style.display = 'none';
            }

            // Show modal
            document.getElementById('reviewModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('reviewModal').classList.remove('show');
            document.body.style.overflow = '';
        }

        function submitRegistration() {
            const submitBtn = document.getElementById('submitRegistrationBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span><span>Submitting...</span>';
            document.getElementById('registrationForm').submit();
        }

        // Close modal on backdrop click
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                alert.style.animation = 'slideUp 0.3s ease-out';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>