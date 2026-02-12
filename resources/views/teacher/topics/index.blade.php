@extends('layouts.teacher')

@section('title', 'Topics - Teacher Dashboard')

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
                    <h1 class="welcome-title">My Topics</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-chalkboard"></i> Manage learning materials for your courses
                        @if(($draftTopics ?? 0) > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $draftTopics ?? 0 }} draft{{ ($draftTopics ?? 0) > 1 ? 's' : '' }} pending</span>
                        @endif
                    </p>
                </div>
            </div>
            @if(($draftTopics ?? 0) > 0)
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-edit"></i>
                    <span class="badge-count">{{ $draftTopics ?? 0 }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Draft Topics</div>
                    <div class="alert-subtitle">{{ $draftTopics ?? 0 }} topic{{ ($draftTopics ?? 0) > 1 ? 's' : '' }} in draft status</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('teacher.topics.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($topics->total() ?? $topics->count()) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-link">
                View all topics <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('teacher.topics.index') }}?status=published" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Published</div>
                    <div class="stat-number">{{ number_format($publishedTopics ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="stat-link">
                View published <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('teacher.topics.index') }}?has_video=true" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">With Video</div>
                    <div class="stat-number">{{ number_format($topicsWithVideo ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-video"></i>
                </div>
            </div>
            <div class="stat-link">
                View with video <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('teacher.topics.index') }}?status=draft" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Drafts</div>
                    <div class="stat-number">{{ number_format($draftTopics ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-link">
                View drafts <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Topics List Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chalkboard" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        My Topics
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search topics..." id="search-topics">
                        </div>
                        <a href="{{ route('teacher.topics.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Create Topic
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

                    @if($topics->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h3 class="empty-title">No topics yet</h3>
                            <p class="empty-text">You haven't created any topics. Start building your content by adding the first topic.</p>
                            <a href="{{ route('teacher.topics.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i>
                                Create Your First Topic
                            </a>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                Topics organize content and can contain videos and learning materials
                            </div>
                        </div>
                    @else
                        <!-- Topics List -->
                        <div class="table-responsive">
                            <table class="topics-table" id="topics-table">
                                <thead>
                                    <tr>
                                        <th>Topic Title</th>
                                        <th class="hide-on-mobile">Status</th>
                                        <th class="hide-on-tablet">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topics as $topic)
                                    <tr class="clickable-row" 
                                        data-href="{{ route('teacher.topics.show', Crypt::encrypt($topic->id)) }}"
                                        data-title="{{ strtolower($topic->title) }}">
                                        <td>
                                            <div class="topic-info-cell">
                                                <div class="topic-icon topic-{{ ($loop->index % 3) + 1 }}">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <div class="topic-details">
                                                    <div class="topic-name">{{ $topic->title }}</div>
                                                    @if($topic->course)
                                                    <div class="topic-course-indicator">
                                                        <i class="fas fa-book"></i>
                                                        {{ $topic->course->title }}
                                                    </div>
                                                    @endif
                                                    @if($topic->video_link)
                                                    <div class="topic-video-indicator">
                                                        <i class="fas fa-video"></i>
                                                        Has video content
                                                    </div>
                                                    @endif
                                                    <div class="topic-mobile-info">
                                                        <div class="status-mobile">
                                                            @if($topic->is_published)
                                                            <span class="item-badge badge-success">
                                                                <i class="fas fa-check-circle"></i>
                                                                Published
                                                            </span>
                                                            @else
                                                            <span class="item-badge badge-warning">
                                                                <i class="fas fa-clock"></i>
                                                                Draft
                                                            </span>
                                                            @endif
                                                        </div>
                                                        <div class="date-mobile">
                                                            {{ $topic->created_at->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hide-on-mobile">
                                            @if($topic->is_published)
                                                <span class="item-badge badge-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Published
                                                </span>
                                            @else
                                                <span class="item-badge badge-warning">
                                                    <i class="fas fa-clock"></i>
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="hide-on-tablet">
                                            <div class="created-date">{{ $topic->created_at->format('M d, Y') }}</div>
                                            <div class="created-ago">{{ $topic->created_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($topics instanceof \Illuminate\Pagination\AbstractPaginator && $topics->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $topics->firstItem() }} to {{ $topics->lastItem() }} of {{ $topics->total() }} entries
                    </div>
                    <div class="pagination-links">
                        @if($topics->onFirstPage())
                        <span class="pagination-btn disabled">Previous</span>
                        @else
                        <a href="{{ $topics->previousPageUrl() }}" class="pagination-btn">Previous</a>
                        @endif
                        
                        @foreach(range(1, min(5, $topics->lastPage())) as $page)
                            @if($page == $topics->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                            @else
                            <a href="{{ $topics->url($page) }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($topics->hasMorePages())
                        <a href="{{ $topics->nextPageUrl() }}" class="pagination-btn">Next</a>
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
                        <a href="{{ route('teacher.topics.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create New Topic</div>
                                <div class="action-subtitle">Add learning material</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('teacher.courses.index') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">My Courses</div>
                                <div class="action-subtitle">Manage your courses</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <button id="export-csv" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-file-export"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Export Topics</div>
                                <div class="action-subtitle">Download as CSV</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Topic Statistics Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Topic Statistics
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <a href="{{ route('teacher.topics.index') }}?month={{ now()->format('Y-m') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Topics This Month</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $topicsThisMonth ?? 0 }}</div>
                        </a>
                        
                        <a href="{{ route('teacher.topics.index') }}?status=published" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Published Topics</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $publishedTopics ?? 0 }}</div>
                        </a>
                        
                        <a href="{{ route('teacher.topics.index') }}?status=draft" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Draft Topics</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $draftTopics ?? 0 }}</div>
                        </a>
                        
                        <a href="{{ route('teacher.topics.index') }}?has_video=true" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Topics with Video</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ $topicsWithVideo ?? 0 }}</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            My Topics • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>

    <!-- Hidden Print Content -->
    <div id="print-content" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #4f46e5; margin-bottom: 5px;">My Topics Report</h1>
                <p style="color: #666; margin-bottom: 10px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
                <hr style="border: 1px solid #e5e7eb; margin: 20px 0;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;">Topic Statistics Summary</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #eef2ff; padding: 15px; border-radius: 8px; border: 1px solid #c7d2fe;">
                        <div style="font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px;">{{ $topics->total() ?? $topics->count() }}</div>
                        <div style="font-size: 14px; color: #4f46e5;">Total Topics</div>
                    </div>
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 24px; font-weight: bold; color: #d97706; margin-bottom: 5px;">{{ $publishedTopics ?? 0 }}</div>
                        <div style="font-size: 14px; color: #d97706;">Published Topics</div>
                    </div>
                    <div style="background: #fee2e2; padding: 15px; border-radius: 8px; border: 1px solid #fecaca;">
                        <div style="font-size: 24px; font-weight: bold; color: #dc2626; margin-bottom: 5px;">{{ $draftTopics ?? 0 }}</div>
                        <div style="font-size: 14px; color: #dc2626;">Draft Topics</div>
                    </div>
                    <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd;">
                        <div style="font-size: 24px; font-weight: bold; color: #0284c7; margin-bottom: 5px;">{{ $topicsWithVideo ?? 0 }}</div>
                        <div style="font-size: 14px; color: #0284c7;">Topics with Video</div>
                    </div>
                </div>
            </div>
            
            <h2 style="color: #333; margin-bottom: 15px;">Topic List</h2>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Topic Title</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Course</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Status</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Has Video</th>
                        <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: bold; color: #374151;">Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                    <tr>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $topic->title }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $topic->course->title ?? 'Not assigned' }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">
                            @if($topic->is_published)
                                <span style="color: #059669; font-weight: 500;">Published</span>
                            @else
                                <span style="color: #d97706; font-weight: 500;">Draft</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">
                            @if($topic->video_link)
                                <span style="color: #059669;">Yes</span>
                            @else
                                <span style="color: #6b7280;">No</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $topic->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="color: #6b7280; font-size: 14px;">
                    Total Topics: {{ $topics->total() ?? $topics->count() }} | 
                    Generated by: {{ Auth::user()->f_name }} {{ Auth::user()->l_name }} | 
                    Page 1 of 1
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-index.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make rows clickable
        const clickableRows = document.querySelectorAll('.clickable-row');
        
        clickableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't redirect if user clicked on a link or button
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
            
            // Add hover effect
            row.style.cursor = 'pointer';
        });

        // Search functionality
        const searchInput = document.getElementById('search-topics');
        const topicRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && topicRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                topicRows.forEach(row => {
                    const topicTitle = row.dataset.title || '';
                    
                    if (searchTerm === '' || topicTitle.includes(searchTerm)) {
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
                    <title>My Topics Report</title>
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
        document.getElementById('export-csv')?.addEventListener('click', function() {
            const table = document.getElementById('topics-table');
            const rows = table.querySelectorAll('tr');
            const csv = [];
            
            // Add headers
            const headers = ['Topic Title', 'Status', 'Created Date'];
            csv.push(headers.join(','));
            
            // Add data rows
            table.querySelectorAll('tbody tr').forEach(row => {
                const cells = [];
                const columns = row.querySelectorAll('td');
                
                // Topic Title
                const topicNameDiv = columns[0].querySelector('.topic-name');
                cells.push(`"${topicNameDiv ? topicNameDiv.textContent.trim() : ''}"`);
                
                // Status
                let status = 'Unknown';
                const statusBadge = columns[1]?.querySelector('.item-badge');
                if (statusBadge) {
                    status = statusBadge.textContent.trim();
                } else {
                    const mobileStatus = columns[0].querySelector('.status-mobile .item-badge');
                    if (mobileStatus) {
                        status = mobileStatus.textContent.trim();
                    }
                }
                cells.push(`"${status}"`);
                
                // Created Date
                let createdDate = 'Data not available';
                if (columns[2]) {
                    const createdDateDiv = columns[2].querySelector('.created-date');
                    if (createdDateDiv) {
                        createdDate = createdDateDiv.textContent.trim();
                    }
                } else {
                    const mobileDate = columns[0].querySelector('.date-mobile');
                    if (mobileDate) {
                        createdDate = mobileDate.textContent.trim();
                    }
                }
                cells.push(`"${createdDate}"`);
                
                csv.push(cells.join(','));
            });
            
            // Create and download CSV file
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `my_topics_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Show success message
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: 'success',
                title: 'Topics exported successfully!',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        });

        // Show notifications from session
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'success',
                title: '{{ session('success') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: '{{ session('error') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
</script>
@endpush