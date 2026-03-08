@extends('layouts.admin')

@section('title', 'Legal Pages')

@push('styles')
<style>
    .page-header { margin-bottom: 1.75rem; }
    .page-title  { font-size: 1.5rem; font-weight: 700; color: #1a202c; margin: 0; }
    .page-subtitle { font-size: .875rem; color: #718096; margin: .25rem 0 0; }

    /* ─── Legal cards grid ─── */
    .legal-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.25rem;
    }
    .legal-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(85,43,32,.08);
        overflow: hidden;
        border: 1px solid #f0ebe8;
        transition: box-shadow .2s, transform .2s;
    }
    .legal-card:hover { box-shadow: 0 8px 28px rgba(85,43,32,.14); transform: translateY(-2px); }

    .legal-card-header {
        background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
        color: #fff;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    .legal-card-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: rgba(221,178,56,.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        color: #ddb238;
        flex-shrink: 0;
    }
    .legal-card-title { font-size: 1rem; font-weight: 600; margin: 0; }
    .legal-card-body  { padding: 1.1rem 1.25rem; }
    .legal-preview {
        font-size: .82rem;
        color: #4a5568;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 56px;
        margin-bottom: .75rem;
    }
    .legal-preview.empty { color: #a0aec0; font-style: italic; }

    .legal-meta { font-size: .73rem; color: #718096; display: flex; flex-direction: column; gap: .2rem; }
    .legal-meta span { display: flex; align-items: center; gap: .35rem; }

    .legal-card-footer {
        padding: .75rem 1.25rem;
        border-top: 1px solid #f7f0ec;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }
    .btn-edit-legal {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem 1rem;
        background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-edit-legal:hover { opacity: .88; transform: translateY(-1px); }

    /* ─── Modal ─── */
    .modal-overlay {
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .modal-overlay.open {
        visibility: visible;
        opacity: 1;
        pointer-events: all;
    }
    .modal-box {
        background: #fff;
        border-radius: 18px;
        width: 100%;
        max-width: 700px;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(85,43,32,.25);
        transform: translateY(20px) scale(0.97);
        opacity: 0;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease;
    }
    .modal-overlay.open .modal-box {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
    .modal-header {
        background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
        color: #fff;
        padding: 1.25rem 1.5rem;
        border-radius: 18px 18px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky; top: 0; z-index: 1;
    }
    .modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: .5rem; }
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
    .form-group textarea {
        width: 100%;
        border: 2px solid #e5e7eb;
        border-radius: 9px;
        padding: .6rem .85rem;
        font-size: .9rem;
        transition: border-color .15s;
        color: #1a202c;
        font-family: inherit;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #ddb238;
        box-shadow: 0 0 0 3px rgba(221,178,56,.12);
    }
    .form-group textarea { resize: vertical; min-height: 240px; }

    .form-check { display: flex; align-items: center; gap: .5rem; margin-top: .25rem; }
    .form-check input[type=checkbox] { width: 16px; height: 16px; accent-color: #552b20; }
    .form-check label { margin: 0; font-weight: 500; font-size: .875rem; color: #4a5568; }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f0ebe8;
        display: flex;
        justify-content: flex-end;
        gap: .6rem;
        position: sticky; bottom: 0; background: #fff;
        border-radius: 0 0 18px 18px;
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

    /* ─── Flash ─── */
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

    .legal-note {
        background: linear-gradient(135deg, #fef3c7, #fffbeb);
        border: 1px solid #fde68a;
        border-left: 4px solid #f59e0b;
        border-radius: 10px;
        padding: .85rem 1.1rem;
        margin-bottom: 1.5rem;
        font-size: .85rem;
        color: #92400e;
        display: flex;
        gap: .6rem;
        align-items: flex-start;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">

    @if(session('success'))
        <div class="flash-alert flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-alert flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-shield-alt" style="color:#ddb238;margin-right:.5rem;"></i>Legal Pages</h1>
        <p class="page-subtitle">Manage Privacy Policy, Terms & Conditions, and Cookie Policy</p>
    </div>

    <div class="legal-note">
        <i class="fas fa-info-circle" style="margin-top:.1rem;flex-shrink:0;"></i>
        <div>Click <strong>Edit</strong> on any card to update its content. These pages are displayed to users when published.</div>
    </div>

    <div class="legal-grid">
        @php
            $icons = [
                'privacy_policy'   => ['fas fa-user-shield', 'Privacy Policy'],
                'terms_conditions' => ['fas fa-file-contract', 'Terms & Conditions'],
                'cookie_policy'    => ['fas fa-cookie-bite', 'Cookie Policy'],
            ];
        @endphp

        @foreach(array_keys(\App\Models\LegalPage::TYPES) as $type)
            @php $page = $pages[$type] ?? null; @endphp
            @if($page)
            <div class="legal-card">
                <div class="legal-card-header">
                    <div class="legal-card-icon">
                        <i class="{{ $icons[$type][0] ?? 'fas fa-file' }}"></i>
                    </div>
                    <div class="legal-card-title">{{ $page->title }}</div>
                </div>
                <div class="legal-card-body">
                    @if($page->content)
                        <div class="legal-preview">{{ strip_tags($page->content) }}</div>
                    @else
                        <div class="legal-preview empty">No content yet. Click Edit to add content.</div>
                    @endif
                    <div class="legal-meta">
                        <span>
                            <i class="fas fa-user-circle"></i>
                            <strong>Created by:</strong>&nbsp;
                            {{ $page->creator ? $page->creator->f_name . ' ' . $page->creator->l_name . ' (' . ($page->creator->role == 1 ? 'Admin' : 'User') . ')' : '—' }}
                        </span>
                        @if($page->updater)
                            <span>
                                <i class="fas fa-edit"></i>
                                <strong>Last updated by:</strong>&nbsp;
                                {{ $page->updater->f_name . ' ' . $page->updater->l_name . ' (' . ($page->updater->role == 1 ? 'Admin' : 'User') . ')' }}
                                &nbsp;·&nbsp;{{ $page->updated_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-footer-actions">
                    <form method="POST" action="{{ route('admin.legals.toggle-publish', $page) }}" style="margin:0;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-toggle-status {{ $page->is_published ? 'published' : 'draft' }}">
                            <span class="toggle-track"><span class="toggle-thumb"></span></span>
                            <span class="toggle-label">{{ $page->is_published ? 'Published' : 'Draft' }}</span>
                        </button>
                    </form>
                    <div class="action-dropdown-wrapper">
                        <button class="btn-action-dots" onclick="toggleActionDropdown(this)"><i class="fas fa-ellipsis-v"></i></button>
                        <div class="action-dropdown-menu">
                            <button class="dropdown-item"
                                onclick="openEditModal({{ $page->id }}, {{ json_encode($page->title) }}, {{ json_encode($page->content) }}, {{ $page->is_published ? 'true' : 'false' }}, {{ json_encode($icons[$type][0]) }})">
                                <i class="fas fa-pen"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>

{{-- ─── Edit Modal ─── --}}
<div class="modal-overlay" id="editModal" onclick="closeModalOnBg(event)">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalHeading"><i class="fas fa-pen" style="color:#ddb238;"></i> Edit Legal Page</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Page Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" id="editTitle" required>
                </div>
                <div class="form-group">
                    <label>Content <span style="color:#ef4444">*</span></label>
                    <textarea name="content" id="editContent" required placeholder="Write the legal content here..."></textarea>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_published" id="editPublish" value="1">
                    <label for="editPublish">Published (visible to users)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
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

    function openEditModal(id, title, content, isPublished, icon) {
        const base = "{{ url('admin/legals') }}";
        document.getElementById('editForm').action = base + '/' + id;
        document.getElementById('editTitle').value   = title;
        document.getElementById('editContent').value = content;
        document.getElementById('editPublish').checked = isPublished;
        document.getElementById('editModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('editModal').classList.remove('open');
    }

    function closeModalOnBg(e) {
        if (e.target === document.getElementById('editModal')) closeModal();
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
