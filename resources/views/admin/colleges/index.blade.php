@extends('layouts.admin')

@section('title', 'Colleges - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/colleges-index.css') }}">
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
                    <h1 class="welcome-title">College Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-university"></i> Manage all academic colleges and departments
                    </p>
                </div>
            </div>
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-school"></i>
                    <span class="badge-count">{{ $totalColleges }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Total Colleges</div>
                    <div class="alert-subtitle">{{ $activeColleges }} active colleges</div>
                </div>
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
                <div class="stat-icon">
                    <i class="fas fa-university"></i>
                </div>
            </div>
            <div class="stat-link">
                View all colleges <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.colleges.index') }}?status=1" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Active Colleges</div>
                    <div class="stat-number">{{ number_format($activeColleges) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-link">
                View active <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.users.index') }}?role=4" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ number_format($totalStudents) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-link">
                View all students <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="#" class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Students/College</div>
                    <div class="stat-number">{{ number_format($avgStudentsPerCollege) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
            </div>
            <div class="stat-link">
                Statistics <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Colleges List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-university" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        All Colleges
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search colleges..." id="search-colleges">
                        </div>
                        <a href="{{ route('admin.colleges.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add College
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

                    @if($colleges->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <h3 class="empty-title">No colleges yet</h3>
                            <p class="empty-text">You haven't created any colleges. Start by adding the first college or department.</p>
                            <a href="{{ route('admin.colleges.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Create Your First College
                            </a>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                Colleges help organize students by their academic departments
                            </div>
                        </div>
                    @else
                        <!-- Colleges List -->
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
                                        try {
                                            $encryptedId = Crypt::encrypt($college->id);
                                        } catch (\Exception $e) {
                                            $encryptedId = '';
                                            \Log::error('Failed to encrypt college ID: ' . $e->getMessage());
                                        }
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
                                                    <div class="college-desc">{{ Str::limit($college->description, 50) }}</div>
                                                    @endif
                                                    <div class="college-mobile-info">
                                                        <div class="college-code-mobile">
                                                            <i class="fas fa-calendar-alt"></i> {{ Str::limit($college->college_year, 30) }}
                                                        </div>
                                                        @if($college->status == 1)
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
                                            <span class="item-date">{{ $college->created_at->format('M d, Y') }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
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
                        
                        @foreach(range(1, min(5, $colleges->lastPage())) as $page)
                            @if($page == $colleges->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                            @else
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
                        <a href="{{ route('admin.colleges.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add New College</div>
                                <div class="action-subtitle">Create a new college/department</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}?role=4" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Manage Students</div>
                                <div class="action-subtitle">View and manage all students</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <button id="print-report" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-file-export"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Print Report</div>
                                <div class="action-subtitle">Print college list</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                        
                        <button id="export-csv" class="action-card action-info" style="display: none;">
                            <div class="action-icon">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Export CSV</div>
                                <div class="action-subtitle">Download college list as CSV</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- College Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        College Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Colleges</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $totalColleges }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Active Colleges</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $activeColleges }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Enrolled Students</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $totalStudents }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Avg Students/College</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $avgStudentsPerCollege }}</div>
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
            College Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>

    <!-- Hidden Print Content -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5; margin-bottom: 5px;">ADSCO College Management Report</h1>
                <p style="color: #666; margin-bottom: 10px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;">College Statistics Summary</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #eef2ff; padding: 15px; border-radius: 8px; border: 1px solid #c7d2fe;">
                        <div style="font-size: 24px; font-weight: bold; color: #4f46e5;">{{ $totalColleges }}</div>
                        <div style="font-size: 14px; color: #4f46e5;">Total Colleges</div>
                    </div>
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 24px; font-weight: bold; color: #d97706;">{{ $activeColleges }}</div>
                        <div style="font-size: 14px; color: #d97706;">Active Colleges</div>
                    </div>
                    <div style="background: #dcfce7; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0;">
                        <div style="font-size: 24px; font-weight: bold; color: #059669;">{{ $totalStudents }}</div>
                        <div style="font-size: 14px; color: #059669;">Enrolled Students</div>
                    </div>
                    <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd;">
                        <div style="font-size: 24px; font-weight: bold; color: #0284c7;">{{ $avgStudentsPerCollege }}</div>
                        <div style="font-size: 14px; color: #0284c7;">Avg Students/College</div>
                    </div>
                </div>
            </div>
            
            <h2 style="color: #333; margin-bottom: 15px;">College List</h2>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">College Name</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Years</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Students</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left;">Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colleges as $college)
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $college->college_name }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $college->college_year }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $college->students_count }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">
                            @if($college->status == 1)
                                <span style="color: #059669;">Active</span>
                            @else
                                <span style="color: #d97706;">Inactive</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $college->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="color: #6b7280; font-size: 14px;">
                    Total Colleges: {{ $colleges->total() }} | 
                    Generated by: {{ Auth::user()->f_name }} {{ Auth::user()->l_name }} | 
                    Page 1 of 1
                </p>
            </div>
        </div>
    </div>
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
        const searchInput = document.getElementById('search-colleges');
        const collegeRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && collegeRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                collegeRows.forEach(row => {
                    const collegeName = row.dataset.name || '';
                    
                    if (searchTerm === '' || collegeName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Print functionality
        document.getElementById('print-report')?.addEventListener('click', function() {
            const printContent = document.getElementById('print-content').innerHTML;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>College Management Report</title>
                    <style>
                        @media print {
                            @page {
                                size: landscape;
                                margin: 0.5in;
                            }
                            body {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            table {
                                page-break-inside: auto;
                            }
                            tr {
                                page-break-inside: avoid;
                                page-break-after: auto;
                            }
                        }
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 20px;
                        }
                        h1, h2, h3 {
                            margin-top: 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th {
                            background-color: #f3f4f6 !important;
                            -webkit-print-color-adjust: exact;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 100);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });

        // Export to CSV functionality
        const exportBtn = document.getElementById('export-csv');
        if (exportBtn) {
            exportBtn.style.display = 'flex';
            exportBtn.addEventListener('click', function() {
                const table = document.getElementById('colleges-table');
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
                        
                        // College Name
                        const collegeNameDiv = columns[0].querySelector('.college-name');
                        cells.push(`"${collegeNameDiv ? collegeNameDiv.textContent.trim() : ''}"`);
                        
                        // Years
                        if (columns[1]) {
                            const yearsSpan = columns[1].querySelector('.college-years');
                            cells.push(`"${yearsSpan ? yearsSpan.textContent.trim() : ''}"`);
                        } else {
                            cells.push('""');
                        }
                        
                        // Students Count
                        if (columns[2]) {
                            const studentsCountDiv = columns[2].querySelector('.count-number');
                            cells.push(studentsCountDiv ? studentsCountDiv.textContent.trim() : '0');
                        } else {
                            cells.push('0');
                        }
                        
                        // Status
                        if (columns[3]) {
                            const statusSpan = columns[3].querySelector('.item-badge');
                            cells.push(`"${statusSpan ? statusSpan.textContent.trim() : ''}"`);
                        } else {
                            cells.push('""');
                        }
                        
                        // Created Date
                        if (columns[4]) {
                            const dateSpan = columns[4].querySelector('.item-date');
                            cells.push(`"${dateSpan ? dateSpan.textContent.trim() : ''}"`);
                        } else {
                            cells.push('""');
                        }
                        
                        csv.push(cells.join(','));
                    }
                });
                
                // Create and download CSV file
                const csvContent = csv.join('\n');
                const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                
                link.setAttribute('href', url);
                link.setAttribute('download', `colleges_export_${new Date().toISOString().slice(0,10)}.csv`);
                link.style.visibility = 'hidden';
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }
    });
</script>
@endpush