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
        
        <a href="{{ route('registrar.users.index') }}" class="nav-item {{ str_starts_with($currentRoute, 'registrar.users') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
        
        <a href="{{ route('registrar.enrollments') }}" class="nav-item {{ $currentRoute == 'registrar.enrollments' ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i>
            <span>Enrollments</span>
        </a>
        
        <a href="{{ route('registrar.schedule') }}" class="nav-item {{ $currentRoute == 'registrar.schedule' ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Schedule</span>
        </a>
        
        <a href="{{ route('registrar.profile.edit') }}" class="nav-item {{ $currentRoute == 'registrar.profile.edit' ? 'active' : '' }}">
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