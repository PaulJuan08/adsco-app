@extends('layouts.teacher')

@section('title', 'Quizzes')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Quizzes</h1>
        <p>Manage and organize assessment quizzes</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="quick-stats">
    <div class="stat-item">
        <div class="stat-number">{{ $quizzes->total() }}</div>
        <div class="stat-label">Total Quizzes</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">{{ $quizzes->where('is_published', true)->count() }}</div>
        <div class="stat-label">Published</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Quiz List</h2>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search quizzes..." id="search-input">
            </div>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Quiz
            </a>
        </div>
    </div>

    @if($quizzes->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-question-circle"></i>
        </div>
        <h3>No quizzes yet</h3>
        <p>Get started by creating your first quiz</p>
        <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create Quiz
        </a>
    </div>
    @else
    <div class="table-container">
        <table class="quiz-table">
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Questions</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizzes as $quiz)
                <tr class="quiz-row" data-search="{{ strtolower($quiz->title . ' ' . $quiz->description) }}">
                    <td>
                        <div class="quiz-info">
                            <div class="quiz-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div>
                                <div class="quiz-title">{{ $quiz->title }}</div>
                                <div class="quiz-desc">{{ Str::limit($quiz->description, 60) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge">{{ $quiz->total_questions ?? 0 }} Qs</span>
                    </td>
                    <td>
                        @if($quiz->is_published)
                            <span class="status published">
                                <i class="fas fa-circle"></i> Public
                            </span>
                        @else
                            <span class="status draft">
                                <i class="fas fa-circle"></i> Private
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="date">{{ $quiz->created_at->format('M d, Y') }}</div>
                        <div class="time">{{ $quiz->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('teacher.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                               class="btn-icon view" title="View Quiz">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('teacher.quizzes.edit', Crypt::encrypt($quiz->id)) }}" 
                               class="btn-icon edit" title="Edit Quiz">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('teacher.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon delete" title="Delete Quiz">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($quizzes->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $quizzes->firstItem() }}-{{ $quizzes->lastItem() }} of {{ $quizzes->total() }}
        </div>
        <div class="pagination-links">
            {{ $quizzes->links() }}
        </div>
    </div>
    @endif
    @endif
</div>

<style>
    /* Quick Stats */
    .quick-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-item {
        flex: 1;
        background: white;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    /* Card Header */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }
    
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    /* Search Box */
    .search-box {
        position: relative;
        min-width: 250px;
    }
    
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--secondary);
    }
    
    .search-box input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn:hover {
        background: #4f46e5;
    }
    
    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }
    
    .empty-icon {
        font-size: 3rem;
        color: var(--secondary);
        opacity: 0.5;
        margin-bottom: 1rem;
    }
    
    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: var(--secondary);
        margin-bottom: 1.5rem;
    }
    
    /* Table */
    .table-container {
        overflow-x: auto;
    }
    
    .quiz-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .quiz-table thead {
        background: #f8fafc;
        border-bottom: 2px solid var(--border);
    }
    
    .quiz-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        font-size: 0.875rem;
    }
    
    .quiz-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .quiz-table tbody tr:hover {
        background: #f9fafb;
    }
    
    /* Quiz Info */
    .quiz-info {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .quiz-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    
    .quiz-title {
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }
    
    .quiz-desc {
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    /* Badges */
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #e0e7ff;
        color: var(--primary);
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Status */
    .status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status i {
        font-size: 0.5rem;
    }
    
    .status.published {
        color: #059669;
    }
    
    .status.draft {
        color: #ef4444;
    }
    
    /* Date */
    .date {
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }
    
    .time {
        font-size: 0.75rem;
        color: var(--secondary);
        margin-top: 0.125rem;
    }
    
    /* Action Buttons with Colors */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
    }
    
    .btn-icon.view {
        color: #3b82f6;
    }
    
    .btn-icon.edit {
        color: #f59e0b;
    }
    
    .btn-icon.delete {
        color: #ef4444;
    }
    
    .btn-icon.view:hover {
        background: #dbeafe;
        transform: translateY(-1px);
    }
    
    .btn-icon.edit:hover {
        background: #fef3c7;
        transform: translateY(-1px);
    }
    
    .btn-icon.delete:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }
    
    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
    }
    
    .pagination-info {
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .quick-stats {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .search-box {
            width: 100%;
            min-width: unset;
        }
        
        .action-buttons {
            flex-wrap: wrap;
        }
        
        .quiz-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .quiz-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple search functionality
        const searchInput = document.getElementById('search-input');
        const quizRows = document.querySelectorAll('.quiz-row');
        
        if (searchInput && quizRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                quizRows.forEach(row => {
                    const searchData = row.dataset.search || '';
                    
                    if (searchTerm === '' || searchData.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection