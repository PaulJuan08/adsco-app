<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">S</div>
        <div class="logo-text">SchoolSys</div>
    </div>
    
    <nav class="sidebar-nav">
        @php
            $currentRoute = Route::currentRouteName();
        @endphp
        
        <a href="{{ route('dashboard') }}" class="nav-item {{ $currentRoute == 'dashboard' ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('student.courses') }}" class="nav-item {{ $currentRoute == 'student.courses' ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>My Courses</span>
        </a>
        
        <a href="{{ route('student.attendance') }}" class="nav-item {{ $currentRoute == 'student.attendance' ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i>
            <span>My Attendance</span>
        </a>
        
        <a href="{{ route('student.grades') }}" class="nav-item {{ $currentRoute == 'student.grades' ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>My Grades</span>
        </a>
        
        <a href="{{ route('student.schedule') }}" class="nav-item {{ $currentRoute == 'student.schedule' ? 'active' : '' }}">
            <i class="fas fa-clock"></i>
            <span>My Schedule</span>
        </a>
        
        <a href="{{ route('student.profile.edit') }}" class="nav-item {{ $currentRoute == 'student.profile.edit' ? 'active' : '' }}">
            <i class="fas fa-user-cog"></i>
            <span>Profile</span>
        </a>
    </nav>
    
    <button class="nav-item logout-btn" onclick="document.getElementById('logout-form').submit()">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </button>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</aside>