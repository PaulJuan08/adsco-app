<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Dashboard')</title>
    
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
    
    <!-- Layout CSS (includes sidebar, dropdown, badge, footer styles) -->
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
                        <div class="sidebar-brand-sub">Teacher Panel</div>
                    </div>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Learning Materials Dropdown -->
                <div class="sidebar-dropdown">
                    <div class="sidebar-dropdown-btn {{ request()->routeIs('teacher.courses.*') || request()->routeIs('teacher.topics.*') ? 'active' : '' }}">
                        <i class="fas fa-book-open"></i>
                        <span>Learning Materials</span>
                        <i class="fas fa-chevron-right dropdown-arrow"></i>
                    </div>
                    <div class="sidebar-dropdown-menu">
                        <a href="{{ route('teacher.courses.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span>Courses</span>
                        </a>
                        <a href="{{ route('teacher.topics.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.topics.*') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span>Topics</span>
                        </a>
                    </div>
                </div>

                <!-- To-Do Dropdown -->
                <div class="sidebar-dropdown">
                    @php
                        $teacherId = Auth::id();

                        $pendingQuizzes = \App\Models\Quiz::where('created_by', $teacherId)
                            ->where('is_published', 0)
                            ->count();

                        $pendingAssignments = \App\Models\Assignment::where('created_by', $teacherId)
                            ->where('is_published', 0)
                            ->count();

                        $pendingSubmissions = \App\Models\AssignmentSubmission::whereHas('assignment', function($q) use ($teacherId) {
                                $q->where('created_by', $teacherId);
                            })
                            ->where('status', 'submitted')
                            ->count();

                        $pendingCount = $pendingQuizzes + $pendingAssignments + $pendingSubmissions;
                    @endphp
                    <div class="sidebar-dropdown-btn {{ request()->routeIs('teacher.todo.*') || request()->routeIs('teacher.quizzes.*') || request()->routeIs('teacher.assignments.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>To-Do</span>
                        @if($pendingCount > 0)
                            <span class="badge-count">{{ $pendingCount }}</span>
                        @else
                            <i class="fas fa-chevron-right dropdown-arrow"></i>
                        @endif
                    </div>
                    <div class="sidebar-dropdown-menu">
                        <a href="{{ route('teacher.quizzes.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.quizzes.*') ? 'active' : '' }}">
                            <i class="fas fa-brain"></i>
                            <span>Quizzes</span>
                            @if($pendingQuizzes > 0)
                                <span class="badge-count" style="font-size:0.6rem; padding:0.05rem 0.35rem; min-width:16px;">{{ $pendingQuizzes }}</span>
                            @endif
                        </a>
                        <a href="{{ route('teacher.assignments.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.assignments.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Assignments</span>
                            @if($pendingAssignments > 0)
                                <span class="badge-count" style="font-size:0.6rem; padding:0.05rem 0.35rem; min-width:16px;">{{ $pendingAssignments }}</span>
                            @endif
                        </a>
                        <a href="{{ route('teacher.todo.progress') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.todo.progress*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Progress</span>
                            @if($pendingSubmissions > 0)
                                <span class="badge-count" style="font-size:0.6rem; padding:0.05rem 0.35rem; min-width:16px;">{{ $pendingSubmissions }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Enrollments -->
                <a href="{{ route('teacher.enrollments.index') }}" class="sidebar-nav-item {{ request()->routeIs('teacher.enrollments*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Enrollments</span>
                </a>

                <!-- Discussions -->
                <a href="{{ route('teacher.discussions.index') }}" class="sidebar-nav-item {{ request()->routeIs('teacher.discussions.*') || request()->routeIs('teacher.courses.discussions*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i>
                    <span>Discussions</span>
                </a>

                <!-- Analytics -->
                <a href="{{ route('teacher.analytics.index') }}" class="sidebar-nav-item {{ request()->routeIs('teacher.analytics.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Analytics</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <!-- Profile link -->
                <a href="{{ route('teacher.profile.show') }}" class="sidebar-user-profile-link">
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
                            @php
                                $roleMapping = [
                                    1 => 'Admin',
                                    2 => 'Registrar',
                                    3 => 'Teacher',
                                    4 => 'Student'
                                ];
                                $user     = Auth::user();
                                $roleText = $user ? ($roleMapping[$user->role] ?? 'User') : 'Guest';
                            @endphp
                            <div class="sidebar-user-name">{{ $user ? $user->f_name : 'Guest' }}</div>
                            <div class="sidebar-user-role">{{ $roleText }}</div>
                        </div>
                    </div>
                </a>
                
                <button class="sidebar-nav-item sidebar-logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </aside>
        
        <!-- Main Content -->
        <!-- Mobile sidebar toggle -->
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

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

    {{-- ─── CRUD Modal ─── --}}
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
            document.getElementById('crudModalBox').style.maxWidth = maxWidth || '860px';
            document.getElementById('crudModalTitle').textContent = title;
            document.getElementById('crudModalBody').innerHTML = '<div style="text-align:center;padding:2rem;color:#552b20;font-size:2rem;"><i class="fas fa-spinner fa-spin"></i></div>';
            document.getElementById('crudModalOverlay').classList.add('open');
            document.getElementById('crudModalBox').classList.add('open');
            document.body.style.overflow = 'hidden';
            fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
                .then(function(r){return r.json();})
                .then(function(data){
                    if (data.css && !_crudLoadedCss[data.css]) {
                        var lnk = document.createElement('link');
                        lnk.rel = 'stylesheet'; lnk.href = data.css;
                        document.head.appendChild(lnk);
                        _crudLoadedCss[data.css] = true;
                    }
                    document.getElementById('crudModalBody').innerHTML = data.html;
                    document.querySelectorAll('#crudModalBody script').forEach(function(s){var n=document.createElement('script');n.textContent=s.textContent;document.head.appendChild(n);s.remove();});
                    _initCrudForm();
                })
                .catch(function(){document.getElementById('crudModalBody').innerHTML='<p style="color:#dc2626;text-align:center;padding:1rem;">Failed to load form. Please try again.</p>';});
        }
        function closeCrudModal() {
            document.getElementById('crudModalOverlay').classList.remove('open');
            document.getElementById('crudModalBox').classList.remove('open');
            document.body.style.overflow = '';
        }
        function _initCrudForm() {
            if (window.initQuillEditors) window.initQuillEditors(document.getElementById('crudModalBody'));
            var form = document.querySelector('#crudModalBody form[method="POST"], #crudModalBody form[method="post"]');
            if (!form || form.dataset.noCrud) return;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('[type=submit]');
                var orig = btn ? btn.innerHTML : '';
                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...'; }
                form.querySelectorAll('.crud-field-error').forEach(function(el){el.remove();});
                form.querySelectorAll('.is-invalid,.error').forEach(function(el){el.classList.remove('is-invalid','error');});
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
                })
                .then(function(r){return r.json().then(function(d){return{ok:r.ok,status:r.status,data:d};});})
                .then(function(res){
                    if (res.ok) {
                        closeCrudModal();
                        _crudToast('success', res.data.message || 'Saved successfully!');
                        setTimeout(function(){ if(res.data.redirect) window.location.href=res.data.redirect; else window.location.reload(); }, 1800);
                    } else if (res.status === 422) {
                        Object.entries(res.data.errors).forEach(function([field, msgs]){
                            var inp = form.querySelector('[name="'+field+'"]') || form.querySelector('[name="'+field+'[]"]');
                            if (inp) {
                                inp.classList.add('error');
                                var err = document.createElement('div');
                                err.className = 'form-error crud-field-error';
                                err.textContent = msgs[0];
                                inp.parentNode.insertBefore(err, inp.nextSibling);
                            }
                        });
                        _crudToast('error', 'Please fix the form errors.');
                    } else {
                        _crudToast('error', (res.data&&res.data.message)||'Something went wrong.');
                    }
                })
                .catch(function(){_crudToast('error','Network error. Please try again.');})
                .finally(function(){if(btn){btn.disabled=false;btn.innerHTML=orig;}});
            });
        }
        function _crudToast(type, msg) {
            if (typeof showToast !== 'undefined') {
                showToast(msg, type);
            } else { alert(msg); }
        }
        document.addEventListener('keydown', function(e){if(e.key==='Escape')closeCrudModal();});
    </script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const dropdowns = document.querySelectorAll('.sidebar-dropdown');

            dropdowns.forEach(dropdown => {
                const menu  = dropdown.querySelector('.sidebar-dropdown-menu');
                const arrow = dropdown.querySelector('.dropdown-arrow');

                if (!menu) return;

                let hoverTimeout;
                let isHovering = false;

                const openMenu = () => {
                    cancelAnimationFrame(hoverTimeout);
                    isHovering = true;
                    requestAnimationFrame(() => {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                        menu.style.opacity   = '1';
                        if (arrow) arrow.style.transform = 'rotate(90deg)';
                    });
                };

                const closeMenu = () => {
                    isHovering = false;
                    const hasActiveChild = dropdown.querySelector('.sidebar-dropdown-item.active');
                    if (!hasActiveChild) {
                        hoverTimeout = requestAnimationFrame(() => {
                            if (!isHovering) {
                                menu.style.maxHeight = '0';
                                menu.style.opacity   = '0';
                                if (arrow) arrow.style.transform = 'rotate(0deg)';
                            }
                        });
                    }
                };

                dropdown.addEventListener('mouseenter', openMenu);
                dropdown.addEventListener('mouseleave', closeMenu);
                menu.addEventListener('mouseenter', () => { cancelAnimationFrame(hoverTimeout); isHovering = true; });
                menu.addEventListener('mouseleave', closeMenu);

                // Resize: keep open panels correctly sized
                window.addEventListener('resize', () => {
                    if (menu.style.maxHeight !== '0px' && menu.style.maxHeight !== '') {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                    }
                });
            });

            // Keep dropdowns open when a child link is the active page
            document.querySelectorAll('.sidebar-dropdown-item.active').forEach(item => {
                const dropdown = item.closest('.sidebar-dropdown');
                if (!dropdown) return;
                const menu  = dropdown.querySelector('.sidebar-dropdown-menu');
                const arrow = dropdown.querySelector('.dropdown-arrow');
                if (menu) {
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                    menu.style.opacity   = '1';
                }
                if (arrow) arrow.style.transform = 'rotate(90deg)';
            });
        });
    </script>

    @include('partials.toast')

    {{-- Global auto-filter: client-side card filter or debounced server submit --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[method="GET"]').forEach(function (form) {
            form.querySelectorAll('input[type="text"], input[type="search"]').forEach(function (inp) {
                var selector = inp.dataset.clientFilter;
                if (selector) {
                    // Pure client-side: show/hide cards instantly, no page reload
                    inp.addEventListener('input', function () {
                        var q = inp.value.trim().toLowerCase();
                        var cards = document.querySelectorAll(selector);
                        var visible = 0;
                        cards.forEach(function (card) {
                            var title = (card.querySelector('.todo-card-title') || card).textContent.toLowerCase();
                            var match = !q || title.includes(q);
                            card.style.display = match ? '' : 'none';
                            if (match) visible++;
                        });
                        var empty = document.querySelector('.empty-todo');
                        if (empty) empty.style.display = visible === 0 && q ? '' : 'none';
                    });
                    return;
                }
                if (inp.id === 'search-users' || inp.id === 'search-courses' || inp.id === 'search-topics' || inp.id === 'search-colleges' || inp.id === 'search-programs') return;
                var t;
                inp.addEventListener('input', function () {
                    clearTimeout(t);
                    t = setTimeout(function () { form.submit(); }, 500);
                });
            });
            form.querySelectorAll('select').forEach(function (sel) {
                if (sel.classList.contains('auto-filter')) return;
                sel.addEventListener('change', function () { form.submit(); });
            });
        });
    });
    </script>

    {{-- Global AJAX form interceptor + ajaxDelete helper --}}
    <script>
    (function () {
        var _csrf = function () { var m = document.querySelector('meta[name="csrf-token"]'); return m ? m.content : ''; };

        window.ajaxDelete = function (url, onSuccess) {
            var fd = new FormData();
            fd.append('_method', 'DELETE');
            fd.append('_token', _csrf());
            fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf() },
                body: fd
            })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.message) showToast(d.message, d.type || 'success');
                if (typeof onSuccess === 'function') { onSuccess(d); return; }
                if (d.redirect) { setTimeout(function () { window.location.href = d.redirect; }, 1200); }
                else { setTimeout(function () { window.location.reload(); }, 1200); }
            })
            .catch(function () { showToast('Delete failed. Please try again.', 'error'); });
        };

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (form.closest('#crudModalBody')) return;
            if ((form.getAttribute('method') || '').toLowerCase() === 'get') return;
            if (form.dataset.noAjax || form.dataset.noCrud) return;
            if (form.id === 'itemDeleteForm') return;
            if (form.querySelector('input[type="file"]')) return;

            e.preventDefault();
            var formData = new FormData(form);
            var action = form.getAttribute('action') || window.location.href;

            fetch(action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json, */*', 'X-CSRF-TOKEN': _csrf() },
                body: formData,
                redirect: 'follow'
            })
            .then(function (r) {
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (d) {
                        if (r.ok) {
                            if (d.message) showToast(d.message, d.type || 'success');
                            if (d.redirect) { setTimeout(function () { window.location.href = d.redirect; }, 1200); }
                            else if (d.reload !== false) { setTimeout(function () { window.location.reload(); }, 1200); }
                        } else {
                            showToast(d.message || 'An error occurred.', 'error');
                        }
                    });
                } else {
                    window.location.href = r.url;
                }
            })
            .catch(function () { showToast('Network error. Please try again.', 'error'); });
        });
    })();
    </script>
</body>
</html>