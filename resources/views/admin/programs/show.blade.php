@extends('layouts.admin')

@section('title', 'Program Details - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/programs-show.css') }}">
@endpush

@section('content')
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-graduation-cap card-icon"></i>
                <h2 class="card-title">Program Details</h2>
            </div>
            <div class="top-actions">
                <a href="{{ route('admin.programs.edit', Crypt::encrypt($program->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="top-action-btn delete-btn" id="deleteButton">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="{{ $program->college ? route('admin.colleges.show', Crypt::encrypt($program->college->id)) : route('admin.programs.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Hidden Delete Form -->
            <form action="{{ route('admin.programs.destroy', Crypt::encrypt($program->id)) }}" method="POST" id="deleteForm" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

            <!-- Hidden Unassign Student Form (reused) -->
            <form action="{{ route('admin.programs.students.unassign', Crypt::encrypt($program->id)) }}" method="POST" id="unassignForm" style="display: none;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="student_id" id="unassignStudentId">
            </form>
            
            <!-- Program Avatar & Basic Info -->
            <div class="program-avatar-section">
                <div class="program-details-avatar" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    {{ strtoupper(substr($program->program_name, 0, 1)) }}
                </div>
                <h3 class="program-title">{{ $program->program_name }}</h3>
                @if($program->program_code)
                <p class="program-code">{{ $program->program_code }}</p>
                @endif
                <div class="program-status-container">
                    <div class="status-badge {{ $program->status == 1 ? 'status-published' : 'status-draft' }}">
                        <i class="fas {{ $program->status == 1 ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $program->status == 1 ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
            
            <!-- Statistics Grid -->
            <div class="stats-grid-small">
                <div class="stat-box">
                    <div class="stat-box-value">{{ $program->students_count ?? 0 }}</div>
                    <div class="stat-box-label">Enrolled Students</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">
                        @if($program->college)
                            {{ Str::limit($program->college->college_name, 15) }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="stat-box-label">College</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">{{ $program->created_at->format('M Y') }}</div>
                    <div class="stat-box-label">Created</div>
                </div>
            </div>
            
            <!-- Detailed Information -->
            <div class="details-grid">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i>
                        Program Information
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Program Name</div>
                        <div class="detail-value">{{ $program->program_name }}</div>
                    </div>
                    @if($program->program_code)
                    <div class="detail-row">
                        <div class="detail-label">Program Code</div>
                        <div class="detail-value">{{ $program->program_code }}</div>
                    </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-label">College</div>
                        <div class="detail-value">
                            @if($program->college)
                                <a href="{{ route('admin.colleges.show', Crypt::encrypt($program->college->id)) }}" style="color:#4f46e5;text-decoration:none;">
                                    {{ $program->college->college_name }}
                                </a>
                            @else
                                <span style="color:#f59e0b;">Not assigned</span>
                            @endif
                        </div>
                    </div>
                    @if($program->description)
                    <div class="detail-row">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">{{ $program->description }}</div>
                    </div>
                    @endif
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-chart-bar"></i>
                        Statistics
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Program ID</div>
                        <div class="detail-value">#{{ $program->id }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Created</div>
                        <div class="detail-value">
                            {{ $program->created_at->format('M d, Y') }}
                            <div class="detail-subvalue"><i class="fas fa-clock"></i> {{ $program->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Updated</div>
                        <div class="detail-value">
                            {{ $program->updated_at->format('M d, Y') }}
                            <div class="detail-subvalue"><i class="fas fa-clock"></i> {{ $program->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            @if($program->status == 1)
                                <span style="color:#10b981;">Active (1)</span>
                            @else
                                <span style="color:#f59e0b;">Inactive (0)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ═══════════════════════════════════════════════════════════
                 STUDENTS SECTION — Full CRUD for students in this program
                 ═══════════════════════════════════════════════════════════ -->
            <div class="detail-section">
                <div class="detail-section-title" style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <i class="fas fa-users"></i>
                        Enrolled Students ({{ $program->students_count ?? 0 }} total)
                    </div>
                    <div style="display:flex;gap:0.5rem;">
                        <!-- Assign Existing Student -->
                        <button type="button" class="btn btn-outline" style="padding:0.5rem 1rem;font-size:0.875rem;" id="btnAssignStudent">
                            <i class="fas fa-user-plus"></i> Assign Existing Student
                        </button>
                        <!-- Create & Add New Student -->
                        <a href="{{ route('admin.users.create') }}?role=4&program_id={{ $program->id }}&college_id={{ $program->college_id }}" class="btn btn-primary" style="padding:0.5rem 1rem;font-size:0.875rem;">
                            <i class="fas fa-plus-circle"></i> Add New Student
                        </a>
                    </div>
                </div>

                <!-- Search -->
                <div class="search-container" style="margin-top:1rem;">
                    <input type="text" class="search-input" id="searchStudent" placeholder="Search by name, email, or student ID...">
                    @if($program->college && $program->college->college_year)
                    <select class="filter-select" id="filterYear">
                        <option value="">All Years</option>
                        @foreach(explode(',', $program->college->college_year) as $year)
                            @if(trim($year))
                                <option value="{{ trim($year) }}">{{ trim($year) }}</option>
                            @endif
                        @endforeach
                    </select>
                    @endif
                </div>

                @if($students->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-user-graduate"></i></div>
                        <h3 class="empty-title">No students yet</h3>
                        <p class="empty-text">This program doesn't have any enrolled students. Add or assign one now.</p>
                        <a href="{{ route('admin.users.create') }}?role=4&program_id={{ $program->id }}&college_id={{ $program->college_id }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add First Student
                        </a>
                    </div>
                @else
                    <div class="student-list" id="studentList">
                        @foreach($students as $student)
                        <div class="student-item"
                             data-name="{{ strtolower($student->f_name . ' ' . $student->l_name) }}"
                             data-email="{{ strtolower($student->email) }}"
                             data-id="{{ strtolower($student->student_id ?? '') }}"
                             data-year="{{ $student->college_year ?? '' }}">
                            <div class="student-info">
                                <div class="student-avatar">
                                    {{ strtoupper(substr($student->f_name, 0, 1)) }}{{ strtoupper(substr($student->l_name, 0, 1)) }}
                                </div>
                                <div class="student-details">
                                    <div class="student-name">{{ $student->f_name }} {{ $student->l_name }}</div>
                                    <div class="student-meta">
                                        <i class="fas fa-id-card"></i> {{ $student->student_id ?? 'No ID' }} ·
                                        <i class="fas fa-envelope"></i> {{ $student->email }} ·
                                        <i class="fas fa-calendar-alt"></i> Added: {{ $student->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:1rem;">
                                @if($student->college_year)
                                    <span class="student-year-badge">{{ $student->college_year }}</span>
                                @endif
                                <div class="student-actions">
                                    <a href="{{ route('admin.users.show', Crypt::encrypt($student->id)) }}" class="student-action-btn view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', Crypt::encrypt($student->id)) }}" class="student-action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="student-action-btn remove"
                                            title="Remove from program"
                                            onclick="confirmUnassign({{ $student->id }}, '{{ $student->f_name }} {{ $student->l_name }}')">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                    <button type="button" class="student-action-btn delete-student"
                                            title="Delete student permanently"
                                            onclick="confirmDelete('{{ Crypt::encrypt($student->id) }}', '{{ $student->f_name }} {{ $student->l_name }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($students->hasPages())
                    <div style="margin-top:2rem;">
                        {{ $students->links() }}
                    </div>
                    @endif
                @endif
            </div>

            @if(session('success'))
            <div class="message-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="message-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
        </div>
    </div>

    <!-- ═══════════════════════════════════════════
         ASSIGN EXISTING STUDENT MODAL
         ═══════════════════════════════════════════ -->
    <div id="assignModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:white;border-radius:16px;padding:2rem;max-width:500px;width:90%;max-height:80vh;overflow-y:auto;position:relative;">
            <button onclick="closeAssignModal()" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer;color:#6b7280;">
                <i class="fas fa-times"></i>
            </button>
            <h3 style="font-size:1.25rem;font-weight:700;color:#1f2937;margin-bottom:0.5rem;">
                <i class="fas fa-user-plus" style="color:#4f46e5;margin-right:0.5rem;"></i>
                Assign Student to Program
            </h3>
            <p style="font-size:0.875rem;color:#6b7280;margin-bottom:1.5rem;">
                Search for an existing student and assign them to <strong>{{ $program->program_name }}</strong>.
                The student's college will also be updated automatically.
            </p>

            <form action="{{ route('admin.programs.students.assign', Crypt::encrypt($program->id)) }}" method="POST" id="assignForm">
                @csrf
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:0.5rem;">Search Student</label>
                    <input type="text" id="studentSearch" placeholder="Type name, email or student ID..." autocomplete="off"
                        style="width:100%;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:8px;font-size:0.875rem;box-sizing:border-box;">
                    <div id="studentSuggestions" style="border:1px solid #e5e7eb;border-top:none;border-radius:0 0 8px 8px;max-height:200px;overflow-y:auto;display:none;"></div>
                </div>

                <div id="selectedStudentInfo" style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:0.875rem;margin-bottom:1rem;">
                    <div style="font-weight:600;color:#166534;" id="selectedStudentName"></div>
                    <div style="font-size:0.8125rem;color:#15803d;" id="selectedStudentMeta"></div>
                </div>

                <input type="hidden" name="student_id" id="selectedStudentIdInput">

                @if($program->college && $program->college->college_year)
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:0.5rem;">Year Level</label>
                    <select name="college_year" style="width:100%;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:8px;font-size:0.875rem;box-sizing:border-box;">
                        <option value="">-- Select Year --</option>
                        @foreach(explode(',', $program->college->college_year) as $year)
                            @if(trim($year))
                                <option value="{{ trim($year) }}">{{ trim($year) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @endif

                <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
                    <button type="button" onclick="closeAssignModal()"
                        style="padding:0.625rem 1.25rem;border:1px solid #e5e7eb;border-radius:8px;background:white;font-size:0.875rem;cursor:pointer;color:#374151;">
                        Cancel
                    </button>
                    <button type="submit" id="assignSubmitBtn" disabled
                        style="padding:0.625rem 1.25rem;border:none;border-radius:8px;background:#4f46e5;color:white;font-size:0.875rem;font-weight:600;cursor:pointer;opacity:0.5;">
                        <i class="fas fa-user-plus"></i> Assign Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete student form (hidden, submitted by JS) -->
    <form id="deleteStudentForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('styles')
<style>
    .program-avatar-section { text-align: center; margin-bottom: 1.5rem; }
    .program-details-avatar { width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:2rem;font-weight:700;margin:0 auto 0.75rem; }
    .program-title { font-size:1.5rem;font-weight:700;color:#1f2937;margin:0 0 0.25rem; }
    .program-code { color:#6b7280;font-size:0.9375rem;margin:0 0 0.75rem; }
    .program-status-container { display:flex;justify-content:center;margin-top:0.5rem; }
    .status-badge { display:inline-flex;align-items:center;gap:0.375rem;padding:0.375rem 1rem;border-radius:999px;font-size:0.8125rem;font-weight:600; }
    .status-published { background:#d1fae5;color:#065f46; }
    .status-draft { background:#fef3c7;color:#92400e; }

    .stats-grid-small { display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin:1.5rem 0; }
    .stat-box { background:#f9fafb;padding:1rem;border-radius:8px;text-align:center;border:1px solid #e5e7eb; }
    .stat-box-value { font-size:1.5rem;font-weight:700;color:#4f46e5; }
    .stat-box-label { font-size:0.8125rem;color:#6b7280;margin-top:0.25rem; }

    .details-grid { display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem; }
    .detail-section { margin-bottom:1.5rem; }
    .detail-section-title { font-size:0.9375rem;font-weight:600;color:#374151;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem; }
    .detail-section-title i { color:#4f46e5; }
    .detail-row { display:flex;justify-content:space-between;padding:0.625rem 0;border-bottom:1px solid #f3f4f6; }
    .detail-label { font-size:0.875rem;color:#6b7280;font-weight:500; }
    .detail-value { font-size:0.875rem;color:#1f2937;font-weight:500;text-align:right;max-width:60%; }
    .detail-subvalue { font-size:0.75rem;color:#9ca3af;margin-top:0.125rem; }

    .student-list { margin-top:1rem; }
    .student-item { display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1rem;background:#f9fafb;border-radius:8px;margin-bottom:0.5rem;border:1px solid #e5e7eb;transition:all 0.2s ease; }
    .student-item:hover { background:#f3f4f6;border-color:#4f46e5;transform:translateX(4px);box-shadow:-4px 0 0 #4f46e5; }
    .student-info { display:flex;align-items:center;gap:1rem;flex:1; }
    .student-avatar { width:40px;height:40px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:0.875rem;flex-shrink:0; }
    .student-name { font-weight:600;font-size:0.9375rem;color:#1f2937; }
    .student-meta { font-size:0.8125rem;color:#6b7280;margin-top:0.125rem; }
    .student-meta i { margin-right:0.25rem;font-size:0.75rem; }
    .student-year-badge { background:#e5e7eb;padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;color:#4b5563;white-space:nowrap; }
    .student-actions { display:flex;gap:0.5rem;align-items:center; }
    .student-action-btn { padding:0.375rem 0.75rem;border-radius:6px;font-size:0.75rem;font-weight:500;text-decoration:none;transition:all 0.2s ease;border:none;cursor:pointer; }
    .student-action-btn.view { background:#4f46e5;color:white; }
    .student-action-btn.view:hover { background:#4338ca; }
    .student-action-btn.edit { background:#f59e0b;color:white; }
    .student-action-btn.edit:hover { background:#d97706; }
    .student-action-btn.remove { background:#6b7280;color:white; }
    .student-action-btn.remove:hover { background:#4b5563; }
    .student-action-btn.delete-student { background:#ef4444;color:white; }
    .student-action-btn.delete-student:hover { background:#dc2626; }

    .search-container { display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem; }
    .search-input { flex:1;min-width:200px;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:8px;font-size:0.875rem; }
    .search-input:focus { outline:none;border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,0.1); }
    .filter-select { padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:8px;font-size:0.875rem;background:white;min-width:150px; }

    .empty-state { text-align:center;padding:2rem 1rem; }
    .empty-icon { font-size:2.5rem;color:#cbd5e0;margin-bottom:0.75rem; }
    .empty-title { font-size:1rem;font-weight:600;color:#718096;margin-bottom:0.25rem; }
    .empty-text { font-size:0.8125rem;color:#a0aec0;margin-bottom:1rem; }

    .message-success { margin-top:1.25rem;padding:0.875rem 1.25rem;background:#f0fff4;color:#276749;border-radius:10px;border-left:4px solid #48bb78;font-size:0.875rem;display:flex;align-items:center;gap:0.5rem; }
    .message-error { margin-top:1.25rem;padding:0.875rem 1.25rem;background:#fff5f5;color:#c53030;border-radius:10px;border-left:4px solid #f56565;font-size:0.875rem;display:flex;align-items:center;gap:0.5rem; }

    .btn { display:inline-flex;align-items:center;gap:0.5rem;padding:0.625rem 1.25rem;border-radius:8px;font-size:0.875rem;font-weight:600;text-decoration:none;cursor:pointer;border:none;transition:all 0.2s ease; }
    .btn-primary { background:#4f46e5;color:white; }
    .btn-primary:hover { background:#4338ca; }
    .btn-outline { background:white;color:#374151;border:1px solid #e5e7eb; }
    .btn-outline:hover { background:#f9fafb; }

    @media (max-width:768px) {
        .details-grid { grid-template-columns:1fr; }
        .stats-grid-small { grid-template-columns:1fr 1fr; }
        .student-item { flex-direction:column;align-items:flex-start;gap:0.75rem; }
        .student-actions { width:100%;justify-content:flex-end; }
    }

    /* Suggestion dropdown */
    #studentSuggestions .suggestion-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        font-size: 0.875rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    #studentSuggestions .suggestion-item:hover { background: #f3f4f6; }
    #studentSuggestions .suggestion-item .s-name { font-weight: 600; color: #1f2937; }
    #studentSuggestions .suggestion-item .s-meta { font-size: 0.8125rem; color: #6b7280; margin-top: 0.125rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

// ── Delete program ──────────────────────────────────────────────────────────
document.getElementById('deleteButton')?.addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Delete Program?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f56565',
        cancelButtonColor: '#a0aec0',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
});

// ── Search / filter students ────────────────────────────────────────────────
const searchInput  = document.getElementById('searchStudent');
const filterYear   = document.getElementById('filterYear');

function filterStudents() {
    const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const year = filterYear ? filterYear.value : '';
    document.querySelectorAll('.student-item').forEach(item => {
        const matchSearch = term === '' || item.dataset.name.includes(term) || item.dataset.email.includes(term) || item.dataset.id.includes(term);
        const matchYear   = year === '' || item.dataset.year === year;
        item.style.display = (matchSearch && matchYear) ? 'flex' : 'none';
    });
}
searchInput?.addEventListener('input', filterStudents);
filterYear?.addEventListener('change', filterStudents);

// ── Unassign (remove from program, keep user) ───────────────────────────────
function confirmUnassign(studentId, studentName) {
    Swal.fire({
        title: 'Remove from Program?',
        html: `<strong>${studentName}</strong> will be removed from this program.<br>The student account will not be deleted.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6b7280',
        cancelButtonColor: '#a0aec0',
        confirmButtonText: 'Yes, Remove',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('unassignStudentId').value = studentId;
            document.getElementById('unassignForm').submit();
        }
    });
}

// ── Delete student permanently ──────────────────────────────────────────────
function confirmDelete(encryptedId, studentName) {
    Swal.fire({
        title: 'Delete Student?',
        html: `This will permanently delete <strong>${studentName}</strong>.<br>This action cannot be undone.`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#a0aec0',
        confirmButtonText: 'Yes, Delete Permanently',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteStudentForm');
            form.action = `/admin/users/${encryptedId}`;
            form.submit();
        }
    });
}

// ── Assign existing student modal ───────────────────────────────────────────
document.getElementById('btnAssignStudent')?.addEventListener('click', function() {
    const modal = document.getElementById('assignModal');
    modal.style.display = 'flex';
});

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
    document.getElementById('studentSearch').value = '';
    document.getElementById('studentSuggestions').style.display = 'none';
    document.getElementById('selectedStudentInfo').style.display = 'none';
    document.getElementById('selectedStudentIdInput').value = '';
    document.getElementById('assignSubmitBtn').disabled = true;
    document.getElementById('assignSubmitBtn').style.opacity = '0.5';
}

document.getElementById('assignModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});

// Live student search (AJAX)
let searchTimeout;
document.getElementById('studentSearch')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    if (query.length < 2) {
        document.getElementById('studentSuggestions').style.display = 'none';
        return;
    }
    searchTimeout = setTimeout(() => {
        fetch(`{{ route('admin.programs.students.search', Crypt::encrypt($program->id)) }}?q=${encodeURIComponent(query)}`, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const box = document.getElementById('studentSuggestions');
            if (!data.length) {
                box.innerHTML = '<div style="padding:0.75rem 1rem;font-size:0.875rem;color:#6b7280;">No students found.</div>';
                box.style.display = 'block';
                return;
            }
            box.innerHTML = data.map(s => `
                <div class="suggestion-item" onclick="selectStudent(${s.id}, '${s.f_name} ${s.l_name}', '${s.student_id ?? ''}', '${s.email}')">
                    <div class="s-name">${s.f_name} ${s.l_name}</div>
                    <div class="s-meta">${s.student_id ? s.student_id + ' · ' : ''}${s.email}</div>
                </div>
            `).join('');
            box.style.display = 'block';
        });
    }, 300);
});

function selectStudent(id, name, studentId, email) {
    document.getElementById('selectedStudentIdInput').value = id;
    document.getElementById('studentSearch').value = name;
    document.getElementById('studentSuggestions').style.display = 'none';
    document.getElementById('selectedStudentName').textContent = name;
    document.getElementById('selectedStudentMeta').textContent = (studentId ? studentId + ' · ' : '') + email;
    document.getElementById('selectedStudentInfo').style.display = 'block';
    const btn = document.getElementById('assignSubmitBtn');
    btn.disabled = false;
    btn.style.opacity = '1';
}

// ── Session notifications ───────────────────────────────────────────────────
@if(session('success'))
    Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true, icon:'success', title:'{{ session('success') }}' });
@endif
@if(session('error'))
    Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true, icon:'error', title:'{{ session('error') }}' });
@endif
</script>
@endpush