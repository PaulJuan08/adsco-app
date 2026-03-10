@extends('layouts.student')

@section('title', 'Programs - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Degree Programs</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-graduation-cap"></i> Browse all available academic programs
                    </p>
                </div>
            </div>
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-graduation-cap"></i>
                    <span class="badge-count">{{ $totalPrograms }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Active Programs</div>
                    <div class="alert-subtitle">{{ $totalColleges }} colleges</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Programs</div>
                    <div class="stat-number">{{ number_format($totalPrograms) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Colleges</div>
                    <div class="stat-number">{{ number_format($totalColleges) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-university"></i></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('student.programs.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
            <div class="search-container" style="flex: 1; min-width: 200px; max-width: 400px;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="search-input" placeholder="Search programs..."
                    value="{{ request('search') }}">
            </div>
            <select name="college_id" class="form-select" style="padding: 0.6rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; color: #374151; background: white; min-width: 180px;">
                <option value="">All Colleges</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}" {{ request('college_id') == $college->id ? 'selected' : '' }}>
                        {{ $college->college_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.25rem;">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if(request('search') || request('college_id'))
            <a href="{{ route('student.programs.index') }}" class="btn btn-outline" style="padding: 0.6rem 1.25rem;">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Programs Grid -->
    @if($programs->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-graduation-cap"></i></div>
            <h3 class="empty-title">No programs found</h3>
            <p class="empty-text">
                @if(request('search') || request('college_id'))
                    No programs match your search criteria.
                @else
                    There are no active programs at the moment.
                @endif
            </p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
            @foreach($programs as $program)
            @php $encryptedId = Crypt::encrypt($program->id); @endphp
            <div style="background: white; border-radius: 12px; box-shadow: var(--shadow-md); overflow: hidden; cursor: pointer; border: 1px solid #e5e7eb;"
                 onclick="window.location.href='{{ route('student.programs.show', $encryptedId) }}'">
                <div style="background: linear-gradient(135deg, #059669, #10b981); padding: 1.5rem; color: white;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 55px; height: 55px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: bold;">
                            {{ strtoupper(substr($program->program_name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; line-height: 1.3;">{{ $program->program_name }}</h3>
                            @if($program->program_code)
                            <p style="margin: 0.25rem 0 0; opacity: 0.9; font-size: 0.85rem;">
                                <i class="fas fa-code"></i> {{ $program->program_code }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-university" style="color: #6b7280; font-size: 0.85rem;"></i>
                        <span style="font-size: 0.85rem; color: #6b7280;">{{ $program->college->college_name ?? 'N/A' }}</span>
                    </div>

                    @if($program->description)
                    <p style="color: #4b5563; font-size: 0.88rem; line-height: 1.5; margin-bottom: 0.75rem;">
                        {{ Str::limit($program->description, 90) }}
                    </p>
                    @endif

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f3f4f6; padding-top: 0.75rem; margin-top: 0.5rem;">
                        <span style="font-size: 0.85rem; color: #6b7280;">
                            <i class="fas fa-users"></i> {{ number_format($program->students_count) }} students
                        </span>
                        <span style="color: #059669; font-size: 0.88rem; font-weight: 500;">
                            View Details <i class="fas fa-arrow-right"></i>
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($programs->hasPages())
        <div style="margin-top: 2rem;">
            {{ $programs->appends(request()->query())->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
