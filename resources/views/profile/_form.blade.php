<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="profileForm">
    @csrf
    @method('PUT')

    {{-- Profile Photo --}}
    <div style="margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div id="photoPreviewWrap" style="flex-shrink:0;">
            @if($user->profile_photo_url)
                <img id="photoPreview" src="{{ $user->profile_photo_url }}" alt="Photo"
                     style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2.5px solid #e5e7eb;">
            @else
                <div id="photoPreview" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#552b20,#3d1f17);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($user->f_name,0,1).substr($user->l_name,0,1)) }}
                </div>
            @endif
        </div>
        <div style="flex:1;">
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Profile Photo
            </label>
            <label for="profile_photo_input" style="display:inline-flex;align-items:center;gap:.4rem;padding:.4rem .9rem;border-radius:7px;border:1.5px dashed #d1d5db;background:#f9fafb;color:#552b20;font-size:.8125rem;font-weight:600;cursor:pointer;">
                <i class="fas fa-camera"></i> Choose Photo
            </label>
            <input type="file" id="profile_photo_input" name="profile_photo" accept="image/*"
                   style="display:none;" onchange="previewProfilePhoto(this)">
            <div id="photoFileName" style="font-size:.75rem;color:#6b7280;margin-top:.3rem;"></div>
            <div id="photoSizeWarning" style="display:none;font-size:.75rem;color:#dc2626;margin-top:.3rem;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:.3rem .6rem;">
                <i class="fas fa-exclamation-triangle"></i> File too large. Maximum allowed size is <strong>2MB</strong>. Please choose a smaller image.
            </div>
            @error('profile_photo')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
            @if($user->profile_photo_url)
                <label style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;color:#6b7280;margin-top:.3rem;cursor:pointer;">
                    <input type="checkbox" name="remove_photo" value="1" onchange="toggleRemovePhoto(this)"> Remove current photo
                </label>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                First Name <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="f_name"
                   value="{{ old('f_name', $user->f_name) }}" required
                   placeholder="First name"
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('f_name')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                Last Name <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="l_name"
                   value="{{ old('l_name', $user->l_name) }}" required
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
               value="{{ old('email', $user->email) }}" required
               placeholder="email@example.com"
               style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
        @error('email')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Contact</label>
            <input type="text" name="contact"
                   value="{{ old('contact', $user->contact) }}"
                   placeholder="+63 9XX..."
                   style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            @error('contact')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
        </div>
        <div>
            <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Gender</label>
            <select name="sex" style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;background:#fff;outline:none;box-sizing:border-box;">
                <option value="">Select</option>
                <option value="male" {{ old('sex', $user->sex) == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('sex')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
        </div>
    </div>

    <div style="background:#f8fafc;border:1px solid #e0e7ff;border-radius:10px;padding:1rem;margin-bottom:1.5rem;">
        <div style="font-size:.8125rem;font-weight:600;color:#4f46e5;margin-bottom:.75rem;"><i class="fas fa-lock"></i> Change Password (optional)</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div>
                <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">New Password</label>
                <input type="password" name="password" autocomplete="new-password"
                       placeholder="Leave blank to keep current"
                       style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
                @error('password')<div style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</div>@enderror
            </div>
            <div>
                <label style="display:block;font-size:.8125rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Confirm Password</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                       placeholder="Confirm new password"
                       style="width:100%;padding:.55rem .75rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;box-sizing:border-box;">
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.75rem;border-top:1px solid #f0ebe8;">
        <button type="button" onclick="closeCrudModal()"
                style="padding:.5rem 1.2rem;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.875rem;font-weight:600;cursor:pointer;">
            Cancel
        </button>
        <button type="submit"
                style="padding:.5rem 1.2rem;border-radius:8px;background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border:none;font-size:.875rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</form>
<script>
function previewProfilePhoto(input) {
    const file = input.files[0];
    const warning = document.getElementById('photoSizeWarning');
    const nameEl  = document.getElementById('photoFileName');
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!file) return;

    if (file.size > maxSize) {
        warning.style.display = 'block';
        nameEl.textContent = '';
        input.value = '';
        return;
    }

    warning.style.display = 'none';
    nameEl.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    const reader = new FileReader();
    reader.onload = function(e) {
        const wrap = document.getElementById('photoPreviewWrap');
        wrap.innerHTML = '<img id="photoPreview" src="' + e.target.result + '" alt="Photo" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2.5px solid #552b20;">';
    };
    reader.readAsDataURL(file);
}
function toggleRemovePhoto(cb) {
    const wrap = document.getElementById('photoPreviewWrap');
    if (cb.checked) {
        wrap.style.opacity = '0.3';
    } else {
        wrap.style.opacity = '1';
    }
}
</script>
