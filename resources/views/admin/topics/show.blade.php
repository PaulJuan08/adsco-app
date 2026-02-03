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
            
            <!-- Video Link -->
            @if($topic->video_link)
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Video Link</label>
                <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <i class="fas fa-video" style="color: #dc2626; font-size: 1.25rem;"></i>
                        <a href="{{ $topic->video_link }}" target="_blank" 
                           style="color: var(--primary); text-decoration: none; font-weight: 500; font-size: 1rem;">
                            {{ $topic->video_link }}
                        </a>
                    </div>
                    
                    <!-- Try to embed video if it's from supported platforms -->
                    @php
                        $embedUrl = null;
                        
                        // Check for YouTube
                        if (str_contains($topic->video_link, 'youtube.com/watch?v=')) {
                            $videoId = substr($topic->video_link, strpos($topic->video_link, 'v=') + 2);
                            $videoId = strtok($videoId, '&'); // Remove any additional parameters
                            $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
                        } 
                        // Check for YouTube short links
                        elseif (str_contains($topic->video_link, 'youtu.be/')) {
                            $videoId = substr($topic->video_link, strrpos($topic->video_link, '/') + 1);
                            $videoId = strtok($videoId, '?'); // Remove any parameters
                            $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
                        }
                        // Check for Vimeo
                        elseif (str_contains($topic->video_link, 'vimeo.com/')) {
                            $videoId = substr($topic->video_link, strrpos($topic->video_link, '/') + 1);
                            $videoId = strtok($videoId, '?'); // Remove any parameters
                            $embedUrl = $videoId ? "https://player.vimeo.com/video/{$videoId}" : null;
                        }
                    @endphp
                    
                    @if($embedUrl)
                    <div style="margin-top: 1rem;">
                        <iframe 
                            width="100%" 
                            height="400" 
                            src="{{ $embedUrl }}" 
                            title="Video Player" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                            style="border-radius: 8px;">
                        </iframe>
                    </div>
                    @else
                    <div style="margin-top: 0.5rem;">
                        <p style="color: #6b7280; font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i> Video cannot be embedded. Click the link above to watch.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Attachment -->
            @if($topic->attachment)
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary); font-size: 0.875rem;">Attachment</label>
                <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                    @php
                        $icon = \App\Http\Controllers\Admin\TopicController::getFileIcon($topic->attachment);
                        $color = \App\Http\Controllers\Admin\TopicController::getFileColor($topic->attachment);
                        $type = \App\Http\Controllers\Admin\TopicController::getFileType($topic->attachment);
                    @endphp
                    
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: white; border-radius: 8px; border: 1px solid var(--border);">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: {{ $color }}20; display: flex; align-items: center; justify-content: center;">
                            <i class="{{ $icon }}" style="font-size: 1.5rem; color: {{ $color }};"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--dark); margin-bottom: 4px;">
                                {{ $type }}
                            </div>
                            <a href="{{ $topic->attachment }}" target="_blank" 
                               style="color: var(--primary); text-decoration: none; font-size: 0.875rem; word-break: break-all;">
                                {{ $topic->attachment }}
                            </a>
                        </div>
                        <div>
                            <a href="{{ $topic->attachment }}" target="_blank" 
                               style="padding: 8px 16px; background: var(--primary); color: white; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.875rem;">
                                <i class="fas fa-external-link-alt"></i> Open
                            </a>
                        </div>
                    </div>
                    
                    <div style="margin-top: 1rem; padding: 0.75rem; background: white; border-radius: 6px; border: 1px solid var(--border);">
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem;">
                            <i class="fas fa-info-circle"></i> How to open this file:
                        </h4>
                        <ul style="margin: 0; padding-left: 1.5rem; color: #6b7280; font-size: 0.875rem; line-height: 1.5;">
                            <li>Click the "Open" button above to view the file</li>
                            <li>If it's a Google Drive link, you may need to sign in to your Google account</li>
                            <li>For PDF files, they will open in your browser or download</li>
                            <li>For Office documents, they may open in Office Online or download</li>
                        </ul>
                    </div>
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
    
    /* Video embed responsiveness */
    iframe {
        max-width: 100%;
    }
    
    /* File attachment styles */
    .file-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .file-pdf {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
    }
    
    .file-word {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
    }
    
    .file-excel {
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
    }
    
    .file-powerpoint {
        background: rgba(217, 119, 6, 0.1);
        color: #d97706;
    }
    
    .file-image {
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
    }
</style>
@endsection