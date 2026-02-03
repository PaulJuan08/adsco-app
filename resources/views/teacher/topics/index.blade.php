@extends('layouts.teacher')

@section('title', 'Topics')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Topics</h1>
        <p>Manage learning materials for your courses</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Left Column - Topics List -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="card-title">All Topics</h2>
                <a href="{{ route('teacher.topics.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-plus"></i> Create Topic
                </a>
            </div>
            
            <div style="padding: 1.5rem;">
                @if(session('success'))
                <div style="margin-bottom: 1.5rem; padding: 0.75rem; background: #dcfce7; color: #065f46; border-radius: 6px; font-size: 0.875rem; border-left: 4px solid #10b981;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                    {{ session('success') }}
                </div>
                @endif
                
                @if($topics->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <th style="padding: 0.75rem; text-align: left; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Title</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Status</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Video</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Attachment</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Created</th>
                                <th style="padding: 0.75rem; text-align: left; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topics as $topic)
                            <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;">
                                <td style="padding: 0.75rem;">
                                    <div style="font-weight: 500; color: var(--dark);">{{ $topic->title }}</div>
                                    @if($topic->learning_outcomes)
                                    <div style="font-size: 0.75rem; color: var(--secondary); margin-top: 0.25rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ Str::limit($topic->learning_outcomes, 60) }}
                                    </div>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem;">
                                    @if($topic->is_published)
                                    <span style="padding: 0.25rem 0.75rem; background: #dcfce7; color: #065f46; border-radius: 20px; font-size: 0.75rem; font-weight: 500;">
                                        Published
                                    </span>
                                    @else
                                    <span style="padding: 0.25rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 0.75rem; font-weight: 500;">
                                        Draft
                                    </span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem;">
                                    @if($topic->video_link)
                                    <span style="color: var(--success);">
                                        <i class="fas fa-video"></i> Yes
                                    </span>
                                    @else
                                    <span style="color: var(--secondary);">
                                        <i class="fas fa-video-slash"></i> No
                                    </span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem;">
                                    @if($topic->attachment)
                                    <span style="color: var(--success);">
                                        <i class="fas fa-paperclip"></i> Yes
                                    </span>
                                    @else
                                    <span style="color: var(--secondary);">
                                        <i class="fas fa-times"></i> No
                                    </span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--secondary);">
                                    {{ $topic->created_at->format('M d, Y') }}
                                </td>
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <a href="{{ route('teacher.topics.show', Crypt::encrypt($topic->id)) }}" 
                                           style="padding: 0.25rem 0.5rem; background: #f3f4f6; color: var(--secondary); border-radius: 4px; text-decoration: none; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('teacher.topics.edit', Crypt::encrypt($topic->id)) }}" 
                                           style="padding: 0.25rem 0.5rem; background: #e0e7ff; color: var(--primary); border-radius: 4px; text-decoration: none; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                    {{ $topics->links() }}
                </div>
                @else
                <div style="text-align: center; padding: 3rem 1rem; color: var(--secondary);">
                    <i class="fas fa-folder-open" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                    <div style="font-size: 1rem; font-weight: 500; color: var(--secondary); margin-bottom: 0.5rem;">
                        No Topics Yet
                    </div>
                    <div style="font-size: 0.875rem; color: #9ca3af; margin-bottom: 1.5rem;">
                        Start by creating your first topic
                    </div>
                    <a href="{{ route('teacher.topics.create') }}" 
                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.5rem; background: var(--primary); color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                        <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Create First Topic
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column - Statistics -->
    <div>
        <!-- Stats Card -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h2 class="card-title">Topic Statistics</h2>
            </div>
            <div style="padding: 1rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="padding: 1rem; background: #f0f9ff; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #0369a1; margin-bottom: 0.25rem;">
                            {{ $publishedTopics }}
                        </div>
                        <div style="font-size: 0.75rem; color: #0c4a6e; font-weight: 500;">Published</div>
                    </div>
                    
                    <div style="padding: 1rem; background: #fef3c7; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #b45309; margin-bottom: 0.25rem;">
                            {{ $draftTopics }}
                        </div>
                        <div style="font-size: 0.75rem; color: #92400e; font-weight: 500;">Draft</div>
                    </div>
                    
                    <div style="padding: 1rem; background: #f0fdf4; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #15803d; margin-bottom: 0.25rem;">
                            {{ $topicsThisMonth }}
                        </div>
                        <div style="font-size: 0.75rem; color: #166534; font-weight: 500;">This Month</div>
                    </div>
                    
                    <div style="padding: 1rem; background: #e0e7ff; border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: #4f46e5; margin-bottom: 0.25rem;">
                            {{ $topicsWithVideo }}
                        </div>
                        <div style="font-size: 0.75rem; color: #3730a3; font-weight: 500;">With Video</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            <div style="padding: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="{{ route('teacher.topics.create') }}" 
                       style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; color: #374151; transition: all 0.2s;">
                        <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500; font-size: 0.875rem;">Create Topic</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Add new learning material</div>
                        </div>
                        <i class="fas fa-chevron-right" style="margin-left: auto; color: #9ca3af;"></i>
                    </a>
                    
                    <a href="{{ route('teacher.courses.index') }}" 
                       style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; color: #374151; transition: all 0.2s;">
                        <div style="width: 32px; height: 32px; background: #10b981; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500; font-size: 0.875rem;">My Courses</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Manage your courses</div>
                        </div>
                        <i class="fas fa1chevron-right" style="margin-left: auto; color: #9ca3af;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection