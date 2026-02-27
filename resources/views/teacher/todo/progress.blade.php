@extends('layouts.teacher')

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
                <a href="{{ route('teacher.todo.progress') }}" class="top-action-btn">
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
        <a href="{{ route('teacher.todo.progress', ['type' => 'quiz'] + request()->except('type')) }}" 
           class="progress-tab {{ $type === 'quiz' ? 'active' : '' }}">
            <i class="fas fa-brain"></i> Quiz Attempts
        </a>
        <a href="{{ route('teacher.todo.progress', ['type' => 'assignment'] + request()->except('type')) }}" 
           class="progress-tab {{ $type === 'assignment' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Assignment Submissions
        </a>
    </div>

    <!-- Filters Section - Matching header-actions-bar pattern -->
    <div class="progress-filters">
        <form method="GET" action="{{ route('teacher.todo.progress') }}">
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
                    <a href="{{ route('teacher.todo.progress', ['type' => $type]) }}" 
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
            <a href="{{ route('teacher.todo.progress', ['type' => $type] + request()->except('item_id')) }}" class="remove">
                <i class="fas fa-times-circle"></i> Remove
            </a>
        </div>
        @endif
    @endif

    <!-- Summary Cards -->
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
        
        @if($type === 'quiz')
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalAttempts ?? 0 }}</div>
                <div class="stat-label">Total Attempts</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $passedCount ?? 0 }}</div>
                <div class="stat-label">Passed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $avgScore ? round($avgScore) . '%' : 'N/A' }}</div>
                <div class="stat-label">Average Score</div>
            </div>
        </div>
        @else
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-upload"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalSubmissions ?? 0 }}</div>
                <div class="stat-label">Total Submissions</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $gradedCount ?? 0 }}</div>
                <div class="stat-label">Graded</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $pendingCount ?? 0 }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $avgGrade ? round($avgGrade) . '%' : 'N/A' }}</div>
                <div class="stat-label">Average Grade</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Progress Table -->
    @if($type === 'quiz')
        @include('teacher.todo.partials.quiz-progress', ['attempts' => $quizProgress])
    @else
        @include('teacher.todo.partials.assignment-progress', ['submissions' => $assignmentProgress])
    @endif

    <!-- Pagination -->
    @if(($type === 'quiz' && $quizProgress->hasPages()) || ($type === 'assignment' && $assignmentProgress->hasPages()))
    <div class="pagination-container">
        {{ $type === 'quiz' ? $quizProgress->appends(request()->query())->links() : $assignmentProgress->appends(request()->query())->links() }}
    </div>
    @endif

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Student Progress • Updated {{ now()->format('M d, Y h:i A') }}
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
                
                // Show loading state
                programFilter.innerHTML = '<option value="">Loading...</option>';
                
                // Fetch programs
                fetch(`{{ url('teacher/todo/colleges') }}/${collegeId}/programs`)
                    .then(response => response.json())
                    .then(programs => {
                        programFilter.innerHTML = '<option value="">All Programs</option>';
                        
                        if (programs && programs.length > 0) {
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
                        }
                    })
                    .catch(error => {
                        console.error('Error loading programs:', error);
                        programFilter.innerHTML = '<option value="">Error loading programs</option>';
                    });
            });
        }

        // Auto-submit filters on change (optional) - uncomment if you want auto-submit
        // const filterSelects = document.querySelectorAll('.filter-select:not(#program-filter)');
        // filterSelects.forEach(select => {
        //     select.addEventListener('change', function() {
        //         this.form.submit();
        //     });
        // });

        // Debounced search
        const searchInput = document.querySelector('input[name="search_name"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 800); // Slightly longer debounce for better UX
            });
        }

        // Initialize tooltips if any
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = this.dataset.tooltip;
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
                tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                
                this.addEventListener('mouseleave', function() {
                    tooltip.remove();
                }, { once: true });
            });
        });

        // Handle grade assignment if on assignment tab
        @if($type === 'assignment')
        const gradeButtons = document.querySelectorAll('.grade-btn');
        gradeButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const submissionId = this.dataset.submissionId;
                const studentName = this.dataset.studentName;
                const currentGrade = this.dataset.currentGrade;
                
                // You can implement a grade modal here
                if (typeof openGradeModal === 'function') {
                    openGradeModal(submissionId, studentName, currentGrade);
                }
            });
        });
        @endif
    });

    // Optional: Function to open grade modal (implement based on your modal system)
    function openGradeModal(submissionId, studentName, currentGrade) {
        // Implement grade modal logic
        console.log('Grade submission:', submissionId, studentName, currentGrade);
    }

    // Optional: Export functionality
    function exportProgress() {
        const type = '{{ $type }}';
        const url = new URL('{{ route("teacher.todo.progress.export") }}');
        url.searchParams.append('type', type);
        url.searchParams.append('college_id', '{{ $collegeId }}');
        url.searchParams.append('program_id', '{{ $programId }}');
        url.searchParams.append('year', '{{ $year }}');
        url.searchParams.append('item_id', '{{ $itemId }}');
        url.searchParams.append('search_name', '{{ $searchName }}');
        
        window.location.href = url.toString();
    }
</script>
@endpush