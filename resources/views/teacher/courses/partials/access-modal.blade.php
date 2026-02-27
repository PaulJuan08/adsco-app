{{-- resources/views/teacher/courses/partials/access-modal.blade.php --}}
<div class="filter-bar">
    <form id="filter-form" onsubmit="return false;">
        <div class="filter-row">
            <div class="filter-group">
                <label class="filter-label"><i class="fas fa-search"></i> Search</label>
                <input type="text" class="filter-input" id="student-search" placeholder="Search by name, email, ID...">
            </div>
            
            <div class="filter-group">
                <label class="filter-label"><i class="fas fa-university"></i> College</label>
                <select class="filter-select" id="college-filter">
                    <option value="">All Colleges</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}">{{ $college->college_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label class="filter-label"><i class="fas fa-book"></i> Program</label>
                <select class="filter-select" id="program-filter" disabled>
                    <option value="">All Programs</option>
                </select>
            </div>
        </div>
    </form>
</div>

<div class="student-table-wrap">
    <table class="student-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>ID</th>
                <th>Program</th>
                <th>College</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                @php
                    $isEnrolled = in_array($student->id, $enrolledStudentIds);
                @endphp
                <tr data-college-id="{{ $student->college_id }}" data-program-id="{{ $student->program_id }}">
                    <td>
                        <div class="student-name">{{ $student->full_name }}</div>
                        <div class="student-sub">{{ $student->email }}</div>
                    </td>
                    <td>
                        <span class="student-id">{{ $student->student_id ?? 'N/A' }}</span>
                    </td>
                    <td>
                        {{ $student->program->program_name ?? 'N/A' }}
                    </td>
                    <td>
                        {{ $student->college->college_name ?? 'N/A' }}
                    </td>
                    <td>
                        <span class="status-badge {{ $isEnrolled ? 'status-enrolled' : 'status-not-enrolled' }}">
                            @if($isEnrolled)
                                <i class="fas fa-check-circle"></i> Enrolled
                            @else
                                <i class="fas fa-times-circle"></i> Not Enrolled
                            @endif
                        </span>
                    </td>
                    <td>
                        <label class="enrollment-toggle">
                            <input type="checkbox" 
                                   class="toggle-enrollment" 
                                   value="{{ $student->id }}"
                                   data-url="{{ route('teacher.courses.toggle-enrollment', ['encryptedId' => $encryptedCourseId]) }}"
                                   {{ $isEnrolled ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">
                        <div class="empty-state">
                            <i class="fas fa-user-graduate"></i>
                            <h3>No Students Found</h3>
                            <p>There are no students in the system yet.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($students->hasPages())
    <div class="pagination-row">
        <div class="pagination-info">
            Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
        </div>
        <div class="pagination-links">
            {{ $students->links() }}
        </div>
    </div>
@endif