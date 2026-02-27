@extends('layouts.admin')

@section('title', 'Student Progress — Quizzes & Assignments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/progress.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Dashboard Header — consistent with all index pages -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->l_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Student Progress</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-tasks"></i> Track student performance across quizzes and assignments
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.todo.progress') }}" class="top-action-btn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </a>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Progress Tabs -->
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

    <!-- Filters Section - Matching header-actions-bar pattern -->
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
                           placeholder="Search by name..." 
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

                <div class="filter-group" style="display: flex; flex-direction: row; gap: 0.5rem; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.todo.progress', ['type' => $type]) }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Display -->
    @if($itemId)
        @php
            $filteredItem = $type === 'quiz' 
                ? $quizList->firstWhere('id', $itemId) 
                : $assignList->firstWhere('id', $itemId);
        @endphp
        @if($filteredItem)
        <div class="item-filter">
            <i class="fas fa-filter"></i>
            <span>Filtering by: <strong>{{ $filteredItem->title }}</strong></span>
            <a href="{{ route('admin.todo.progress', ['type' => $type] + request()->except('item_id')) }}" class="remove">
                <i class="fas fa-times-circle"></i> Remove
            </a>
        </div>
        @endif
    @endif

    <!-- Progress Table -->
    @if($type === 'quiz')
        @include('admin.todo.partials.quiz-progress', ['attempts' => $quizProgress])
    @else
        @include('admin.todo.partials.assignment-progress', ['submissions' => $assignmentProgress])
    @endif

    <!-- Footer -->
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
                        
                        // If there was a previously selected program, try to select it
                        const selectedProgram = '{{ $programId }}';
                        if (selectedProgram) {
                            programFilter.value = selectedProgram;
                        }
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