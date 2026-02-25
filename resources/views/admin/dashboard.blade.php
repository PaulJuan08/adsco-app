@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Welcome back, {{ auth()->user()->f_name }}</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-calendar-day"></i> {{ now()->format('l, F d, Y') }}
                        @if($pendingApprovals > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $pendingApprovals }} approval{{ $pendingApprovals > 1 ? 's' : '' }} pending</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.users.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number">{{ number_format($totalUsers) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-link">
                View all users <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.users.index') }}?status=pending" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-number">{{ number_format($pendingApprovals) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-link">
                Review now <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.courses.index') }}" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Courses</div>
                    <div class="stat-number">{{ number_format($totalCourses) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-link">
                Manage courses <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.topics.index') }}" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($totalTopics) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-link">
                Browse topics <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Charts Grid - Row 1 -->
    <div class="charts-grid">
        <!-- Fixed User Distribution Card with Pie Chart and Statistics -->
        <div class="chart-card">
            <div class="chart-header">
                <span class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    User Distribution
                </span>
                <span class="badge badge-blue">{{ $totalUsers }} Total Users</span>
            </div>
            
            @php
                $total = max(1, $userStats['students'] + $userStats['teachers'] + $userStats['registrars'] + $userStats['admins']);
                $studentsPercent = round(($userStats['students'] / $total) * 100, 1);
                $teachersPercent = round(($userStats['teachers'] / $total) * 100, 1);
                $registrarsPercent = round(($userStats['registrars'] / $total) * 100, 1);
                $adminsPercent = round(($userStats['admins'] / $total) * 100, 1);
            @endphp

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1rem;">
                <!-- Left side: Donut Chart -->
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div style="position: relative; width: 180px; height: 180px;">
                        <canvas id="userDonutChart" width="180" height="180"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <span style="font-size: 2rem; font-weight: 700; color: var(--gray-900);">{{ $totalUsers }}</span>
                            <span style="font-size: 0.75rem; color: var(--gray-500); display: block;">users</span>
                        </div>
                    </div>
                </div>

                <!-- Right side: Statistics and Legend -->
                <div>
                    <!-- Legend -->
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 12px; height: 12px; border-radius: 4px; background: var(--chart-blue);"></span>
                            <span style="flex: 1; font-size: 0.875rem; color: var(--gray-700);">Students</span>
                            <span style="font-weight: 600; color: var(--gray-900);">{{ number_format($userStats['students']) }}</span>
                            <span style="font-size: 0.75rem; color: var(--gray-500);">({{ $studentsPercent }}%)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 12px; height: 12px; border-radius: 4px; background: var(--chart-green);"></span>
                            <span style="flex: 1; font-size: 0.875rem; color: var(--gray-700);">Teachers</span>
                            <span style="font-weight: 600; color: var(--gray-900);">{{ number_format($userStats['teachers']) }}</span>
                            <span style="font-size: 0.75rem; color: var(--gray-500);">({{ $teachersPercent }}%)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 12px; height: 12px; border-radius: 4px; background: var(--chart-yellow);"></span>
                            <span style="flex: 1; font-size: 0.875rem; color: var(--gray-700);">Registrars</span>
                            <span style="font-weight: 600; color: var(--gray-900);">{{ number_format($userStats['registrars']) }}</span>
                            <span style="font-size: 0.75rem; color: var(--gray-500);">({{ $registrarsPercent }}%)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 12px; height: 12px; border-radius: 4px; background: var(--chart-red);"></span>
                            <span style="flex: 1; font-size: 0.875rem; color: var(--gray-700);">Admins</span>
                            <span style="font-weight: 600; color: var(--gray-900);">{{ number_format($userStats['admins']) }}</span>
                            <span style="font-size: 0.75rem; color: var(--gray-500);">({{ $adminsPercent }}%)</span>
                        </div>
                    </div>

                    <!-- Quick Stats Summary -->
                    <div style="background: var(--gray-50); border-radius: var(--radius-lg); padding: 1rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <span style="font-size: 0.75rem; color: var(--gray-500);">Most Active Role</span>
                                <div style="font-size: 1rem; font-weight: 600; color: var(--gray-900);">
                                    @php
                                        $roles = [
                                            'students' => $userStats['students'],
                                            'teachers' => $userStats['teachers'],
                                            'registrars' => $userStats['registrars'],
                                            'admins' => $userStats['admins']
                                        ];
                                        $maxRole = array_keys($roles, max($roles))[0];
                                    @endphp
                                    {{ ucfirst($maxRole) }}
                                </div>
                            </div>
                            <div>
                                <span style="font-size: 0.75rem; color: var(--gray-500);">Average per Role</span>
                                <div style="font-size: 1rem; font-weight: 600; color: var(--gray-900);">
                                    {{ round($total / 4, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Overview Bar Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <span class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Platform Overview
                </span>
                <span class="badge badge-success">Live Stats</span>
            </div>
            <div class="bar-chart-container">
                @php
                    $maxValue = max($totalUsers, $totalCourses, $totalTopics, $totalQuizzes, $totalAssignments, $activeEnrollments, $todayLogins);
                    $bars = [
                        ['label' => 'Students', 'value' => $userStats['students'], 'color' => 'var(--chart-blue)', 'icon' => 'fa-user-graduate'],
                        ['label' => 'Teachers', 'value' => $userStats['teachers'], 'color' => 'var(--chart-green)', 'icon' => 'fa-chalkboard-teacher'],
                        ['label' => 'Courses', 'value' => $totalCourses, 'color' => 'var(--chart-yellow)', 'icon' => 'fa-book'],
                        ['label' => 'Topics', 'value' => $totalTopics, 'color' => 'var(--chart-purple)', 'icon' => 'fa-chalkboard'],
                        ['label' => 'Quizzes', 'value' => $totalQuizzes, 'color' => 'var(--chart-orange)', 'icon' => 'fa-question-circle'],
                        ['label' => 'Assignments', 'value' => $totalAssignments, 'color' => 'var(--chart-pink)', 'icon' => 'fa-tasks'],
                        ['label' => 'Enrollments', 'value' => $activeEnrollments, 'color' => 'var(--chart-teal)', 'icon' => 'fa-user-check'],
                        ['label' => 'Logins Today', 'value' => $todayLogins, 'color' => 'var(--chart-red)', 'icon' => 'fa-sign-in-alt'],
                    ];
                @endphp
                
                <div class="bar-chart">
                    @foreach($bars as $bar)
                    <div class="bar-row">
                        <span class="bar-label">
                            <i class="fas {{ $bar['icon'] }}" style="margin-right: 0.5rem; color: {{ $bar['color'] }};"></i>
                            {{ $bar['label'] }}
                        </span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: 0%; background: {{ $bar['color'] }};" 
                                 data-width="{{ $maxValue > 0 ? round(($bar['value'] / $maxValue) * 100) : 0 }}">
                                <span class="bar-tooltip">{{ number_format($bar['value']) }}</span>
                            </div>
                        </div>
                        <span class="bar-value">{{ number_format($bar['value']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid - Row 2: Monthly Creation Timeline -->
    <div class="charts-grid" style="margin-top: 1.25rem;">
        <!-- Monthly Creation Timeline Chart -->
        <div class="chart-card" style="grid-column: span 2;">
            <div class="chart-header">
                <span class="chart-title">
                    <i class="fas fa-calendar-alt"></i>
                    Monthly Creations - {{ date('Y') }}
                </span>
                <div class="chart-legend">
                    <span class="legend-item">
                        <span class="legend-dot" style="background: var(--chart-blue);"></span>
                        Users
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot" style="background: var(--chart-green);"></span>
                        Colleges
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot" style="background: var(--chart-orange);"></span>
                        Courses
                    </span>
                </div>
            </div>
            
            @php
                $currentYear = date('Y');
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                // Initialize arrays with zeros
                $monthlyUsers = array_fill(0, 12, 0);
                $monthlyColleges = array_fill(0, 12, 0);
                $monthlyCourses = array_fill(0, 12, 0);
                
                // Get actual data from database for the current year
                for ($i = 1; $i <= 12; $i++) {
                    $startDate = date('Y-m-d', strtotime($currentYear . '-' . $i . '-01'));
                    $endDate = date('Y-m-t', strtotime($currentYear . '-' . $i . '-01'));
                    
                    // Count users created in this month
                    $monthlyUsers[$i-1] = App\Models\User::whereBetween('created_at', [$startDate, $endDate])->count();
                    
                    // Count colleges created in this month
                    $monthlyColleges[$i-1] = App\Models\College::whereBetween('created_at', [$startDate, $endDate])->count();
                    
                    // Count courses created in this month
                    $monthlyCourses[$i-1] = App\Models\Course::whereBetween('created_at', [$startDate, $endDate])->count();
                }
                
                // Calculate totals
                $totalYearlyUsers = array_sum($monthlyUsers);
                $totalYearlyColleges = array_sum($monthlyColleges);
                $totalYearlyCourses = array_sum($monthlyCourses);
                
                // Find peak months
                $peakUserMonth = $months[array_search(max($monthlyUsers), $monthlyUsers)];
                $peakCollegeMonth = $months[array_search(max($monthlyColleges), $monthlyColleges)];
                $peakCourseMonth = $months[array_search(max($monthlyCourses), $monthlyCourses)];
            @endphp
            
            <div style="padding: 1.5rem;">
                <canvas id="monthlyCreationChart" width="800" height="300"></canvas>
            </div>
            
            <!-- Summary Stats for the Year -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding: 1rem 1.5rem 1.5rem; border-top: 1px solid var(--gray-200);">
                <div>
                    <span style="font-size: 0.75rem; color: var(--gray-500);">Total Users ({{ $currentYear }})</span>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--chart-blue);">{{ number_format($totalYearlyUsers) }}</div>
                    <span style="font-size: 0.7rem; color: var(--gray-500);">Peak: {{ $peakUserMonth }}</span>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--gray-500);">Total Colleges ({{ $currentYear }})</span>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--chart-green);">{{ number_format($totalYearlyColleges) }}</div>
                    <span style="font-size: 0.7rem; color: var(--gray-500);">Peak: {{ $peakCollegeMonth }}</span>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--gray-500);">Total Courses ({{ $currentYear }})</span>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--chart-orange);">{{ number_format($totalYearlyCourses) }}</div>
                    <span style="font-size: 0.7rem; color: var(--gray-500);">Peak: {{ $peakCourseMonth }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid" style="margin-top: 1.25rem;">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Pending Approvals Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user-clock" style="color: var(--warning); margin-right: 0.5rem;"></i>
                        Pending Approvals
                    </h2>
                    <a href="{{ route('admin.users.index') }}?status=pending" class="stat-link">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body">
                    @if($pendingUsers->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="empty-title">All Clear!</h3>
                            <p class="empty-text">No pending approvals at the moment. All users are approved and active.</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($pendingUsers->take(5) as $user)
                            <div class="list-item">
                                <div class="item-avatar" style="background: linear-gradient(135deg, var(--warning), var(--warning-dark));">
                                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $user->f_name }} {{ $user->l_name }}</div>
                                    <div class="item-details">{{ $user->email }}</div>
                                    <div class="item-meta">
                                        @php
                                            $roleName = match($user->role) {
                                                1 => 'Admin',
                                                2 => 'Registrar',
                                                3 => 'Teacher',
                                                4 => 'Student',
                                                default => 'Unknown'
                                            };
                                            $badgeClass = match($user->role) {
                                                1 => 'badge-danger',
                                                2 => 'badge-info',
                                                3 => 'badge-success',
                                                4 => 'badge-primary',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="item-badge {{ $badgeClass }}">
                                            <i class="fas fa-user"></i> {{ $roleName }}
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $user->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Are you sure you want to approve this user?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Content Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-stream" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Recent Content
                    </h2>
                    <span class="badge badge-blue">Latest</span>
                </div>
                
                <div class="card-body">
                    @php
                        $recentTopics = App\Models\Topic::latest()->take(3)->get();
                        $recentAssignments = App\Models\Assignment::latest()->take(3)->get();
                        $recentQuizzes = App\Models\Quiz::latest()->take(3)->get();
                    @endphp
                    
                    @if($recentTopics->isEmpty() && $recentAssignments->isEmpty() && $recentQuizzes->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3 class="empty-title">No Content Yet</h3>
                            <p class="empty-text">Start creating learning materials. Add topics, assignments, or quizzes to get started.</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($recentTopics as $topic)
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $topic->title }}</div>
                                    <div class="item-details">{{ Str::limit($topic->description, 60) }}</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-primary">
                                            <i class="fas fa-book"></i> Topic
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $topic->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($recentAssignments as $assignment)
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $assignment->title }}</div>
                                    <div class="item-details">
                                        @if(method_exists($assignment, 'course') && $assignment->course)
                                            {{ $assignment->course->title ?? 'Assignment' }}
                                        @else
                                            Assignment
                                        @endif
                                    </div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-success">
                                            <i class="fas fa-tasks"></i> Assignment
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $assignment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.todo.assignment.show', Crypt::encrypt($assignment->id)) }}" 
                                    class="btn btn-success btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($recentQuizzes as $quiz)
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $quiz->title }}</div>
                                    <div class="item-details">
                                        @if(method_exists($quiz, 'course') && $quiz->course)
                                            {{ $quiz->course->title ?? 'Quiz' }}
                                        @else
                                            Quiz
                                        @endif
                                    </div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-warning">
                                            <i class="fas fa-question-circle"></i> Quiz
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $quiz->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--warning); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.topics.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="action-title">Create Topic</div>
                            <div class="action-subtitle">Add new learning material</div>
                        </a>
                        
                        <a href="{{ route('admin.quizzes.create') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="action-title">Create Quiz</div>
                            <div class="action-subtitle">Add new quiz/test</div>
                        </a>
                        
                        <a href="{{ route('admin.assignments.create') }}" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="action-title">Create Assignment</div>
                            <div class="action-subtitle">Add new assignment</div>
                        </a>
                        
                        <a href="{{ route('admin.courses.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book-medical"></i>
                            </div>
                            <div class="action-title">Create Course</div>
                            <div class="action-subtitle">Setup new academic course</div>
                        </a>
                        
                        <a href="{{ route('admin.users.create') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-title">Add User</div>
                            <div class="action-subtitle">Register staff or student</div>
                        </a>
                        
                        <a href="{{ route('admin.colleges.create') }}" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="action-title">Add College</div>
                            <div class="action-subtitle">Create new college</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Overview Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        System Overview
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <a href="{{ route('admin.quizzes.index') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Quizzes</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ number_format($totalQuizzes) }}</div>
                        </a>
                        
                        <a href="{{ route('admin.assignments.index') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Assignments</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ number_format($totalAssignments) }}</div>
                        </a>
                        
                        <a href="{{ route('admin.colleges.index') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Colleges</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ number_format(App\Models\College::count()) }}</div>
                        </a>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Today's Logins</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ number_format($todayLogins) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unverified Users Alert -->
            @if($unverifiedCount > 0)
            <div class="dashboard-card" style="background: linear-gradient(135deg, #fff7ed, #fef3c7); border-color: #fde68a;">
                <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 1.25rem;">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div style="flex: 1;">
                        <h4 style="font-weight: 700; color: #92400e; margin: 0;">{{ $unverifiedCount }} Unverified {{ Str::plural('User', $unverifiedCount) }}</h4>
                        <p style="font-size: 0.75rem; color: #b45309; margin: 0;">Email verification pending</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-warning btn-sm">
                        View <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Version 1.0.0 • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // 1. USER DISTRIBUTION DONUT CHART
    // ============================================
    const donutCtx = document.getElementById('userDonutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Students', 'Teachers', 'Registrars', 'Admins'],
            datasets: [{
                data: [
                    {{ $userStats['students'] }},
                    {{ $userStats['teachers'] }},
                    {{ $userStats['registrars'] }},
                    {{ $userStats['admins'] }}
                ],
                backgroundColor: [
                    'var(--chart-blue)',
                    'var(--chart-green)',
                    'var(--chart-yellow)',
                    'var(--chart-red)'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // ============================================
    // 2. MONTHLY CREATION TIMELINE CHART
    // ============================================
    const timelineCtx = document.getElementById('monthlyCreationChart').getContext('2d');
    new Chart(timelineCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Users',
                    data: {{ json_encode($monthlyUsers) }},
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'var(--chart-blue)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Colleges',
                    data: {{ json_encode($monthlyColleges) }},
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'var(--chart-green)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Courses',
                    data: {{ json_encode($monthlyCourses) }},
                    backgroundColor: 'rgba(249, 115, 22, 0.7)',
                    borderColor: 'var(--chart-orange)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'var(--gray-900)',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255, 255, 255, 0.8)',
                    borderColor: 'var(--gray-800)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw || 0;
                            return `${label}: ${value}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'var(--gray-200)',
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        color: 'var(--gray-500)'
                    },
                    title: {
                        display: true,
                        text: 'Number Created',
                        color: 'var(--gray-500)',
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'var(--gray-600)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // ============================================
    // 3. ANIMATE BAR CHARTS
    // ============================================
    document.querySelectorAll('.bar-fill').forEach(function(el, index) {
        const width = el.dataset.width || '0';
        setTimeout(function() {
            el.style.transition = 'width 1s cubic-bezier(0.4, 0, 0.2, 1)';
            el.style.width = width + '%';
        }, 200 + (index * 30));
    });

    // ============================================
    // 4. STAGGER ANIMATION FOR STAT CARDS
    // ============================================
    document.querySelectorAll('.stat-card').forEach(function(el, index) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(function() {
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 100 + (index * 80));
    });

    // ============================================
    // 5. TOOLTIP FOR BAR CHARTS
    // ============================================
    const tooltip = document.createElement('div');
    tooltip.className = 'chart-tooltip';
    tooltip.style.cssText = `
        position: fixed;
        background: var(--gray-900);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s;
        z-index: 1000;
    `;
    document.body.appendChild(tooltip);

    document.querySelectorAll('.bar-fill').forEach(function(el) {
        el.addEventListener('mouseenter', function(e) {
            const value = this.parentElement.nextElementSibling.textContent;
            tooltip.textContent = value;
            tooltip.style.opacity = '1';
        });

        el.addEventListener('mousemove', function(e) {
            tooltip.style.left = (e.pageX + 10) + 'px';
            tooltip.style.top = (e.pageY - 30) + 'px';
        });

        el.addEventListener('mouseleave', function() {
            tooltip.style.opacity = '0';
        });
    });
});
</script>

<style>
/* Additional styles for tooltips and chart enhancements */
.bar-fill {
    position: relative;
    transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.bar-fill:hover {
    filter: brightness(1.1);
}

.bar-tooltip {
    position: absolute;
    right: 5px;
    top: -20px;
    background: var(--gray-900);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    opacity: 0;
    transition: opacity 0.2s;
    pointer-events: none;
    white-space: nowrap;
}

.bar-fill:hover .bar-tooltip {
    opacity: 1;
}

/* Chart container adjustments */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}

@media (max-width: 1024px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

/* Donut chart container */
#userDonutChart {
    max-width: 180px;
    max-height: 180px;
}

/* Timeline chart container */
#monthlyCreationChart {
    width: 100%;
    height: 300px !important;
}
</style>
@endpush
@endsection