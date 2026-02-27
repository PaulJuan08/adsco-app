@extends('layouts.admin')

@section('title', $course->title . ' - Course Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-show.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.courses.index') }}">Courses</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">{{ Str::limit($course->title, 30) }}</span>
    </div>

    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon"><i class="fas fa-book"></i></div>
                <h1 class="card-title">{{ $course->title }}</h1>
            </div>
            <div class="top-actions">
                <button type="button" class="top-action-btn grant-access" onclick="openAccessModal()">
                    <i class="fas fa-user-plus"></i> Grant Access
                </button>
                @if($course->is_published)
                    <button type="button" class="top-action-btn unpublish-btn" onclick="confirmUnpublish()">
                        <i class="fas fa-eye-slash"></i> Unpublish
                    </button>
                @else
                    <button type="button" class="top-action-btn publish-btn" onclick="confirmPublish()">
                        <i class="fas fa-eye"></i> Publish
                    </button>
                @endif
                <a href="{{ route('admin.courses.edit', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="top-action-btn delete-btn" onclick="confirmDelete()">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="{{ route('admin.courses.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <div class="course-preview">
                <div class="course-preview-avatar"><i class="fas fa-book"></i></div>
                <div class="course-preview-content">
                    <h2 class="course-preview-title">{{ $course->title }}</h2>
                    <div class="course-preview-meta">
                        <span class="course-preview-badge {{ $course->is_published ? 'published' : 'draft' }}">
                            <i class="fas {{ $course->is_published ? 'fa-check-circle' : 'fa-pen' }}"></i>
                            {{ $course->is_published ? 'Published' : 'Draft' }}
                        </span>
                        <span><i class="fas fa-code"></i> {{ $course->course_code }}</span>
                        <span><i class="fas fa-star"></i> {{ $course->credits ?? 3 }} Credits</span>
                        @if($course->teacher)
                            <span><i class="fas fa-chalkboard-teacher"></i> {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</span>
                        @endif
                        @if($course->start_date)
                            <span><i class="fas fa-calendar-alt"></i> Starts: {{ $course->start_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $enrolledCount   = $course->students_count ?? 0;
                $topicsCount     = $course->topics_count ?? 0;
                $publishedTopics = $course->published_topics_count ?? 0;
                $maxStudents     = $course->max_students ?? 'Unlimited';
            @endphp

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value">{{ $enrolledCount }}</div>
                    <div class="stat-label">Enrolled Students</div>
                    @if(is_numeric($maxStudents))
                        <div class="stat-sub">Max: {{ $maxStudents }}</div>
                    @endif
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-list"></i></div>
                    <div class="stat-value">{{ $topicsCount }}</div>
                    <div class="stat-label">Total Topics</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value">{{ $publishedTopics }}</div>
                    <div class="stat-label">Published Topics</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-value">{{ $course->created_at->format('M d') }}</div>
                    <div class="stat-label">Created</div>
                </div>
            </div>

            <div class="two-column-layout">
                <div class="form-column">

                    <div class="detail-section">
                        <h3 class="detail-section-title"><i class="fas fa-align-left"></i> Description</h3>
                        <div class="description-box">{{ $course->description ?? 'No description provided.' }}</div>
                    </div>

                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-list"></i> Course Topics
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: auto;">
                                <span style="font-size:0.75rem; color:#718096;">
                                    {{ $topicsCount }} total &bull; {{ $publishedTopics }} published
                                </span>
                                <button onclick="openAddTopicModal()" class="action-btn-small action-btn-success">
                                    <i class="fas fa-plus-circle"></i> Add Topic
                                </button>
                            </div>
                        </h3>

                        @if($course->topics && $course->topics->count() > 0)
                            <div class="search-container" style="margin-bottom:1rem;">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search topics..." id="topicSearch">
                            </div>
                            <div style="display:flex; gap:0.75rem; margin-bottom:1rem; font-size:0.75rem; color:#718096; flex-wrap:wrap;">
                                <span><i class="fas fa-circle" style="color:#48bb78; font-size:0.5rem; vertical-align:middle;"></i> Published — visible to students</span>
                                <span><i class="fas fa-circle" style="color:#ed8936; font-size:0.5rem; vertical-align:middle;"></i> Draft — hidden from students</span>
                            </div>
                            <div class="topics-section" id="topicsList">
                                @foreach($course->topics as $topic)
                                <div class="topic-card {{ !$topic->is_published ? 'topic-card--draft' : '' }}" id="topic-{{ $topic->id }}">
                                    <div class="topic-header">
                                        <div style="flex:1; min-width:0;">
                                            <div class="topic-title">
                                                {{ $topic->title }}
                                                @if($topic->is_published)
                                                    <span class="status-badge status-published" style="font-size:0.65rem; margin-left:0.5rem;">
                                                        <i class="fas fa-check-circle"></i> Published
                                                    </span>
                                                @else
                                                    <span class="status-badge status-draft" style="font-size:0.65rem; margin-left:0.5rem;">
                                                        <i class="fas fa-clock"></i> Draft
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="font-size:0.6875rem; color:#a0aec0; margin-top:0.2rem;">
                                                <i class="fas fa-clock"></i> Added {{ $topic->created_at->diffForHumans() }}
                                                @if(!$topic->is_published)
                                                    &bull; <span style="color:#ed8936;">Hidden from students</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="display:flex; gap:0.5rem; align-items:center; flex-shrink:0;">
                                            @if($topic->is_published)
                                                <button class="action-btn-small action-btn-warning"
                                                        data-encrypted-id="{{ urlencode(Crypt::encrypt($topic->id)) }}"
                                                        onclick="toggleTopicPublish({{ $topic->id }}, false, this)"
                                                        title="Unpublish this topic">
                                                    <i class="fas fa-eye-slash"></i> Unpublish
                                                </button>
                                            @else
                                                <button class="action-btn-small action-btn-success"
                                                        data-encrypted-id="{{ urlencode(Crypt::encrypt($topic->id)) }}"
                                                        onclick="toggleTopicPublish({{ $topic->id }}, true, this)"
                                                        title="Publish this topic">
                                                    <i class="fas fa-eye"></i> Publish
                                                </button>
                                            @endif
                                            <button class="action-btn-small"
                                                    onclick="removeTopic({{ $topic->id }}, '{{ addslashes($topic->title) }}')">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="topic-content">
                                        <div class="topic-description">{{ $topic->description ?? 'No description provided.' }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state" id="topicsList">
                                <i class="fas fa-folder-open"></i>
                                <h3>No Topics Yet</h3>
                                <p>Start by adding topics to this course</p>
                                <button onclick="openAddTopicModal()" class="btn-sm btn-sm-primary" style="margin-top:1rem;">
                                    <i class="fas fa-plus"></i> Add First Topic
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-users"></i> Enrolled Students
                            <span style="margin-left:auto; font-size:0.75rem; color:#718096;">{{ $enrolledCount }} enrolled</span>
                        </h3>
                        @php $enrolledStudents = $course->students()->latest()->limit(5)->get(); @endphp
                        @if($enrolledStudents->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="fas fa-user-graduate"></i></div>
                                <h3>No students enrolled yet</h3>
                                <p>Use the Grant Access button to add students</p>
                            </div>
                        @else
                            @foreach($enrolledStudents as $student)
                            <div style="margin-bottom:0.75rem; padding:0.75rem; background:#f8fafc; border-radius:8px;">
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <div class="student-avatar-small">
                                        {{ strtoupper(substr($student->f_name ?? '', 0, 1) . substr($student->l_name ?? '', 0, 1)) }}
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-weight:600; color:#2d3748;">{{ $student->full_name }}</div>
                                        <div style="font-size:0.75rem; color:#718096; display:flex; gap:1rem; margin-top:0.25rem;">
                                            <span><i class="fas fa-id-card" style="margin-right:0.25rem;"></i>{{ $student->student_id ?? 'N/A' }}</span>
                                            <span><i class="fas fa-envelope" style="margin-right:0.25rem;"></i>{{ $student->email }}</span>
                                        </div>
                                    </div>
                                    <span class="status-badge status-enrolled"><i class="fas fa-check-circle"></i> Enrolled</span>
                                </div>
                            </div>
                            @endforeach
                            @if($enrolledCount > 5)
                            <div style="text-align:center; margin-top:1rem;">
                                <button onclick="openAccessModal()" class="btn-sm btn-sm-outline">
                                    <i class="fas fa-users"></i> View All {{ $enrolledCount }} Students
                                </button>
                            </div>
                            @endif
                        @endif
                    </div>

                </div>

                <div class="sidebar-column">
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-info-circle"></i> Course Details</h3>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-heading"></i> Title</span>
                            <span class="val">{{ Str::limit($course->title, 20) }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-code"></i> Code</span>
                            <span class="val">{{ $course->course_code }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="val" style="color:{{ $course->is_published ? '#48bb78' : '#ed8936' }}">
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-star"></i> Credits</span>
                            <span class="val">{{ $course->credits ?? 3 }}</span>
                        </div>
                        @if($course->teacher)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-chalkboard-teacher"></i> Teacher</span>
                            <span class="val">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</span>
                        </div>
                        @endif
                        @if($course->start_date)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Start Date</span>
                            <span class="val">{{ $course->start_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($course->end_date)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> End Date</span>
                            <span class="val">{{ $course->end_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-users"></i> Max Students</span>
                            <span class="val highlight">{{ $course->max_students ?? 'Unlimited' }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user-graduate"></i> Enrolled</span>
                            <span class="val">{{ $enrolledCount }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user-circle"></i> Created By</span>
                            <span class="val">
                                @if($course->creator)
                                    {{ $course->creator->f_name }} {{ $course->creator->l_name }}
                                @else
                                    <span style="color:#a0aec0;">System</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Created At</span>
                            <span class="val">
                                {{ $course->created_at->format('M d, Y') }}
                                <span style="display:block; font-size:0.7rem; color:#718096;">{{ $course->created_at->diffForHumans() }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                        <button onclick="openAccessModal()" class="quick-action-link">
                            <i class="fas fa-user-plus"></i><span>Grant Student Access</span>
                        </button>
                        <button onclick="openAddTopicModal()" class="quick-action-link">
                            <i class="fas fa-plus-circle"></i><span>Add Topics</span>
                        </button>
                        <a href="{{ route('admin.courses.edit', $encryptedId) }}" class="quick-action-link">
                            <i class="fas fa-edit"></i><span>Edit Course Details</span>
                        </a>
                        <a href="{{ route('admin.courses.index') }}" class="quick-action-link">
                            <i class="fas fa-list"></i><span>All Courses</span>
                        </a>
                    </div>

                    <div class="sidebar-card help-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-lightbulb"></i> Quick Tips</h3>
                        <div class="help-text">
                            <p style="margin-bottom:0.75rem;"><strong>Grant Access:</strong> Manage which students can enroll in this course.</p>
                            <p style="margin-bottom:0.75rem;"><strong>Publishing:</strong> Students can only see published courses and published topics.</p>
                            <p><strong>Topics:</strong> Use Publish/Unpublish on each topic to control student visibility.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Access Management Modal --}}
<div id="accessModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Manage Student Access - {{ $course->title }}</h3>
            <button class="modal-close" onclick="closeAccessModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="modal-body">
            <div class="loading-container">
                <i class="fas fa-spinner fa-spin loading-spinner"></i>
                <p>Loading students...</p>
            </div>
        </div>
    </div>
</div>

{{-- Add Topic Modal --}}
<div class="modal-overlay" id="addTopicModal">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-plus-circle" style="margin-right:0.5rem;"></i>Add Topics to Course</div>
            <button class="modal-close" onclick="closeAddTopicModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="search-container" style="margin-bottom:1rem;">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search available topics..." id="modalTopicSearch" onkeyup="searchTopics()">
            </div>
            <div id="availableTopicsList" class="topics-list">
                <div class="loading-container">
                    <i class="fas fa-spinner fa-spin loading-spinner"></i>
                    <p>Loading topics...</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeAddTopicModal()">Cancel</button>
            <button class="btn btn-primary" onclick="addSelectedTopics()"><i class="fas fa-check"></i> Add Selected</button>
        </div>
    </div>
</div>

<form id="publish-form" method="POST" action="{{ route('admin.courses.publish', $encryptedId) }}" style="display:none;">
    @csrf @method('PATCH')
</form>
<form id="delete-form" method="POST" action="{{ route('admin.courses.destroy', $encryptedId) }}" style="display:none;">
    @csrf @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const ROUTES = {
    availableTopics : '{{ route("admin.courses.available-topics", $encryptedId) }}',
    addTopic        : '{{ route("admin.courses.add-topic",        $encryptedId) }}',
    addTopics       : '{{ route("admin.courses.add-topics",       $encryptedId) }}',
    removeTopic     : '{{ route("admin.courses.remove-topic",     $encryptedId) }}',
    accessModal     : '{{ route("admin.courses.access.modal",     $encryptedId) }}',
    publishTopic    : '{{ url("admin/topics") }}',
};

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function showNotification(message, type = 'info') {
    Swal.fire({
        toast: true, position: 'top-end', showConfirmButton: false,
        timer: 4000, timerProgressBar: true, icon: type, title: message,
        didOpen: toast => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
}

// ── ACCESS MODAL ──────────────────────────────────────────────
function openAccessModal() {
    document.getElementById('accessModal').classList.add('show');
    fetch(ROUTES.accessModal)
        .then(r => r.text())
        .then(html => { document.getElementById('modal-body').innerHTML = html; initializeModalScripts(); })
        .catch(() => {
            document.getElementById('modal-body').innerHTML = `
                <div class="empty-state"><i class="fas fa-exclamation-circle"></i>
                <h3>Error Loading Students</h3>
                <button onclick="openAccessModal()" class="btn-sm btn-sm-primary" style="margin-top:1rem;">
                    <i class="fas fa-redo"></i> Retry</button></div>`;
        });
}

function closeAccessModal() {
    const modal = document.getElementById('accessModal');
    modal.classList.remove('show');
    setTimeout(() => {
        if (!modal.classList.contains('show')) {
            document.getElementById('modal-body').innerHTML = `
                <div class="loading-container">
                    <i class="fas fa-spinner fa-spin loading-spinner"></i><p>Loading students...</p>
                </div>`;
        }
    }, 300);
}

window.addEventListener('click', e => {
    if (e.target === document.getElementById('accessModal')) closeAccessModal();
});

function initializeModalScripts() {
    document.querySelectorAll('.enrollment-toggle input').forEach(toggle => {
        const fresh = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(fresh, toggle);
        fresh.addEventListener('change', function(e) {
            e.stopPropagation();
            const wasChecked = this.checked, self = this;
            self.disabled = true;
            fetch(this.dataset.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ student_id: self.value })
            })
            .then(r => { if (!r.ok) return r.json().then(d => { throw new Error(d.message || 'Error'); }); return r.json(); })
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    const badge = self.closest('tr')?.querySelector('.status-badge');
                    if (badge) {
                        badge.className = `status-badge ${data.enrolled ? 'status-enrolled' : 'status-not-enrolled'}`;
                        badge.innerHTML = data.enrolled ? '<i class="fas fa-check-circle"></i> Enrolled' : '<i class="fas fa-times-circle"></i> Not Enrolled';
                    }
                } else { self.checked = !wasChecked; showNotification(data.message || 'Failed.', 'error'); }
            })
            .catch(err => { self.checked = !wasChecked; showNotification(err.message, 'error'); })
            .finally(() => { self.disabled = false; });
        });
    });

    const collegeFilter = document.getElementById('college-filter');
    const programFilter = document.getElementById('program-filter');
    if (collegeFilter) {
        const fresh = collegeFilter.cloneNode(true);
        collegeFilter.parentNode.replaceChild(fresh, collegeFilter);
        fresh.addEventListener('change', function() {
            programFilter.innerHTML = '<option value="">All Programs</option>';
            programFilter.disabled = !this.value;
            if (!this.value) return;
            fetch(`/admin/colleges/${this.value}/programs`).then(r => r.json()).then(list => list.forEach(p => {
                const o = document.createElement('option'); o.value = p.id; o.textContent = p.program_name || p.name;
                programFilter.appendChild(o);
            }));
        });
    }

    const si = document.getElementById('student-search');
    if (si) {
        const fresh = si.cloneNode(true);
        si.parentNode.replaceChild(fresh, si);
        fresh.addEventListener('input', function() {
            const t = this.value.toLowerCase();
            document.querySelectorAll('.student-table tbody tr').forEach(row => {
                const n = row.querySelector('.student-name')?.textContent.toLowerCase() ?? '';
                const em = row.querySelector('.student-sub')?.textContent.toLowerCase() ?? '';
                const id = row.querySelector('.student-id')?.textContent.toLowerCase() ?? '';
                row.style.display = (n.includes(t) || em.includes(t) || id.includes(t)) ? '' : 'none';
            });
        });
    }

    function filterTable() {
        const cid = document.getElementById('college-filter')?.value;
        const pid = document.getElementById('program-filter')?.value;
        document.querySelectorAll('.student-table tbody tr').forEach(row => {
            let show = true;
            if (cid && row.dataset.collegeId != cid) show = false;
            if (show && pid && row.dataset.programId != pid) show = false;
            row.style.display = show ? '' : 'none';
        });
    }
    document.getElementById('college-filter')?.addEventListener('change', filterTable);
    document.getElementById('program-filter')?.addEventListener('change', filterTable);
}

// ── TOPIC PUBLISH / UNPUBLISH ─────────────────────────────────
function toggleTopicPublish(topicId, publish, btn) {
    const encryptedTopicId = btn.dataset.encryptedId;
    if (!encryptedTopicId) { showNotification('Cannot toggle — please refresh the page.', 'warning'); return; }

    Swal.fire({
        title: publish ? 'Publish Topic?' : 'Unpublish Topic?',
        text: publish ? 'This topic will become visible to enrolled students.' : 'This topic will be hidden from students.',
        icon: publish ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: publish ? '#48bb78' : '#f56565',
        cancelButtonColor: '#6b7280',
        confirmButtonText: publish ? 'Yes, Publish' : 'Yes, Unpublish',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (!result.isConfirmed) return;
        btn.disabled = true;
        const saved = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(`${ROUTES.publishTopic}/${encryptedTopicId}/publish`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: '_method=PATCH'
        })
        .then(r => {
            const ct = r.headers.get('content-type') ?? '';
            if (ct.includes('application/json')) return r.json();
            if (r.ok || r.redirected) return { success: true };
            throw new Error(`HTTP ${r.status}`);
        })
        .then(data => {
            if (data.success !== false) {
                updateTopicCardDOM(topicId, publish, btn);
                showNotification(`Topic ${publish ? 'published' : 'unpublished'} successfully!`, 'success');
            } else {
                btn.innerHTML = saved;
                showNotification(data.message || 'Failed to update topic.', 'error');
            }
        })
        .catch(err => { btn.innerHTML = saved; showNotification('An error occurred: ' + err.message, 'error'); })
        .finally(() => { btn.disabled = false; });
    });
}

function updateTopicCardDOM(topicId, isPublished, btn) {
    const card = document.getElementById(`topic-${topicId}`);
    if (!card) return;

    card.classList.toggle('topic-card--draft', !isPublished);

    const titleEl = card.querySelector('.topic-title');
    titleEl?.querySelector('.status-badge')?.remove();
    if (titleEl) {
        const badge = document.createElement('span');
        badge.className = `status-badge ${isPublished ? 'status-published' : 'status-draft'}`;
        badge.style.cssText = 'font-size:0.65rem; margin-left:0.5rem;';
        badge.innerHTML = isPublished ? '<i class="fas fa-check-circle"></i> Published' : '<i class="fas fa-clock"></i> Draft';
        titleEl.appendChild(badge);
    }

    const subEl = card.querySelector('.topic-header > div > div:last-child');
    if (subEl) {
        subEl.querySelectorAll('span[style*="color:#ed8936"]').forEach(n => n.remove());
        subEl.innerHTML = subEl.innerHTML.replace(/\s*&bull;\s*$/, '');
        if (!isPublished) subEl.innerHTML += ' &bull; <span style="color:#ed8936;">Hidden from students</span>';
    }

    const encId = btn.dataset.encryptedId;
    if (isPublished) {
        btn.className = 'action-btn-small action-btn-warning';
        btn.setAttribute('onclick', `toggleTopicPublish(${topicId}, false, this)`);
        btn.title = 'Unpublish this topic';
        btn.innerHTML = '<i class="fas fa-eye-slash"></i> Unpublish';
    } else {
        btn.className = 'action-btn-small action-btn-success';
        btn.setAttribute('onclick', `toggleTopicPublish(${topicId}, true, this)`);
        btn.title = 'Publish this topic';
        btn.innerHTML = '<i class="fas fa-eye"></i> Publish';
    }
    btn.dataset.encryptedId = encId;
}

// ── ADD / REMOVE TOPICS ───────────────────────────────────────
let selectedTopics = [];
let allAvailableTopics = [];
let currentCourseTopics = {!! $course->topics->pluck('id')->toJson() !!};

function openAddTopicModal() {
    document.getElementById('addTopicModal').classList.add('active');
    loadAvailableTopics();
}
function closeAddTopicModal() {
    document.getElementById('addTopicModal').classList.remove('active');
    selectedTopics = [];
}

function loadAvailableTopics() {
    document.getElementById('availableTopicsList').innerHTML = `
        <div class="loading-container"><i class="fas fa-spinner fa-spin loading-spinner"></i><p>Loading topics...</p></div>`;
    fetch(ROUTES.availableTopics, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
    })
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}: ${r.statusText}`); return r.json(); })
    .then(data => {
        if (data.error) throw new Error(data.message || data.error);
        allAvailableTopics = Array.isArray(data) ? data : [];
        renderAvailableTopics(allAvailableTopics);
    })
    .catch(err => {
        document.getElementById('availableTopicsList').innerHTML = `
            <div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Error Loading Topics</h3>
            <p>${err.message}</p>
            <button onclick="loadAvailableTopics()" class="btn-sm btn-sm-primary" style="margin-top:1rem;">
                <i class="fas fa-redo"></i> Retry</button></div>`;
    });
}

function renderAvailableTopics(topics) {
    const container = document.getElementById('availableTopicsList');
    if (!Array.isArray(topics) || topics.length === 0) {
        container.innerHTML = `
            <div class="empty-state"><i class="fas fa-folder-open"></i><h3>No Topics Available</h3>
            <p>All topics are already added to this course.</p>
            <a href="{{ route('admin.topics.create') }}" class="btn-sm btn-sm-primary" style="margin-top:1rem;">
                <i class="fas fa-plus"></i> Create New Topic</a></div>`;
        return;
    }
    container.innerHTML = topics.map(topic => {
        const desc = topic.description || topic.content || 'No description provided.';
        const truncated = desc.length > 120 ? desc.substring(0, 120) + '...' : desc;
        const isSelected = selectedTopics.includes(topic.id);
        const status = topic.is_published
            ? '<span class="status-badge status-published" style="font-size:0.7rem;"><i class="fas fa-check-circle"></i> Published</span>'
            : '<span class="status-badge status-draft" style="font-size:0.7rem;"><i class="fas fa-clock"></i> Draft</span>';
        return `
            <div class="topic-item ${isSelected ? 'selected' : ''}" onclick="toggleTopicSelection(${topic.id})">
                <div class="topic-item-header">
                    <div class="topic-item-title">${topic.title || 'Untitled Topic'} ${status}</div>
                    <button class="add-btn" onclick="event.stopPropagation(); addSingleTopic(${topic.id})">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div class="topic-item-description">${truncated}</div>
            </div>`;
    }).join('');
}

function toggleTopicSelection(topicId) {
    selectedTopics = selectedTopics.includes(topicId)
        ? selectedTopics.filter(id => id !== topicId)
        : [...selectedTopics, topicId];
    renderAvailableTopics(allAvailableTopics);
    const s = document.getElementById('modalTopicSearch')?.value;
    if (s) searchTopics();
}

function addSingleTopic(topicId) {
    if (currentCourseTopics.includes(topicId)) { showNotification('Topic already added.', 'warning'); return; }
    fetch(ROUTES.addTopic, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ topic_id: topicId })
    })
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(data => {
        if (data.success) {
            currentCourseTopics.push(topicId);
            allAvailableTopics = allAvailableTopics.filter(t => t.id !== topicId);
            renderAvailableTopics(allAvailableTopics);
            addTopicToDisplay(data.topic);
            showNotification('Topic added successfully!', 'success');
        } else { showNotification(data.message || 'Failed.', 'error'); }
    })
    .catch(err => showNotification('An error occurred: ' + err.message, 'error'));
}

function addSelectedTopics() {
    if (!selectedTopics.length) { showNotification('Please select at least one topic.', 'warning'); return; }
    fetch(ROUTES.addTopics, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ topic_ids: selectedTopics })
    })
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(data => {
        if (data.success) {
            currentCourseTopics = [...currentCourseTopics, ...selectedTopics];
            allAvailableTopics  = allAvailableTopics.filter(t => !selectedTopics.includes(t.id));
            renderAvailableTopics(allAvailableTopics);
            (data.topics || []).forEach(t => addTopicToDisplay(t));
            selectedTopics = [];
            if (allAvailableTopics.length === 0) closeAddTopicModal();
            showNotification(data.message || 'Topics added successfully!', 'success');
        } else { showNotification(data.message || 'Failed.', 'error'); }
    })
    .catch(err => showNotification('An error occurred: ' + err.message, 'error'));
}

function addTopicToDisplay(topic) {
    let topicsList = document.getElementById('topicsList');
    if (!topicsList) return;

    if (topicsList.classList.contains('empty-state')) {
        const newEl = document.createElement('div');
        newEl.className = 'topics-section'; newEl.id = 'topicsList';
        topicsList.replaceWith(newEl); topicsList = newEl;
    }
    topicsList.querySelector('.empty-state')?.remove();

    const isPublished = !!topic.is_published;
    const encId = topic.encrypted_id || '';
    const statusBadge = isPublished
        ? '<span class="status-badge status-published" style="font-size:0.65rem; margin-left:0.5rem;"><i class="fas fa-check-circle"></i> Published</span>'
        : '<span class="status-badge status-draft" style="font-size:0.65rem; margin-left:0.5rem;"><i class="fas fa-clock"></i> Draft</span>';
    const hiddenNote = isPublished ? '' : ' &bull; <span style="color:#ed8936;">Hidden from students</span>';
    const publishBtn = isPublished
        ? `<button class="action-btn-small action-btn-warning" data-encrypted-id="${encId}" onclick="toggleTopicPublish(${topic.id}, false, this)" title="Unpublish"><i class="fas fa-eye-slash"></i> Unpublish</button>`
        : `<button class="action-btn-small action-btn-success" data-encrypted-id="${encId}" onclick="toggleTopicPublish(${topic.id}, true, this)" title="Publish"><i class="fas fa-eye"></i> Publish</button>`;

    const el = document.createElement('div');
    el.className = `topic-card${isPublished ? '' : ' topic-card--draft'}`;
    el.id = `topic-${topic.id}`;
    el.innerHTML = `
        <div class="topic-header">
            <div style="flex:1; min-width:0;">
                <div class="topic-title">${topic.title || 'Untitled Topic'}${statusBadge}</div>
                <div style="font-size:0.6875rem; color:#a0aec0; margin-top:0.2rem;">
                    <i class="fas fa-clock"></i> Just added${hiddenNote}
                </div>
            </div>
            <div style="display:flex; gap:0.5rem; align-items:center; flex-shrink:0;">
                ${publishBtn}
                <button class="action-btn-small" onclick="removeTopic(${topic.id}, '${(topic.title || '').replace(/'/g, "\\'")}')">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
        </div>
        <div class="topic-content">
            <div class="topic-description">${topic.description || topic.content || 'No description provided.'}</div>
        </div>`;
    topicsList.appendChild(el);
}

function removeTopic(topicId, topicTitle) {
    Swal.fire({
        title: 'Remove Topic?', text: `Remove "${topicTitle}" from this course?`,
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#f56565', cancelButtonColor: '#a0aec0',
        confirmButtonText: 'Yes, Remove', cancelButtonText: 'Cancel'
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(ROUTES.removeTopic, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ topic_id: topicId })
        })
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => {
            if (data.success) {
                currentCourseTopics = currentCourseTopics.filter(id => id !== topicId);
                document.getElementById(`topic-${topicId}`)?.remove();
                if (data.topic) allAvailableTopics.push(data.topic);
                const tl = document.getElementById('topicsList');
                if (tl && tl.children.length === 0) {
                    tl.innerHTML = `<div class="empty-state"><i class="fas fa-folder-open"></i>
                        <h3>No Topics Yet</h3><p>Start by adding topics to this course</p>
                        <button onclick="openAddTopicModal()" class="btn-sm btn-sm-primary" style="margin-top:1rem;">
                            <i class="fas fa-plus"></i> Add First Topic</button></div>`;
                }
                showNotification(data.message || 'Topic removed!', 'success');
            } else { showNotification(data.message || 'Failed to remove.', 'error'); }
        })
        .catch(err => showNotification('An error occurred: ' + err.message, 'error'));
    });
}

function searchTopics() {
    const t = document.getElementById('modalTopicSearch')?.value.toLowerCase() || '';
    renderAvailableTopics(allAvailableTopics.filter(topic =>
        (topic.title || '').toLowerCase().includes(t) ||
        (topic.description || '').toLowerCase().includes(t) ||
        (topic.content || '').toLowerCase().includes(t)
    ));
}

const topicSearchEl = document.getElementById('topicSearch');
if (topicSearchEl) {
    topicSearchEl.addEventListener('input', function() {
        const t = this.value.toLowerCase();
        document.querySelectorAll('.topic-card').forEach(card => {
            const title = card.querySelector('.topic-title')?.textContent.toLowerCase() || '';
            const desc  = card.querySelector('.topic-description')?.textContent.toLowerCase() || '';
            card.style.display = (title.includes(t) || desc.includes(t)) ? '' : 'none';
        });
    });
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        if (document.getElementById('addTopicModal')?.classList.contains('active')) closeAddTopicModal();
        if (document.getElementById('accessModal')?.classList.contains('show')) closeAccessModal();
    }
});
document.getElementById('addTopicModal')?.addEventListener('click', e => {
    if (e.target === document.getElementById('addTopicModal')) closeAddTopicModal();
});

// ── COURSE ACTIONS ────────────────────────────────────────────
function confirmPublish() {
    Swal.fire({ title: 'Publish Course?', text: 'This course will be visible to enrolled students.',
        icon: 'question', showCancelButton: true, confirmButtonColor: '#48bb78', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Publish', cancelButtonText: 'Cancel'
    }).then(r => { if (r.isConfirmed) document.getElementById('publish-form').submit(); });
}
function confirmUnpublish() {
    Swal.fire({ title: 'Unpublish Course?', text: 'This course will be hidden from students.',
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#f56565', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Unpublish', cancelButtonText: 'Cancel'
    }).then(r => { if (r.isConfirmed) document.getElementById('publish-form').submit(); });
}
function confirmDelete() {
    Swal.fire({ title: 'Delete Course?', text: 'This cannot be undone.',
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#f56565', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete', cancelButtonText: 'Cancel', reverseButtons: true
    }).then(r => { if (r.isConfirmed) document.getElementById('delete-form').submit(); });
}

@if(session('success')) showNotification('{{ session('success') }}', 'success'); @endif
@if(session('error'))   showNotification('{{ session('error') }}',   'error');   @endif
@if(session('warning')) showNotification('{{ session('warning') }}', 'warning'); @endif
</script>
@endpush