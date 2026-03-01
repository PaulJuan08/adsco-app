@extends('layouts.admin')

@section('title', 'Student Progress — Quizzes & Assignments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/todo-index.css') }}">
<style>
    /* Progress page specific styles */
    .progress-filters {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .filter-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-label i {
        margin-right: 0.25rem;
        color: #667eea;
        width: 16px;
    }

    .filter-select, .filter-input {
        padding: 0.625rem 0.875rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: white;
    }

    .filter-select:focus, .filter-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-select:disabled {
        background: #f7fafc;
        color: #a0aec0;
        cursor: not-allowed;
    }

    .progress-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        background: white;
        padding: 0.5rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .progress-tab {
        flex: 1;
        padding: 0.75rem;
        text-align: center;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        color: #718096;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
        background: transparent;
    }

    .progress-tab.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .progress-tab i {
        margin-right: 0.375rem;
    }

    .progress-table {
        width: 100%;
        border-collapse: collapse;
    }

    .progress-table th {
        text-align: left;
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        background: #f8fafc;
    }

    .progress-table td {
        padding: 1rem;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.875rem;
        color: #2d3748;
    }

    .progress-table tr:hover td {
        background: #f8fafc;
    }

    .student-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .student-info {
        flex: 1;
    }

    .student-name {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 0.125rem;
    }

    .student-meta {
        font-size: 0.75rem;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .score-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .score-high {
        background: #f0fff4;
        color: #22543d;
        border: 1px solid #9ae6b4;
    }

    .score-medium {
        background: #fefcbf;
        color: #975a16;
        border: 1px solid #fbd38d;
    }

    .score-low {
        background: #fff5f5;
        color: #c53030;
        border: 1px solid #feb2b2;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-passed {
        background: #f0fff4;
        color: #22543d;
        border: 1px solid #9ae6b4;
    }

    .status-failed {
        background: #fff5f5;
        color: #c53030;
        border: 1px solid #feb2b2;
    }

    .status-pending {
        background: #fefcbf;
        color: #975a16;
        border: 1px solid #fbd38d;
    }

    .status-graded {
        background: #e6fffa;
        color: #2c7a7b;
        border: 1px solid #9de0d9;
    }

    .status-submitted {
        background: #e6fffa;
        color: #2c7a7b;
        border: 1px solid #9de0d9;
    }

    .status-late {
        background: #fff5f5;
        color: #c53030;
        border: 1px solid #feb2b2;
    }

    .view-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #4a5568;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .view-btn:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px dashed #e2e8f0;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #718096;
        margin-bottom: 1.5rem;
    }

    .pagination-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        font-size: 0.875rem;
        color: #718096;
    }

    .pagination-links {
        display: flex;
        gap: 0.25rem;
    }

    .pagination-links a, .pagination-links span {
        padding: 0.375rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #4a5568;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination-links a:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination-links .active span {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .item-filter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8fafc;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #4a5568;
    }

    .item-filter i {
        color: #667eea;
    }

    .item-filter .remove {
        color: #f56565;
        cursor: pointer;
        transition: color 0.2s;
    }

    .item-filter .remove:hover {
        color: #c53030;
    }

    @media (max-width: 768px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .progress-table {
            display: block;
            overflow-x: auto;
        }

        .student-cell {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Header --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Student Progress</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-tasks"></i> Track student performance across quizzes and assignments
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Progress Tabs --}}
    <div class="progress-tabs">
        <a href="{{ route('admin.todo.progress', ['type' => 'quiz'] + request()->except('type')) }}" 
           class="progress-tab {{ $type === 'quiz' ? 'active' : '' }}">
            <i class="fas fa-brain"></i> Quiz Attempts
        </a>
        <a href="{{ route('admin.todo.progress', ['type' => 'assignment'] + request()->except('type')) }}" 
           class="progress-tab {{ $type === 'assignment' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Assignment Submissions
        </a>
    </div>

    {{-- Filters --}}
    <div class="progress-filters">
        <form method="GET" action="{{ route('admin.todo.progress') }}">
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div class="filter-grid">
                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-university"></i> College</span>
                    <select name="college_id" class="filter-select" id="college-filter">
                        <option value="">All Colleges</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}" {{ $collegeId == $college->id ? 'selected' : '' }}>
                                {{ $college->college_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-graduation-cap"></i> Program</span>
                    <select name="program_id" class="filter-select" id="program-filter" {{ !$collegeId ? 'disabled' : '' }}>
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ $programId == $program->id ? 'selected' : '' }}>
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-calendar"></i> Year</span>
                    <select name="year" class="filter-select">
                        <option value="">All Years</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-search"></i> Student</span>
                    <input type="text" 
                           name="search_name" 
                           class="filter-input"
                           placeholder="Name..." 
                           value="{{ $searchName }}">
                </div>

                @if($type === 'quiz')
                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-brain"></i> Quiz</span>
                    <select name="item_id" class="filter-select">
                        <option value="">All Quizzes</option>
                        @foreach($quizList as $quiz)
                            <option value="{{ $quiz->id }}" {{ $itemId == $quiz->id ? 'selected' : '' }}>
                                {{ Str::limit($quiz->title, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-file-alt"></i> Assignment</span>
                    <select name="item_id" class="filter-select">
                        <option value="">All Assignments</option>
                        @foreach($assignList as $assignment)
                            <option value="{{ $assignment->id }}" {{ $itemId == $assignment->id ? 'selected' : '' }}>
                                {{ Str::limit($assignment->title, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="filter-group" style="display: flex; flex-direction: row; gap: 0.5rem; align-items: center;">
                    <button type="submit" class="btn-sm btn-sm-primary" style="flex: 1;">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.todo.progress', ['type' => $type]) }}" 
                       class="btn-sm btn-sm-outline">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Active Filters --}}
    @if($itemId)
        @php
            $filteredItem = $type === 'quiz' 
                ? $quizList->firstWhere('id', $itemId) 
                : $assignList->firstWhere('id', $itemId);
        @endphp
        @if($filteredItem)
        <div class="item-filter" style="margin-bottom: 1.5rem;">
            <i class="fas fa-filter"></i>
            <span>Filtering by: <strong>{{ $filteredItem->title }}</strong></span>
            <a href="{{ route('admin.todo.progress', ['type' => $type] + request()->except('item_id')) }}" class="remove">
                <i class="fas fa-times-circle"></i> Remove
            </a>
        </div>
        @endif
    @endif

    {{-- Progress Table --}}
    @if($type === 'quiz')
        {{-- Quiz Attempts --}}
        @include('admin.todo.partials.quiz-progress', ['attempts' => $quizProgress])
    @else
        {{-- Assignment Submissions --}}
        @include('admin.todo.partials.assignment-progress', ['submissions' => $assignmentProgress])
    @endif

    {{-- Footer --}}
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Student Progress • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic Program Loading based on College
        const collegeFilter = document.getElementById('college-filter');
        const programFilter = document.getElementById('program-filter');
        
        if (collegeFilter) {
            collegeFilter.addEventListener('change', function() {
                const collegeId = this.value;
                
                // Clear and disable program filter
                programFilter.innerHTML = '<option value="">All Programs</option>';
                programFilter.disabled = !collegeId;
                
                if (!collegeId) return;
                
                // Fetch programs
                fetch(`{{ url('admin/todo/colleges') }}/${collegeId}/programs`)
                    .then(response => response.json())
                    .then(programs => {
                        programs.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.id;
                            option.textContent = program.program_name;
                            programFilter.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading programs:', error));
            });
        }

        // Auto-submit filters on change (optional)
        const filterSelects = document.querySelectorAll('.filter-select:not(#program-filter)');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });

        // Debounced search
        const searchInput = document.querySelector('input[name="search_name"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }
    });
</script>
@endpush