<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($editing ?? false)
        @method('PUT')
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div style="grid-column:1/-1;">
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
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Credits</label>
            <input type="number" name="credits" min="1" max="10"
                   value="{{ old('credits', $course->credits ?? 3) }}"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('credits')
                <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Description</label>
        <textarea name="description" rows="2" placeholder="Course description..."
                  style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;resize:vertical;outline:none;box-sizing:border-box;">{{ old('description', $course->description ?? '') }}</textarea>
    </div>

    @php
        // Pre-compute selected teacher IDs: primary + pivot, deduplicated
        $selectedTeacherIds = old('teacher_ids', array_unique(array_filter(
            array_merge(
                ($course->teacher_id ?? null) ? [$course->teacher_id] : [],
                ($assignedTeacherIds ?? [])
            )
        )));
    @endphp
    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Assign Teachers
            <span style="font-weight:400;color:#9ca3af;font-size:.75rem;"> (first selected becomes lead teacher)</span>
        </label>
        <div style="max-height:150px;overflow-y:auto;border:1.5px solid #e5e7eb;border-radius:8px;padding:.5rem .75rem;background:#fafafa;">
            @forelse($teachers as $teacher)
                <label style="display:flex;align-items:center;gap:.6rem;padding:.3rem 0;cursor:pointer;font-size:.8125rem;border-bottom:1px solid #f3f4f6;">
                    <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                           {{ in_array($teacher->id, $selectedTeacherIds) ? 'checked' : '' }}
                           style="accent-color:#552b20;width:15px;height:15px;flex-shrink:0;">
                    @if($teacher->profile_photo_url)
                        <img src="{{ $teacher->profile_photo_url }}" alt="{{ $teacher->f_name }}"
                             style="width:24px;height:24px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    @else
                        <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr($teacher->f_name,0,1)) }}
                        </div>
                    @endif
                    <span style="flex:1;">{{ $teacher->f_name }} {{ $teacher->l_name }}</span>
                    @if($teacher->employee_id)
                        <span style="color:#9ca3af;font-size:.75rem;">({{ $teacher->employee_id }})</span>
                    @endif
                </label>
            @empty
                <div style="color:#9ca3af;font-size:.8125rem;padding:.4rem 0;">No teachers available.</div>
            @endforelse
        </div>
    </div>

    <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:1.5rem;">
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
