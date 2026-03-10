@extends('layouts.student')

@section('title', 'Attendance - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Attendance</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-clipboard-list"></i> Track your attendance records
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="empty-state" style="margin-top: 3rem;">
        <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
        <h3 class="empty-title">Attendance Coming Soon</h3>
        <p class="empty-text">Your attendance records will be displayed here once the feature is enabled.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection
