@extends('layouts.teacher')

@section('title', 'Assignment Access — ' . $assignment->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-form.css') }}">
<style>
    /* Additional styles for access management */
    :root {
        --primary: #f59e0b;
        --primary-dark: #d97706;
        --primary-light: rgba(245, 158, 11, 0.1);
    }
    
    /* Breadcrumb styling */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }
    
    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .breadcrumb i {
        color: #cbd5e0;
        font-size: 0.75rem;
    }
    
    .breadcrumb .current {
        color: #718096;
        font-weight: 500;
    }
    
    .assignment-info-bar {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .assignment-icon {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }
    
    .assignment-info-content {
        flex: 1;
    }
    
    .assignment-info-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }
    
    .assignment-info-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    
    .assignment-info-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #4a5568;
    }
    
    .assignment-info-meta i {
        color: var(--primary);
        width: 16px;
    }
    
    .access-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
        align-items: start;
    }
    
    .filter-bar {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    
    .filter-row {
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
        color: var(--primary);
    }
    
    .filter-select, .filter-input {
        padding: 0.5rem 0.75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .filter-select:focus, .filter-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    .filter-select:disabled {
        background: #f7fafc;
        color: #a0aec0;
        cursor: not-allowed;
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s;
    }
    
    .btn-sm-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }
    
    .btn-sm-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }
    
    .btn-sm-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }
    
    .btn-sm-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
    }
    
    .btn-sm-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }
    
    .btn-sm-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 101, 101, 0.4);
    }
    
    .btn-sm-outline {
        background: white;
        border: 1.5px solid #e2e8f0;
        color: #718096;
    }
    
    .btn-sm-outline:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
        color: #4a5568;
    }
    
    .bulk-bar {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .bulk-bar input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }
    
    .bulk-bar label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
        cursor: pointer;
    }
    
    .bulk-stats {
        margin-left: auto;
        font-size: 0.75rem;
        color: #718096;
        background: #f7fafc;
        padding: 0.375rem 1rem;
        border-radius: 20px;
    }
    
    .student-table-wrap {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    
    .student-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .student-table th {
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
    
    .student-table td {
        padding: 1rem;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.875rem;
        color: #2d3748;
    }
    
    .student-table tr:hover td {
        background: #f8fafc;
    }
    
    .student-name {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 0.125rem;
    }
    
    .student-sub {
        font-size: 0.75rem;
        color: #718096;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .status-allowed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-revoked {
        background: #fee2e2;
        color: #b91c1c;
    }
    
    .status-none {
        background: #f3f4f6;
        color: #4b5563;
    }
    
    .access-toggle {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }
    
    .access-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        transition: .3s;
        border-radius: 24px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }
    
    .pagination-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        font-size: 0.8125rem;
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
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .pagination-links .active span {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border-color: var(--primary);
    }
    
    .sidebar-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .sidebar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    
    .sidebar-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #edf2f7;
    }
    
    .sidebar-card-title i {
        color: var(--primary);
    }
    
    .info-row-sm {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #edf2f7;
        font-size: 0.875rem;
    }
    
    .info-row-sm:last-child {
        border-bottom: none;
    }
    
    .info-row-sm .lbl {
        color: #718096;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-row-sm .lbl i {
        color: var(--primary);
        width: 16px;
    }
    
    .info-row-sm .val {
        font-weight: 600;
        color: #2d3748;
    }
    
    .info-row-sm .val.highlight {
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    .quick-action-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem;
        margin-bottom: 0.5rem;
        border-radius: 10px;
        text-decoration: none;
        color: #2d3748;
        transition: all 0.2s;
        background: #f8fafc;
    }
    
    .quick-action-link:hover {
        background: white;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .quick-action-link i {
        width: 20px;
        color: var(--primary);
    }
    
    .quick-action-link span {
        flex: 1;
        font-size: 0.875rem;
    }
    
    .help-card {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    }
    
    .help-text {
        font-size: 0.8125rem;
        color: #4a5568;
        line-height: 1.5;
    }
    
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 0.75rem 1.25rem;
        background: var(--primary);
        color: white;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #a0aec0;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .empty-state h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #718096;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        font-size: 0.8125rem;
    }
    
    @media (max-width: 1024px) {
        .access-layout {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .assignment-info-bar {
            flex-direction: column;
            text-align: center;
        }
        
        .assignment-info-meta {
            justify-content: center;
        }
        
        .filter-row {
            grid-template-columns: 1fr;
        }
        
        .bulk-bar {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .bulk-stats {
            margin-left: 0;
        }
        
        .student-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('teacher.todo.index') }}">To-Do</a>
        <i class="fas fa-chevron-right"></i>
        <span>Assignment Access</span>
        <i class="fas fa-chevron-right"></i>
        <span class="current">{{ Str::limit($assignment->title, 30) }}</span>
    </div>

    {{-- Main Container --}}
    <div class="form-container">
        {{-- Header --}}
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h2 class="card-title">Assignment Access Management</h2>
            </div>
            <div class="top-actions">
                <a href="{{ route('teacher.assignments.edit', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit Assignment
                </a>
                <a href="{{ route('teacher.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="top-action-btn">
                    <i class="fas fa-chart-bar"></i> Progress
                </a>
                <a href="{{ route('teacher.assignments.show', $encryptedId) }}" class="top-action-btn">
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

        {{-- Assignment Info Bar --}}
        <div class="assignment-info-bar">
            <div class="assignment-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="assignment-info-content">
                <div class="assignment-info-title">{{ $assignment->title }}</div>
                <div class="assignment-info-meta">
                    <span><i class="fas fa-book"></i> {{ $assignment->course?->course_name ?? 'No Course' }}</span>
                    @if($assignment->topic)
                        <span><i class="fas fa-tag"></i> {{ $assignment->topic->name }}</span>
                    @endif
                    <span><i class="fas fa-star"></i> {{ $assignment->points }} points</span>
                    <span><i class="fas fa-users"></i> {{ $assignment->allowed_count }} allowed</span>
                    @if($assignment->due_date)
                        <span><i class="fas fa-calendar-alt"></i> Due: {{ $assignment->due_date->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Access Layout --}}
        <div class="access-layout">
            {{-- Left Column: Student Table --}}
            <div>
                {{-- Filter Bar --}}
                <form method="GET" action="{{ route('teacher.todo.assignment.access', $encryptedId) }}" class="filter-bar">
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
                        
                        <a href="{{ route('teacher.todo.assignment.access', $encryptedId) }}" 
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
                                    formaction="{{ route('teacher.todo.assignment.grant', $encryptedId) }}" 
                                    class="btn-sm btn-sm-success">
                                <i class="fas fa-check-circle"></i> Grant Selected
                            </button>
                            
                            <button type="submit" 
                                    formaction="{{ route('teacher.todo.assignment.revoke', $encryptedId) }}" 
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
                                    <th>ID</th>
                                    <th>College</th>
                                    <th>Program</th>
                                    <th>Year</th>
                                    <th>Access</th>
                                    <th>Submission</th>
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
                                               data-status="{{ $student->access_status ?? 'none' }}"
                                               style="width: 16px; height: 16px; cursor: pointer;">
                                    </td>
                                    <td>
                                        <div class="student-name">
                                            {{ $student->l_name }}, {{ $student->f_name }}
                                        </div>
                                        <div class="student-sub">
                                            {{ $student->email }}
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-family: monospace;">{{ $student->student_id ?? '—' }}</span>
                                    </td>
                                    <td>{{ $student->college?->college_name ?? '—' }}</td>
                                    <td>{{ $student->program?->program_name ?? '—' }}</td>
                                    <td>{{ $student->college_year ?? '—' }}</td>
                                    <td>
                                        @php
                                            $status = $student->access_status ?? 'none';
                                        @endphp
                                        <label class="access-toggle">
                                            <input type="checkbox"
                                                   class="toggle-access"
                                                   data-url="{{ route('teacher.todo.assignment.toggle', [$encryptedId, $student->id]) }}"
                                                   {{ $status === 'allowed' ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                    <td>
                                        @php
                                            $subStatus = $student->submission_status ?? null;
                                        @endphp
                                        @if($subStatus)
                                            <span class="status-badge {{ $subStatus == 'graded' ? 'status-allowed' : ($subStatus == 'late' ? 'status-revoked' : 'status-none') }}">
                                                <i class="fas fa-{{ $subStatus == 'graded' ? 'check' : ($subStatus == 'late' ? 'exclamation' : 'clock') }}"></i>
                                                {{ ucfirst($subStatus) }}
                                            </span>
                                        @else
                                            <span style="color: #a0aec0;">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="empty-state">
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
                {{-- Assignment Info Card --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-info-circle"></i> Assignment Information
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-heading"></i> Title</span>
                        <span class="val">{{ Str::limit($assignment->title, 20) }}</span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                        <span class="val" style="color: {{ $assignment->is_published ? '#48bb78' : '#ed8936' }}">
                            {{ $assignment->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-star"></i> Points</span>
                        <span class="val">{{ $assignment->points }}</span>
                    </div>
                    
                    @if($assignment->course)
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-book"></i> Course</span>
                        <span class="val">{{ $assignment->course->course_name }}</span>
                    </div>
                    @endif
                    
                    @if($assignment->due_date)
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-calendar-alt"></i> Due Date</span>
                        <span class="val">{{ $assignment->due_date->format('M d, Y') }}</span>
                    </div>
                    @endif
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-users"></i> Allowed Students</span>
                        <span class="val highlight">{{ $assignment->allowed_count }}</span>
                    </div>
                    
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-file-upload"></i> Submissions</span>
                        <span class="val">{{ $assignment->submissions_count }}</span>
                    </div>
                </div>

                {{-- Quick Actions Card --}}
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </div>
                    
                    <a href="{{ route('teacher.assignments.edit', $encryptedId) }}" class="quick-action-link">
                        <i class="fas fa-edit"></i>
                        <span>Edit Assignment Details</span>
                    </a>
                    
                    <a href="{{ route('teacher.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="quick-action-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>View Progress Reports</span>
                    </a>
                    
                    <a href="{{ route('teacher.assignments.show', $encryptedId) }}" class="quick-action-link">
                        <i class="fas fa-eye"></i>
                        <span>View Assignment</span>
                    </a>
                    
                    <a href="{{ route('teacher.assignments.index') }}" class="quick-action-link">
                        <i class="fas fa-list"></i>
                        <span>All Assignments</span>
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

{{-- Hidden Forms --}}
<form id="grant-form" method="POST" action="{{ route('teacher.todo.assignment.grant', $encryptedId) }}" style="display: none;">
    @csrf
    <input type="hidden" name="student_ids" id="grant-student-ids">
</form>

<form id="revoke-form" method="POST" action="{{ route('teacher.todo.assignment.revoke', $encryptedId) }}" style="display: none;">
    @csrf
    <input type="hidden" name="student_ids" id="revoke-student-ids">
</form>

<form id="toggle-form" method="POST" style="display: none;">
    @csrf
</form>

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
                    
                    // Update the data-status attribute for bulk actions
                    const row = self.closest('tr');
                    const checkbox = row.querySelector('.student-checkbox');
                    if (checkbox) {
                        checkbox.dataset.status = data.status;
                    }
                    
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
                
                // Count only students with 'allowed' status for revoke
                const allowedSelected = document.querySelectorAll('.student-checkbox:checked[data-status="allowed"]').length;
                
                if (allowedSelected === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'No Active Access',
                        text: 'Selected students do not have active access to revoke.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                if (!confirm(`Are you sure you want to revoke access for ${allowedSelected} student(s)?`)) {
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