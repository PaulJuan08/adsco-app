@extends('layouts.student')

@section('title', 'Timetable - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Timetable</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-calendar-week"></i> Your class schedule
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="empty-state" style="margin-top: 3rem;">
        <div class="empty-icon"><i class="fas fa-calendar-week"></i></div>
        <h3 class="empty-title">Timetable Coming Soon</h3>
        <p class="empty-text">Your class timetable will be available here once it has been set up by your administrator.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection
