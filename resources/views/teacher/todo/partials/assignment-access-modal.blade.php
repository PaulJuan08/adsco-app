{{-- Filter Bar --}}
<form method="GET" action="{{ route('teacher.todo.assignment.access.modal', $encryptedId) }}" class="filter-bar" id="modal-filter-form">
    <div class="filter-row">
        <div class="filter-group">
            <span class="filter-label"><i class="fas fa-university"></i> College</span>
            <select name="college_id" class="filter-select" id="modal-college-filter">
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
            <select name="program_id" class="filter-select" id="modal-program-filter" {{ !$collegeId ? 'disabled' : '' }}>
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
        
        <a href="{{ route('admin.todo.assignment.access.modal', $encryptedId) }}" 
           class="btn-sm btn-sm-outline" style="align-self: flex-end;">
            <i class="fas fa-times"></i> Clear
        </a>
    </div>
</form>

{{-- Bulk Actions Form --}}
<form method="POST" id="modal-bulk-form">
    @csrf
    <div class="student-table-wrap">
        <div class="bulk-bar">
            <input type="checkbox" id="modal-select-all">
            <label for="modal-select-all">Select All</label>
            
            <button type="submit" 
                    formaction="{{ route('admin.todo.assignment.grant', $encryptedId) }}" 
                    class="btn-sm btn-sm-success">
                <i class="fas fa-check-circle"></i> Grant Selected
            </button>
            
            <button type="submit" 
                    formaction="{{ route('admin.todo.assignment.revoke', $encryptedId) }}" 
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
                                   data-url="{{ route('admin.todo.assignment.toggle', [$encryptedId, $student->id]) }}"
                                   {{ $status === 'allowed' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </td>
                    <td>
                        @php
                            $subStatus = $student->submission_status ?? null;
                        @endphp
                        @if($subStatus)
                            <span class="status-badge status-{{ $subStatus }}">
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
                        <h3>No Students Found</h3>
                        <p>No students match your filter criteria.</p>
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

<script>
    // Initialize modal-specific scripts
    (function() {
        // Select All Checkbox for modal
        const modalSelectAll = document.getElementById('modal-select-all');
        const modalCheckboxes = document.querySelectorAll('.student-checkbox');
        
        if (modalSelectAll) {
            modalSelectAll.addEventListener('change', function() {
                modalCheckboxes.forEach(cb => cb.checked = this.checked);
            });
        }

        // Dynamic Program Loading for modal
        const modalCollegeFilter = document.getElementById('modal-college-filter');
        const modalProgramFilter = document.getElementById('modal-program-filter');
        
        if (modalCollegeFilter) {
            modalCollegeFilter.addEventListener('change', function() {
                const collegeId = this.value;
                
                modalProgramFilter.innerHTML = '<option value="">All Programs</option>';
                modalProgramFilter.disabled = !collegeId;
                
                if (!collegeId) return;
                
                fetch(`{{ url('admin/todo/colleges') }}/${collegeId}/programs`)
                    .then(response => response.json())
                    .then(programs => {
                        programs.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.id;
                            option.textContent = program.program_name;
                            modalProgramFilter.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading programs:', error));
            });
        }

        // Handle filter form submission via AJAX
        const filterForm = document.getElementById('modal-filter-form');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const url = this.action + '?' + new URLSearchParams(formData).toString();
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const modalBody = document.querySelector('#accessModal .modal-body');
                    if (modalBody) {
                        modalBody.innerHTML = html;
                    }
                })
                .catch(error => console.error('Error applying filters:', error));
            });
        }
    })();
</script>