@extends('layouts.teacher')

@section('title', 'Quiz Access — ' . $quiz->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-access.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('teacher.todo.index') }}">To-Do</a>
        <i class="fas fa-chevron-right"></i>
        <span>Quiz Access</span>
        <i class="fas fa-chevron-right"></i>
        <span class="current">{{ Str::limit($quiz->title, 30) }}</span>
    </div>

    {{-- Main Container --}}
    <div class="form-container">
        {{-- Header --}}
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h2 class="card-title">Quiz Access Management</h2>
            </div>
            <div class="top-actions">
                <a href="{{ route('teacher.quizzes.edit', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit Quiz
                </a>
                <a href="{{ route('teacher.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="top-action-btn">
                    <i class="fas fa-chart-bar"></i> Progress
                </a>
                <a href="{{ route('teacher.todo.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        {{-- Quiz Info Bar --}}
        <div class="quiz-info-bar">
            <div class="quiz-icon">
                <i class="fas fa-brain"></i>
            </div>
            <div class="quiz-info-content">
                <div class="quiz-info-title">{{ $quiz->title }}</div>
                <div class="quiz-info-meta">
                    <span><i class="fas fa-question-circle"></i> {{ $quiz->total_questions }} Questions</span>
                    <span><i class="fas fa-clock"></i> {{ $quiz->duration }} min</span>
                    <span><i class="fas fa-trophy"></i> {{ $quiz->passing_score }}% to pass</span>
                    <span><i class="fas fa-users"></i> {{ $quiz->allowed_count }} allowed</span>
                </div>
            </div>
        </div>

        {{-- Access Layout --}}
        <div class="access-layout">
            {{-- Left Column: Student Table --}}
            <div>
                {{-- Filter Bar --}}
                <form method="GET" action="{{ route('teacher.todo.quiz.access', $encryptedId) }}" class="filter-bar">
                    <div class="filter-row">
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
                            <select name="program_id" class="filter-select" id="program-filter">
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
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <span class="filter-label"><i class="fas fa-search"></i> Student</span>
                            <input type="text" 
                                   name="search_name" 
                                   class="filter-input"
                                   placeholder="Name or ID..." 
                                   value="{{ $searchName }}">
                        </div>
                        
                        <button type="submit" class="btn-sm btn-sm-primary" style="align-self: flex-end;">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        
                        <a href="{{ route('teacher.todo.quiz.access', $encryptedId) }}" 
                           class="btn-sm btn-sm-outline" style="align-self: flex-end;">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>

                {{-- Bulk Actions Form --}}
                <form method="POST" id="bulk-form">
                    @csrf
                    <div class="student-table-wrap">
                        <div class="bulk-bar">
                            <input type="checkbox" id="select-all">
                            <label for="select-all">Select All</label>
                            
                            <button type="submit" 
                                    formaction="{{ route('teacher.todo.quiz.grant', $encryptedId) }}" 
                                    class="btn-sm btn-sm-success">
                                <i class="fas fa-check-circle"></i> Grant Selected
                            </button>
                            
                            <button type="submit" 
                                    formaction="{{ route('teacher.todo.quiz.revoke', $encryptedId) }}" 
                                    class="btn-sm btn-sm-danger">
                                <i class="fas fa-ban"></i> Revoke Selected
                            </button>
                            
                            <span class="bulk-stats">
                                {{ $students->total() }} student(s) found
                            </span>
                        </div>

                        {{-- Student Table --}}
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>Student</th>
                                    <th>College</th>
                                    <th>Program</th>
                                    <th>Year</th>
                                    <th style="text-align: center;">Access</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr>
                                    <td>
                                        <input type="checkbox" 
                                               name="student_ids[]" 
                                               value="{{ $student->id }}"
                                               class="student-checkbox"
                                               style="width: 16px; height: 16px; cursor: pointer;">
                                    </td>
                                    <td>
                                        <div class="student-name">
                                            {{ $student->l_name }}, {{ $student->f_name }}
                                        </div>
                                        <div class="student-sub">
                                            {{ $student->student_id ?? $student->email }}
                                        </div>
                                    </td>
                                    <td>{{ $student->college?->college_name ?? '—' }}</td>
                                    <td>{{ $student->program?->program_name ?? '—' }}</td>
                                    <td>{{ $student->college_year ?? '—' }}</td>
                                    <td style="text-align: center;">
                                        <label class="access-toggle">
                                            <input type="checkbox"
                                                   class="toggle-access"
                                                   data-url="{{ route('teacher.todo.quiz.toggle', [$encryptedId, $student->id]) }}"
                                                   {{ $student->access_status === 'allowed' ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>No students found matching your criteria.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        @if($students->hasPages())
                        <div class="pagination-row">
                            <span>
                                Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}
                            </span>
                            <div class="pagination-links">
                                {{ $students->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Right Column: Sidebar --}}
            <div>
                {{-- Quiz Info Card --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-brain"></i> Quiz Information
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-heading"></i> Title</span>
                        <span class="val">{{ Str::limit($quiz->title, 20) }}</span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                        <span class="val" style="color: {{ $quiz->is_published ? '#48bb78' : '#ed8936' }}">
                            {{ $quiz->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-question-circle"></i> Questions</span>
                        <span class="val">{{ $quiz->total_questions }}</span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-clock"></i> Duration</span>
                        <span class="val">{{ $quiz->duration }} min</span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-trophy"></i> Passing Score</span>
                        <span class="val">{{ $quiz->passing_score }}%</span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-users"></i> Allowed Students</span>
                        <span class="val highlight">{{ $quiz->allowed_count }}</span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-paper-plane"></i> Total Attempts</span>
                        <span class="val">{{ $quiz->attempts->count() }}</span>
                    </div>
                </div>

                {{-- Quick Actions Card --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </div>
                    
                    <a href="{{ route('teacher.quizzes.edit', $encryptedId) }}" class="quick-action-link">
                        <i class="fas fa-edit"></i>
                        <span>Edit Quiz Details</span>
                    </a>
                    
                    <a href="{{ route('teacher.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="quick-action-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>View Progress Reports</span>
                    </a>
                    
                    <a href="{{ route('teacher.quizzes.show', $encryptedId) }}" class="quick-action-link">
                        <i class="fas fa-eye"></i>
                        <span>Preview Quiz</span>
                    </a>
                    
                    <a href="{{ route('teacher.todo.index') }}" class="quick-action-link">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to To-Do</span>
                    </a>
                </div>

                {{-- Help Card --}}
                <div class="sidebar-card help-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-lightbulb"></i> How It Works
                    </div>
                    
                    <div class="help-text">
                        <p style="margin-bottom: 0.75rem;">
                            <strong>Toggle Switch:</strong> Click the toggle next to each student to grant or revoke access instantly.
                        </p>
                        <p style="margin-bottom: 0.75rem;">
                            <strong>Bulk Actions:</strong> Use checkboxes to select multiple students and grant/revoke access in bulk.
                        </p>
                        <p>
                            <strong>Filters:</strong> Use the filters above to narrow down students by college, program, or year.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All Checkbox
        const selectAll = document.getElementById('select-all');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                studentCheckboxes.forEach(cb => cb.checked = this.checked);
            });
        }

        // Individual Toggle Access via AJAX
        document.querySelectorAll('.toggle-access').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const url = this.dataset.url;
                const checked = this.checked;
                const self = this;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Create toast notification
                    const toast = document.createElement('div');
                    toast.className = 'toast-notification';
                    toast.textContent = data.status === 'allowed' ? '✓ Access granted' : '✗ Access revoked';
                    toast.style.background = data.status === 'allowed' ? '#48bb78' : '#f56565';
                    document.body.appendChild(toast);
                    
                    setTimeout(() => toast.remove(), 2500);
                })
                .catch(error => {
                    self.checked = !checked; // Revert on error
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update access status.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            });
        });

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
                fetch(`{{ url('teacher/todo/colleges') }}/${collegeId}/programs`)
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

        // Form submission confirmation for bulk revoke
        document.querySelectorAll('button[formaction*="revoke"]').forEach(button => {
            button.addEventListener('click', function(e) {
                const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Students Selected',
                        text: 'Please select at least one student.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                if (!confirm(`Are you sure you want to revoke access for ${checkedCount} student(s)?`)) {
                    e.preventDefault();
                }
            });
        });

        // Form submission confirmation for bulk grant
        document.querySelectorAll('button[formaction*="grant"]').forEach(button => {
            button.addEventListener('click', function(e) {
                const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Students Selected',
                        text: 'Please select at least one student.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>
@endpush