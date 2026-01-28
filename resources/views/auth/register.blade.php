<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ADSCO LMS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --school-navy: #002147;
            --heritage-gold: #D4AF37;
            --slate-grey: #708090;
            --crimson: #990000;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .register-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 33, 71, 0.1);
        }
        
        .card-header {
            background-color: var(--school-navy);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .btn-primary {
            background-color: var(--school-navy);
            border-color: var(--school-navy);
            padding: 12px 30px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #001835;
            border-color: #001835;
        }
        
        .btn-secondary {
            background-color: var(--heritage-gold);
            border-color: var(--heritage-gold);
            color: var(--school-navy);
            font-weight: 500;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--school-navy);
        }
        
        .form-control:focus {
            border-color: var(--heritage-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
        
        .table th {
            background-color: #f8f9fa;
            color: var(--school-navy);
        }
        
        a {
            color: var(--school-navy);
            text-decoration: none;
        }
        
        a:hover {
            color: var(--heritage-gold);
        }
    </style>
</head>
<body>
    <!-- Simple Navigation -->
    <nav class="navbar navbar-dark bg-school-navy mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="bi bi-book-half me-2"></i>
                <strong>ADSCO LMS</strong>
            </a>
            <div>
                <a href="{{ route('login') }}" class="text-white me-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
                <a href="{{ route('register') }}" class="text-white">
                    <i class="bi bi-person-plus me-1"></i>Register
                </a>
            </div>
        </div>
    </nav>
    
    <div class="register-container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Register New Account</h4>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form id="registrationForm" method="POST" action="{{ route('register.submit') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="f_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="f_name" name="f_name" 
                                       value="{{ old('f_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="l_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="l_name" name="l_name" 
                                       value="{{ old('l_name') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="age" class="form-label">Age *</label>
                                <input type="number" class="form-control" id="age" name="age" 
                                       value="{{ old('age') }}" min="15" max="100" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sex" class="form-label">Sex *</label>
                                <select class="form-select" id="sex" name="sex" required>
                                    <option value="">Select</option>
                                    <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact Number *</label>
                                <input type="tel" class="form-control" id="contact" name="contact" 
                                       value="{{ old('contact') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Account Type *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>Registrar</option>
                            <option value="3" {{ old('role') == '3' ? 'selected' : '' }}>Teacher</option>
                            <option value="4" {{ old('role') == '4' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="employeeIdField" style="display: none;">
                        <label for="employee_id" class="form-label">Employee ID *</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" 
                               value="{{ old('employee_id') }}">
                    </div>
                    
                    <div class="mb-3" id="studentIdField" style="display: none;">
                        <label for="student_id" class="form-label">Student ID *</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" 
                               value="{{ old('student_id') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary btn-lg" id="reviewBtn">
                            <i class="bi bi-eye me-2"></i>Review Registration
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4 pt-3 border-top">
                    <p class="mb-0">Already have an account? 
                        <a href="{{ route('login') }}" class="fw-bold">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-school-navy text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-check me-2"></i>Review Registration Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">First Name</th>
                                <td id="review_f_name"></td>
                            </tr>
                            <tr>
                                <th>Last Name</th>
                                <td id="review_l_name"></td>
                            </tr>
                            <tr>
                                <th>Age</th>
                                <td id="review_age"></td>
                            </tr>
                            <tr>
                                <th>Sex</th>
                                <td id="review_sex"></td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td id="review_contact"></td>
                            </tr>
                            <tr>
                                <th>Email Address</th>
                                <td id="review_email"></td>
                            </tr>
                            <tr>
                                <th>Account Type</th>
                                <td id="review_role"></td>
                            </tr>
                            <tr id="review_employee_id_row" style="display: none;">
                                <th>Employee ID</th>
                                <td id="review_employee_id"></td>
                            </tr>
                            <tr id="review_student_id_row" style="display: none;">
                                <th>Student ID</th>
                                <td id="review_student_id"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-pencil me-1"></i>Edit Details
                    </button>
                    <button type="button" class="btn btn-primary" id="submitRegistrationBtn">
                        <i class="bi bi-check-lg me-1"></i>Submit Registration
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="text-center mt-5 pt-4 border-top">
        <p class="text-muted">
            <i class="bi bi-c-circle"></i> {{ date('Y') }} ADSCO Learning Management System. All rights reserved.
        </p>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            console.log('Registration page loaded');
            
            // Show/hide ID fields based on role selection
            $('#role').change(function() {
                const role = $(this).val();
                
                if (role == '1' || role == '2' || role == '3') {
                    // Admin, Registrar, or Teacher - show Employee ID
                    $('#employeeIdField').show();
                    $('#employee_id').prop('required', true);
                    $('#studentIdField').hide();
                    $('#student_id').prop('required', false);
                } else if (role == '4') {
                    // Student - show Student ID
                    $('#studentIdField').show();
                    $('#student_id').prop('required', true);
                    $('#employeeIdField').hide();
                    $('#employee_id').prop('required', false);
                } else {
                    // No role selected
                    $('#employeeIdField').hide();
                    $('#studentIdField').hide();
                    $('#employee_id').prop('required', false);
                    $('#student_id').prop('required', false);
                }
            });
            
            // Trigger change on page load if role is already selected
            $('#role').trigger('change');
            
            // Review button click handler
            $('#reviewBtn').click(function() {
                reviewRegistration();
            });
            
            // Submit button in modal
            $('#submitRegistrationBtn').click(function() {
                submitRegistration();
            });
        });
        
        function reviewRegistration() {
            // Collect form data
            const formData = {
                f_name: $('#f_name').val(),
                l_name: $('#l_name').val(),
                age: $('#age').val(),
                sex: $('#sex').val(),
                contact: $('#contact').val(),
                email: $('#email').val(),
                role: $('#role').val(),
                employee_id: $('#employee_id').val(),
                student_id: $('#student_id').val(),
                role_name: $('#role option:selected').text()
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
            const password = $('#password').val();
            const confirmPassword = $('#password_confirmation').val();
            
            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                return;
            }
            
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }
            
            // Populate review modal
            $('#review_f_name').text(formData.f_name);
            $('#review_l_name').text(formData.l_name);
            $('#review_age').text(formData.age);
            $('#review_sex').text(formData.sex === 'male' ? 'Male' : 'Female');
            $('#review_contact').text(formData.contact);
            $('#review_email').text(formData.email);
            $('#review_role').text(formData.role_name);
            
            // Show/hide ID fields in review
            if (formData.role == '1' || formData.role == '2' || formData.role == '3') {
                $('#review_employee_id').text(formData.employee_id);
                $('#review_employee_id_row').show();
                $('#review_student_id_row').hide();
            } else if (formData.role == '4') {
                $('#review_student_id').text(formData.student_id);
                $('#review_student_id_row').show();
                $('#review_employee_id_row').hide();
            } else {
                $('#review_employee_id_row').hide();
                $('#review_student_id_row').hide();
            }
            
            // Show modal
            const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
            reviewModal.show();
        }
        
        function submitRegistration() {
            // Submit the form
            $('#registrationForm').submit();
        }
    </script>
</body>
</html>