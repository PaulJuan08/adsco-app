<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<nav class="auth-navbar" id="navbar">
    <div class="navbar-container">
        <a href="/" class="navbar-brand">
            <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="navbar-logo">
            <span class="navbar-brand-text">ADSCO</span>
        </a>

        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="navbar-links" id="navbarLinks">
            <a href="/" class="navbar-link {{ ($activePage ?? '') === 'home' ? 'active' : '' }}">Home</a>

            @auth
            <a href="{{ url('/dashboard') }}" class="navbar-link">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="navbar-link {{ ($activePage ?? '') === 'login' ? 'active' : '' }}">Log in</a>
            <a href="{{ route('register') }}" class="navbar-btn">Register</a>
            @endauth
        </div>
    </div>

    <div class="navbar-mobile-menu" id="mobileMenu">
        <a href="/" class="navbar-mobile-link">Home</a>
        @auth
        <a href="{{ url('/dashboard') }}" class="navbar-mobile-link">Dashboard</a>
        @else
        <a href="{{ route('login') }}" class="navbar-mobile-link">Log in</a>
        <a href="{{ route('register') }}" class="navbar-mobile-link navbar-mobile-btn">Register</a>
        @endauth
    </div>
</nav>
<script>
(function () {
    var btn = document.getElementById('mobileMenuBtn');
    var menu = document.getElementById('mobileMenu');
    if (btn && menu) {
        btn.addEventListener('click', function () {
            menu.classList.toggle('open');
        });
    }

    var navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
})();
</script>
