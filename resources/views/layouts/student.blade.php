<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/adsco-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer">

    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- Layout CSS -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

    <!-- Rich Text Editor -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
    <link rel="stylesheet" href="{{ asset('css/quill-editor.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="{{ asset('js/quill-editor.js') }}" defer></script>
    <!-- Modal CSS -->
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">

    @stack('styles')
</head>
<body>
    <div class="layout-with-sidebar">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-header-link">
                    <div class="sidebar-logo">
                        <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo">
                    </div>
                    <div class="sidebar-brand">
                        <div class="sidebar-brand-name">ADSCO</div>
                        <div class="sidebar-brand-sub">Student Portal</div>
                    </div>
                </a>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('student.courses.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>My Courses</span>
                </a>

                <a href="{{ route('student.todo.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.todo.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span>To-Do</span>
                    @php
                        $studentId = Auth::id();

                        $activeQuizzesCount = \App\Models\QuizStudentAccess::where('student_id', $studentId)
                            ->where('status', 'allowed')
                            ->whereHas('quiz', function($q) {
                                $q->where('is_published', 1);
                            })
                            ->whereDoesntHave('quiz.attempts', function($q) use ($studentId) {
                                $q->where('user_id', $studentId)
                                  ->where('passed', 1)
                                  ->whereNotNull('completed_at');
                            })
                            ->count();

                        $activeAssignmentsCount = \App\Models\AssignmentStudentAccess::where('student_id', $studentId)
                            ->where('status', 'allowed')
                            ->whereHas('assignment', function($q) {
                                $q->where('is_published', 1);
                            })
                            ->whereDoesntHave('assignment.submissions', function($q) use ($studentId) {
                                $q->where('student_id', $studentId)
                                  ->where('status', 'graded');
                            })
                            ->count();

                        $studentTodoPending = $activeQuizzesCount + $activeAssignmentsCount;
                    @endphp
                    @if($studentTodoPending > 0)
                        <span class="badge-count">{{ $studentTodoPending }}</span>
                    @endif
                </a>

                <!-- Discussions -->
                <a href="{{ route('student.discussions.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.discussions.*') || request()->routeIs('student.courses.discussions*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i>
                    <span>Discussions</span>
                </a>

                <!-- My Progress -->
                <a href="{{ route('student.progress.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.progress.*') || request()->routeIs('student.grades.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>My Progress</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <!-- Profile link -->
                <a href="{{ route('student.profile.show') }}" class="sidebar-user-profile-link">
                    <div class="sidebar-user-profile">
                        <div class="sidebar-user-avatar">
                            @if(Auth::user()->profile_photo_url)
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->f_name }}" class="avatar-image">
                            @elseif(Auth::user()->sex === 'female')
                                <i class="fas fa-person-dress" style="font-size:1.25rem;"></i>
                            @else
                                <i class="fas fa-person" style="font-size:1.25rem;"></i>
                            @endif
                        </div>
                        <div class="sidebar-user-details">
                            <div class="sidebar-user-name">{{ Auth::user()->f_name }} {{ Auth::user()->l_name }}</div>
                            <div class="sidebar-user-role">Student</div>
                        </div>
                    </div>
                </a>

                <button class="sidebar-logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </aside>

        <!-- Mobile sidebar toggle -->
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <div class="content-wrapper" style="display:flex; flex-direction:column; min-height:100vh;">
            @include('components.announcement-banner')
            <div style="flex:1;">
                @yield('content')
            </div>
            @include('components.dashboard-footer')
        </div>
    </div>

    @stack('scripts')

    @include('components.legal-modal')

    {{-- ─── CRUD Modal (shared infrastructure) ─── --}}
    <div id="crudModalOverlay" onclick="closeCrudModal()"></div>
    <div id="crudModalBox">
        <div style="background:linear-gradient(135deg,#552b20 0%,#3d1f17 100%);padding:1.1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <h3 id="crudModalTitle" style="margin:0;font-size:1rem;font-weight:700;color:#fff;"></h3>
            <button onclick="closeCrudModal()" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:32px;height:32px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.9rem;"><i class="fas fa-times"></i></button>
        </div>
        <div id="crudModalBody" style="padding:1.5rem 1.5rem 1rem;overflow-y:auto;flex:1;"></div>
    </div>
    <script>
        var _crudLoadedCss = {};
        function openCrudModal(url, title, maxWidth) {
            document.getElementById('crudModalBox').style.maxWidth = maxWidth || '680px';
            document.getElementById('crudModalTitle').textContent = title;
            document.getElementById('crudModalBody').innerHTML = '<div style="text-align:center;padding:2rem;color:#552b20;font-size:2rem;"><i class="fas fa-spinner fa-spin"></i></div>';
            document.getElementById('crudModalOverlay').classList.add('open');
            document.getElementById('crudModalBox').classList.add('open');
            document.body.style.overflow = 'hidden';
            fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
                .then(function(r){return r.json();})
                .then(function(data){
                    if (data.css && !_crudLoadedCss[data.css]) {
                        var lnk = document.createElement('link'); lnk.rel='stylesheet'; lnk.href=data.css;
                        document.head.appendChild(lnk); _crudLoadedCss[data.css]=true;
                    }
                    document.getElementById('crudModalBody').innerHTML = data.html;
                    document.querySelectorAll('#crudModalBody script').forEach(function(s){var n=document.createElement('script');n.textContent=s.textContent;document.head.appendChild(n);s.remove();});
                    _initCrudForm();
                })
                .catch(function(){document.getElementById('crudModalBody').innerHTML='<p style="color:#dc2626;text-align:center;padding:1rem;">Failed to load form.</p>';});
        }
        function closeCrudModal(){document.getElementById('crudModalOverlay').classList.remove('open');document.getElementById('crudModalBox').classList.remove('open');document.body.style.overflow='';}
        function _initCrudForm(){
            if(window.initQuillEditors)window.initQuillEditors(document.getElementById('crudModalBody'));
            var form=document.querySelector('#crudModalBody form');if(!form)return;
            form.addEventListener('submit',function(e){
                e.preventDefault();
                var btn=form.querySelector('[type=submit]'),orig=btn?btn.innerHTML:'';
                if(btn){btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Saving...';}
                form.querySelectorAll('.crud-field-error').forEach(function(el){el.remove();});
                form.querySelectorAll('.is-invalid,.error').forEach(function(el){el.classList.remove('is-invalid','error');});
                fetch(form.action,{method:'POST',body:new FormData(form),headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
                .then(function(r){return r.json().then(function(d){return{ok:r.ok,status:r.status,data:d};});})
                .then(function(res){
                    if(res.ok){closeCrudModal();_crudToast('success',res.data.message||'Saved!');setTimeout(function(){if(res.data.redirect)window.location.href=res.data.redirect;else window.location.reload();},1800);}
                    else if(res.status===422){Object.entries(res.data.errors).forEach(function([f,m]){var inp=form.querySelector('[name="'+f+'"]')||form.querySelector('[name="'+f+'[]"]');if(inp){inp.classList.add('error');var err=document.createElement('div');err.className='form-error crud-field-error';err.textContent=m[0];inp.parentNode.insertBefore(err,inp.nextSibling);}});_crudToast('error','Please fix the form errors.');}
                    else{_crudToast('error',(res.data&&res.data.message)||'Something went wrong.');}
                })
                .catch(function(){_crudToast('error','Network error.');})
                .finally(function(){if(btn){btn.disabled=false;btn.innerHTML=orig;}});
            });
        }
        function _crudToast(type,msg){if(typeof Swal!=='undefined'){Swal.fire({icon:type,title:type==='success'?'Saved!':'Error',text:msg,timer:2500,showConfirmButton:false});}else if(typeof showToast!=='undefined'){showToast(msg,type);}else{alert(msg);}}
        document.addEventListener('keydown',function(e){if(e.key==='Escape')closeCrudModal();});
    </script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
    </script>

    @include('partials.toast')
</body>
</html>
