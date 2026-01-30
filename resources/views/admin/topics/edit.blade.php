@extends('layouts.admin')

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
        <h2 class="card-title">Edit Topic Information</h2>
        <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to View
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('admin.topics.update', Crypt::encrypt($topic->id)) }}" method="POST">
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
            
            <div style="margin-bottom: 1.5rem;">
                <label for="title" class="form-label">Topic Title *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $topic->title) }}" 
                       required
                       placeholder="e.g., Introduction to Variables"
                       style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('title') border-color: var(--danger); @enderror">
                @error('title')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="content" class="form-label">Content *</label>
                <textarea id="content" 
                          name="content" 
                          rows="8"
                          required
                          placeholder="Enter the main content for this topic..."
                          style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical; @error('content') border-color: var(--danger); @enderror">{{ old('content', $topic->content) }}</textarea>
                @error('content')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Publish Status -->
            <div style="margin-bottom: 1.5rem;">
                <label for="is_published" class="form-label">Publish Status</label>
                @error('is_published')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="video_link" class="form-label">Video Link (Optional)</label>
                <input type="url" 
                       id="video_link" 
                       name="video_link" 
                       value="{{ old('video_link', $topic->video_link) }}"
                       placeholder="https://youtube.com/watch?v=..."
                       style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('video_link') border-color: var(--danger); @enderror">
                @error('video_link')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
                
                @if($topic->video_link)
                <div style="margin-top: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 8px; color: var(--secondary); font-size: 0.875rem;">
                        <i class="fas fa-info-circle"></i>
                        <span>Current video: 
                            <a href="{{ $topic->video_link }}" target="_blank" style="color: var(--primary);">
                                {{ Str::limit($topic->video_link, 50) }}
                            </a>
                        </span>
                    </div>
                </div>
                @endif
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to delete this topic? This action cannot be undone.')"
                            style="padding: 10px 20px; background: transparent; color: var(--danger); border: 1px solid var(--danger); border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-trash"></i> Delete Topic
                    </button>
                </form>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" 
                       style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" 
                            style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-save"></i> Update Topic
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }
    
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
    
    input, select, textarea {
        transition: border-color 0.15s ease-in-out;
    }
    
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
</style>

@push('scripts')
<script>
    // Add confirmation for delete button in form
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButton = document.querySelector('button[type="submit"][style*="color: var(--danger)"]');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this topic? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
@endsection