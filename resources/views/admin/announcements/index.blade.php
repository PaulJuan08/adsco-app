@extends('layouts.admin')

@section('title', 'Announcements')

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .page-title { font-size: 1.5rem; font-weight: 700; color: #1a202c; margin: 0; }
    .page-subtitle { font-size: 0.875rem; color: #718096; margin: 0.25rem 0 0; }

    /* ─── Cards ─── */
    .ann-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .ann-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(85,43,32,.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border: 1px solid #f0ebe8;
        transition: box-shadow .2s;
    }
    .ann-card:hover { box-shadow: 0 6px 24px rgba(85,43,32,.13); }

    .ann-card-stripe {
        height: 5px;
    }
    .stripe-info    { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stripe-warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stripe-success { background: linear-gradient(90deg, #10b981, #34d399); }
    .stripe-danger  { background: linear-gradient(90deg, #ef4444, #f87171); }

    .ann-card-body { padding: 1.25rem; flex: 1; }
    .ann-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1a202c;
        margin: 0 0 .5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ann-card-content {
        font-size: 0.85rem;
        color: #4a5568;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: .75rem;
    }
    .ann-meta {
        font-size: 0.75rem;
        color: #718096;
        display: flex;
        flex-direction: column;
        gap: .2rem;
    }
    .ann-meta span { display: flex; align-items: center; gap: .35rem; }

    .ann-card-footer {
        padding: .75rem 1.25rem;
        border-top: 1px solid #f7f0ec;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        flex-wrap: wrap;
    }
    .ann-badges { display: flex; gap: .4rem; flex-wrap: wrap; }
    .badge-type {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .badge-info    { background: #dbeafe; color: #1d4ed8; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-danger  { background: #fee2e2; color: #991b1b; }

    .badge-status {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
    }
    .badge-published   { background: #d1fae5; color: #065f46; }
    .badge-unpublished { background: #f3f4f6; color: #6b7280; }
    .badge-expired     { background: #fee2e2; color: #991b1b; }

    .ann-actions { display: flex; gap: .4rem; }
    .btn-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        transition: all .15s;
        text-decoration: none;
    }
    .btn-icon-edit   { background: #f3f4f6; color: #552b20; }
    .btn-icon-edit:hover { background: #552b20; color: #fff; }
    .btn-icon-toggle-on  { background: #d1fae5; color: #065f46; }
    .btn-icon-toggle-on:hover  { background: #065f46; color: #fff; }
    .btn-icon-toggle-off { background: #f3f4f6; color: #6b7280; }
    .btn-icon-toggle-off:hover { background: #6b7280; color: #fff; }
    .btn-icon-delete { background: #fee2e2; color: #991b1b; }
    .btn-icon-delete:hover { background: #991b1b; color: #fff; }

    /* ─── Create / Edit Modal ─── */
    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff;
        border-radius: 18px;
        width: 100%;
        max-width: 620px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(85,43,32,.25);
        animation: modalIn .25s ease;
    }
    @@keyframes modalIn {
        from { opacity:0; transform:translateY(20px) scale(.97); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .modal-header {
        background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
        color: #fff;
        padding: 1.25rem 1.5rem;
        border-radius: 18px 18px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 600; }
    .modal-close {
        background: rgba(255,255,255,.15);
        border: none; color: #fff;
        width: 32px; height: 32px;
        border-radius: 8px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        transition: background .15s;
    }
    .modal-close:hover { background: rgba(255,255,255,.3); }
    .modal-body { padding: 1.5rem; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display: block; font-weight: 600; font-size: .875rem; color: #552b20; margin-bottom: .4rem; }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 2px solid #e5e7eb;
        border-radius: 9px;
        padding: .6rem .85rem;
        font-size: .9rem;
        transition: border-color .15s;
        color: #1a202c;
        background: #fff;
        font-family: inherit;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #ddb238;
        box-shadow: 0 0 0 3px rgba(221,178,56,.12);
    }
    .form-group textarea { resize: vertical; min-height: 120px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-check { display: flex; align-items: center; gap: .5rem; margin-top: .25rem; }
    .form-check input[type=checkbox] { width: 16px; height: 16px; accent-color: #552b20; }
    .form-check label { margin: 0; font-weight: 500; font-size: .875rem; }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f0ebe8;
        display: flex;
        justify-content: flex-end;
        gap: .6rem;
    }
    .btn-cancel {
        padding: .55rem 1.25rem;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        background: #fff;
        color: #4a5568;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-cancel:hover { background: #f3f4f6; }
    .btn-save {
        padding: .55rem 1.5rem;
        border-radius: 8px;
        border: none;
        background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
        color: #fff;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-save:hover { opacity: .9; transform: translateY(-1px); }

    /* ─── Empty state ─── */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(85,43,32,.08);
    }
    .empty-icon { font-size: 3rem; color: #ddb238; margin-bottom: 1rem; }
    .empty-state h3 { color: #552b20; margin-bottom: .5rem; }
    .empty-state p  { color: #718096; font-size: .9rem; }

    /* ─── Alert ─── */
    .flash-alert {
        padding: .85rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: .6rem;
        font-size: .9rem;
        font-weight: 500;
    }
    .flash-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
    .flash-error   { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }

    /* ─── Create button ─── */
    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .6rem 1.25rem;
        background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: .9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }
    .btn-create:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(85,43,32,.3); }
</style>
@endpush

@section('content')
<div class="dashboard-container">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flash-alert flash-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash-alert flash-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="fas fa-bullhorn" style="color:#ddb238;margin-right:.5rem;"></i>Announcements</h1>
            <p class="page-subtitle">Create and manage announcements visible to all users</p>
        </div>
        <button class="btn-create" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> New Announcement
        </button>
    </div>

    {{-- Cards --}}
    @if($announcements->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-bullhorn"></i></div>
            <h3>No Announcements Yet</h3>
            <p>Create your first announcement to notify all users.</p>
        </div>
    @else
        <div class="ann-grid">
            @foreach($announcements as $ann)
                @php
                    $isExpired = $ann->end_date && $ann->end_date->isPast();
                    $statusLabel = $isExpired ? 'Expired' : ($ann->is_published ? 'Published' : 'Draft');
                    $statusClass  = $isExpired ? 'badge-expired' : ($ann->is_published ? 'badge-published' : 'badge-unpublished');
                @endphp
                <div class="ann-card">
                    <div class="ann-card-stripe stripe-{{ $ann->type }}"></div>
                    <div class="ann-card-body">
                        <div class="ann-card-title">{{ $ann->title }}</div>
                        <div class="ann-card-content">{{ strip_tags($ann->content) }}</div>
                        <div class="ann-meta">
                            <span><i class="fas fa-user-circle"></i>
                                <strong>Created by:</strong>&nbsp;
                                {{ $ann->creator ? $ann->creator->f_name . ' ' . $ann->creator->l_name . ' (' . ($ann->creator->role == 1 ? 'Admin' : 'User') . ')' : '—' }}
                            </span>
                            @if($ann->updater && $ann->updated_at != $ann->created_at)
                                <span><i class="fas fa-edit"></i>
                                    <strong>Last updated by:</strong>&nbsp;
                                    {{ $ann->updater->f_name . ' ' . $ann->updater->l_name . ' (' . ($ann->updater->role == 1 ? 'Admin' : 'User') . ')' }}
                                    &nbsp;·&nbsp;{{ $ann->updated_at->diffForHumans() }}
                                </span>
                            @endif
                            @if($ann->end_date)
                                <span><i class="fas fa-calendar-times"></i>
                                    <strong>Ends:</strong>&nbsp;{{ $ann->end_date->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer-actions">
                        <form method="POST" action="{{ route('admin.announcements.toggle-publish', $ann) }}" style="margin:0;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-toggle-status {{ $ann->is_published ? 'published' : 'draft' }}">
                                <span class="toggle-track"><span class="toggle-thumb"></span></span>
                                <span class="toggle-label">{{ $ann->is_published ? 'Published' : 'Draft' }}</span>
                            </button>
                        </form>
                        <div class="action-dropdown-wrapper">
                            <button class="btn-action-dots" onclick="toggleActionDropdown(this)"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-dropdown-menu">
                                <button class="dropdown-item"
                                    onclick="openEditModal({{ $ann->id }}, {{ json_encode($ann->title) }}, {{ json_encode($ann->content) }}, '{{ $ann->type }}', '{{ $ann->end_date?->format('Y-m-d') ?? '' }}', {{ $ann->is_published ? 'true' : 'false' }})">
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <div class="dropdown-divider"></div>
                                <button onclick="confirmDeleteAnn({{ $ann->id }}, '{{ addslashes($ann->title) }}')" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Hidden delete form --}}
<form id="annDeleteForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

{{-- ─── Create Modal ─── --}}
<div class="modal-overlay" id="createModal" onclick="closeModalOnBg(event,'createModal')">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle" style="margin-right:.5rem;color:#ddb238;"></i>New Announcement</h3>
            <button class="modal-close" onclick="closeModal('createModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" placeholder="Announcement title" required>
                </div>
                <div class="form-group">
                    <label>Content <span style="color:#ef4444">*</span></label>
                    <textarea name="content" data-quill placeholder="Write your announcement here..." required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type">
                            <option value="info">ℹ️ Info</option>
                            <option value="warning">⚠️ Warning</option>
                            <option value="success">✅ Success</option>
                            <option value="danger">🚨 Danger</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>End Date <span style="color:#718096;font-size:.8rem;">(optional)</span></label>
                        <input type="date" name="end_date" min="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_published" id="createPublish" value="1">
                    <label for="createPublish">Publish immediately (visible to all users)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn-save"><i class="fas fa-save" style="margin-right:.4rem;"></i>Create</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Edit Modal ─── --}}
<div class="modal-overlay" id="editModal" onclick="closeModalOnBg(event,'editModal')">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-pen" style="margin-right:.5rem;color:#ddb238;"></i>Edit Announcement</h3>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" id="editTitle" required>
                </div>
                <div class="form-group">
                    <label>Content <span style="color:#ef4444">*</span></label>
                    <textarea name="content" id="editContent" data-quill required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" id="editType">
                            <option value="info">ℹ️ Info</option>
                            <option value="warning">⚠️ Warning</option>
                            <option value="success">✅ Success</option>
                            <option value="danger">🚨 Danger</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="editEndDate">
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_published" id="editPublish" value="1">
                    <label for="editPublish">Published (visible to all users)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-save"><i class="fas fa-save" style="margin-right:.4rem;"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Action dropdown
    window.toggleActionDropdown = function(btn) {
        if (!btn._menu) btn._menu = btn.nextElementSibling;
        var menu = btn._menu;
        var isOpen = menu.classList.contains('open');
        document.querySelectorAll('.action-dropdown-menu.open').forEach(function(d) { d.classList.remove('open'); });
        if (!isOpen) {
            if (menu.parentNode !== document.body) document.body.appendChild(menu);
            var rect = btn.getBoundingClientRect();
            menu.style.left = 'auto';
            menu.style.right = (window.innerWidth - rect.right) + 'px';
            if (rect.top > 130) {
                menu.style.top = 'auto';
                menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
            } else {
                menu.style.top = (rect.bottom + 4) + 'px';
                menu.style.bottom = 'auto';
            }
            menu.classList.add('open');
        }
    };
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-dropdown-wrapper'))
            document.querySelectorAll('.action-dropdown-menu.open').forEach(function(d) { d.classList.remove('open'); });
    });
    window.addEventListener('scroll', function() {
        document.querySelectorAll('.action-dropdown-menu.open').forEach(function(d) { d.classList.remove('open'); });
    }, true);

    window.confirmDeleteAnn = function(id, name) {
        Swal.fire({
            title: 'Delete Announcement?',
            text: `"${name}" will be permanently deleted.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('annDeleteForm');
                form.action = `{{ url('admin/announcements') }}/${id}`;
                form.submit();
            }
        });
    };

    function openCreateModal() {
        document.getElementById('createModal').classList.add('open');
    }

    function openEditModal(id, title, content, type, endDate, isPublished) {
        const base = "{{ url('admin/announcements') }}";
        document.getElementById('editForm').action = base + '/' + id;
        document.getElementById('editTitle').value   = title;
        window.setQuillContent('editContent', content);
        document.getElementById('editType').value    = type;
        document.getElementById('editEndDate').value = endDate;
        document.getElementById('editPublish').checked = isPublished;
        document.getElementById('editModal').classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function closeModalOnBg(e, id) {
        if (e.target === document.getElementById(id)) closeModal(id);
    }

    // Escape key closes any open modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
        }
    });
</script>
@endpush
