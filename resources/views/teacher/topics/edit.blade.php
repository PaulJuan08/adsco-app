@extends('layouts.teacher')

@section('title', 'Edit Topic - ' . $topic->title)

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Edit Topic</h1>
        <p>Update topic information</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit Topic - {{ $topic->title }}</h2>
        <a href="{{ route('teacher.topics.show', Crypt::encrypt($topic->id)) }}" 
           style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to View
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('teacher.topics.update', Crypt::encrypt($topic->id)) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if($errors->any())
            <div style="margin: 0 0 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.875rem;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Title -->
            <div style="margin-bottom: 1.5rem;">
                <label for="title" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Topic Title *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $topic->title) }}"
                       required
                       placeholder="Enter topic title"
                       style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
            </div>
            
            <!-- Video Link -->
            <div style="margin-bottom: 1.5rem;">
                <label for="video_link" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">
                    Video Link (Optional)
                    <span style="font-weight: normal; color: var(--secondary); font-size: 0.875rem;">
                        - Enter YouTube, Vimeo, or direct video URL
                    </span>
                </label>
                <input type="text" 
                       id="video_link" 
                       name="video_link" 
                       value="{{ old('video_link', $topic->video_link) }}"
                       placeholder="https://www.youtube.com/watch?v=..."
                       style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
            </div>
            
            <!-- Attachment -->
            <div style="margin-bottom: 1.5rem;">
                <label for="attachment" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">
                    Attachment Link (Optional)
                    <span style="font-weight: normal; color: var(--secondary); font-size: 0.875rem;">
                        - Enter Google Drive, Dropbox, or direct file URL
                    </span>
                </label>
                <input type="text" 
                       id="attachment" 
                       name="attachment" 
                       value="{{ old('attachment', $topic->attachment) }}"
                       placeholder="https://drive.google.com/file/... or https://example.com/files/document.pdf"
                       style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
            </div>
            
            <!-- Publish Status -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Publish Status</label>
                <div style="display: flex; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" 
                               name="is_published" 
                               value="1" 
                               {{ (old('is_published', $topic->is_published) == 1) ? 'checked' : '' }}>
                        <span>Published</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" 
                               name="is_published" 
                               value="0" 
                               {{ (old('is_published', $topic->is_published) == 0) ? 'checked' : '' }}>
                        <span>Draft</span>
                    </label>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('teacher.topics.show', Crypt::encrypt($topic->id)) }}" 
                   style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500;">
                    Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                    Update Topic
                </button>
            </div>
        </form>
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
    
    input:focus, textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
</style>
@endsection