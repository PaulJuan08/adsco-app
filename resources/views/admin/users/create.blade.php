@extends('layouts.admin')

@section('title', 'Add New User - Admin Dashboard')

@push('styles')
<style>
    /* Modern Form Container - Smaller and Better Spaced */
    .form-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin: 1.5rem auto;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        max-width: 1200px;
        width: 95%;
    }

    .form-container:hover {
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .view-all-link {
        color: white;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .view-all-link:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        gap: 0.75rem;
    }

    .card-body {
        padding: 1.5rem 1.75rem;
    }

    /* Form Sections - Compact */
    .form-section {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .form-section:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
    }
    
    .form-section-title i {
        color: #667eea;
        font-size: 1.125rem;
        width: 20px;
        text-align: center;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
        position: relative;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.375rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.8125rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .form-label.required::after {
        content: " *";
        color: #f56565;
    }
    
    .form-control {
        display: block;
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #1a202c;
        background-color: white;
        background-clip: padding-box;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    
    .form-control:focus {
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .form-control.is-invalid {
        border-color: #f56565;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23f56565' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23f56565' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.875rem center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        padding-right: calc(1.5em + 0.875rem);
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #f56565;
        font-weight: 500;
    }
    
    .form-text {
        font-size: 0.6875rem;
        color: #718096;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .form-select {
        display: block;
        width: 100%;
        padding: 0.625rem 2.25rem 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #1a202c;
        background-color: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23718096' viewBox='0 0 16 16'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.875rem center;
        background-size: 16px 12px;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .form-select.is-invalid {
        border-color: #f56565;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23f56565' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23f56565' stroke='none'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23718096' viewBox='0 0 16 16'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        background-position: right 2.25rem center, right 0.875rem center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem), 16px 12px;
        padding-right: calc(2.25rem + 1.125rem);
    }
    
    /* Password Strength Meter - Compact */
    .password-strength-container {
        margin-top: 0.5rem;
    }
    
    .password-strength-meter {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        margin-bottom: 0.375rem;
        overflow: hidden;
        position: relative;
    }
    
    .password-strength-fill {
        height: 100%;
        width: 0%;
        transition: all 0.4s ease;
        border-radius: 2px;
        position: relative;
    }
    
    .password-strength-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    .strength-weak {
        background: linear-gradient(90deg, #f56565 0%, #e53e3e 100%);
        width: 33.33%;
    }
    
    .strength-medium {
        background: linear-gradient(90deg, #ed8936 0%, #dd6b20 100%);
        width: 66.66%;
    }
    
    .strength-strong {
        background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
        width: 100%;
    }
    
    .strength-text {
        font-size: 0.6875rem;
        font-weight: 600;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    /* Form Actions - Compact */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 0 0.5rem;
        border-top: 1px solid #e2e8f0;
        margin-top: 0.5rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 600;
        font-size: 0.8125rem;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1.5px solid transparent;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.3px;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.7s ease;
    }

    .btn:hover::before {
        left: 100%;
    }
    
    .btn-outline {
        background: white;
        border-color: #cbd5e0;
        color: #4a5568;
    }
    
    .btn-outline:hover {
        background: #f7fafc;
        border-color: #a0aec0;
        color: #2d3748;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary:active {
        transform: translateY(-1px);
    }
    
    /* Validation alert - Compact */
    .validation-alert {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        color: #c53030;
        border: 1px solid #fc8181;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        font-size: 0.8125rem;
        box-shadow: 0 2px 8px rgba(245, 101, 101, 0.1);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .validation-alert i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    .validation-alert ul {
        margin: 0.5rem 0 0 1.25rem;
        padding: 0;
    }
    
    /* Role options - Modern and Compact */
    .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .role-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .role-option::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .role-option:hover::before {
        transform: scaleX(1);
    }
    
    .role-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e0;
    }
    
    .role-option.active {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }

    .role-option.active::before {
        transform: scaleX(1);
    }
    
    .role-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    
    .role-option:hover .role-icon {
        transform: rotate(8deg) scale(1.05);
    }
    
    .role-content {
        flex: 1;
        min-width: 0;
    }
    
    .role-title {
        font-weight: 700;
        font-size: 0.875rem;
        color: #2d3748;
        margin-bottom: 0.125rem;
    }
    
    .role-description {
        font-size: 0.6875rem;
        color: #718096;
        line-height: 1.3;
    }
    
    .role-id-required {
        font-size: 0.625rem;
        color: #a0aec0;
        margin-top: 0.125rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .role-check {
        color: #667eea;
        opacity: 0;
        transition: opacity 0.3s ease;
        font-size: 1rem;
    }
    
    .role-option.active .role-check {
        opacity: 1;
    }
    
    /* ID Fields Section - Compact */
    #idFieldsSection {
        margin-top: 1rem;
        padding: 1.25rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
        border-radius: 10px;
        border: 1px dashed #cbd5e0;
        transition: all 0.3s ease;
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .id-field-group {
        background: white;
        padding: 1.25rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }
    
    .id-suggestion {
        font-size: 0.6875rem;
        color: #48bb78;
        background: #f0fff4;
        padding: 0.375rem 0.625rem;
        border-radius: 6px;
        margin-top: 0.375rem;
        border: 1px solid #9ae6b4;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* User Preview for Create Page */
    .user-preview {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .user-preview-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 auto 0.75rem;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .user-preview-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.125rem;
    }
    
    .user-preview-email {
        font-size: 0.8125rem;
        color: #718096;
        margin-bottom: 0.5rem;
    }
    
    .user-preview-role {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.6875rem;
        font-weight: 600;
        background: #edf2f7;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }

    /* Responsive Design */
    @media (max-width: 1280px) {
        .form-container {
            max-width: 1100px;
            width: 90%;
        }
    }

    @media (max-width: 768px) {
        .form-container {
            width: 95%;
            margin: 1rem auto;
        }

        .card-header {
            padding: 1rem 1.25rem;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .view-all-link {
            align-self: stretch;
            justify-content: center;
        }

        .card-body {
            padding: 1.25rem;
        }

        .form-section {
            padding: 1rem 1.25rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .role-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .form-container {
            width: 98%;
            margin: 0.75rem auto;
            border-radius: 16px;
        }

        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.125rem;
        }

        .form-section-title {
            font-size: 0.9375rem;
        }

        .role-title {
            font-size: 0.8125rem;
        }

        .role-icon {
            width: 36px;
            height: 36px;
            font-size: 0.875rem;
        }

        .form-section {
            padding: 1rem;
        }

        #idFieldsSection {
            padding: 1rem;
        }

        .id-field-group {
            padding: 1rem;
        }
    }
</style>
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