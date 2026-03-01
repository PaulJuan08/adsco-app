@extends('layouts.teacher')

@section('title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile-show.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-user-circle me-2"></i>
                    My Profile
                </h1>
                <p class="page-subtitle">View and manage your profile information</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="message-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="message-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Profile Card -->
            <div class="profile-container">
                <div class="profile-card">
                    <div class="profile-header text-center">
                        <div class="profile-avatar-wrapper">
                            @php
                                $avatarClass = 'default';
                                $avatarIcon = 'fa-user-circle';
                                
                                if(isset($user->sex)) {
                                    if($user->sex == 'male') {
                                        $avatarClass = 'male';
                                        $avatarIcon = 'fa-mars';
                                    } elseif($user->sex == 'female') {
                                        $avatarClass = 'female';
                                        $avatarIcon = 'fa-venus';
                                    }
                                }
                            @endphp
                            
                            <div class="profile-avatar-icon {{ $avatarClass }}">
                                <i class="fas {{ $avatarIcon }}"></i>
                            </div>
                        </div>
                        <h2 class="profile-name">{{ $user->f_name }} {{ $user->l_name }}</h2>
                        <p class="profile-role">
                            <span class="badge">
                                <i class="fas fa-chalkboard-teacher me-1"></i>
                                Teacher
                            </span>
                        </p>
                    </div>
                    
                    <div class="profile-body">
                        <div class="detail-section">
                            <div class="detail-section-title">
                                <i class="fas fa-id-card"></i>
                                Personal Information
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-envelope fa-fw"></i> Email
                                </div>
                                <div class="detail-value">{{ $user->email }}</div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-phone fa-fw"></i> Contact
                                </div>
                                <div class="detail-value">{{ $user->contact ?? 'Not provided' }}</div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-venus-mars fa-fw"></i> Gender
                                </div>
                                <div class="detail-value">{{ $user->sex ? ucfirst($user->sex) : 'Not specified' }}</div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-clock fa-fw"></i> Last Login
                                </div>
                                <div class="detail-value">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</div>
                            </div>

                            @if($user->employee_id)
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-id-badge fa-fw"></i> Employee ID
                                </div>
                                <div class="detail-value">{{ $user->employee_id }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="profile-footer">
                        <a href="{{ route('teacher.profile.edit') }}" class="btn btn-primary w-100">
                            <i class="fas fa-edit me-2"></i>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection