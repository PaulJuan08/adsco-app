@extends('layouts.admin')

@section('title', 'College Details - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/colleges-show.css') }}">
@endpush

@section('content')
    <!-- College Profile Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-university card-icon"></i>
                <h2 class="card-title">College Details</h2>
            </div>
            <div class="top-actions">
                <a href="{{ route('admin.colleges.edit', Crypt::encrypt($college->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="top-action-btn delete-btn" id="deleteButton">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="{{ route('admin.colleges.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Hidden Delete Form -->
            <form action="{{ route('admin.colleges.destroy', Crypt::encrypt($college->id)) }}" method="POST" id="deleteForm" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            
            <!-- College Avatar & Basic Info -->
            <div class="college-avatar-section">
                <div class="college-details-avatar" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    {{ strtoupper(substr($college->college_name, 0, 1)) }}
                </div>
                <h3 class="college-title">{{ $college->college_name }}</h3>
                <p class="college-years">{{ Str::limit($college->college_year, 50) }}</p>
                <div class="college-status-container">
                    <div class="status-badge {{ $college->status == 1 ? 'status-published' : 'status-draft' }}">
                        <i class="fas {{ $college->status == 1 ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $college->status == 1 ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
            
            <!-- Statistics Grid -->
            <div class="stats-grid-small">
                <div class="stat-box">
                    <div class="stat-box-value">{{ $college->students_count ?? 0 }}</div>
                    <div class="stat-box-label">Total Students</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">{{ $programs->count() }}</div>
                    <div class="stat-box-label">Programs</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">{{ $college->created_at->format('M Y') }}</div>
                    <div class="stat-box-label">Established</div>
                </div>
            </div>
            
            <!-- Detailed Information -->
            <div class="details-grid">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i>
                        College Information
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">College Name</div>
                        <div class="detail-value">{{ $college->college_name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Available Years</div>
                        <div class="detail-value">
                            <div class="years-tags">
                                @foreach(explode(',', $college->college_year) as $year)
                                    @if(trim($year))
                                        <span class="year-tag">{{ trim($year) }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @if($college->description)
                    <div class="detail-row">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">{{ $college->description }}</div>
                    </div>
                    @endif
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-chart-bar"></i>
                        Statistics
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">College ID</div>
                        <div class="detail-value">#{{ $college->id }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Created</div>
                        <div class="detail-value">
                            {{ $college->created_at->format('M d, Y') }}
                            <div class="detail-subvalue">
                                <i class="fas fa-clock"></i> {{ $college->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Updated</div>
                        <div class="detail-value">
                            {{ $college->updated_at->format('M d, Y') }}
                            <div class="detail-subvalue">
                                <i class="fas fa-clock"></i> {{ $college->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            @if($college->status == 1)
                                <span style="color: #10b981;">Active (1)</span>
                            @else
                                <span style="color: #f59e0b;">Inactive (0)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Programs Section - Read Only -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Programs ({{ $programs->count() }} total)
                </div>
                
                @if($programs->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-graduation-cap"></i></div>
                        <h3 class="empty-title">No Programs Yet</h3>
                        <p class="empty-text">This college doesn't have any programs yet.</p>
                    </div>
                @else
                    <div class="programs-list">
                        @foreach($programs as $program)
                        <div class="program-item">
                            <div class="program-info">
                                <div class="program-avatar">
                                    {{ strtoupper(substr($program->program_name, 0, 1)) }}
                                </div>
                                <div class="program-details">
                                    <div class="program-name">{{ $program->program_name }}</div>
                                    @if($program->program_code)
                                    <div class="program-meta">
                                        <i class="fas fa-code"></i> {{ $program->program_code }}
                                        &nbsp;·&nbsp;
                                        <i class="fas fa-users"></i> {{ $program->students_count ?? 0 }} students
                                    </div>
                                    @else
                                    <div class="program-meta">
                                        <i class="fas fa-users"></i> {{ $program->students_count ?? 0 }} students
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="program-actions">
                                <a href="{{ route('admin.programs.show', ['encryptedId' => Crypt::encrypt($program->id)]) }}" class="program-action-btn view">
                                    <i class="fas fa-eye"></i> View Program
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Updated info note -->
                    <div style="margin-top: 1.25rem; padding: 0.875rem 1.25rem; background: #eff6ff; border-radius: 8px; border-left: 4px solid #3b82f6; font-size: 0.875rem; color: #1e40af; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-info-circle"></i>
                        <span>Click <strong>View Program</strong> to see students enrolled in that program.</span>
                    </div>
                @endif
            </div>
            
            <!-- All Students Section - Read Only -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-users"></i>
                    All Students in This College ({{ $college->students_count ?? 0 }} total)
                </div>
                
                <!-- Search and Filter -->
                <div class="search-container">
                    <input type="text" class="search-input" id="searchStudent" placeholder="Search by name, email, or student ID...">
                    <select class="filter-select" id="filterYear">
                        <option value="">All Years</option>
                        @foreach(explode(',', $college->college_year) as $year)
                            @if(trim($year))
                                <option value="{{ trim($year) }}">{{ trim($year) }}</option>
                            @endif
                        @endforeach
                    </select>
                    <select class="filter-select" id="filterProgram">
                        <option value="">All Programs</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                @if($students->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-user-graduate"></i></div>
                        <h3 class="empty-title">No students yet</h3>
                        <p class="empty-text">No students are enrolled in this college yet.</p>
                    </div>
                @else
                    <div class="student-list" id="studentList">
                        @foreach($students as $student)
                        <div class="student-item" 
                             data-name="{{ strtolower($student->f_name . ' ' . $student->l_name) }}"
                             data-email="{{ strtolower($student->email) }}"
                             data-id="{{ strtolower($student->student_id ?? '') }}"
                             data-year="{{ $student->college_year ?? '' }}"
                             data-program="{{ $student->program_id ?? '' }}">
                            <div class="student-info">
                                <div class="student-avatar">
                                    {{ strtoupper(substr($student->f_name, 0, 1)) }}{{ strtoupper(substr($student->l_name, 0, 1)) }}
                                </div>
                                <div class="student-details">
                                    <div class="student-name">{{ $student->f_name }} {{ $student->l_name }}</div>
                                    <div class="student-meta">
                                        <i class="fas fa-id-card"></i> {{ $student->student_id ?? 'No ID' }} ·
                                        <i class="fas fa-envelope"></i> {{ $student->email }}
                                        @if($student->program)
                                            · <i class="fas fa-graduation-cap"></i> {{ $student->program->program_name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                @if($student->college_year)
                                    <span class="student-year-badge">{{ $student->college_year }}</span>
                                @endif
                                <!-- Removed View/Edit action buttons -->
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($students->hasPages())
                    <div style="margin-top: 2rem; display: flex; justify-content: center;">
                        {{ $students->links() }}
                    </div>
                    @endif
                @endif
            </div>
            
            @if(session('success'))
            <div class="message-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="message-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .programs-list { margin-top: 1rem; }
    .program-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    .program-item:hover {
        background: #f3f4f6;
        border-color: #4f46e5;
        transform: translateX(4px);
        box-shadow: -4px 0 0 #4f46e5;
    }
    .program-info { display: flex; align-items: center; gap: 1rem; flex: 1; }
    .program-avatar {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 600; font-size: 0.875rem; flex-shrink: 0;
    }
    .program-details { display: flex; flex-direction: column; }
    .program-name { font-weight: 600; font-size: 0.9375rem; color: #1f2937; }
    .program-meta { font-size: 0.8125rem; color: #6b7280; margin-top: 0.125rem; }
    .program-meta i { margin-right: 0.25rem; font-size: 0.75rem; }
    .program-actions { display: flex; gap: 0.5rem; align-items: center; }
    .program-action-btn {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .program-action-btn.view { background: #4f46e5; color: white; }
    .program-action-btn.view:hover { background: #4338ca; transform: translateY(-2px); box-shadow: 0 2px 8px rgba(79,70,229,0.3); }

    .years-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 0.5rem 0; }
    .year-tag { background: #f3f4f6; padding: 0.375rem 1rem; border-radius: 999px; font-size: 0.8125rem; font-weight: 500; color: #4b5563; border: 1px solid #e5e7eb; }

    .student-list { margin-top: 1rem; }
    .student-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.75rem 1rem; background: #f9fafb; border-radius: 8px;
        margin-bottom: 0.5rem; border: 1px solid #e5e7eb; transition: all 0.2s ease;
    }
    .student-item:hover { background: #f3f4f6; border-color: #4f46e5; transform: translateX(4px); box-shadow: -4px 0 0 #4f46e5; }
    .student-info { display: flex; align-items: center; gap: 1rem; flex: 1; }
    .student-avatar {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 600; font-size: 0.875rem; flex-shrink: 0;
    }
    .student-details { display: flex; flex-direction: column; }
    .student-name { font-weight: 600; font-size: 0.9375rem; color: #1f2937; }
    .student-meta { font-size: 0.8125rem; color: #6b7280; margin-top: 0.125rem; }
    .student-meta i { margin-right: 0.25rem; font-size: 0.75rem; }
    .student-year-badge { background: #e5e7eb; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; color: #4b5563; white-space: nowrap; }

    .search-container { margin-bottom: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .search-input { flex: 1; min-width: 200px; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.875rem; transition: all 0.2s ease; }
    .search-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
    .filter-select { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.875rem; background: white; min-width: 150px; }

    .empty-state { text-align: center; padding: 2rem 1rem; }
    .empty-icon { font-size: 2.5rem; color: #cbd5e0; margin-bottom: 0.75rem; }
    .empty-title { font-size: 1rem; font-weight: 600; color: #718096; margin-bottom: 0.25rem; }
    .empty-text { font-size: 0.8125rem; color: #a0aec0; margin-bottom: 1rem; }

    .message-success { margin-top: 1.25rem; padding: 0.875rem 1.25rem; background: #f0fff4; color: #276749; border-radius: 10px; border-left: 4px solid #48bb78; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; }
    .message-error { margin-top: 1.25rem; padding: 0.875rem 1.25rem; background: #fff5f5; color: #c53030; border-radius: 10px; border-left: 4px solid #f56565; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; }

    @media (max-width: 768px) {
        .search-container { flex-direction: column; }
        .filter-select { width: 100%; }
        .student-item, .program-item { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButton = document.getElementById('deleteButton');
    if (deleteButton) {
        deleteButton.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete College?',
                text: 'This action cannot be undone. All college data will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f56565',
                cancelButtonColor: '#a0aec0',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                    deleteButton.disabled = true;
                    document.getElementById('deleteForm').submit();
                }
            });
        });
    }

    // Search + filter
    const searchInput = document.getElementById('searchStudent');
    const filterYear  = document.getElementById('filterYear');
    const filterProg  = document.getElementById('filterProgram');
    const studentItems = document.querySelectorAll('.student-item');

    function filterStudents() {
        const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const year = filterYear ? filterYear.value : '';
        const prog = filterProg ? filterProg.value : '';

        studentItems.forEach(item => {
            const matchSearch = term === '' || item.dataset.name.includes(term) || item.dataset.email.includes(term) || item.dataset.id.includes(term);
            const matchYear   = year === '' || item.dataset.year === year;
            const matchProg   = prog === '' || item.dataset.program === prog;
            item.style.display = (matchSearch && matchYear && matchProg) ? 'flex' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterStudents);
    if (filterYear)  filterYear.addEventListener('change', filterStudents);
    if (filterProg)  filterProg.addEventListener('change', filterStudents);

    @if(session('success'))
        Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true, icon:'success', title:'{{ session('success') }}' });
    @endif
    @if(session('error'))
        Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true, icon:'error', title:'{{ session('error') }}' });
    @endif
});
</script>
@endpush