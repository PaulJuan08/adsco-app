<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($editing ?? false)
        @method('PUT')
    @endif

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Topic Title <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" name="title"
               value="{{ old('title', $topic->title ?? '') }}"
               required placeholder="e.g., Introduction to Variables"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('title')
            <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Description</label>
        <textarea name="description" data-quill rows="2" placeholder="Topic description..."
                  style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;resize:vertical;outline:none;box-sizing:border-box;">{{ old('description', $topic->description ?? '') }}</textarea>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Video Link</label>
            <input type="url" name="video_link"
                   value="{{ old('video_link', $topic->video_link ?? '') }}"
                   placeholder="https://youtube.com/..."
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('video_link')
                <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Attachment Link</label>
            <input type="url" name="attachment"
                   value="{{ old('attachment', $topic->attachment ?? '') }}"
                   placeholder="https://drive.google.com/..."
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('attachment')
                <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            PDF File
            @if(($topic->pdf_file ?? null))
                <span style="font-weight:400;color:#10b981;font-size:.75rem;"> (Current: {{ $topic->pdf_file }})</span>
            @endif
        </label>
        <input type="file" name="pdf_file" accept=".pdf"
               style="width:100%;padding:.45rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;box-sizing:border-box;">
        <div style="font-size:.72rem;color:#9ca3af;margin-top:.25rem;">PDF only, max 10MB. Leave empty to keep existing file.</div>
        @error('pdf_file')
            <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:1.5rem;">
        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;font-weight:500;color:#374151;">
            <input type="checkbox" name="is_published" value="1"
                   {{ old('is_published', $topic->is_published ?? true) ? 'checked' : '' }}
                   style="accent-color:#552b20;width:16px;height:16px;">
            Publish topic (visible to students)
        </label>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.75rem;border-top:1px solid #f0ebe8;">
        <button type="button" onclick="closeCrudModal()"
                style="padding:.5rem 1.2rem;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.875rem;font-weight:600;cursor:pointer;">
            Cancel
        </button>
        <button type="submit"
                style="padding:.5rem 1.2rem;border-radius:8px;background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border:none;font-size:.875rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-save"></i> {{ ($editing ?? false) ? 'Update Topic' : 'Create Topic' }}
        </button>
    </div>
</form>
