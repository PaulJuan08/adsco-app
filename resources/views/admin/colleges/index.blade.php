@extends('layouts.admin')

@section('title', 'Colleges - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/colleges-index.css') }}">
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
                    <h1 class="welcome-title">College Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-university"></i> Manage all academic colleges and departments
                        <span class="separator">•</span>
                        <span class="pending-notice">
                            <i class="fas fa-school"></i> {{ $totalColleges }} colleges · {{ $activeColleges }} active
                        </span>
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.colleges.create') }}" class="top-action-btn">
                    <i class="fas fa-plus-circle"></i> Add College
                </a>
                <a href="{{ route('admin.users.index') }}?role=4" class="top-action-btn">
                    <i class="fas fa-users"></i> Manage Students
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.colleges.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Colleges</div>
                    <div class="stat-number">{{ number_format($totalColleges) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-university"></i></div>
            </div>
            <div class="stat-link">View all colleges <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.colleges.index') }}?status=1" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Active Colleges</div>
                    <div class="stat-number">{{ number_format($activeColleges) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="stat-link">View active <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="{{ route('admin.users.index') }}?role=4" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ number_format($totalStudents) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-link">View all students <i class="fas fa-arrow-right"></i></div>
        </a>

        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Students/College</div>
                    <div class="stat-number">{{ number_format($avgStudentsPerCollege) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
            </div>
            <div class="stat-link">Statistics <i class="fas fa-arrow-right"></i></div>
        </div>
    </div>

    <!-- Colleges List — full width -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-university" style="color: var(--primary); margin-right: 0.5rem;"></i>
                All Colleges
            </h2>
            <div class="header-actions-bar">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search colleges..." id="search-colleges">
                </div>
                <button id="print-report" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
                <button id="export-csv" class="btn btn-secondary">
                    <i class="fas fa-file-csv"></i> Export
                </button>
                <a href="{{ route('admin.colleges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add College
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

            @if($colleges->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-university"></i></div>
                    <h3 class="empty-title">No colleges yet</h3>
                    <p class="empty-text">Start by adding the first college or department.</p>
                    <a href="{{ route('admin.colleges.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Create Your First College
                    </a>
                    <div class="empty-hint">
                        <i class="fas fa-lightbulb"></i> Colleges help organize students by academic departments
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="colleges-table" id="colleges-table">
                        <thead>
                            <tr>
                                <th>College Name</th>
                                <th class="hide-on-mobile">Years</th>
                                <th>Students</th>
                                <th class="hide-on-tablet">Status</th>
                                <th class="hide-on-tablet">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($colleges as $college)
                            @php
                                try { $encryptedId = Crypt::encrypt($college->id); }
                                catch (\Exception $e) { $encryptedId = ''; }
                            @endphp
                            <tr class="clickable-row"
                                data-href="{{ $encryptedId ? route('admin.colleges.show', ['encryptedId' => $encryptedId]) : '#' }}"
                                data-name="{{ strtolower($college->college_name) }}"
                                data-college-id="{{ $college->id }}"
                                data-encrypted="{{ $encryptedId }}">
                                <td>
                                    <div class="college-info-cell">
                                        <div class="college-icon college-{{ ($loop->index % 3) + 1 }}">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="college-details">
                                            <div class="college-name">{{ $college->college_name }}</div>
                                            @if($college->description)
                                            <div class="college-desc">{{ Str::limit($college->description, 60) }}</div>
                                            @endif
                                            <div class="college-mobile-info">
                                                <div class="college-code-mobile">
                                                    <i class="fas fa-calendar-alt"></i> {{ Str::limit($college->college_year, 30) }}
                                                </div>
                                                @if($college->status == 1)
                                                    <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                                                @else
                                                    <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-on-mobile">
                                    <span class="college-years">{{ Str::limit($college->college_year, 30) }}</span>
                                </td>
                                <td>
                                    <div class="students-count">
                                        <div class="count-number">{{ $college->students_count }}</div>
                                        <div class="count-label">enrolled</div>
                                    </div>
                                </td>
                                <td class="hide-on-tablet">
                                    @if($college->status == 1)
                                        <span class="item-badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                                    @else
                                        <span class="item-badge badge-warning"><i class="fas fa-clock"></i> Inactive</span>
                                    @endif
                                </td>
                                <td class="hide-on-tablet">
                                    <span class="item-date">{{ $college->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($colleges->hasPages())
        <div class="card-footer">
            <div class="pagination-info">
                Showing {{ $colleges->firstItem() }} to {{ $colleges->lastItem() }} of {{ $colleges->total() }} entries
            </div>
            <div class="pagination-links">
                @if($colleges->onFirstPage())
                    <span class="pagination-btn disabled">Previous</span>
                @else
                    <a href="{{ $colleges->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif

                @foreach(range(1, $colleges->lastPage()) as $page)
                    @if($page == $colleges->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @elseif(abs($page - $colleges->currentPage()) <= 2)
                        <a href="{{ $colleges->url($page) }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($colleges->hasMorePages())
                    <a href="{{ $colleges->nextPageUrl() }}" class="pagination-btn">Next</a>
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
            College Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>

    <!-- Hidden Print Content -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5;">ADSCO College Management Report</h1>
                <p style="color: #666;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">College Name</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Years</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Students</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colleges as $college)
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $college->college_name }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $college->college_year }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $college->students_count }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $college->status == 1 ? 'Active' : 'Inactive' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $college->created_at->format('M d, Y') }}</td>
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

    // Search
    const searchInput = document.getElementById('search-colleges');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.style.display = row.dataset.name.includes(term) ? '' : 'none';
            });
        });
    }

    // Print
    document.getElementById('print-report')?.addEventListener('click', function () {
        const content = document.getElementById('print-content').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>College Report</title>
            <style>body{font-family:Arial,sans-serif;padding:20px;} table{width:100%;border-collapse:collapse;} th{background:#f3f4f6;}</style>
            </head><body>${content}<script>window.onload=()=>{window.print();setTimeout(()=>window.close(),100);}<\/script></body></html>`);
        win.document.close();
    });

    // Export CSV
    document.getElementById('export-csv')?.addEventListener('click', function () {
        const rows = [['College Name', 'Years', 'Students', 'Status', 'Created']];
        document.querySelectorAll('#colleges-table tbody tr').forEach(row => {
            if (row.style.display === 'none') return;
            const cols = row.querySelectorAll('td');
            rows.push([
                cols[0]?.querySelector('.college-name')?.textContent.trim() ?? '',
                cols[1]?.querySelector('.college-years')?.textContent.trim() ?? '',
                cols[2]?.querySelector('.count-number')?.textContent.trim() ?? '',
                cols[3]?.querySelector('.item-badge')?.textContent.trim() ?? '',
                cols[4]?.querySelector('.item-date')?.textContent.trim() ?? '',
            ].map(v => `"${v}"`));
        });
        const blob = new Blob(['\uFEFF' + rows.map(r => r.join(',')).join('\n')], { type: 'text/csv;charset=utf-8;' });
        const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(blob), download: `colleges_${new Date().toISOString().slice(0,10)}.csv` });
        document.body.appendChild(a); a.click(); a.remove();
    });

});
</script>
@endpush