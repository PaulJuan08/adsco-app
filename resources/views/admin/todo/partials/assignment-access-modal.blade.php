{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.todo.assignment.access.modal', $encryptedId) }}" class="filter-bar" id="modal-filter-form">
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
        
        <button type="button" id="modal-clear-btn" class="btn-sm btn-sm-outline" style="align-self: flex-end;">
            <i class="fas fa-times"></i> Clear
        </button>
    </div>
</form>

{{-- Bulk Actions Form --}}
<form method="POST" id="modal-bulk-form" data-no-crud="1">
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
(function(){
    // Toggle individual access
    document.querySelectorAll('#crudModalBody .toggle-access').forEach(function(toggle){
        toggle.addEventListener('change', function(){
            var url = this.dataset.url;
            var checked = this.checked;
            var el = this;
            fetch(url, {
                method: 'POST',
                headers: {'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}
            }).then(function(r){ return r.json(); }).catch(function(){ el.checked = !checked; });
        });
    });
    // Select all
    var selectAll = document.getElementById('modal-select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function(){
            document.querySelectorAll('#crudModalBody .student-checkbox').forEach(function(cb){ cb.checked = selectAll.checked; });
        });
    }
    // Auto-filter: selects → immediate, text → debounced
    var filterForm = document.getElementById('modal-filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e){ e.preventDefault(); });
        function _submitModalFilter() {
            var url = filterForm.action + '?' + new URLSearchParams(new FormData(filterForm)).toString();
            document.getElementById('crudModalBody').innerHTML = '<div style="text-align:center;padding:2rem;color:#552b20;font-size:2rem;"><i class="fas fa-spinner fa-spin"></i></div>';
            fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
                .then(function(r){ return r.json(); })
                .then(function(data){ document.getElementById('crudModalBody').innerHTML = data.html; document.querySelectorAll('#crudModalBody script').forEach(function(s){var n=document.createElement('script');n.textContent=s.textContent;document.head.appendChild(n);s.remove();}); })
                .catch(function(){ document.getElementById('crudModalBody').innerHTML='<p style="color:#dc2626;text-align:center;padding:1rem;">Failed to load. Please try again.</p>'; });
        }
        filterForm.querySelectorAll('select').forEach(function(sel){ sel.addEventListener('change', _submitModalFilter); });
        var _ft; filterForm.querySelectorAll('input[type="text"]').forEach(function(inp){ inp.addEventListener('input', function(){ clearTimeout(_ft); _ft = setTimeout(_submitModalFilter, 500); }); });
        var clearBtn = document.getElementById('modal-clear-btn');
        if (clearBtn) { clearBtn.addEventListener('click', function(){ filterForm.querySelectorAll('select').forEach(function(s){ s.value=''; }); filterForm.querySelectorAll('input[type="text"]').forEach(function(i){ i.value=''; }); _submitModalFilter(); }); }
    }
})();
</script>