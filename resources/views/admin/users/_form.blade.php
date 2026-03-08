<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($editing ?? false)
        @method('PUT')
    @endif

    {{-- Profile Photo (edit mode only) --}}
    @if(($editing ?? false) && $user)
    <div style="margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div id="ufPhotoPreviewWrap" style="flex-shrink:0;">
            @if($user->profile_photo_url)
                <img id="ufPhotoPreview" src="{{ $user->profile_photo_url }}" alt="Photo"
                     style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2.5px solid #e5e7eb;">
            @else
                <div id="ufPhotoPreview" style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#552b20,#3d1f17);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.25rem;font-weight:700;">
                    {{ strtoupper(substr($user->f_name,0,1).substr($user->l_name,0,1)) }}
                </div>
            @endif
        </div>
        <div style="flex:1;">
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Profile Photo</label>
            <label for="uf_profile_photo" style="display:inline-flex;align-items:center;gap:.4rem;padding:.4rem .9rem;border-radius:7px;border:1.5px dashed #d1d5db;background:#f9fafb;color:#552b20;font-size:.8125rem;font-weight:600;cursor:pointer;">
                <i class="fas fa-camera"></i> Choose Photo
            </label>
            <input type="file" id="uf_profile_photo" name="profile_photo" accept="image/*"
                   style="display:none;" onchange="ufPreviewPhoto(this)">
            <div id="ufPhotoFileName" style="font-size:.75rem;color:#6b7280;margin-top:.3rem;"></div>
            <div id="ufPhotoSizeWarning" style="display:none;font-size:.75rem;color:#dc2626;margin-top:.3rem;">
                <i class="fas fa-exclamation-triangle"></i> Max size is <strong>2MB</strong>.
            </div>
            @error('profile_photo')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
            @if($user->profile_photo_url)
                <label style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;color:#6b7280;margin-top:.3rem;cursor:pointer;">
                    <input type="checkbox" name="remove_photo" value="1"> Remove current photo
                </label>
            @endif
        </div>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                First Name <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="f_name" id="uf_f_name"
                   value="{{ old('f_name', $user->f_name ?? '') }}" required
                   placeholder="First name"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('f_name')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Last Name <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="l_name" id="uf_l_name"
                   value="{{ old('l_name', $user->l_name ?? '') }}" required
                   placeholder="Last name"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('l_name')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Email <span style="color:#dc2626;">*</span>
        </label>
        <input type="email" name="email"
               value="{{ old('email', $user->email ?? '') }}" required
               placeholder="email@example.com"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('email')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Age</label>
            <input type="number" name="age" min="15" max="100"
                   value="{{ old('age', $user->age ?? '') }}" placeholder="e.g. 20"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Gender</label>
            <select name="sex" style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
                <option value="">Select</option>
                <option value="male" {{ old('sex', $user->sex ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('sex', $user->sex ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Contact</label>
            <input type="text" name="contact"
                   value="{{ old('contact', $user->contact ?? '') }}" placeholder="+63 9XX..."
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Role <span style="color:#dc2626;">*</span>
        </label>
        <select name="role" id="uf_role" onchange="ufRoleChange(this.value)"
                style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;" required>
            <option value="">Select role</option>
            <option value="1" {{ old('role', $user->role ?? '') == 1 ? 'selected' : '' }}>Admin</option>
            <option value="2" {{ old('role', $user->role ?? '') == 2 ? 'selected' : '' }}>Registrar</option>
            <option value="3" {{ old('role', $user->role ?? '') == 3 ? 'selected' : '' }}>Teacher</option>
            <option value="4" {{ old('role', $user->role ?? '') == 4 ? 'selected' : '' }}>Student</option>
        </select>
        @error('role')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
    </div>

    {{-- Employee ID (Registrar/Teacher) --}}
    <div id="uf_emp_section" style="display:none;margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Employee ID <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" name="employee_id" id="uf_employee_id"
               value="{{ old('employee_id', $user->employee_id ?? '') }}"
               placeholder="e.g. EMP-2026-XXXX"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('employee_id')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
    </div>

    {{-- Student ID --}}
    <div id="uf_stu_section" style="display:none;margin-bottom:1rem;">
        <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
            Student ID <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" name="student_id" id="uf_student_id"
               value="{{ old('student_id', $user->student_id ?? '') }}"
               placeholder="e.g. STU-2026-0001"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('student_id')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
    </div>

    {{-- Academic section (Student only) --}}
    <div id="uf_academic_section" style="display:none;margin-bottom:1rem;padding:1rem;background:#f8fafc;border:1px solid #e0e7ff;border-radius:10px;">
        <div style="font-size:.8125rem;font-weight:600;color:#4f46e5;margin-bottom:.75rem;"><i class="fas fa-university"></i> Academic Assignment</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:.75rem;">
            <div>
                <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">College</label>
                <select name="college_id" id="uf_college_id" onchange="ufCollegeChange(this.value)"
                        style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
                    <option value="">— Select College —</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}"
                                data-years="{{ $college->college_year }}"
                                {{ old('college_id', $user->college_id ?? '') == $college->id ? 'selected' : '' }}>
                            {{ $college->college_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Program</label>
                <select name="program_id" id="uf_program_id"
                        style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
                    <option value="">— Select College First —</option>
                </select>
            </div>
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Year Level</label>
            <select name="college_year" id="uf_college_year"
                    style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
                <option value="">— Select College First —</option>
            </select>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Password {{ ($editing ?? false) ? '' : '*' }}
            </label>
            <input type="password" name="password" id="uf_password" autocomplete="new-password"
                   {{ ($editing ?? false) ? '' : 'required' }}
                   placeholder="{{ ($editing ?? false) ? 'Leave blank to keep current' : 'Min 8 characters' }}"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('password')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Confirm Password {{ ($editing ?? false) ? '' : '*' }}
            </label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                   {{ ($editing ?? false) ? '' : 'required' }}
                   placeholder="Confirm password"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.75rem;border-top:1px solid #f0ebe8;">
        <button type="button" onclick="closeCrudModal()"
                style="padding:.5rem 1.2rem;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.875rem;font-weight:600;cursor:pointer;">
            Cancel
        </button>
        <button type="submit"
                style="padding:.5rem 1.2rem;border-radius:8px;background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border:none;font-size:.875rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-save"></i> {{ ($editing ?? false) ? 'Update User' : 'Create User' }}
        </button>
    </div>
</form>

<script>
(function(){
    var _csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var _csrf = _csrfMeta ? _csrfMeta.content : '';

    window.ufPreviewPhoto = function(input) {
        var file = input.files[0];
        var warning = document.getElementById('ufPhotoSizeWarning');
        var nameEl  = document.getElementById('ufPhotoFileName');
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            warning.style.display = 'block';
            nameEl.textContent = '';
            input.value = '';
            return;
        }
        warning.style.display = 'none';
        nameEl.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        var reader = new FileReader();
        reader.onload = function(e) {
            var wrap = document.getElementById('ufPhotoPreviewWrap');
            wrap.innerHTML = '<img id="ufPhotoPreview" src="' + e.target.result + '" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2.5px solid #552b20;">';
        };
        reader.readAsDataURL(file);
    };

    window.ufRoleChange = function(roleId) {
        var empSection = document.getElementById('uf_emp_section');
        var stuSection = document.getElementById('uf_stu_section');
        var acadSection = document.getElementById('uf_academic_section');
        var empInput = document.getElementById('uf_employee_id');
        var stuInput = document.getElementById('uf_student_id');
        if (!empSection) return;
        roleId = parseInt(roleId);
        empSection.style.display  = (roleId === 2 || roleId === 3) ? 'block' : 'none';
        stuSection.style.display  = (roleId === 4) ? 'block' : 'none';
        acadSection.style.display = (roleId === 4) ? 'block' : 'none';
        if (empInput) empInput.required = (roleId === 2 || roleId === 3);
        if (stuInput) stuInput.required = (roleId === 4);
    };

    window.ufCollegeChange = function(collegeId, preselectProgramId, preselectYear) {
        var programSelect = document.getElementById('uf_program_id');
        var yearSelect    = document.getElementById('uf_college_year');
        if (!programSelect) return;
        if (!collegeId) {
            programSelect.innerHTML = '<option value="">— Select College First —</option>';
            yearSelect.innerHTML    = '<option value="">— Select College First —</option>';
            return;
        }
        fetch('/api/registration/colleges/' + collegeId + '/programs', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf }
        })
        .then(function(r){ return r.json(); })
        .then(function(programs) {
            programSelect.innerHTML = '<option value="">— Select Program —</option>';
            programs.forEach(function(p) {
                var opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.program_name + (p.program_code ? ' (' + p.program_code + ')' : '');
                if (preselectProgramId && p.id == preselectProgramId) opt.selected = true;
                programSelect.appendChild(opt);
            });
        });
        var collegeOpt = document.querySelector('#uf_college_id option[value="' + collegeId + '"]');
        var yearsRaw   = collegeOpt ? collegeOpt.dataset.years : '';
        yearSelect.innerHTML = '<option value="">— Select Year Level —</option>';
        if (yearsRaw) {
            yearsRaw.split(',').map(function(y){ return y.trim(); }).filter(Boolean).forEach(function(y) {
                var opt = document.createElement('option');
                opt.value = y; opt.textContent = y;
                if (preselectYear === y) opt.selected = true;
                yearSelect.appendChild(opt);
            });
        }
    };

    // Initialize on load
    var roleSelect = document.getElementById('uf_role');
    if (roleSelect && roleSelect.value) {
        ufRoleChange(roleSelect.value);
        @if($editing ?? false)
        var collegeId = '{{ $user->college_id ?? '' }}';
        var programId = '{{ $user->program_id ?? '' }}';
        var year      = '{{ $user->college_year ?? '' }}';
        if (collegeId) {
            ufCollegeChange(collegeId, programId, year);
        }
        @endif
    }
})();
</script>
