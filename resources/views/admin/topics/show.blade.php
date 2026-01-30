@extends('layouts.admin')

@section('title', 'View Topic - ' . $topic->title)

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>{{ $topic->title }}</h1>
        <p>View topic details</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Topic Details -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h2 class="card-title">Topic Details</h2>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" 
                   style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--primary); color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.topics.index') }}" 
                   style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div style="padding: 1.5rem;">
            <!-- Status Badge -->
            <div style="margin-bottom: 1.5rem;">
                @if($topic->is_published)
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #dcfce7; color: #166534; border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-check-circle"></i> Published
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #fef3c7; color: #92400e; border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-clock"></i> Draft
                    </span>
                @endif
            </div>
            
            <!-- Topic Title -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Topic Title</label>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border); font-size: 1.125rem; font-weight: 600; color: var(--dark);">
                    {{ $topic->title }}
                </div>
            </div>
           
            <!-- Content -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Content</label>
                <div style="padding: 1.5rem; background: white; border-radius: 8px; border: 1px solid var(--border); line-height: 1.6; color: var(--dark);">
                    {!! nl2br(e($topic->content)) !!}
                </div>
            </div>
            
            <!-- Video Link -->
            @if($topic->video_link)
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Video Link</label>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <i class="fas fa-video" style="color: #dc2626;"></i>
                        <a href="{{ $topic->video_link }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                            {{ $topic->video_link }}
                        </a>
                    </div>
                    
                    @if(str_contains($topic->video_link, 'youtube.com') || str_contains($topic->video_link, 'youtu.be'))
                        <!-- YouTube Embed -->
                        @php
                            $videoId = '';
                            if (str_contains($topic->video_link, 'youtube.com/watch?v=')) {
                                $videoId = substr($topic->video_link, strpos($topic->video_link, 'v=') + 2);
                            } elseif (str_contains($topic->video_link, 'youtu.be/')) {
                                $videoId = substr($topic->video_link, strrpos($topic->video_link, '/') + 1);
                            }
                            $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
                        @endphp
                        
                        @if($embedUrl)
                        <div style="margin-top: 1rem;">
                            <iframe 
                                width="100%" 
                                height="400" 
                                src="{{ $embedUrl }}" 
                                title="YouTube video player" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                style="border-radius: 8px;">
                            </iframe>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Metadata -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Created</label>
                    <div style="font-weight: 500; color: var(--dark);">
                        {{ $topic->created_at->format('F d, Y h:i A') }}
                    </div>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Last Updated</label>
                    <div style="font-weight: 500; color: var(--dark);">
                        {{ $topic->updated_at->format('F d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Actions</h2>
        </div>
        <div style="padding: 1.5rem;">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="{{ route('admin.topics.edit', Crypt::encrypt($topic->id)) }}" 
                   style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #e0e7ff; color: var(--primary); border-radius: 8px; text-decoration: none; font-weight: 500;">
                    <div style="width: 36px; height: 36px; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <span>Edit Topic</span>
                </a>
                
                <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" style="display: block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to delete this topic? This action cannot be undone.')"
                            style="width: 100%; display: flex; align-items: center; gap: 12px; padding: 12px; background: #fee2e2; color: var(--danger); border: none; border-radius: 8px; text-decoration: none; font-weight: 500; cursor: pointer;">
                        <div style="width: 36px; height: 36px; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-trash"></i>
                        </div>
                        <span>Delete Topic</span>
                    </button>
                </form>
                
                <a href="{{ route('admin.topics.index') }}" 
                   style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f3f4f6; color: var(--secondary); border-radius: 8px; text-decoration: none; font-weight: 500;">
                    <div style="width: 36px; height: 36px; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <span>Back to Topics</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.5rem;
    }
    
    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection