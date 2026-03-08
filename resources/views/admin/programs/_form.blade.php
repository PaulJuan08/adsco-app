<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($editing ?? false)
        @method('PUT')
    @endif

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            College <span style="color:#dc2626;">*</span>
        </label>
        <select name="college_id"
                style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
            <option value="">Select a college</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}" {{ old('college_id', $program->college_id ?? '') == $college->id ? 'selected' : '' }}>
                    {{ $college->college_name }}
                </option>
            @endforeach
        </select>
        @error('college_id')
            <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Program Name <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="program_name"
                   value="{{ old('program_name', $program->program_name ?? '') }}"
                   required placeholder="e.g., Bachelor of Science in IT"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('program_name')
                <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Program Code</label>
            <input type="text" name="program_code"
                   value="{{ old('program_code', $program->program_code ?? '') }}"
                   placeholder="e.g., BSIT"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('program_code')
                <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Description</label>
        <textarea name="description" rows="3" placeholder="Enter program description..."
                  style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;resize:vertical;outline:none;box-sizing:border-box;">{{ old('description', $program->description ?? '') }}</textarea>
        @error('description')
            <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Status <span style="color:#dc2626;">*</span>
        </label>
        <select name="status"
                style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
            <option value="1" {{ old('status', $program->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $program->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.75rem;border-top:1px solid #f0ebe8;">
        <button type="button" onclick="closeCrudModal()"
                style="padding:.5rem 1.2rem;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.875rem;font-weight:600;cursor:pointer;">
            Cancel
        </button>
        <button type="submit"
                style="padding:.5rem 1.2rem;border-radius:8px;background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border:none;font-size:.875rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-save"></i> {{ ($editing ?? false) ? 'Update Program' : 'Create Program' }}
        </button>
    </div>
</form>
