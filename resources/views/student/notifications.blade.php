@extends('layouts.student')

@section('title', 'Notifications - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Notifications</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-bell"></i> Stay updated with your latest alerts
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="empty-state" style="margin-top: 3rem;">
        <div class="empty-icon"><i class="fas fa-bell-slash"></i></div>
        <h3 class="empty-title">No Notifications</h3>
        <p class="empty-text">You have no notifications at this time. Check back later for updates from your teachers and administrators.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection
