@extends('layouts.admin')

@section('title', 'Edit Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile-form.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-edit me-2"></i>
                        Edit Profile
                    </h1>
                    <p class="page-subtitle">Update your personal information and password</p>
                </div>
                <a href="{{ route('admin.profile.show') }}" class="btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Display validation errors -->
    @if($errors->any())
    <div class="validation-alert" style="max-width: 900px; margin: 0 auto 1.5rem;">
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

    <!-- Display success message -->
    @if(session('success'))
    <div class="success-alert" style="max-width: 900px; margin: 0 auto 1.5rem;">
        <div style="display: flex; align-items: center;">
            <i class="fas fa-check-circle"></i>
            <strong>{{ session('success') }}</strong>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Profile Edit Form -->
            <div class="form-container">
                <div class="card-header">
                    <div class="card-title-group">
                        <i class="fas fa-user-edit card-icon"></i>
                        <h2 class="card-title">Edit Profile Information</h2>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Profile Information Form -->
                    <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Avatar Preview Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-user-circle"></i> Profile Avatar
                            </div>
                            
                            <div class="text-center mb-4">
                                @php
                                    $previewClass = 'default-preview';
                                    $previewIcon = 'fa-user-circle';
                                    
                                    $currentSex = old('sex', $user->sex ?? '');
                                    
                                    if($currentSex == 'male') {
                                        $previewClass = 'male-preview';
                                        $previewIcon = 'fa-mars';
                                    } elseif($currentSex == 'female') {
                                        $previewClass = 'female-preview';
                                        $previewIcon = 'fa-venus';
                                    }
                                @endphp
                                
                                <div class="avatar-preview-icon {{ $previewClass }}" id="avatarPreview">
                                    <i class="fas {{ $previewIcon }}" id="previewIcon"></i>
                                </div>
                                <div class="mt-2">
                                    <small class="form-text">
                                        <i class="fas fa-info-circle"></i> Avatar is based on your gender selection
                                    </small>
                                </div>
                            </div>
                        </div>
                        
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
                                           class="form-control @error('f_name') is-invalid @enderror" 
                                           id="f_name" 
                                           name="f_name" 
                                           value="{{ old('f_name', $user->f_name) }}" 
                                           required>
                                    @error('f_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="l_name" class="form-label required">
                                        <i class="fas fa-user"></i> Last Name
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('l_name') is-invalid @enderror" 
                                           id="l_name" 
                                           name="l_name" 
                                           value="{{ old('l_name', $user->l_name) }}" 
                                           required>
                                    @error('l_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="form-label required">
                                        <i class="fas fa-envelope"></i> Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> Used for login
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contact" class="form-label">
                                        <i class="fas fa-phone"></i> Contact Number
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('contact') is-invalid @enderror" 
                                           id="contact" 
                                           name="contact" 
                                           value="{{ old('contact', $user->contact) }}"
                                           placeholder="+63 912 345 6789">
                                    @error('contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="sex" class="form-label">
                                        <i class="fas fa-venus-mars"></i> Gender
                                    </label>
                                    <select id="sex" name="sex" class="form-select @error('sex') is-invalid @enderror" onchange="updateAvatarPreview(this.value)">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('sex', $user->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> Used for avatar display
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-grid" style="margin-top: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user-tag"></i> Role
                                    </label>
                                    <input type="text" class="form-control" value="Administrator" readonly disabled>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-id-badge"></i> Employee ID
                                    </label>
                                    <input type="text" class="form-control" value="{{ $user->employee_id ?? 'Not assigned' }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Security Section - Password Change (Optional) -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-shield-alt"></i> Change Password (Optional)
                            </div>
                            
                            <div class="alert alert-info" style="margin-bottom: 1.5rem; background: #ebf8ff; border: 1px solid #90cdf4; color: #2c5282; border-radius: 8px; padding: 0.75rem 1rem;">
                                <i class="fas fa-info-circle"></i>
                                Leave password fields blank if you don't want to change your password.
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-key"></i> New Password
                                    </label>
                                    <input type="password" 
                                           id="password" 
                                           name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Enter new password" 
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
                                </div>
                                
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-check-circle"></i> Confirm New Password
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
                        
                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-light">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitButton">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update avatar preview based on gender selection
    function updateAvatarPreview(gender) {
        const previewDiv = document.getElementById('avatarPreview');
        const previewIcon = document.getElementById('previewIcon');
        
        // Remove existing gender classes
        previewDiv.classList.remove('male-preview', 'female-preview', 'default-preview');
        
        if (gender === 'male') {
            previewDiv.classList.add('male-preview');
            previewIcon.className = 'fas fa-mars';
        } else if (gender === 'female') {
            previewDiv.classList.add('female-preview');
            previewIcon.className = 'fas fa-venus';
        } else {
            previewDiv.classList.add('default-preview');
            previewIcon.className = 'fas fa-user-circle';
        }
    }

    // Password strength meter
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    
    if (passwordInput && passwordStrength && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check password criteria
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Reset classes
            passwordStrength.className = 'password-strength-fill';
            
            if (password.length === 0) { 
                strengthText.textContent = ''; 
                return; 
            }
            
            // Set strength level
            if (strength <= 2) { 
                passwordStrength.classList.add('strength-weak'); 
                strengthText.innerHTML = '<i class="fas fa-exclamation-circle"></i> Weak'; 
                strengthText.style.color = '#f56565'; 
            }
            else if (strength <= 4) { 
                passwordStrength.classList.add('strength-fair'); 
                strengthText.innerHTML = '<i class="fas fa-info-circle"></i> Fair'; 
                strengthText.style.color = '#ed8936'; 
            }
            else if (strength <= 5) { 
                passwordStrength.classList.add('strength-good'); 
                strengthText.innerHTML = '<i class="fas fa-check-circle"></i> Good'; 
                strengthText.style.color = '#ecc94b'; 
            }
            else { 
                passwordStrength.classList.add('strength-strong'); 
                strengthText.innerHTML = '<i class="fas fa-shield-alt"></i> Strong'; 
                strengthText.style.color = '#48bb78'; 
            }
        });
    }

    // Password match validation
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatch = document.getElementById('passwordMatch');
    
    if (confirmPasswordInput && passwordMatch) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput ? passwordInput.value : '';
            
            if (this.value) {
                if (password === this.value) {
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

    // Form submit validation
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const newPassword = passwordInput ? passwordInput.value : '';
        const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : '';
        
        // Only validate if password fields are filled
        if (newPassword || confirmPassword) {
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
                return;
            }
            
            if (newPassword.length > 0 && newPassword.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return;
            }
        }
        
        const submitBtn = document.getElementById('submitButton');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
    });
</script>
@endpush