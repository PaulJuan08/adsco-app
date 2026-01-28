<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADSCO LMS - @yield('title')</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        :root {
            --school-navy: #002147;
            --heritage-gold: #D4AF37;
            --slate-grey: #708090;
            --crimson: #990000;
            --pure-white: #FFFFFF;
        }
        
        .bg-school-navy { background-color: var(--school-navy); }
        .text-heritage-gold { color: var(--heritage-gold); }
        .bg-heritage-gold { background-color: var(--heritage-gold); }
        .border-heritage-gold { border-color: var(--heritage-gold); }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--school-navy);
            color: white;
        }
        
        .sidebar a:hover {
            background-color: rgba(212, 175, 55, 0.2);
        }
        
        .card {
            border: 1px solid var(--slate-grey);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: var(--school-navy);
            border-color: var(--school-navy);
        }
        
        .btn-primary:hover {
            background-color: #001835;
            border-color: #001835;
        }
        
        .btn-secondary {
            background-color: var(--heritage-gold);
            border-color: var(--heritage-gold);
            color: var(--school-navy);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            .mobile-menu {
                display: block;
            }
            .desktop-menu {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        @if(auth()->check())
            @include('layouts.navigation')
        @endif
        
        <main>
            @yield('content')
        </main>
        
        @include('layouts.footer')
    </div>
    
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>