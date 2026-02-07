@extends('layouts.admin')

@section('title', isset($topic) ? 'Edit Topic' : 'Create Topic')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>{{ isset($topic) ? 'Edit Topic' : 'Create Topic' }}</h1>
        <p>{{ isset($topic) ? 'Update topic information' : 'Add a new topic' }}</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ isset($topic) ? 'Edit Topic' : 'Create New Topic' }}</h2>
        <a href="{{ route('admin.topics.index') }}" 
           style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to Topics
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ isset($topic) ? route('admin.topics.update', Crypt::encrypt($topic->id)) : route('admin.topics.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($topic))
                @method('PUT')
            @endif
            
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
                       value="{{ old('title', $topic->title ?? '') }}"
                       required
                       placeholder="Enter topic title"
                       style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
            </div>

            <!-- PDF File Upload -->
            <div style="margin-bottom: 1.5rem;">
                <label for="pdf_file" class="form-label">Upload PDF Document (Optional)</label>
                <input type="file" 
                    id="pdf_file" 
                    name="pdf_file" 
                    accept=".pdf"
                    style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; background: white; @error('pdf_file') border-color: var(--danger); @enderror">
                <div style="color: var(--secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                    <i class="fas fa-info-circle"></i> Maximum file size: 10MB. PDF files only.
                </div>
                
                <!-- Show current PDF if exists -->
                @if(isset($topic) && $topic->pdf_file)
                <div style="margin-top: 1rem; padding: 0.75rem; background: #f8fafc; border-radius: 6px; border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-file-pdf" style="color: #dc2626;"></i>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; margin-bottom: 2px;">Current PDF:</div>
                            <div style="font-size: 0.875rem; color: #6b7280; word-break: break-all;">
                                {{ basename($topic->pdf_file) }}
                            </div>
                        </div>
                        <div style="display: flex; gap: 4px;">
                            <a href="{{ asset($topic->pdf_file) }}" target="_blank" 
                            style="padding: 6px 12px; background: var(--primary); color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem;">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ asset($topic->pdf_file) }}" download 
                            style="padding: 6px 12px; background: transparent; color: var(--primary); border: 1px solid var(--primary); border-radius: 4px; text-decoration: none; font-size: 0.75rem;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border); font-size: 0.75rem; color: #6b7280;">
                        <i class="fas fa-exclamation-triangle"></i> Uploading a new PDF will replace the current one.
                    </div>
                </div>
                @endif
                
                @error('pdf_file')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
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
                       value="{{ old('video_link', $topic->video_link ?? '') }}"
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
                       value="{{ old('attachment', $topic->attachment ?? '') }}"
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
                               {{ (old('is_published', $topic->is_published ?? 1) == 1) ? 'checked' : '' }}>
                        <span>Published</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" 
                               name="is_published" 
                               value="0" 
                               {{ (old('is_published', $topic->is_published ?? 1) == 0) ? 'checked' : '' }}>
                        <span>Draft</span>
                    </label>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('admin.topics.index') }}" 
                   style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500;">
                    Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                    {{ isset($topic) ? 'Update Topic' : 'Create Topic' }}
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