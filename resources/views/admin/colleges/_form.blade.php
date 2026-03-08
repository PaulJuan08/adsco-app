<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($editing ?? false)
        @method('PUT')
    @endif

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            College Name <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" name="college_name"
               value="{{ old('college_name', $college->college_name ?? '') }}"
               required placeholder="e.g., College of Engineering"
               class="form-input @error('college_name') error @enderror"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('college_name')
            <div class="form-error" style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Year Levels <span style="color:#dc2626;">*</span>
            <span style="font-weight:400;color:#9ca3af;"> (comma-separated)</span>
        </label>
        <input type="text" name="college_year" id="crudCollegeYear"
               value="{{ old('college_year', $college->college_year ?? '1st Year,2nd Year,3rd Year,4th Year') }}"
               required placeholder="e.g., 1st Year,2nd Year,3rd Year,4th Year"
               class="form-input @error('college_year') error @enderror"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('college_year')
            <div class="form-error" style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
        <div style="margin-top:.5rem;display:flex;gap:.35rem;flex-wrap:wrap;">
            @foreach(['1st Year','2nd Year','3rd Year','4th Year','5th Year'] as $yr)
                <span onclick="crudAddCollegeYear('{{ $yr }}')"
                      style="cursor:pointer;padding:.2rem .55rem;background:#f5ede8;border-radius:20px;font-size:.72rem;color:#552b20;border:1px solid #e8d5cc;user-select:none;"
                      onmouseenter="this.style.background='#e8d5cc'" onmouseleave="this.style.background='#f5ede8'">
                    + {{ $yr }}
                </span>
            @endforeach
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Description</label>
        <textarea name="description" rows="3" placeholder="Enter college description..."
                  class="form-textarea @error('description') error @enderror"
                  style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;resize:vertical;outline:none;box-sizing:border-box;">{{ old('description', $college->description ?? '') }}</textarea>
        @error('description')
            <div class="form-error" style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Status <span style="color:#dc2626;">*</span>
        </label>
        <select name="status"
                style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
            <option value="1" {{ old('status', $college->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $college->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <div class="form-error" style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.75rem;border-top:1px solid #f0ebe8;">
        <button type="button" onclick="closeCrudModal()"
                style="padding:.5rem 1.2rem;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.875rem;font-weight:600;cursor:pointer;">
            Cancel
        </button>
        <button type="submit"
                style="padding:.5rem 1.2rem;border-radius:8px;background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border:none;font-size:.875rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-save"></i> {{ ($editing ?? false) ? 'Update College' : 'Create College' }}
        </button>
    </div>
</form>
<script>
function crudAddCollegeYear(year) {
    var inp = document.getElementById('crudCollegeYear');
    if (!inp) return;
    var years = inp.value.trim() ? inp.value.split(',').map(function(y){return y.trim();}).filter(Boolean) : [];
    if (!years.includes(year)) { years.push(year); inp.value = years.join(', '); }
}
</script>
