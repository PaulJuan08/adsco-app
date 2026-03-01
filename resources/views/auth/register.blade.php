<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ADSCO LMS</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}" type="image/png">
    <!-- <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lf-InMsAAAAALLl-UT7ohlaUuRFIMQLqxhD15I8"></script> -->
    <style>
        .select-loading { opacity: 0.6; pointer-events: none; }
        .dropdown-hint {
            font-size: 0.75rem; color: #6b7280;
            margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;
        }
        .spinner-sm {
            display: inline-block; width: 12px; height: 12px;
            border: 2px solid #e5e7eb;
            border-top-color: var(--color-adsco-primary, #4f46e5);
            border-radius: 50%; animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .section-heading {
            color: var(--color-adsco-primary);
            font-size: 1.1rem;
            border-bottom: 2px solid var(--color-adsco-accent);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .field-note {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="auth-particle auth-particle-1"></div>
    <div class="auth-particle auth-particle-2"></div>
    <div class="auth-particle auth-particle-3"></div>

    <nav class="auth-navbar">
        <div class="container">
            <a href="/" class="brand">
                <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="brand-logo">
                <span class="brand-text">ADS<span class="accent">CO</span></span>
            </a>
            <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
            <div class="nav-links" id="navLinks">
                <a href="/" class="nav-link"><i class="fas fa-home"></i><span>Home</span></a>
                <a href="{{ route('login') }}" class="nav-link"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
                <a href="{{ route('register') }}" class="nav-link active"><i class="fas fa-user-plus"></i><span>Register</span></a>
            </div>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-card auth-card-wide">
            <div class="auth-card-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="logo">
                </div>
                <h1>Create Your Account</h1>
                <p class="subtitle">Join ADSCO Learning Management System</p>
            </div>

            <div class="auth-card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <form id="registrationForm" method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    {{-- ── Personal Information ─────────────────────────────── --}}
                    <h3 class="section-heading"><i class="fas fa-user-circle"></i> Personal Information</h3>

                    <div class="row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="f_name" class="form-label">First Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="f_name" name="f_name"
                                       value="{{ old('f_name') }}" placeholder="Enter first name" required>
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="l_name" class="form-label">Last Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="l_name" name="l_name"
                                       value="{{ old('l_name') }}" placeholder="Enter last name" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-md-4">
                            <div class="form-group">
                                <label for="age" class="form-label">Age <span class="required">*</span></label>
                                <input type="number" class="form-control" id="age" name="age"
                                       value="{{ old('age') }}" min="15" max="100" placeholder="Age" required>
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <div class="form-group">
                                <label for="sex" class="form-label">Sex <span class="required">*</span></label>
                                <select class="form-select" id="sex" name="sex" required>
                                    <option value="">Select Sex</option>
                                    <option value="male"   {{ old('sex') == 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <div class="form-group">
                                <label for="contact" class="form-label">Contact Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="contact" name="contact"
                                       value="{{ old('contact') }}" placeholder="09XX XXX XXXX" required>
                            </div>
                        </div>
                    </div>

                    {{-- ── Account Information ──────────────────────────────── --}}
                    <h3 class="section-heading" style="margin-top:2rem;"><i class="fas fa-id-card"></i> Account Information</h3>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ old('email') }}" placeholder="your.email@example.com" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="role" class="form-label">Account Type <span class="required">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>Registrar</option>
                                    <option value="3" {{ old('role') == '3' ? 'selected' : '' }}>Teacher</option>
                                    <option value="4" {{ old('role') == '4' ? 'selected' : '' }}>Student</option>
                                </select>
                            </div>
                        </div>

                        {{-- Employee ID (Registrar / Teacher) --}}
                        <div class="col col-md-6" id="employeeIdField" style="display:none;">
                            <div class="form-group">
                                <label for="employee_id" class="form-label">Employee ID <span class="required">*</span></label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id"
                                       value="{{ old('employee_id') }}" placeholder="Enter employee ID">
                            </div>
                        </div>

                        {{-- Student ID --}}
                        <div class="col col-md-6" id="studentIdField" style="display:none;">
                            <div class="form-group">
                                <label for="student_id" class="form-label">Student ID <span class="required">*</span></label>
                                <input type="text" class="form-control" id="student_id" name="student_id"
                                       value="{{ old('student_id') }}" placeholder="Enter student ID">
                            </div>
                        </div>
                    </div>

                    {{-- ── Academic Information (students only) ────────────── --}}
                    <div id="academicSection" style="display:none;">
                        <h3 class="section-heading" style="margin-top:2rem;"><i class="fas fa-graduation-cap"></i> Academic Information</h3>

                        {{-- Step 1: College --}}
                        <div class="form-group">
                            <label for="college_id" class="form-label">College / Department <span class="required">*</span></label>
                            <select class="form-select" id="college_id" name="college_id">
                                <option value="">— Select College —</option>
                            </select>
                            <div class="dropdown-hint" id="collegeHint" style="display:none;">
                                <span class="spinner-sm"></span>&nbsp;Loading colleges…
                            </div>
                        </div>

                        {{-- Step 2: Program (from programs table) --}}
                        <div class="form-group" id="programField" style="display:none;">
                            <label for="program_id" class="form-label">
                                Degree Program <span class="required">*</span>
                                <small style="color:#6b7280;font-weight:400;">
                                    (e.g. BS Civil Engineering, BS Electrical Engineering)
                                </small>
                            </label>
                            <select class="form-select" id="program_id" name="program_id">
                                <option value="">— Select Degree Program —</option>
                            </select>
                            <div class="dropdown-hint" id="programHint" style="display:none;">
                                <span class="spinner-sm"></span>&nbsp;Loading programs…
                            </div>
                        </div>

                        {{-- Step 3: Year Level --}}
                        <div class="form-group" id="yearField" style="display:none;">
                            <label for="college_year" class="form-label">Year Level <span class="required">*</span></label>
                            <select class="form-select" id="college_year" name="college_year">
                                <option value="">— Select Year Level —</option>
                            </select>
                            <div class="dropdown-hint" id="yearHint" style="display:none;">
                                <span class="spinner-sm"></span>&nbsp;Loading year levels…
                            </div>
                        </div>
                        
                        <div class="field-note">
                            <i class="fas fa-info-circle"></i> Your college and program selection determines your academic track.
                        </div>
                    </div>

                    {{-- ── Security ─────────────────────────────────────────── --}}
                    <h3 class="section-heading" style="margin-top:2rem;"><i class="fas fa-lock"></i> Security</h3>

                    <div class="row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">Password <span class="required">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Minimum 8 characters" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password','icon1')">
                                        <i class="fas fa-eye" id="icon1"></i>
                                    </button>
                                </div>
                                <span class="form-text">Minimum 8 characters</span>
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="required">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation" placeholder="Re-enter password" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation','icon2')">
                                        <i class="fas fa-eye" id="icon2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:2rem;">
                        <button type="button" class="btn btn-primary btn-block btn-lg" id="reviewBtn">
                            <i class="fas fa-eye"></i> Review Registration
                        </button>
                    </div>
                </form>

                <div class="divider"><span>Already have an account?</span></div>
                <div class="text-center">
                    <a href="{{ route('login') }}" class="auth-link" style="font-size:1rem;">
                        <i class="fas fa-sign-in-alt"></i> Login here
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Review Modal ──────────────────────────────────────────────────────── --}}
    <div class="modal" id="reviewModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clipboard-check"></i> Review Registration Details</h5>
                <button type="button" onclick="closeModal()" style="background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr><th width="38%"><i class="fas fa-user"></i> First Name</th><td id="rv_f_name"></td></tr>
                    <tr><th><i class="fas fa-user"></i> Last Name</th><td id="rv_l_name"></td></tr>
                    <tr><th><i class="fas fa-calendar"></i> Age</th><td id="rv_age"></td></tr>
                    <tr><th><i class="fas fa-venus-mars"></i> Sex</th><td id="rv_sex"></td></tr>
                    <tr><th><i class="fas fa-phone"></i> Contact</th><td id="rv_contact"></td></tr>
                    <tr><th><i class="fas fa-envelope"></i> Email</th><td id="rv_email"></td></tr>
                    <tr><th><i class="fas fa-user-tag"></i> Account Type</th><td id="rv_role"></td></tr>
                    {{-- staff --}}
                    <tr id="rv_employee_row" style="display:none;">
                        <th><i class="fas fa-id-badge"></i> Employee ID</th><td id="rv_employee_id"></td>
                    </tr>
                    {{-- student --}}
                    <tr id="rv_student_row" style="display:none;">
                        <th><i class="fas fa-id-badge"></i> Student ID</th><td id="rv_student_id"></td>
                    </tr>
                    <tr id="rv_college_row" style="display:none;">
                        <th><i class="fas fa-university"></i> College</th><td id="rv_college"></td>
                    </tr>
                    <tr id="rv_program_row" style="display:none;">
                        <th><i class="fas fa-book-open"></i> Degree Program</th><td id="rv_program"></td>
                    </tr>
                    <tr id="rv_year_row" style="display:none;">
                        <th><i class="fas fa-layer-group"></i> Year Level</th><td id="rv_year"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-edit"></i> Edit Details
                </button>
                <button type="button" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-check"></i> Submit Registration
                </button>
            </div>
        </div>
    </div>

    <footer class="auth-footer">
        <p><i class="far fa-copyright"></i> {{ date('Y') }} Agusan Del Sur College. All rights reserved.</p>
    </footer>

    <script>
    // ─── Tiny helper ──────────────────────────────────────────────────────────
    const el = id => document.getElementById(id);

    function setLoading(selectId, hintId, on) {
        el(selectId).classList.toggle('select-loading', on);
        el(hintId).style.display = on ? 'flex' : 'none';
    }

    function resetSelect(selectId, label) {
        el(selectId).innerHTML = `<option value="">${label}</option>`;
    }

    // ─── Mobile nav ───────────────────────────────────────────────────────────
    el('mobileMenuBtn')?.addEventListener('click', () => {
        el('navLinks').classList.toggle('show');
        const icon = el('mobileMenuBtn').querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    });

    // ─── Role change ──────────────────────────────────────────────────────────
    el('role').addEventListener('change', function () {
        const role      = this.value;
        const isStudent = role === '4';
        const isStaff   = ['2','3'].includes(role); // Registrar or Teacher

        el('employeeIdField').style.display = isStaff   ? 'block' : 'none';
        el('employee_id').required          = isStaff;

        el('studentIdField').style.display  = isStudent ? 'block' : 'none';
        el('student_id').required           = isStudent;

        el('academicSection').style.display = isStudent ? 'block' : 'none';

        if (isStudent) {
            loadColleges();
        } else {
            // Reset cascading dropdowns
            resetSelect('college_id',        '— Select College —');
            resetSelect('program_id', '— Select Degree Program —');
            resetSelect('college_year',      '— Select Year Level —');
            el('programField').style.display = 'none';
            el('yearField').style.display    = 'none';
        }
    });

    // Restore on page reload (handles Laravel old() repopulation)
    el('role').dispatchEvent(new Event('change'));

    // ─── Load colleges ────────────────────────────────────────────────────────
    async function loadColleges() {
        setLoading('college_id', 'collegeHint', true);
        resetSelect('college_id',        '— Select College —');
        resetSelect('program_id', '— Select Degree Program —');
        resetSelect('college_year',      '— Select Year Level —');
        el('programField').style.display = 'none';
        el('yearField').style.display    = 'none';

        try {
            const res      = await fetch('{{ route("api.registration.colleges") }}');
            const colleges = await res.json();

            colleges.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.college_name;
                el('college_id').appendChild(opt);
            });

            // Restore old value after a validation error redirect
            const oldCollege = '{{ old("college_id") }}';
            if (oldCollege) {
                el('college_id').value = oldCollege;
                el('college_id').dispatchEvent(new Event('change'));
            }
        } catch (e) {
            console.error('Could not load colleges:', e);
        } finally {
            setLoading('college_id', 'collegeHint', false);
        }
    }

    // ─── College change → load programs + year levels ─────────────────
    el('college_id').addEventListener('change', async function () {
        const collegeId = this.value;

        resetSelect('program_id', '— Select Degree Program —');
        resetSelect('college_year',      '— Select Year Level —');
        el('programField').style.display = 'none';
        el('yearField').style.display    = 'none';

        if (!collegeId) return;

        // ── Load programs from programs table ─────────────────────────────────
        el('programField').style.display = 'block';
        setLoading('program_id', 'programHint', true);

        try {
            // Calls: GET /api/registration/colleges/{id}/programs
            // Returns programs from the programs table
            const res      = await fetch(`{{ url('api/registration/colleges') }}/${collegeId}/programs`);
            const programs = await res.json();

            if (!programs || programs.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'No programs available for this college';
                el('program_id').appendChild(opt);
            } else {
                programs.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    // Show: "BS Civil Engineering (BSCE)" or just the name
                    opt.textContent = p.program_code
                        ? `${p.program_name} (${p.program_code})`
                        : p.program_name;
                    el('program_id').appendChild(opt);
                });
            }

            const oldProgram = '{{ old("program_id") }}';
            if (oldProgram) el('program_id').value = oldProgram;

        } catch (e) {
            console.error('Could not load degree programs:', e);
        } finally {
            setLoading('program_id', 'programHint', false);
        }

        // ── Load year levels ──────────────────────────────────────────────────
        el('yearField').style.display = 'block';
        setLoading('college_year', 'yearHint', true);

        try {
            const res   = await fetch(`{{ url('api/registration/colleges') }}/${collegeId}/years`);
            const years = await res.json();

            if (years && years.length > 0) {
                years.forEach(y => {
                    const opt = document.createElement('option');
                    opt.value = y;
                    opt.textContent = y;
                    el('college_year').appendChild(opt);
                });
            } else {
                // Fallback year levels if none returned
                const defaultYears = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                defaultYears.forEach(y => {
                    const opt = document.createElement('option');
                    opt.value = y;
                    opt.textContent = y;
                    el('college_year').appendChild(opt);
                });
            }

            const oldYear = '{{ old("college_year") }}';
            if (oldYear) el('college_year').value = oldYear;

        } catch (e) {
            console.error('Could not load year levels:', e);
            // Fallback year levels on error
            const defaultYears = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
            defaultYears.forEach(y => {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                el('college_year').appendChild(opt);
            });
        } finally {
            setLoading('college_year', 'yearHint', false);
        }
    });

    // ─── Password toggle ──────────────────────────────────────────────────────
    function togglePassword(inputId, iconId) {
        const input = el(inputId);
        const icon  = el(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    // ─── Review modal ─────────────────────────────────────────────────────────
    el('reviewBtn').addEventListener('click', openReview);
    el('submitBtn').addEventListener('click', submitForm);

    function openReview() {
        const role      = el('role').value;
        const isStudent = role === '4';
        const isStaff   = ['2','3'].includes(role);

        // Validate basics
        const basics = ['f_name','l_name','age','sex','contact','email','role'];
        for (const f of basics) {
            if (!el(f).value.trim()) {
                alert(`Please fill in the "${f.replace(/_/g,' ')}" field.`);
                return;
            }
        }
        if (isStaff   && !el('employee_id').value.trim()) { alert('Please enter your Employee ID.'); return; }
        if (isStudent && !el('student_id').value.trim())  { alert('Please enter your Student ID.'); return; }
        if (isStudent && !el('college_id').value)         { alert('Please select a college.'); return; }
        if (isStudent && !el('program_id').value)  { alert('Please select a degree program.'); return; }
        if (isStudent && !el('college_year').value)       { alert('Please select a year level.'); return; }

        const pwd  = el('password').value;
        const conf = el('password_confirmation').value;
        if (pwd.length < 8) { alert('Password must be at least 8 characters.'); return; }
        if (pwd !== conf)   { alert('Passwords do not match.'); return; }

        // Populate review table
        el('rv_f_name').textContent  = el('f_name').value;
        el('rv_l_name').textContent  = el('l_name').value;
        el('rv_age').textContent     = el('age').value;
        el('rv_sex').textContent     = el('sex').value === 'male' ? 'Male' : 'Female';
        el('rv_contact').textContent = el('contact').value;
        el('rv_email').textContent   = el('email').value;

        const roleSel = el('role');
        el('rv_role').textContent = roleSel.options[roleSel.selectedIndex].text;

        // Staff row
        el('rv_employee_row').style.display = isStaff ? '' : 'none';
        if (isStaff) el('rv_employee_id').textContent = el('employee_id').value;

        // Student rows
        ['rv_student_row','rv_college_row','rv_program_row','rv_year_row'].forEach(id => {
            el(id).style.display = isStudent ? '' : 'none';
        });

        if (isStudent) {
            el('rv_student_id').textContent = el('student_id').value;

            const colSel = el('college_id');
            el('rv_college').textContent    = colSel.options[colSel.selectedIndex]?.text ?? '';

            const prgSel = el('program_id');
            el('rv_program').textContent    = prgSel.options[prgSel.selectedIndex]?.text ?? '';

            const yrSel  = el('college_year');
            el('rv_year').textContent       = yrSel.options[yrSel.selectedIndex]?.text ?? '';
        }

        el('reviewModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        el('reviewModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function submitForm() {
        const btn = el('submitBtn');
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner"></span> Submitting…';
        el('registrationForm').submit();
    }

    el('reviewModal').addEventListener('click', e => {
        if (e.target === el('reviewModal')) closeModal();
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(a => {
            a.style.opacity = '0';
            setTimeout(() => a.remove(), 300);
        });
    }, 5000);

    // function onClick(e) {
    //     e.preventDefault();
    //     grecaptcha.enterprise.ready(async () => {
    //     const token = await grecaptcha.enterprise.execute('6Lf-InMsAAAAALLl-UT7ohlaUuRFIMQLqxhD15I8', {action: 'LOGIN'});
    //     });
    // }
    </script>
</body>
</html>