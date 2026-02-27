@extends('layouts.admin')

@section('title', 'Programs - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/programs-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Dashboard Header — consistent with dashboard.css -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Program Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-graduation-cap"></i> Manage all academic programs across colleges
                        <span class="separator">•</span>
                        <span class="pending-notice">
                            <i class="fas fa-graduation-cap"></i> {{ $totalPrograms }} programs · {{ $activePrograms }} active
                        </span>
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.programs.create') }}" class="top-action-btn">
                    <i class="fas fa-plus-circle"></i> Add Program
                </a>
                <a href="{{ route('admin.colleges.index') }}" class="top-action-btn">
                    <i class="fas fa-university"></i> Colleges
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.programs.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Programs</div>
                    <div class="stat-number">{{ number_format($totalPrograms) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
            </div>
            <div class="stat-link">View all programs <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.programs.index') }}?status=1" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Active Programs</div>
                    <div class="stat-number">{{ number_format($activePrograms) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-link">View active <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.programs.index') }}?has_students=true" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">With Students</div>
                    <div class="stat-number">{{ number_format($programsWithStudents) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-link">View enrolled <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.programs.index') }}?status=0" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Inactive Programs</div>
                    <div class="stat-number">{{ number_format($inactivePrograms) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-link">View inactive <i class="fas fa-arrow-right"></i></div>
        </a>
    </div>

    <!-- Programs List — full width -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-graduation-cap" style="color: var(--primary); margin-right: 0.5rem;"></i>
                All Programs
            </h2>
            <div class="header-actions-bar">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search programs..." id="search-programs">
                </div>
                <div class="filter-container">
                    <select class="form-select" id="college-filter">
                        <option value="">All Colleges</option>
                        @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ request('college_id') == $college->id ? 'selected' : '' }}>
                            {{ $college->college_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button id="print-report" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
                <button id="export-csv" class="btn btn-secondary">
                    <i class="fas fa-file-csv"></i> Export
                </button>
                <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Program
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            @if($programs->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3 class="empty-title">No programs yet</h3>
                    <p class="empty-text">Start by adding the first academic program.</p>
                    <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Create Your First Program
                    </a>
                    <div class="empty-hint">
                        <i class="fas fa-lightbulb"></i> Programs are degree offerings under each college
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="programs-table" id="programs-table">
                        <thead>
                            <tr>
                                <th>Program Name</th>
                                <th class="hide-on-mobile">Code</th>
                                <th class="hide-on-tablet">College</th>
                                <th>Students</th>
                                <th class="hide-on-tablet">Status</th>
                                <th class="hide-on-tablet">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programs as $program)
                            @php
                                try { $encryptedId = Crypt::encrypt($program->id); }
                                catch (\Exception $e) { $encryptedId = ''; }
                            @endphp
                            <tr class="clickable-row"
                                data-href="{{ $encryptedId ? route('admin.programs.show', ['encryptedId' => $encryptedId]) : '#' }}"
                                data-name="{{ strtolower($program->program_name) }}"
                                data-code="{{ strtolower($program->program_code ?? '') }}"
                                data-college="{{ strtolower($program->college->college_name ?? '') }}"
                                data-college-id="{{ $program->college_id ?? '' }}"
                                data-program-id="{{ $program->id }}"
                                data-encrypted="{{ $encryptedId }}">
                                <td>
                                    <div class="program-info-cell">
                                        <div class="program-icon program-{{ ($loop->index % 3) + 1 }}">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="program-details">
                                            <div class="program-name">{{ $program->program_name }}</div>
                                            @if($program->description)
                                            <div class="program-desc">{{ Str::limit($program->description, 60) }}</div>
                                            @endif
                                            <div class="program-mobile-info">
                                                <div class="program-code-mobile">
                                                    <i class="fas fa-code"></i> {{ $program->program_code ?? 'No code' }}
                                                </div>
                                                @if($program->college)
                                                <div class="college-mobile">
                                                    <i class="fas fa-university"></i> {{ Str::limit($program->college->college_name, 30) }}
                                                </div>
                                                @endif
                                                @if($program->status == 1)
                                                    <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                                                @else
                                                    <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-on-mobile">
                                    <span class="program-code">{{ $program->program_code ?? '—' }}</span>
                                </td>
                                <td class="hide-on-tablet">
                                    @if($program->college)
                                    <div class="college-info">
                                        <div class="college-avatar">
                                            {{ strtoupper(substr($program->college->college_name, 0, 1)) }}
                                        </div>
                                        <div class="college-details">
                                            <div class="college-name">{{ Str::limit($program->college->college_name, 30) }}</div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="no-college">No college</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="students-count">
                                        <div class="count-number">{{ $program->students_count }}</div>
                                        <div class="count-label">enrolled</div>
                                    </div>
                                </td>
                                <td class="hide-on-tablet">
                                    @if($program->status == 1)
                                        <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                                    @else
                                        <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Inactive</span>
                                    @endif
                                </td>
                                <td class="hide-on-tablet">
                                    <span class="item-date">{{ $program->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($programs->hasPages())
        <div class="card-footer">
            <div class="pagination-info">
                Showing {{ $programs->firstItem() }} to {{ $programs->lastItem() }} of {{ $programs->total() }} entries
            </div>
            <div class="pagination-links">
                @if($programs->onFirstPage())
                    <span class="pagination-btn disabled">Previous</span>
                @else
                    <a href="{{ $programs->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif

                @foreach(range(1, $programs->lastPage()) as $page)
                    @if($page == $programs->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @elseif(abs($page - $programs->currentPage()) <= 2)
                        <a href="{{ $programs->url($page) }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($programs->hasMorePages())
                    <a href="{{ $programs->nextPageUrl() }}" class="pagination-btn">Next</a>
                @else
                    <span class="pagination-btn disabled">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Program Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>

    <!-- Hidden Print Content -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5;">ADSCO Program Management Report</h1>
                <p style="color: #666;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Program Name</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Code</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">College</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Students</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $program)
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $program->program_name }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $program->program_code ?? '—' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $program->college->college_name ?? 'No college' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $program->students_count }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $program->status == 1 ? 'Active' : 'Inactive' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $program->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Clickable rows
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function (e) {
            if (e.target.closest('a, button')) return;
            const href = this.dataset.href;
            if (href && href !== '#') window.location.href = href;
        });
    });

    // Search + college filter
    const searchInput = document.getElementById('search-programs');
    const collegeFilter = document.getElementById('college-filter');

    function filterRows() {
        const term = searchInput?.value.toLowerCase() ?? '';
        const collegeId = collegeFilter?.value ?? '';
        document.querySelectorAll('.clickable-row').forEach(row => {
            const matchSearch = !term || row.dataset.name.includes(term) || row.dataset.code.includes(term) || row.dataset.college.includes(term);
            const matchCollege = !collegeId || row.dataset.collegeId === collegeId;
            row.style.display = matchSearch && matchCollege ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterRows);

    // College filter — server-side redirect
    collegeFilter?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        this.value ? url.searchParams.set('college_id', this.value) : url.searchParams.delete('college_id');
        window.location.href = url.toString();
    });

    // Print functionality
    document.getElementById('print-report')?.addEventListener('click', function () {
        const content = document.getElementById('print-content').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>Program Report</title>
            <style>
                body{font-family:Arial,sans-serif;padding:20px;} 
                table{width:100%;border-collapse:collapse;} 
                th{background:#f3f4f6;}
                @media print {
                    @page { size: landscape; margin: 0.5in; }
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
            </head><body>${content}<script>
                window.onload=()=>{ window.print(); setTimeout(()=>window.close(), 100); }
            <\/script></body></html>`);
        win.document.close();
    });

    // Export CSV
    document.getElementById('export-csv')?.addEventListener('click', function () {
        const rows = [['Program Name', 'Code', 'College', 'Students', 'Status', 'Created']];
        document.querySelectorAll('#programs-table tbody tr').forEach(row => {
            if (row.style.display === 'none') return;
            const cols = row.querySelectorAll('td');
            rows.push([
                cols[0]?.querySelector('.program-name')?.textContent.trim() ?? '',
                cols[1]?.querySelector('.program-code')?.textContent.trim() ?? '',
                cols[2]?.querySelector('.college-name')?.textContent.trim() ?? '',
                cols[3]?.querySelector('.count-number')?.textContent.trim() ?? '',
                cols[4]?.querySelector('.item-badge')?.textContent.trim() ?? '',
                cols[5]?.querySelector('.item-date')?.textContent.trim() ?? '',
            ].map(v => `"${v}"`));
        });
        const blob = new Blob(['\uFEFF' + rows.map(r => r.join(',')).join('\n')], { type: 'text/csv;charset=utf-8;' });
        const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(blob), download: `programs_${new Date().toISOString().slice(0,10)}.csv` });
        document.body.appendChild(a); a.click(); a.remove();
    });

});
</script>
@endpush