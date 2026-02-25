{{-- resources/views/admin/enrollments/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Enrollment Management - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/enrollment-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Enrollment Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-user-graduate"></i> Manage course enrollments
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.courses.create') }}" class="top-action-btn">
                    <i class="fas fa-plus-circle"></i> Create Course
                </a>
                <a href="{{ route('admin.users.index') }}?role=4" class="top-action-btn">
                    <i class="fas fa-users"></i> Manage Students
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Courses</div>
                    <div class="stat-number">{{ $courses->count() }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published</div>
                    <div class="stat-number">{{ $courses->where('is_published', true)->count() }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Enrollments</div>
                    <div class="stat-number">{{ $courses->sum('students_count') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg per Course</div>
                    <div class="stat-number">{{ $courses->count() > 0 ? round($courses->sum('students_count') / $courses->count(), 1) : 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="enrollment-container">
        <div class="courses-grid">
            @forelse($courses as $course)
            <div class="course-card" data-course-id="{{ $course->id }}" data-encrypted-id="{{ $course->encrypted_id }}">
                <div class="course-card-header">
                    <h3>
                        <i class="fas fa-graduation-cap" style="color: #667eea;"></i>
                        {{ $course->title }}
                    </h3>
                    <span class="course-code">{{ $course->course_code }}</span>
                </div>
                <div class="course-card-body">
                    <div class="course-description">
                        {{ Str::limit($course->description ?? 'No description available', 100) }}
                    </div>
                    
                    <div class="course-stats">
                        <div class="stat-item">
                            <div class="stat-value">{{ $course->students_count }}</div>
                            <div class="stat-label">Enrolled</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $course->credits }}</div>
                            <div class="stat-label">Credits</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">
                                <i class="fas fa-{{ $course->is_published ? 'check-circle' : 'clock' }} text-{{ $course->is_published ? 'success' : 'warning' }}"></i>
                            </div>
                            <div class="stat-label">{{ $course->is_published ? 'Published' : 'Draft' }}</div>
                        </div>
                    </div>
                    
                    <div class="course-actions">
                        <button class="btn btn-primary" onclick="openEnrollModal('{{ $course->encrypted_id }}', '{{ addslashes($course->title) }}')">
                            <i class="fas fa-user-plus"></i> Enroll
                        </button>
                        <button class="btn btn-info" onclick="openViewStudentsModal('{{ $course->encrypted_id }}', '{{ addslashes($course->title) }}')">
                            <i class="fas fa-users"></i> View ({{ $course->students_count }})
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-book-open"></i>
                <h3>No Courses Available</h3>
                <p>Create a course first to manage enrollments.</p>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Course
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Enrollment Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>

<!-- Enroll Students Modal -->
<div class="modal" id="enrollModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>
                <i class="fas fa-user-plus" style="color: #667eea;"></i>
                Enroll Students in <span id="modalCourseTitle"></span>
            </h3>
            <button type="button" class="modal-close" onclick="closeEnrollModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Student ID</label>
                        <input type="text" class="filter-input" id="modalStudentIdFilter" placeholder="Enter student ID...">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Name</label>
                        <input type="text" class="filter-input" id="modalNameFilter" placeholder="Search by name...">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">College</label>
                        <select class="filter-select" id="modalCollegeFilter" onchange="modalLoadPrograms(this.value)">
                            <option value="all">All Colleges</option>
                            @foreach($colleges as $college)
                                <option value="{{ $college->id }}">{{ $college->college_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Program</label>
                        <select class="filter-select" id="modalProgramFilter">
                            <option value="all">All Programs</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Year Level</label>
                        <select class="filter-select" id="modalYearFilter">
                            <option value="all">All Years</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Selected Count -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span class="selected-count" id="modalSelectedCount">0 selected</span>
                <button class="btn btn-sm btn-outline" onclick="modalClearSelections()">
                    <i class="fas fa-times"></i> Clear All
                </button>
            </div>
            
            <!-- Students List -->
            <div id="modalStudentsList" class="students-list-modal">
                <div style="text-align: center; padding: 3rem;">
                    <div class="loading-spinner loading-spinner-lg" style="margin: 0 auto 1rem;"></div>
                    <p style="color: #64748b;">Loading students...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div id="modalPagination" class="pagination-info" style="display: none;">
                <span id="modalPaginationInfo"></span>
                <div class="pagination-controls" id="modalPaginationControls"></div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeEnrollModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-success" onclick="modalEnrollStudents()" id="modalEnrollButton" disabled>
                <i class="fas fa-user-plus"></i> Enroll Selected
            </button>
        </div>
    </div>
</div>

<!-- View Enrolled Students Modal -->
<div class="modal" id="viewStudentsModal">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-header">
            <h3>
                <i class="fas fa-users" style="color: #48bb78;"></i>
                Enrolled Students in <span id="viewModalCourseTitle"></span>
            </h3>
            <button type="button" class="modal-close" onclick="closeViewStudentsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <!-- Search Filter -->
            <div class="filters-section" style="margin-bottom: 1rem;">
                <div class="filter-row" style="margin-bottom: 0;">
                    <div class="filter-group" style="width: 100%;">
                        <label class="filter-label">Search Enrolled Students</label>
                        <div class="search-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" class="filter-input" id="viewSearchFilter" placeholder="Search by name, email, or student ID..." oninput="debouncedSearchEnrolled()">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Enrolled Students List -->
            <div id="viewStudentsList" class="students-list-modal" style="max-height: 400px;">
                <div style="text-align: center; padding: 3rem;">
                    <div class="loading-spinner loading-spinner-lg" style="margin: 0 auto 1rem;"></div>
                    <p style="color: #64748b;">Loading enrolled students...</p>
                </div>
            </div>
            
            <!-- Enrolled Count -->
            <div style="margin-top: 1rem; padding: 0.5rem; background: #f8fafc; border-radius: 8px; text-align: center;">
                <span class="enrolled-count" id="viewEnrolledCount">0</span> students enrolled
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeViewStudentsModal()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============ GLOBAL VARIABLES ============
    let currentEncryptedCourseId = null;
    let currentCourseTitle = '';
    let modalSelectedStudentIds = [];
    let modalCurrentPage = 1;
    let modalLastPage = 1;
    let modalTotalStudents = 0;
    let modalEnrolledStudentIds = [];
    
    // View modal variables
    let viewCurrentEncryptedCourseId = null;
    let viewAllEnrolledStudents = [];
    let viewFilteredStudents = [];
    
    // Debounce function for filters
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ============ ENROLL MODAL FUNCTIONS ============
    function openEnrollModal(encryptedCourseId, courseTitle) {
        currentEncryptedCourseId = encryptedCourseId;
        currentCourseTitle = courseTitle;
        document.getElementById('modalCourseTitle').textContent = courseTitle;
        document.getElementById('enrollModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Reset selections
        modalSelectedStudentIds = [];
        modalUpdateSelectedCount();
        
        // Load enrolled students for this course to mark them as disabled
        loadEnrolledStudentIds(encryptedCourseId).then(() => {
            // Load students with auto filters
            modalLoadStudents();
        });
        
        // Set up auto-filter listeners
        setupAutoFilters();
    }
    
    function closeEnrollModal() {
        document.getElementById('enrollModal').classList.remove('show');
        document.body.style.overflow = '';
        currentEncryptedCourseId = null;
    }
    
    // Load enrolled student IDs for the current course
    async function loadEnrolledStudentIds(encryptedCourseId) {
        try {
            const response = await fetch(`/admin/enrollments/course/${encodeURIComponent(encryptedCourseId)}/student-ids`);
            const data = await response.json();
            modalEnrolledStudentIds = data.student_ids || [];
        } catch (error) {
            console.error('Error loading enrolled student IDs:', error);
            modalEnrolledStudentIds = [];
        }
    }
    
    // Setup auto-filter listeners
    function setupAutoFilters() {
        const filters = ['modalStudentIdFilter', 'modalNameFilter', 'modalCollegeFilter', 'modalProgramFilter', 'modalYearFilter'];
        
        filters.forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                // Remove existing listeners to prevent duplicates
                element.removeEventListener('input', debouncedModalLoadStudents);
                element.removeEventListener('change', debouncedModalLoadStudents);
                
                if (filterId === 'modalCollegeFilter') {
                    element.addEventListener('change', function(e) {
                        modalLoadPrograms(e.target.value);
                        debouncedModalLoadStudents();
                    });
                } else if (filterId === 'modalProgramFilter' || filterId === 'modalYearFilter') {
                    element.addEventListener('change', debouncedModalLoadStudents);
                } else {
                    element.addEventListener('input', debouncedModalLoadStudents);
                }
            }
        });
    }
    
    const debouncedModalLoadStudents = debounce(() => {
        modalLoadStudents(1);
    }, 500);
    
    // Load programs for modal college filter
    function modalLoadPrograms(collegeId) {
        const programSelect = document.getElementById('modalProgramFilter');
        
        if (!collegeId || collegeId === 'all') {
            programSelect.innerHTML = '<option value="all">All Programs</option>';
            return;
        }
        
        fetch(`/admin/enrollments/programs/${collegeId}`)
            .then(response => response.json())
            .then(programs => {
                let options = '<option value="all">All Programs</option>';
                programs.forEach(program => {
                    options += `<option value="${program.id}">${program.program_name} (${program.program_code})</option>`;
                });
                programSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error loading programs:', error);
            });
    }
    
    // Load students for modal with auto filters
    function modalLoadStudents(page = 1) {
        if (!currentEncryptedCourseId) return;
        
        const studentId = document.getElementById('modalStudentIdFilter').value;
        const name = document.getElementById('modalNameFilter').value;
        const collegeId = document.getElementById('modalCollegeFilter').value;
        const programId = document.getElementById('modalProgramFilter').value;
        const year = document.getElementById('modalYearFilter').value;
        
        const studentsList = document.getElementById('modalStudentsList');
        studentsList.innerHTML = '<div style="text-align: center; padding: 3rem;"><div class="loading-spinner loading-spinner-lg" style="margin: 0 auto 1rem;"></div><p>Loading students...</p></div>';
        
        let url = `/admin/enrollments/students?page=${page}&course_id=${encodeURIComponent(currentEncryptedCourseId)}`;
        if (studentId) url += `&student_id=${encodeURIComponent(studentId)}`;
        if (name) url += `&name=${encodeURIComponent(name)}`;
        if (collegeId && collegeId !== 'all') url += `&college_id=${collegeId}`;
        if (programId && programId !== 'all') url += `&program_id=${programId}`;
        if (year && year !== 'all') url += `&college_year=${encodeURIComponent(year)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                modalRenderStudents(data);
                modalCurrentPage = data.current_page;
                modalLastPage = data.last_page;
                modalTotalStudents = data.total;
                modalUpdatePagination();
            })
            .catch(error => {
                console.error('Error loading students:', error);
                studentsList.innerHTML = '<div style="text-align: center; padding: 2rem; color: #ef4444;"><i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i><p>Error loading students. Please try again.</p></div>';
            });
    }
    
    // Render students in modal
    function modalRenderStudents(data) {
        const studentsList = document.getElementById('modalStudentsList');
        
        if (data.data.length === 0) {
            studentsList.innerHTML = '<div class="empty-state"><i class="fas fa-users"></i><h3>No Students Found</h3><p>Try adjusting your filters.</p></div>';
            return;
        }
        
        let html = '';
        data.data.forEach(student => {
            const isSelected = modalSelectedStudentIds.includes(student.id);
            const isEnrolled = modalEnrolledStudentIds.includes(student.id);
            
            html += `
                <div class="student-item ${isEnrolled ? 'disabled' : ''}" data-student-id="${student.id}">
                    <div class="student-checkbox">
                        <input type="checkbox" 
                               value="${student.id}" 
                               ${isSelected ? 'checked' : ''} 
                               ${isEnrolled ? 'disabled' : ''}
                               onchange="modalToggleStudent(${student.id}, this.checked)">
                    </div>
                    <div class="student-avatar">
                        ${student.f_name.charAt(0).toUpperCase()}${student.l_name.charAt(0).toUpperCase()}
                    </div>
                    <div class="student-info">
                        <div class="student-name">
                            ${student.f_name} ${student.l_name}
                            ${student.student_id ? `<span class="student-badge badge-college">ID: ${student.student_id}</span>` : ''}
                            ${isEnrolled ? '<span class="student-status enrolled"><i class="fas fa-check-circle"></i> Already Enrolled</span>' : ''}
                        </div>
                        <div class="student-email">${student.email}</div>
                        <div class="student-details">
                            ${student.college ? `<span class="student-badge badge-college"><i class="fas fa-university"></i> ${student.college.college_name}</span>` : ''}
                            ${student.program ? `<span class="student-badge badge-program"><i class="fas fa-graduation-cap"></i> ${student.program.program_name}</span>` : ''}
                            ${student.college_year ? `<span class="student-badge badge-year"><i class="fas fa-calendar"></i> ${student.college_year}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        studentsList.innerHTML = html;
        document.getElementById('modalPagination').style.display = 'flex';
    }
    
    // Toggle student selection in modal
    function modalToggleStudent(studentId, checked) {
        if (checked) {
            if (!modalSelectedStudentIds.includes(studentId)) {
                modalSelectedStudentIds.push(studentId);
            }
        } else {
            modalSelectedStudentIds = modalSelectedStudentIds.filter(id => id !== studentId);
        }
        
        modalUpdateSelectedCount();
    }
    
    // Update selected count in modal
    function modalUpdateSelectedCount() {
        const count = modalSelectedStudentIds.length;
        document.getElementById('modalSelectedCount').textContent = count + ' selected';
        
        const enrollButton = document.getElementById('modalEnrollButton');
        if (count > 0 && currentEncryptedCourseId) {
            enrollButton.disabled = false;
        } else {
            enrollButton.disabled = true;
        }
    }
    
    // Clear selections in modal
    function modalClearSelections() {
        modalSelectedStudentIds = [];
        modalUpdateSelectedCount();
        
        // Uncheck all checkboxes
        document.querySelectorAll('#modalStudentsList .student-checkbox input[type="checkbox"]:not(:disabled)').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
    
    // Update pagination in modal
    function modalUpdatePagination() {
        const pagination = document.getElementById('modalPagination');
        const paginationInfo = document.getElementById('modalPaginationInfo');
        const paginationControls = document.getElementById('modalPaginationControls');
        
        paginationInfo.textContent = `Page ${modalCurrentPage} of ${modalLastPage} • ${modalTotalStudents} total students`;
        
        let controls = '';
        
        // Previous button
        controls += `<button class="pagination-btn" ${modalCurrentPage === 1 ? 'disabled' : ''} onclick="modalLoadStudents(${modalCurrentPage - 1})">Previous</button>`;
        
        // Page numbers
        const start = Math.max(1, modalCurrentPage - 2);
        const end = Math.min(modalLastPage, modalCurrentPage + 2);
        
        for (let i = start; i <= end; i++) {
            controls += `<button class="pagination-btn ${i === modalCurrentPage ? 'active' : ''}" onclick="modalLoadStudents(${i})">${i}</button>`;
        }
        
        // Next button
        controls += `<button class="pagination-btn" ${modalCurrentPage === modalLastPage ? 'disabled' : ''} onclick="modalLoadStudents(${modalCurrentPage + 1})">Next</button>`;
        
        paginationControls.innerHTML = controls;
    }
    
    // Enroll selected students from modal
    function modalEnrollStudents() {
        if (modalSelectedStudentIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Students Selected',
                text: 'Please select at least one student to enroll.'
            });
            return;
        }
        
        Swal.fire({
            title: 'Enroll Students?',
            html: `You are about to enroll <strong>${modalSelectedStudentIds.length} student(s)</strong> in <strong>${currentCourseTitle}</strong>.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#48bb78',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Enroll',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const enrollButton = document.getElementById('modalEnrollButton');
                enrollButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
                enrollButton.disabled = true;
                
                fetch('{{ route("admin.enrollments.enroll") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        course_id: currentEncryptedCourseId,
                        student_ids: modalSelectedStudentIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Close modal
                        closeEnrollModal();
                        
                        // Update the course card count
                        updateCourseCount(currentEncryptedCourseId, modalSelectedStudentIds.length);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to enroll students. Please try again.'
                    });
                })
                .finally(() => {
                    enrollButton.innerHTML = '<i class="fas fa-user-plus"></i> Enroll Selected';
                    enrollButton.disabled = modalSelectedStudentIds.length === 0;
                });
            }
        });
    }
    
    // Update course count after enrollment
    function updateCourseCount(encryptedCourseId, addedCount) {
        const courseCard = document.querySelector(`.course-card[data-encrypted-id="${encryptedCourseId}"]`);
        if (courseCard) {
            const statValue = courseCard.querySelector('.stat-item:first-child .stat-value');
            const viewButton = courseCard.querySelector('.btn-info');
            
            if (statValue) {
                const currentCount = parseInt(statValue.textContent);
                statValue.textContent = currentCount + addedCount;
            }
            
            if (viewButton) {
                const match = viewButton.textContent.match(/\((\d+)\)/);
                if (match) {
                    const currentCount = parseInt(match[1]);
                    viewButton.innerHTML = `<i class="fas fa-users"></i> View (${currentCount + addedCount})`;
                }
            }
        }
    }
    
    // ============ VIEW STUDENTS MODAL FUNCTIONS ============
    function openViewStudentsModal(encryptedCourseId, courseTitle) {
        viewCurrentEncryptedCourseId = encryptedCourseId;
        document.getElementById('viewModalCourseTitle').textContent = courseTitle;
        document.getElementById('viewStudentsModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Clear search
        document.getElementById('viewSearchFilter').value = '';
        
        // Load enrolled students
        loadEnrolledStudents(encryptedCourseId);
    }
    
    function closeViewStudentsModal() {
        document.getElementById('viewStudentsModal').classList.remove('show');
        document.body.style.overflow = '';
        viewCurrentEncryptedCourseId = null;
        viewAllEnrolledStudents = [];
        viewFilteredStudents = [];
    }
    
    function loadEnrolledStudents(encryptedCourseId) {
        const viewStudentsList = document.getElementById('viewStudentsList');
        viewStudentsList.innerHTML = '<div style="text-align: center; padding: 3rem;"><div class="loading-spinner loading-spinner-lg" style="margin: 0 auto 1rem;"></div><p>Loading enrolled students...</p></div>';
        
        fetch(`/admin/enrollments/course/${encodeURIComponent(encryptedCourseId)}/students`)
            .then(response => response.json())
            .then(students => {
                viewAllEnrolledStudents = students;
                viewFilteredStudents = students;
                renderEnrolledStudents(students);
                document.getElementById('viewEnrolledCount').textContent = students.length;
            })
            .catch(error => {
                console.error('Error loading enrolled students:', error);
                viewStudentsList.innerHTML = '<div style="text-align: center; padding: 2rem; color: #ef4444;"><i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i><p>Error loading enrolled students.</p></div>';
            });
    }
    
    function renderEnrolledStudents(students) {
        const viewStudentsList = document.getElementById('viewStudentsList');
        
        if (students.length === 0) {
            viewStudentsList.innerHTML = '<div class="empty-state"><i class="fas fa-users"></i><h3>No Enrolled Students</h3><p>No students are currently enrolled in this course.</p></div>';
            return;
        }
        
        let html = '';
        students.forEach(student => {
            html += `
                <div class="enrolled-item" style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem; padding: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="enrolled-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                                ${student.name ? student.name.charAt(0).toUpperCase() : '?'}
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; font-size: 0.875rem; margin: 0 0 0.25rem 0;">${student.name}</h4>
                                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">${student.email}</p>
                                <p style="font-size: 0.75rem; color: #64748b; margin: 0.125rem 0 0 0;">Student ID: ${student.student_id || 'N/A'}</p>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline" style="color: #ef4444; border-color: #ef4444;" onclick="removeStudentFromView(${student.id}, '${student.name}', '${viewCurrentEncryptedCourseId}')">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                </div>
            `;
        });
        
        viewStudentsList.innerHTML = html;
    }
    
    function searchEnrolledStudents() {
        const searchTerm = document.getElementById('viewSearchFilter').value.toLowerCase();
        
        if (!searchTerm) {
            viewFilteredStudents = viewAllEnrolledStudents;
        } else {
            viewFilteredStudents = viewAllEnrolledStudents.filter(student => 
                student.name.toLowerCase().includes(searchTerm) ||
                student.email.toLowerCase().includes(searchTerm) ||
                (student.student_id && student.student_id.toLowerCase().includes(searchTerm))
            );
        }
        
        renderEnrolledStudents(viewFilteredStudents);
    }
    
    const debouncedSearchEnrolled = debounce(() => {
        searchEnrolledStudents();
    }, 300);
    
    // ============ REMOVE STUDENT ============
    function removeStudentFromView(studentId, studentName, encryptedCourseId) {
        Swal.fire({
            title: 'Remove Student?',
            html: `Are you sure you want to remove <strong>${studentName}</strong> from this course?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.enrollments.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        course_id: encryptedCourseId,
                        student_id: studentId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Refresh the enrolled students list
                        loadEnrolledStudents(encryptedCourseId);
                        
                        // Update the course card count
                        updateCourseCountAfterRemoval(encryptedCourseId);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove student. Please try again.'
                    });
                });
            }
        });
    }
    
    function updateCourseCountAfterRemoval(encryptedCourseId) {
        const courseCard = document.querySelector(`.course-card[data-encrypted-id="${encryptedCourseId}"]`);
        if (courseCard) {
            const statValue = courseCard.querySelector('.stat-item:first-child .stat-value');
            const viewButton = courseCard.querySelector('.btn-info');
            
            if (statValue) {
                const currentCount = parseInt(statValue.textContent);
                statValue.textContent = Math.max(0, currentCount - 1);
            }
            
            if (viewButton) {
                const match = viewButton.textContent.match(/\((\d+)\)/);
                if (match) {
                    const currentCount = parseInt(match[1]);
                    viewButton.innerHTML = `<i class="fas fa-users"></i> View (${Math.max(0, currentCount - 1)})`;
                }
            }
        }
    }
    
    // ============ CLICK OUTSIDE MODAL ============
    document.addEventListener('click', function(event) {
        const enrollModal = document.getElementById('enrollModal');
        const viewModal = document.getElementById('viewStudentsModal');
        
        if (event.target === enrollModal) {
            closeEnrollModal();
        }
        if (event.target === viewModal) {
            closeViewStudentsModal();
        }
    });
    
    // ============ INITIAL LOAD ============
    document.addEventListener('DOMContentLoaded', function() {
        // Load programs for initial college if any in modal
        const collegeFilter = document.getElementById('modalCollegeFilter');
        if (collegeFilter && collegeFilter.value && collegeFilter.value !== 'all') {
            modalLoadPrograms(collegeFilter.value);
        }
    });
</script>
@endpush