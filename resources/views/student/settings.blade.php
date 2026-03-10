@extends('layouts.student')

@section('title', 'Settings - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Settings</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-cog"></i> Manage your account preferences
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-container" style="max-width: 700px; margin: 2rem auto;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-cog card-icon"></i>
                <h2 class="card-title">Account Settings</h2>
            </div>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="{{ route('profile.edit') }}"
                   style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; text-decoration: none; color: #1f2937;">
                    <div style="width: 42px; height: 42px; background: #ede9fe; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-edit" style="color: #7c3aed;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Edit Profile</div>
                        <div style="font-size: 0.85rem; color: #6b7280;">Update your personal information and photo</div>
                    </div>
                    <i class="fas fa-chevron-right" style="margin-left: auto; color: #9ca3af;"></i>
                </a>

                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; opacity: 0.6;">
                    <div style="width: 42px; height: 42px; background: #fef3c7; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-bell" style="color: #d97706;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Notification Preferences</div>
                        <div style="font-size: 0.85rem; color: #6b7280;">Coming soon</div>
                    </div>
                    <span style="margin-left: auto; font-size: 0.78rem; background: #fef3c7; color: #92400e; padding: 0.2rem 0.6rem; border-radius: 999px;">Soon</span>
                </div>

                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; opacity: 0.6;">
                    <div style="width: 42px; height: 42px; background: #d1fae5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-lock" style="color: #059669;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Privacy & Security</div>
                        <div style="font-size: 0.85rem; color: #6b7280;">Coming soon</div>
                    </div>
                    <span style="margin-left: auto; font-size: 0.78rem; background: #d1fae5; color: #065f46; padding: 0.2rem 0.6rem; border-radius: 999px;">Soon</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
