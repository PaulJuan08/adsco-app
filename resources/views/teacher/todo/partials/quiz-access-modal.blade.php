<div class="access-modal-container">
    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('teacher.todo.quiz.access.modal', $encryptedId) }}" class="filter-bar" id="modal-filter-form">
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
                <i class="fas fa-filter"></i> Apply
            </button>
            
            <a href="{{ route('teacher.todo.quiz.access.modal', $encryptedId) }}" 
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
                        <th>Attempt</th>
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
                        <td>
                            @if($student->attempt_status)
                                <span class="badge {{ $student->attempt_status == 'passed' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $student->attempt_score }}% - {{ ucfirst($student->attempt_status) }}
                                </span>
                                <div style="font-size: 0.6875rem; color: #718096; margin-top: 0.25rem;">
                                    {{ $student->attempt_date ? $student->attempt_date->format('M d, Y') : '' }}
                                </div>
                            @else
                                <span class="badge badge-gray">No attempt</span>
                            @endif
                        </td>
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
                        <td colspan="7" class="empty-state">
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
                    {{ $students->appends(request()->except('_token'))->links() }}
                </div>
            </div>
            @endif
        </div>
    </form>
</div>
