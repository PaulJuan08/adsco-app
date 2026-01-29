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
        
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ str_starts_with($currentRoute, 'admin.users') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
        
        <a href="{{ route('admin.courses.index') }}" class="nav-item {{ str_starts_with($currentRoute, 'admin.courses') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>Courses</span>
        </a>
        
        <a href="{{ route('admin.attendance') }}" class="nav-item {{ $currentRoute == 'admin.attendance' ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i>
            <span>Attendance</span>
        </a>
        
        <a href="{{ route('admin.audit-logs') }}" class="nav-item {{ $currentRoute == 'admin.audit-logs' ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Audit Logs</span>
        </a>
        
        <a href="{{ route('admin.profile.edit') }}" class="nav-item {{ $currentRoute == 'admin.profile.edit' ? 'active' : '' }}">
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