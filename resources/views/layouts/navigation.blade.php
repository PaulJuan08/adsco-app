<nav class="navbar navbar-expand-lg navbar-dark bg-school-navy">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <span class="text-heritage-gold fw-bold">ADSCO</span> LMS
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.courses.index') }}">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.attendance') }}">Attendance</a>
                    </li>
                @elseif(auth()->user()->isRegistrar())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('registrar.users.index') }}">Users</a>
                    </li>
                @elseif(auth()->user()->isTeacher())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teacher.courses.index') }}">My Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teacher.quizzes.index') }}">Quizzes</a>
                    </li>
                @elseif(auth()->user()->isStudent())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('student.courses.index') }}">Courses</a>
                    </li>
                @endif
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->full_name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        @if(auth()->user()->isStudent())
                        <a class="dropdown-item" href="{{ route('student.attendance') }}">
                            <i class="fas fa-calendar-check"></i> My Attendance
                        </a>
                        <a class="dropdown-item" href="{{ route('student.progress') }}">
                            <i class="fas fa-chart-line"></i> My Progress
                        </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>