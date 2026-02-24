@extends('layouts.admin')

@section('title', 'Programs - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/programs-index.css') }}">
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
                    <h1 class="welcome-title">Program Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-graduation-cap"></i> Manage all academic programs across colleges
                    </p>
                </div>
            </div>
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-graduation-cap"></i>
                    <span class="badge-count">{{ $totalPrograms }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Total Programs</div>
                    <div class="alert-subtitle">{{ $activePrograms }} active programs</div>
                </div>
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
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
            <div class="stat-link">
                View all programs <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.programs.index') }}?status=1" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Active Programs</div>
                    <div class="stat-number">{{ number_format($activePrograms) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-link">
                View active <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.programs.index') }}?has_students=true" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">With Students</div>
                    <div class="stat-number">{{ number_format($programsWithStudents) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-link">
                View enrolled <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.programs.index') }}?status=0" class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Inactive Programs</div>
                    <div class="stat-number">{{ number_format($inactivePrograms) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-link">
                View inactive <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Programs List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-graduation-cap" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        All Programs
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search programs..." id="search-programs">
                        </div>
                        <div class="filter-container" style="min-width: 150px;">
                            <select class="form-select" id="college-filter">
                                <option value="">All Colleges</option>
                                @foreach($colleges as $college)
                                <option value="{{ $college->id }}" {{ request('college_id') == $college->id ? 'selected' : '' }}>
                                    {{ $college->college_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add Program
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    @endif

                    @if($programs->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h3 class="empty-title">No programs yet</h3>
                            <p class="empty-text">You haven't created any academic programs. Start by adding the first program.</p>
                            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Create Your First Program
                            </a>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                Programs are degree offerings under each college
                            </div>
                        </div>
                    @else
                        <!-- Programs List -->
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
                                        try {
                                            $encryptedId = Crypt::encrypt($program->id);
                                        } catch (\Exception $e) {
                                            $encryptedId = '';
                                            \Log::error('Failed to encrypt program ID: ' . $e->getMessage());
                                        }
                                    @endphp
                                    <tr class="clickable-row" 
                                        data-href="{{ $encryptedId ? route('admin.programs.show', ['encryptedId' => $encryptedId]) : '#' }}"
                                        data-name="{{ strtolower($program->program_name) }}"
                                        data-code="{{ strtolower($program->program_code ?? '') }}"
                                        data-college="{{ strtolower($program->college->college_name ?? '') }}"
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
                                                    <div class="program-desc">{{ Str::limit($program->description, 50) }}</div>
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
                                                            <span class="item-badge badge-success">
                                                                <i class="fas fa-check-circle"></i> Active
                                                            </span>
                                                        @else
                                                            <span class="item-badge badge-warning">
                                                                <i class="fas fa-clock"></i> Inactive
                                                            </span>
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
                                                <div class="college-avatar" style="background: linear-gradient(135deg, var(--primary), #7c3aed);">
                                                    {{ strtoupper(substr($program->college->college_name, 0, 1)) }}
                                                </div>
                                                <div class="college-details">
                                                    <div class="college-name">{{ Str::limit($program->college->college_name, 25) }}</div>
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
                                                <span class="item-badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            @else
                                                <span class="item-badge badge-warning">
                                                    <i class="fas fa-clock"></i> Inactive
                                                </span>
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

                <!-- Pagination -->
                @if($programs->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $programs->firstItem() }} to {{ $programs->lastItem() }} of {{ $programs->total() }} entries
                    </div>
                    <div class="pagination-links">
                        {{ $programs->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.programs.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add New Program</div>
                                <div class="action-subtitle">Create a new academic program</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.colleges.index') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Manage Colleges</div>
                                <div class="action-subtitle">View and manage colleges</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}?role=4" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Manage Students</div>
                                <div class="action-subtitle">View students by program</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <button id="export-csv" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Export CSV</div>
                                <div class="action-subtitle">Download program list</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Program Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Program Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Programs</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $totalPrograms }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Active Programs</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $activePrograms }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Inactive Programs</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $inactivePrograms }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Students</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $totalStudents }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Program Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make rows clickable with debugging
        const clickableRows = document.querySelectorAll('.clickable-row');

        clickableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't redirect if user clicked on a link or button
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const href = this.dataset.href;
                const encrypted = this.dataset.encrypted;
                const id = this.dataset.collegeId || this.dataset.programId;
                
                // Debug output
                console.log('Row clicked:', {
                    element: this,
                    href: href,
                    encrypted: encrypted,
                    id: id,
                    type: this.dataset.collegeId ? 'college' : 'program'
                });
                
                if (href && href !== '#') {
                    window.location.href = href;
                } else {
                    console.error('Invalid href for row:', href);
                    alert('Unable to navigate: Invalid link. Please check the console for details.');
                }
            });
            
            row.style.cursor = 'pointer';
        });

        // Search functionality
        const searchInput = document.getElementById('search-programs');
        const collegeFilter = document.getElementById('college-filter');
        const programRows = document.querySelectorAll('.clickable-row');
        
        function filterPrograms() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const collegeId = collegeFilter ? collegeFilter.value : '';
            
            programRows.forEach(row => {
                const programName = row.dataset.name || '';
                const programCode = row.dataset.code || '';
                const programCollege = row.dataset.college || '';
                
                const matchesSearch = searchTerm === '' || 
                    programName.includes(searchTerm) || 
                    programCode.includes(searchTerm) || 
                    programCollege.includes(searchTerm);
                
                // College filter logic - would need actual college ID in data attribute
                const matchesCollege = collegeId === '' || row.dataset.collegeId === collegeId;
                
                row.style.display = matchesSearch && matchesCollege ? '' : 'none';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterPrograms);
        }

        if (collegeFilter) {
            collegeFilter.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('college_id', this.value);
                window.location.href = url.toString();
            });
        }

        // Export to CSV
        document.getElementById('export-csv')?.addEventListener('click', function() {
            const table = document.getElementById('programs-table');
            const rows = table.querySelectorAll('tr');
            const csv = [];
            
            // Add headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push('"' + th.textContent.trim() + '"');
            });
            csv.push(headers.join(','));
            
            // Add data rows
            table.querySelectorAll('tbody tr').forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = [];
                    const columns = row.querySelectorAll('td');
                    
                    if (columns.length >= 6) {
                        // Program Name
                        const programNameDiv = columns[0].querySelector('.program-name');
                        cells.push(`"${programNameDiv ? programNameDiv.textContent.trim() : ''}"`);
                        
                        // Program Code
                        const programCodeSpan = columns[1].querySelector('.program-code');
                        cells.push(`"${programCodeSpan ? programCodeSpan.textContent.trim() : ''}"`);
                        
                        // College
                        const collegeNameDiv = columns[2]?.querySelector('.college-name');
                        cells.push(`"${collegeNameDiv ? collegeNameDiv.textContent.trim() : ''}"`);
                        
                        // Students Count
                        const studentsCountDiv = columns[3]?.querySelector('.count-number');
                        cells.push(studentsCountDiv ? studentsCountDiv.textContent.trim() : '0');
                        
                        // Status
                        const statusSpan = columns[4]?.querySelector('.item-badge');
                        cells.push(`"${statusSpan ? statusSpan.textContent.trim() : ''}"`);
                        
                        // Created Date
                        const dateSpan = columns[5]?.querySelector('.item-date');
                        cells.push(`"${dateSpan ? dateSpan.textContent.trim() : ''}"`);
                        
                        csv.push(cells.join(','));
                    }
                }
            });
            
            // Create and download CSV file
            const csvContent = csv.join('\n');
            const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `programs_export_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>
@endpush