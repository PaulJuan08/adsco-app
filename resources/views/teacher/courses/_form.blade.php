<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($editing ?? false)
        @method('PUT')
    @endif

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Course Title <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" name="title"
               value="{{ old('title', $course->title ?? '') }}"
               required placeholder="e.g., Introduction to Programming"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('title')
            <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Course Code <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="course_code"
                   value="{{ old('course_code', $course->course_code ?? '') }}"
                   required placeholder="e.g., CS101"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('course_code')
                <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Credits <span style="color:#dc2626;">*</span>
            </label>
            <input type="number" name="credits" min="1" max="10"
                   value="{{ old('credits', $course->credits ?? 3) }}"
                   required
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('credits')
                <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Description</label>
        <textarea name="description" data-quill rows="3" placeholder="Course description..."
                  style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;resize:vertical;outline:none;box-sizing:border-box;">{{ old('description', $course->description ?? '') }}</textarea>
    </div>

    <div style="margin-bottom:1.5rem;">
        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;font-weight:500;color:#374151;">
            <input type="checkbox" name="is_published" value="1"
                   {{ old('is_published', $course->is_published ?? false) ? 'checked' : '' }}
                   style="accent-color:#552b20;width:16px;height:16px;">
            Publish course (visible to students)
        </label>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.75rem;border-top:1px solid #f0ebe8;">
        <button type="button" onclick="closeCrudModal()"
                style="padding:.5rem 1.2rem;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.875rem;font-weight:600;cursor:pointer;">
            Cancel
        </button>
        <button type="submit"
                style="padding:.5rem 1.2rem;border-radius:8px;background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border:none;font-size:.875rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-save"></i> {{ ($editing ?? false) ? 'Update Course' : 'Create Course' }}
        </button>
    </div>
</form>
