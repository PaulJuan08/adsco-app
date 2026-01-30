@extends('layouts.admin')

@section('title', 'Create New Topic')

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Create New Topic</h1>
        <p>Add a new learning topic</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Topic Information</h2>
        <a href="{{ route('admin.topics.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to Topics
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('admin.topics.store') }}" method="POST">
            @csrf
            
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
                       value="{{ old('title') }}" 
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
                          style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical; @error('content') border-color: var(--danger); @enderror">{{ old('content') }}</textarea>
                @error('content')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="video_link" class="form-label">Video Link (Optional)</label>
                <input type="url" 
                       id="video_link" 
                       name="video_link" 
                       value="{{ old('video_link') }}"
                       placeholder="https://youtube.com/watch?v=..."
                       style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; @error('video_link') border-color: var(--danger); @enderror">
                @error('video_link')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('admin.topics.index') }}" 
                   style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-save"></i> Create Topic
                </button>
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
    
    input, textarea {
        transition: border-color 0.15s ease-in-out;
    }
    
    input:focus, textarea:focus {
        outline: none;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
</style>
@endsection