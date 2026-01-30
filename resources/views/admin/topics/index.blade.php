@extends('layouts.admin')

@section('title', 'Topics - Admin Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>Topics</h1>
        <p>Manage and organize learning topics</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $topics->total() ?? $topics->count() }}</div>
                <div class="stat-label">Total Topics</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $publishedTopics ?? 0 }}</div>
                <div class="stat-label">Published Topics</div>
            </div>
            <div class="stat-icon icon-courses">
                <i class="fas fa-eye"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $draftTopics ?? 0 }}</div>
                <div class="stat-label">Draft Topics</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $topicsWithVideo ?? 0 }}</div>
                <div class="stat-label">Topics with Video</div>
            </div>
            <div class="stat-icon icon-users">
                <i class="fas fa-video"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Topics List Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">All Topics</div>
            <div class="d-flex gap-2 align-items-center">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--secondary);"></i>
                    <input type="text" class="search-input" placeholder="Search topics..." 
                           style="padding: 8px 12px 8px 36px; border: 1px solid var(--border); border-radius: 6px; width: 200px;">
                </div>
                <a href="{{ route('admin.topics.create') }}" class="view-all" style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-plus-circle"></i>
                    Add Topic
                </a>
            </div>
        </div>
        
        @if(session('success'))
        <div style="margin: 0 1.5rem 1.5rem; padding: 12px; background: #dcfce7; color: #065f46; border-radius: 8px; font-size: 0.875rem;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="margin: 0 1.5rem 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.875rem;">
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
            {{ session('error') }}
        </div>
        @endif

        @if($topics->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3 style="color: var(--dark); margin-bottom: 12px;">No topics yet</h3>
            <p style="color: var(--secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                You haven't created any topics. Start building your content by adding the first topic.
            </p>
            <a href="{{ route('admin.topics.create') }}" 
               style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-plus-circle"></i>
                Create Your First Topic
            </a>
            <div style="margin-top: 20px; color: var(--secondary); font-size: 0.875rem;">
                <i class="fas fa-lightbulb" style="margin-right: 6px;"></i>
                Topics organize content and can contain videos and learning materials
            </div>
        </div>
        @else
        <!-- Topics List -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;" id="topics-table">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid var(--border);">
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">
                            Topic Title
                        </th>
                        <!-- <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Description</th> -->
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Status</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Created</th>
                        <th style="padding: 16px; text-align: left; font-weight: 600; color: var(--secondary); font-size: 0.875rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <div class="course-name">{{ $topic->title }}</div>
                                    @if($topic->video_link)
                                    <div style="display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                        <i class="fas fa-video" style="color: #dc2626; font-size: 12px;"></i>
                                        <span style="font-size: 0.75rem; color: var(--secondary);">Has video</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <!-- <td style="padding: 16px;">
                            <div class="course-desc">{{ Str::limit($topic->description, 80) }}</div>
                        </td> -->
                        <td style="padding: 16px;">
                            @if($topic->is_published)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                    Published
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fas fa-clock" style="font-size: 10px;"></i>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 500; color: var(--dark);">{{ $topic->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">{{ $topic->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" title="View" style="padding: 8px; background: #e0e7ff; color: var(--primary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" title="Edit" style="padding: 8px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; text-decoration: none;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this topic?')"
                                            style="padding: 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 6px; cursor: pointer;">
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
        
        <!-- Pagination -->
        @if($topics instanceof \Illuminate\Pagination\AbstractPaginator && $topics->hasPages())
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
            <div style="color: var(--secondary); font-size: 0.875rem;">
                Showing {{ $topics->firstItem() }} to {{ $topics->lastItem() }} of {{ $topics->total() }} entries
            </div>
            <div style="display: flex; gap: 8px;">
                @if($topics->onFirstPage())
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Previous
                </span>
                @else
                <a href="{{ $topics->previousPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Previous
                </a>
                @endif
                
                @foreach(range(1, min(5, $topics->lastPage())) as $page)
                    @if($page == $topics->currentPage())
                    <span style="padding: 8px 12px; background: var(--primary); color: white; border-radius: 6px; font-size: 0.875rem;">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $topics->url($page) }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach
                
                @if($topics->hasMorePages())
                <a href="{{ $topics->nextPageUrl() }}" style="padding: 8px 12px; background: var(--primary-light); color: var(--primary); border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    Next
                </a>
                @else
                <span style="padding: 8px 12px; background: #f3f4f6; color: var(--secondary); border-radius: 6px; font-size: 0.875rem;">
                    Next
                </span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
    
    <!-- Quick Actions Sidebar -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div style="padding: 0.5rem;">
                <a href="{{ route('admin.topics.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Add New Topic</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Create a new learning topic</div>
                    </div>
                </a>
                <a href="{{ route('admin.assignments.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #fce7f3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #db2777;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Create Assignment</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Add assignment to topic</div>
                    </div>
                </a>
                <a href="{{ route('admin.quizzes.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: var(--dark); transition: background 0.3s;">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Create Quiz</div>
                        <div style="font-size: 0.75rem; color: var(--secondary);">Add quiz to topic</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Topic Statistics</div>
            </div>
            <div style="padding: 0.5rem;">
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Topics This Month</span>
                        <span style="font-weight: 600;">{{ $topicsThisMonth ?? 0 }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Published Topics</span>
                        <span style="font-weight: 600;">{{ $publishedTopics ?? 0 }}</span>
                    </div>
                </div>
                <div style="padding: 12px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Draft Topics</span>
                        <span style="font-weight: 600;">{{ $draftTopics ?? 0 }}</span>
                    </div>
                </div>
                <div style="padding: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--secondary); font-size: 0.875rem;">Topics with Video</span>
                        <span style="font-weight: 600;">{{ $topicsWithVideo ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#topics-table tbody tr');
            
            rows.forEach(row => {
                const topicTitle = row.querySelector('.course-name').textContent.toLowerCase();
                const topicDesc = row.querySelector('.course-desc')?.textContent?.toLowerCase() || '';
                
                if (topicTitle.includes(searchTerm) || topicDesc.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
</script>
@endpush
@endsection