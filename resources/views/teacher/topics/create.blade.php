@extends('layout.teacher')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Create Topic</h1>
        <a href="{{ route('teacher.topics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('teacher.topics.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course *</label>
                            <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} ({{ $course->course_code }})
                                </option>
                                @endforeach
                            </select>
                            @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" name="title" id="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="order" class="form-label">Order *</label>
                            <input type="number" name="order" id="order" 
                                   class="form-control @error('order') is-invalid @enderror" 
                                   value="{{ old('order', 1) }}" min="1" required>
                            @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment URL</label>
                            <input type="text" name="attachment" id="attachment" 
                                   class="form-control @error('attachment') is-invalid @enderror" 
                                   value="{{ old('attachment') }}" 
                                   placeholder="https://example.com/file.pdf">
                            @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Content *</label>
                    <textarea name="content" id="content" 
                              class="form-control @error('content') is-invalid @enderror" 
                              rows="10" required>{{ old('content') }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_published" id="is_published" 
                               class="form-check-input" value="1" 
                               {{ old('is_published') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">Publish immediately</label>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Topic
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection