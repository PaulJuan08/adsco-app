@extends('layouts.admin')

@section('title', $assignment->title . ' - Assignment Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-show.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    <div class="breadcrumb">
        <a href="{{ route('admin.todo.index') }}">To-Do</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.todo.index', ['type' => 'assignment']) }}">Assignments</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">{{ Str::limit($assignment->title, 30) }}</span>
    </div>

    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon"><i class="fas fa-file-alt"></i></div>
                <div>
                    <h1 class="card-title">{{ $assignment->title }}</h1>
                    <span class="card-status-badge {{ $assignment->is_published ? 'published' : 'draft' }}">
                        <i class="fas {{ $assignment->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $assignment->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </div>
            <div class="top-actions">
                <a href="{{ route('admin.assignments.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif

            @php
                $submissions    = $assignment->submissions()->with(['student', 'gradedBy'])->latest()->get();
                $totalSubs      = $submissions->count();
                $gradedCount    = $submissions->where('status', 'graded')->count();
                $pendingCount   = $submissions->whereIn('status', ['submitted'])->count();
                $lateCount      = $submissions->where('status', 'late')->count();
                $allowedCount   = $assignment->allowed_students_count ?? 0;
            @endphp

            <div class="two-column-layout">
                {{-- Left Column --}}
                <div class="form-column">

                    {{-- Description --}}
                    <div class="detail-section">
                        <h3 class="detail-section-title"><i class="fas fa-align-left"></i> Description</h3>
                        <div class="description-box rich-text">{!! $assignment->description ?? 'No description provided.' !!}</div>
                    </div>

                    {{-- Instructions --}}
                    @if($assignment->instructions)
                    <div class="detail-section">
                        <h3 class="detail-section-title"><i class="fas fa-list-ol"></i> Instructions</h3>
                        <div class="instructions-box rich-text">{!! $assignment->instructions !!}</div>
                    </div>
                    @endif

                    {{-- Materials --}}
                    @if($assignment->attachment)
                    <div class="detail-section">
                        <h3 class="detail-section-title"><i class="fas fa-paperclip"></i> Assignment Materials</h3>
                        <div style="display:flex;align-items:center;gap:1rem;padding:1rem;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
                            <i class="fas fa-file-pdf" style="font-size:2rem;color:#f56565;"></i>
                            <div style="flex:1;">
                                <div style="font-weight:600;">{{ basename($assignment->attachment) }}</div>
                                <div style="font-size:0.75rem;color:#718096;">Assignment file</div>
                            </div>
                            <a href="{{ Storage::url($assignment->attachment) }}" target="_blank" class="btn-sm btn-sm-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Submissions Table --}}
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-file-upload"></i> Student Submissions
                            <div style="margin-left:auto;display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                                <span style="font-size:0.75rem;color:#718096;">{{ $totalSubs }} total</span>
                                @if($pendingCount > 0)
                                <span style="font-size:0.72rem;padding:0.15rem 0.5rem;background:#fffbeb;color:#d97706;border-radius:20px;border:1px solid #fcd34d;">{{ $pendingCount }} pending</span>
                                @endif
                                @if($lateCount > 0)
                                <span style="font-size:0.72rem;padding:0.15rem 0.5rem;background:#fff5f5;color:#c53030;border-radius:20px;border:1px solid #fc8181;">{{ $lateCount }} late</span>
                                @endif
                                @if($gradedCount > 0)
                                <span style="font-size:0.72rem;padding:0.15rem 0.5rem;background:#f0fff4;color:#276749;border-radius:20px;border:1px solid #9ae6b4;">{{ $gradedCount }} graded</span>
                                @endif
                            </div>
                        </h3>

                        @if($submissions->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-file-upload"></i>
                            <h3>No submissions yet</h3>
                            <p>Students haven't submitted this assignment yet.</p>
                        </div>
                        @else

                        {{-- Filter Bar --}}
                        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;flex-wrap:wrap;">
                            <div style="position:relative;flex:1;min-width:180px;">
                                <i class="fas fa-search" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:0.8rem;"></i>
                                <input type="text" id="sub-search" placeholder="Search by student name or ID..."
                                    style="width:100%;padding:0.55rem 0.75rem 0.55rem 2.25rem;border:1px solid #e2e8f0;border-radius:8px;font-size:0.85rem;box-sizing:border-box;outline:none;">
                            </div>
                            <select id="sub-filter" style="padding:0.55rem 0.75rem;border:1px solid #e2e8f0;border-radius:8px;font-size:0.85rem;color:#4a5568;background:white;cursor:pointer;outline:none;">
                                <option value="all">All Status</option>
                                <option value="submitted">Pending</option>
                                <option value="late">Late</option>
                                <option value="graded">Graded</option>
                            </select>
                        </div>

                        <div style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                            <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                                <thead>
                                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                                        <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#718096;text-transform:uppercase;">Student</th>
                                        <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#718096;text-transform:uppercase;">Submitted</th>
                                        <th style="padding:0.75rem 1rem;text-align:center;font-size:0.75rem;font-weight:600;color:#718096;text-transform:uppercase;">Status</th>
                                        <th style="padding:0.75rem 1rem;text-align:center;font-size:0.75rem;font-weight:600;color:#718096;text-transform:uppercase;">Score</th>
                                    </tr>
                                </thead>
                                <tbody id="submissions-tbody">
                                    @foreach($submissions as $sub)
                                    <tr class="sub-row"
                                        data-status="{{ $sub->status }}"
                                        data-name="{{ strtolower($sub->student?->full_name ?? '') }}"
                                        data-sid="{{ strtolower($sub->student?->student_id ?? '') }}"
                                        onclick="openGradeModal({{ $sub->id }})"
                                        style="border-bottom:1px solid #f0f4f8;cursor:pointer;transition:background 0.15s;">
                                        <td style="padding:0.875rem 1rem;">
                                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-size:0.75rem;font-weight:700;flex-shrink:0;">
                                                    {{ strtoupper(substr($sub->student?->f_name ?? '?', 0, 1) . substr($sub->student?->l_name ?? '', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight:600;color:#2d3748;">
                                                        {{ $sub->student?->full_name ?? '[Deleted Student]' }}
                                                    </div>
                                                    @if($sub->student?->student_id)
                                                    <div style="font-size:0.72rem;color:#a0aec0;">{{ $sub->student->student_id }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding:0.875rem 1rem;color:#718096;font-size:0.82rem;">
                                            {{ $sub->submitted_at->format('M d, Y') }}
                                            <span style="display:block;font-size:0.7rem;color:#a0aec0;">{{ $sub->submitted_at->diffForHumans() }}</span>
                                        </td>
                                        <td style="padding:0.875rem 1rem;text-align:center;">
                                            @if($sub->status == 'graded')
                                            <span style="padding:0.25rem 0.75rem;background:#f0fff4;color:#276749;border-radius:20px;font-size:0.75rem;font-weight:600;border:1px solid #9ae6b4;">
                                                <i class="fas fa-check-circle"></i> Graded
                                            </span>
                                            @elseif($sub->status == 'late')
                                            <span style="padding:0.25rem 0.75rem;background:#fff5f5;color:#c53030;border-radius:20px;font-size:0.75rem;font-weight:600;border:1px solid #fc8181;">
                                                <i class="fas fa-exclamation-circle"></i> Late
                                            </span>
                                            @else
                                            <span style="padding:0.25rem 0.75rem;background:#fffbeb;color:#d97706;border-radius:20px;font-size:0.75rem;font-weight:600;border:1px solid #fcd34d;">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                            @endif
                                        </td>
                                        <td style="padding:0.875rem 1rem;text-align:center;font-weight:700;color:{{ $sub->status == 'graded' ? '#276749' : '#a0aec0' }};">
                                            {{ $sub->status == 'graded' ? $sub->score . '/' . $assignment->points : '—' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="sub-empty" style="display:none;padding:2rem;text-align:center;color:#a0aec0;font-size:0.875rem;">
                                <i class="fas fa-search" style="font-size:1.5rem;margin-bottom:0.5rem;display:block;"></i>
                                No submissions match your filter.
                            </div>
                        </div>
                        @endif
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="sidebar-column">
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-info-circle"></i> Assignment Details</h3>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="val" style="color:{{ $assignment->is_published ? '#48bb78' : '#ed8936' }}">{{ $assignment->is_published ? 'Published' : 'Draft' }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-star"></i> Points</span>
                            <span class="val highlight">{{ $assignment->points }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-clock"></i> Duration</span>
                            <span class="val">{{ $assignment->duration ?? 60 }} min</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-trophy"></i> Passing Score</span>
                            <span class="val">{{ $assignment->passing_score ?? 70 }}%</span>
                        </div>
                        @if($assignment->course)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-book"></i> Course</span>
                            <span class="val">{{ Str::limit($assignment->course->title, 20) }}</span>
                        </div>
                        @endif
                        @if($assignment->topic)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-tag"></i> Topic</span>
                            <span class="val">{{ Str::limit($assignment->topic->title, 20) }}</span>
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
                            <span class="val highlight">{{ $allowedCount }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-file-upload"></i> Submissions</span>
                            <span class="val">{{ $totalSubs }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Graded</span>
                            <span class="val">{{ $gradedCount }}</span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user-circle"></i> Created By</span>
                            <span class="val">
                                @if($assignment->creator)
                                    {{ $assignment->creator->f_name }} {{ $assignment->creator->l_name }}
                                    <span style="display:block;font-size:0.7rem;color:#718096;">({{ $assignment->creator->role == 1 ? 'Admin' : 'Teacher' }})</span>
                                @else
                                    <span style="color:#a0aec0;">System</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Created At</span>
                            <span class="val">{{ $assignment->created_at->format('M d, Y') }}<span style="display:block;font-size:0.7rem;color:#718096;">{{ $assignment->created_at->diffForHumans() }}</span></span>
                        </div>
                        @if($assignment->updater)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user-edit"></i> Last Updated By</span>
                            <span class="val">{{ $assignment->updater->f_name }} {{ $assignment->updater->l_name }}<span style="display:block;font-size:0.7rem;color:#718096;">{{ $assignment->updated_at->format('M d, Y') }} &middot; {{ $assignment->updated_at->diffForHumans() }}</span></span>
                        </div>
                        @elseif($assignment->updated_at != $assignment->created_at)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-clock"></i> Last Updated</span>
                            <span class="val">{{ $assignment->updated_at->format('M d, Y') }}<span style="display:block;font-size:0.7rem;color:#718096;">{{ $assignment->updated_at->diffForHumans() }}</span></span>
                        </div>
                        @endif
                    </div>

                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                        <button onclick="openAccessModal()" class="quick-action-link" style="width:100%;border:none;cursor:pointer;background:var(--gray-100);text-align:left;">
                            <i class="fas fa-user-plus"></i><span>Grant Student Access</span>
                        </button>
                        <button onclick="openCrudModal('{{ route('admin.assignments.edit', $encryptedId) }}', 'Edit Assignment')" class="quick-action-link" style="border:none;cursor:pointer;width:100%;background:transparent;">
                            <i class="fas fa-edit"></i><span>Edit Assignment Details</span>
                        </button>
                        <a href="{{ route('admin.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="quick-action-link">
                            <i class="fas fa-chart-bar"></i><span>View Progress Reports</span>
                        </a>
                        <a href="{{ route('admin.todo.index', ['type' => 'assignment']) }}" class="quick-action-link">
                            <i class="fas fa-list"></i><span>All Assignments</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Submission Grade Modal --}}
<div id="gradeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:1000;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:16px;width:80%;max-width:900px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        {{-- Modal Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div id="gm-avatar" style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-size:0.875rem;font-weight:700;"></div>
                <div>
                    <div id="gm-name" style="font-size:1rem;font-weight:700;color:#2d3748;"></div>
                    <div id="gm-meta" style="font-size:0.75rem;color:#718096;"></div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <span id="gm-status-badge" style="font-size:0.75rem;padding:0.25rem 0.75rem;border-radius:20px;font-weight:600;"></span>
                <button onclick="closeGradeModal()" style="background:none;border:none;cursor:pointer;color:#718096;font-size:1.25rem;padding:0.25rem;"><i class="fas fa-times"></i></button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div style="display:flex;flex:1;overflow:hidden;">
            {{-- Left: Submission Content --}}
            <div style="flex:1;overflow-y:auto;padding:1.5rem;border-right:1px solid #e2e8f0;">
                <div id="gm-answer-section">
                    <div style="font-size:0.75rem;font-weight:700;color:#718096;text-transform:uppercase;margin-bottom:0.5rem;"><i class="fas fa-pencil-alt"></i> Student Answer</div>
                    <div id="gm-answer" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:1rem;font-size:0.9rem;line-height:1.7;min-height:80px;white-space:pre-wrap;"></div>
                </div>
                <div id="gm-attachment-section" style="margin-top:1.25rem;display:none;">
                    <div style="font-size:0.75rem;font-weight:700;color:#718096;text-transform:uppercase;margin-bottom:0.5rem;"><i class="fas fa-paperclip"></i> Attached File</div>
                    <a id="gm-attachment-link" href="#" target="_blank"
                       style="display:inline-flex;align-items:center;gap:0.75rem;padding:0.75rem 1.25rem;background:#eff6ff;color:#3b82f6;border-radius:10px;font-size:0.875rem;font-weight:600;text-decoration:none;border:1px solid #dbeafe;">
                        <i class="fas fa-file"></i>
                        <span id="gm-attachment-name"></span>
                        <i class="fas fa-external-link-alt" style="font-size:0.7rem;opacity:0.7;"></i>
                    </a>
                </div>
                {{-- Existing grade display --}}
                <div id="gm-grade-display" style="display:none;margin-top:1.25rem;padding:1rem;background:#f0fff4;border:1px solid #9ae6b4;border-radius:10px;">
                    <div style="font-size:0.75rem;font-weight:700;color:#718096;text-transform:uppercase;margin-bottom:0.5rem;"><i class="fas fa-star" style="color:#f59e0b;"></i> Current Grade</div>
                    <div style="display:flex;align-items:center;gap:1.5rem;">
                        <div style="font-size:2rem;font-weight:800;color:#276749;" id="gm-score-display"></div>
                        <div id="gm-feedback-display" style="flex:1;font-size:0.875rem;color:#4a5568;border-left:2px solid #9ae6b4;padding-left:1rem;"></div>
                    </div>
                    <div id="gm-graded-by" style="font-size:0.72rem;color:#718096;margin-top:0.5rem;"></div>
                </div>
            </div>

            {{-- Right: Grade Form --}}
            <div style="width:280px;flex-shrink:0;padding:1.5rem;display:flex;flex-direction:column;gap:1rem;overflow-y:auto;">
                <div style="font-size:0.8rem;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:0.05em;"><i class="fas fa-star" style="color:#f59e0b;"></i> Grade Submission</div>

                <form id="gradeForm" method="POST">
                    @csrf
                    <div style="margin-bottom:1rem;">
                        <label style="font-size:0.78rem;font-weight:600;color:#4a5568;display:block;margin-bottom:0.4rem;">Score <span style="color:#a0aec0;">/ {{ $assignment->points }}</span></label>
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="number" name="score" id="gm-score-input"
                                min="0" max="{{ $assignment->points }}" required
                                style="width:90px;padding:0.6rem 0.75rem;border:2px solid #e2e8f0;border-radius:8px;font-size:1rem;font-weight:700;text-align:center;outline:none;"
                                onfocus="this.style.borderColor='var(--primary)'"
                                onblur="this.style.borderColor='#e2e8f0'"
                                placeholder="0">
                            <span style="color:#718096;font-size:0.875rem;">/ {{ $assignment->points }}</span>
                        </div>
                    </div>
                    <div style="margin-bottom:1.25rem;">
                        <label style="font-size:0.78rem;font-weight:600;color:#4a5568;display:block;margin-bottom:0.4rem;">Feedback <span style="color:#a0aec0;">(Optional)</span></label>
                        <textarea name="feedback" id="gm-feedback-input" rows="5"
                            style="width:100%;padding:0.75rem;border:2px solid #e2e8f0;border-radius:8px;font-size:0.875rem;line-height:1.5;resize:vertical;box-sizing:border-box;outline:none;"
                            onfocus="this.style.borderColor='var(--primary)'"
                            onblur="this.style.borderColor='#e2e8f0'"
                            placeholder="Write feedback for the student..."></textarea>
                    </div>
                    <button type="submit" class="btn-sm btn-sm-primary" style="width:100%;padding:0.75rem;font-size:0.9rem;">
                        <i class="fas fa-check"></i> <span id="gm-submit-label">Submit Grade</span>
                    </button>
                </form>

                <div style="border-top:1px solid #e2e8f0;padding-top:1rem;">
                    <div style="font-size:0.72rem;color:#a0aec0;line-height:1.6;">
                        <i class="fas fa-info-circle"></i> Passing score: <strong>{{ $assignment->passing_score ?? 70 }}%</strong><br>
                        Min to pass: <strong>{{ ceil(($assignment->points * ($assignment->passing_score ?? 70)) / 100) }}/{{ $assignment->points }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Access Modal --}}
<div id="accessModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Manage Student Access - {{ $assignment->title }}</h3>
            <button class="modal-close" onclick="closeAccessModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="modal-body">
            <div style="text-align:center;padding:2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size:2rem;color:var(--primary);"></i>
                <p style="margin-top:1rem;color:var(--gray-600);">Loading students...</p>
            </div>
        </div>
    </div>
</div>

<form id="publish-form" method="POST" action="{{ route('admin.assignments.publish', $encryptedId) }}" style="display:none;">@csrf @method('PATCH')</form>
<form id="grant-form" method="POST" style="display:none;">@csrf<input type="hidden" name="student_ids" id="grant-student-ids"></form>
<form id="revoke-form" method="POST" style="display:none;">@csrf<input type="hidden" name="student_ids" id="revoke-student-ids"></form>

@endsection

@php
$submissionsJson = $submissions->map(function($s) {
    return [
        'id'              => $s->id,
        'student_name'    => $s->student ? $s->student->full_name : '[Deleted Student]',
        'student_id_str'  => $s->student ? ($s->student->student_id ?? '') : '',
        'initials'        => strtoupper(substr($s->student ? ($s->student->f_name ?? '?') : '?', 0, 1) . substr($s->student ? ($s->student->l_name ?? '') : '', 0, 1)),
        'submitted_at'    => $s->submitted_at->format('M d, Y h:i A'),
        'submitted_human' => $s->submitted_at->diffForHumans(),
        'status'          => $s->status,
        'answer_text'     => $s->answer_text,
        'attachment_path' => $s->attachment_path ? Storage::url($s->attachment_path) : null,
        'attachment_name' => $s->attachment_path ? basename($s->attachment_path) : null,
        'score'           => $s->score,
        'feedback'        => $s->feedback,
        'graded_by'       => $s->gradedBy ? ($s->gradedBy->f_name . ' ' . $s->gradedBy->l_name) : null,
        'graded_at'       => $s->graded_at ? $s->graded_at->format('M d, Y') : null,
        'grade_url'       => route('admin.todo.submission.grade', $s->id),
    ];
})->values()->toArray();
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const submissions = @json($submissionsJson);

function openGradeModal(id) {
    const sub = submissions.find(s => s.id === id);
    if (!sub) return;

    // Avatar & header
    document.getElementById('gm-avatar').textContent = sub.initials;
    document.getElementById('gm-name').textContent = sub.student_name;
    document.getElementById('gm-meta').textContent = (sub.student_id_str ? sub.student_id_str + ' · ' : '') + sub.submitted_at + (sub.submitted_human ? ' (' + sub.submitted_human + ')' : '');

    // Status badge
    const badge = document.getElementById('gm-status-badge');
    if (sub.status === 'graded') {
        badge.textContent = '✓ Graded'; badge.style.cssText = 'background:#f0fff4;color:#276749;border:1px solid #9ae6b4;padding:0.25rem 0.75rem;border-radius:20px;font-size:0.75rem;font-weight:600;';
    } else if (sub.status === 'late') {
        badge.textContent = '⚠ Late'; badge.style.cssText = 'background:#fff5f5;color:#c53030;border:1px solid #fc8181;padding:0.25rem 0.75rem;border-radius:20px;font-size:0.75rem;font-weight:600;';
    } else {
        badge.textContent = '⏳ Pending'; badge.style.cssText = 'background:#fffbeb;color:#d97706;border:1px solid #fcd34d;padding:0.25rem 0.75rem;border-radius:20px;font-size:0.75rem;font-weight:600;';
    }

    // Answer
    const answerEl = document.getElementById('gm-answer');
    answerEl.textContent = sub.answer_text || 'No written answer provided.';
    if (!sub.answer_text) answerEl.style.color = '#a0aec0';
    else answerEl.style.color = '#2d3748';

    // Attachment
    const attSection = document.getElementById('gm-attachment-section');
    if (sub.attachment_path) {
        document.getElementById('gm-attachment-link').href = sub.attachment_path;
        document.getElementById('gm-attachment-name').textContent = sub.attachment_name;
        attSection.style.display = 'block';
    } else {
        attSection.style.display = 'none';
    }

    // Grade display
    const gradeDisplay = document.getElementById('gm-grade-display');
    if (sub.status === 'graded' && sub.score !== null) {
        document.getElementById('gm-score-display').textContent = sub.score + '/{{ $assignment->points }}';
        const fbEl = document.getElementById('gm-feedback-display');
        fbEl.textContent = sub.feedback || 'No feedback provided.';
        if (!sub.feedback) fbEl.style.color = '#a0aec0';
        if (sub.graded_by) document.getElementById('gm-graded-by').textContent = 'Graded by ' + sub.graded_by + (sub.graded_at ? ' on ' + sub.graded_at : '');
        gradeDisplay.style.display = 'block';
    } else {
        gradeDisplay.style.display = 'none';
    }

    // Form
    document.getElementById('gradeForm').action = sub.grade_url;
    document.getElementById('gm-score-input').value = sub.score ?? '';
    document.getElementById('gm-feedback-input').value = sub.feedback ?? '';
    document.getElementById('gm-submit-label').textContent = sub.status === 'graded' ? 'Update Grade' : 'Submit Grade';

    // Show modal
    const modal = document.getElementById('gradeModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeGradeModal() {
    document.getElementById('gradeModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('gradeModal').addEventListener('click', function(e) {
    if (e.target === this) closeGradeModal();
});

// Filter & Search
const searchInput = document.getElementById('sub-search');
const filterSelect = document.getElementById('sub-filter');
const rows = document.querySelectorAll('.sub-row');
const emptyMsg = document.getElementById('sub-empty');

function filterTable() {
    if (!rows.length) return;
    const term = (searchInput?.value || '').toLowerCase();
    const status = filterSelect?.value || 'all';
    let visible = 0;
    rows.forEach(row => {
        const matchName = !term || row.dataset.name.includes(term) || row.dataset.sid.includes(term);
        const matchStatus = status === 'all' || row.dataset.status === status;
        row.style.display = (matchName && matchStatus) ? '' : 'none';
        if (matchName && matchStatus) visible++;
    });
    if (emptyMsg) emptyMsg.style.display = visible === 0 ? 'block' : 'none';
}

searchInput?.addEventListener('input', filterTable);
filterSelect?.addEventListener('change', filterTable);

// Table row hover
rows.forEach(row => {
    row.addEventListener('mouseenter', () => row.style.background = '#f8fafc');
    row.addEventListener('mouseleave', () => row.style.background = '');
});

// Access Modal
function openAccessModal() {
    document.getElementById('accessModal').classList.add('show');
    fetch('{{ route("admin.todo.assignment.access.modal", $encryptedId) }}')
        .then(r => r.text()).then(html => { document.getElementById('modal-body').innerHTML = html; initializeModalScripts(); })
        .catch(() => { document.getElementById('modal-body').innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Error Loading</h3></div>'; });
}
function closeAccessModal() {
    document.getElementById('accessModal').classList.remove('show');
}
window.addEventListener('click', function(e) {
    if (e.target === document.getElementById('accessModal')) closeAccessModal();
});

function initializeModalScripts() {
    const selectAll = document.getElementById('select-all');
    if (selectAll) selectAll.addEventListener('change', function() { document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked); });
    document.querySelectorAll('.toggle-access').forEach(toggle => {
        toggle.addEventListener('change', function() {
            fetch(this.dataset.url, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                const toast = document.createElement('div'); toast.className = 'toast-notification';
                toast.textContent = data.status === 'allowed' ? '✓ Access granted' : '✗ Access revoked';
                toast.style.background = data.status === 'allowed' ? '#48bb78' : '#f56565';
                document.body.appendChild(toast); setTimeout(() => toast.remove(), 2500);
            }).catch(() => { this.checked = !this.checked; });
        });
    });
    const cf = document.getElementById('college-filter'), pf = document.getElementById('program-filter');
    if (cf) cf.addEventListener('change', function() {
        pf.innerHTML = '<option value="">All Programs</option>'; pf.disabled = !this.value;
        if (!this.value) return;
        fetch(`{{ url('admin/todo/colleges') }}/${this.value}/programs`).then(r => r.json()).then(programs => {
            programs.forEach(p => { const o = document.createElement('option'); o.value = p.id; o.textContent = p.program_name; pf.appendChild(o); });
        });
    });
}
</script>
@endpush
