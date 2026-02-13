<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Agusan Del Sur College - Quality education in Bayugan City since 1966. Empowering students with knowledge, skills, and values for a better future.">
    <title>ADSCO - Agusan Del Sur College</title>
    
    <!-- Vite for CSS (Tailwind v4 + Custom CSS) -->
    @vite(['resources/css/app.css', 'resources/css/welcome.css'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Logo -->
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}?v=1" type="image/png" sizes="128x128">
    
    <!-- Preload critical assets -->
    <link rel="preload" href="{{ asset('assets/img/adsco-logo.png') }}" as="image">
    
    <!-- Development fallback -->
    @env('local')
    <style>
        .vite-loading body { opacity: 0; transition: opacity 0.3s ease-in; }
        .no-vite .bg-adsco-primary { background-color: #552b20 !important; }
        .no-vite .bg-adsco-brown { background-color: #552b20 !important; }
        .no-vite .bg-adsco-accent { background-color: #ddb238 !important; }
        .no-vite .bg-adsco-gold { background-color: #ddb238 !important; }
        .no-vite .bg-adsco-secondary { background-color: #d3541b !important; }
        .no-vite .bg-adsco-orange { background-color: #d3541b !important; }
        .no-vite .text-adsco-gold { color: #ddb238 !important; }
        .no-vite .text-adsco-primary { color: #552b20 !important; }
        .no-vite .text-adsco-brown { color: #552b20 !important; }
        .no-vite .btn-register { background-color: #552b20 !important; }
        .no-vite .btn-submit { background-color: #552b20 !important; }
        .no-vite footer { background-color: #552b20 !important; }
    </style>
    
    <script>
        setTimeout(() => {
            const stylesLoaded = Array.from(document.styleSheets).some(sheet => 
                sheet.href && (sheet.href.includes('app.css') || sheet.href.includes('welcome.css'))
            );
            
            if (!stylesLoaded) {
                console.log('Vite not detected, adding fallback classes');
                document.documentElement.classList.add('no-vite');
                
                const script = document.createElement('script');
                script.src = 'https://cdn.tailwindcss.com';
                script.onload = () => {
                    tailwind.config = {
                        theme: {
                            extend: {
                                colors: {
                                    'adsco-primary': '#552b20',
                                    'adsco-brown': '#552b20',
                                    'adsco-accent': '#ddb238',
                                    'adsco-gold': '#ddb238',
                                    'adsco-secondary': '#d3541b',
                                    'adsco-orange': '#d3541b',
                                    'adsco-neutral': '#aa988b',
                                    'adsco-taupe': '#aa988b',
                                    'adsco-light': '#fefdfb',
                                    'adsco-offwhite': '#fefdfb',
                                }
                            }
                        }
                    }
                };
                document.head.appendChild(script);
            }
        }, 2000);
    </script>
    @endenv
</head>

<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm border-b border-gray-200 fixed w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center group">
                    <img class="h-16 w-auto transition-transform duration-300 group-hover:scale-110" 
                         src="{{ asset('assets/img/adsco-logo.png') }}" 
                         alt="ADSCO Logo">
                    <span class="ml-3 text-xl font-bold text-adsco-primary transition-colors duration-300 group-hover:text-adsco-accent">ADSCO</span>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-adsco-primary focus:outline-none" id="mobile-menu-button">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="nav-link text-gray-900 hover:text-adsco-primary font-medium transition-colors duration-300 relative">
                        Home
                    </a>
                    <a href="#about" class="nav-link text-gray-900 hover:text-adsco-primary font-medium transition-colors duration-300 relative">
                        About
                    </a>
                    <a href="#contact" class="nav-link text-gray-900 hover:text-adsco-primary font-medium transition-colors duration-300 relative">
                        Contact
                    </a>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="nav-link text-gray-900 hover:text-adsco-primary font-medium transition-colors duration-300 relative">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="nav-link text-gray-900 hover:text-adsco-primary font-medium transition-colors duration-300 relative">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 px-6 py-2.5 btn-register text-white font-medium rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                <a href="/" class="block px-3 py-2 text-gray-900 hover:bg-gray-50 hover:text-adsco-primary font-medium rounded-md transition-colors duration-300">Home</a>
                <a href="#about" class="block px-3 py-2 text-gray-900 hover:bg-gray-50 hover:text-adsco-primary font-medium rounded-md transition-colors duration-300">About</a>
                <a href="#contact" class="block px-3 py-2 text-gray-900 hover:bg-gray-50 hover:text-adsco-primary font-medium rounded-md transition-colors duration-300">Contact</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block px-3 py-2 text-gray-900 hover:bg-gray-50 hover:text-adsco-primary font-medium rounded-md transition-colors duration-300">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-900 hover:bg-gray-50 hover:text-adsco-primary font-medium rounded-md transition-colors duration-300">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block mx-3 my-2 px-3 py-2 btn-register text-white font-medium rounded-md text-center">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-24 pb-16 sm:pt-32 sm:pb-20 lg:pt-40 lg:pb-24 overflow-hidden hero-bg-brown">
        <!-- Background layers -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-adsco-primary"></div>
            <div 
                class="absolute inset-0 opacity-20 bg-cover bg-center"
                style="background-image: url('/assets/img/adsco-image1.jpg');"
            ></div>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        
        <!-- Floating particles decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center md:text-left fade-in-up">
                    <div class="inline-block mb-4 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
                        <span class="text-adsco-accent font-semibold text-sm">🎓 Since 1966</span>
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                        Start your journey with 
                        <span class="text-adsco-accent inline-block hover:scale-105 transition-transform duration-300">ADSCO</span>
                    </h1>
                    
                    <p class="text-xl text-gray-200 max-w-2xl mb-8 leading-relaxed">
                        To develop young, dynamic, responsible and successful leaders, professionals, entrepreneurs, and citizens who can contribute to the growth and development
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start mb-12">
                        <a href="{{ route('login') }}" class="group px-8 py-4 btn-primary font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                            Get started
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                        </a>
                        <a href="#about" class="group px-8 py-4 btn-outline-light font-medium rounded-xl backdrop-blur-sm hover:backdrop-blur-md transition-all duration-300 flex items-center justify-center">
                            Learn more
                            <i class="fas fa-book ml-2 group-hover:rotate-12 transition-transform duration-300"></i>
                        </a>
                    </div>
                    
                    <!-- Stats/Highlights -->
                    <div class="flex flex-wrap gap-8 justify-center md:justify-start">
                        <div class="text-center transform hover:scale-110 transition-transform duration-300">
                            <div class="text-4xl font-bold stat-number mb-1 counter" data-target="100">0</div>
                            <div class="text-gray-300 text-sm">Successful Graduates</div>
                        </div>
                        <div class="text-center transform hover:scale-110 transition-transform duration-300">
                            <div class="text-4xl font-bold stat-number mb-1 counter" data-target="98">0</div>
                            <div class="text-gray-300 text-sm">Placement Rate</div>
                        </div>
                        <div class="text-center transform hover:scale-110 transition-transform duration-300">
                            <div class="text-4xl font-bold stat-number mb-1 counter" data-target="10">0</div>
                            <div class="text-gray-300 text-sm">Years Experience</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - Decorative Element -->
                <div class="relative hidden md:block">
                    <div class="relative z-10 animate-float">
                        <div class="absolute top-10 right-10 w-72 h-72 bg-adsco-accent/20 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-10 left-10 w-64 h-64 bg-adsco-secondary/20 rounded-full blur-3xl"></div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="mt-20 text-center">
                <a href="#features" class="inline-flex flex-col items-center scroll-indicator">
                    <span class="text-white/70 text-sm mb-2 animate-pulse">Scroll to explore</span>
                    <div class="w-8 h-12 border-2 border-white/30 rounded-full flex justify-center hover:border-white/50 transition-colors duration-300">
                        <div class="w-1 h-3 bg-white/70 rounded-full mt-2 animate-scroll-down"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-16 sm:py-20 lg:py-24 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in-up">
                <span class="inline-block px-4 py-2 bg-adsco-accent/10 text-adsco-primary font-semibold rounded-full mb-4">
                    Why Choose ADSCO
                </span>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl lg:text-5xl mb-4">
                    Everything you need to succeed
                </h2>
                <p class="max-w-2xl mx-auto text-xl text-gray-600">
                    Our platform provides all the tools for your learning journey
                </p>
            </div>
            
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                    <div class="w-16 h-16 feature-icon rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-lightbulb text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-adsco-primary transition-colors duration-300">
                        Innovative Learning
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Learn from today's information and get ahead of the curve with cutting-edge educational approaches.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                    <div class="w-16 h-16 feature-icon rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-book text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-adsco-primary transition-colors duration-300">
                        Comprehensive Curriculum
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Structured learning paths designed to take you from beginner to expert in your chosen field.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                    <div class="w-16 h-16 feature-icon rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-adsco-primary transition-colors duration-300">
                        Flexible Scheduling
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Learn at your own pace with 24/7 access to all course materials and resources.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                    <div class="w-16 h-16 feature-icon rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-adsco-primary transition-colors duration-300">
                        Expert Faculty
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Learn from experienced professionals who are passionate about student success.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                    <div class="w-16 h-16 feature-icon rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-adsco-primary transition-colors duration-300">
                        Industry Recognition
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Earn certificates and credentials recognized by leading employers nationwide.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card group bg-white p-8 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                    <div class="w-16 h-16 feature-icon rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-hands-helping text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-adsco-primary transition-colors duration-300">
                        Career Support
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Comprehensive career guidance and job placement assistance for all graduates.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 sm:py-20 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in-up">
                <span class="inline-block px-4 py-2 bg-adsco-accent/10 text-adsco-primary font-semibold rounded-full mb-4">
                    Our Story
                </span>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl lg:text-5xl mb-4">
                    About ADSCO
                </h2>
                <p class="max-w-3xl mx-auto text-xl text-gray-600">
                    A legacy of quality education in Agusan del Sur since 1966
                </p>
            </div>
            
            <!-- Mission & Vision Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
                <!-- Mission Card -->
                <div class="mission-card bg-white p-10 rounded-2xl shadow-xl transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 border-l-4 border-adsco-primary">
                    <div class="flex items-center mb-8">
                        <div class="w-16 h-16 mission-icon rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900">Our Mission</h3>
                    </div>
                    <div class="space-y-5">
                        <p class="text-gray-700 flex items-start leading-relaxed pl-1">
                            <span class="text-adsco-primary font-bold mr-3 text-xl">•</span>
                            <span>To develop young, dynamic, responsible and successful leaders, professionals, entrepreneurs, and citizens who can contribute to the growth and development of our country.</span>
                        </p>
                        <p class="text-gray-700 flex items-start leading-relaxed pl-1">
                            <span class="text-adsco-primary font-bold mr-3 text-xl">•</span>
                            <span>To mold young individuals intellectually, socially, emotionally, physically, and spiritually by providing a comprehensive curriculum and support activities.</span>
                        </p>
                        <p class="text-gray-700 flex items-start leading-relaxed pl-1">
                            <span class="text-adsco-primary font-bold mr-3 text-xl">•</span>
                            <span>To enhance the potential of each individual through conceptual, technical, experiential and practical learning.</span>
                        </p>
                        <p class="text-gray-700 flex items-start leading-relaxed pl-1">
                            <span class="text-adsco-primary font-bold mr-3 text-xl">•</span>
                            <span>To provide competent and professional teaching staff responsive to student needs.</span>
                        </p>
                    </div>
                </div>

                <!-- Vision Card -->
                <div class="vision-card bg-white p-10 rounded-2xl shadow-xl transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 border-l-4 border-adsco-accent">
                    <div class="flex items-center mb-8">
                        <div class="w-16 h-16 vision-icon rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900">Our Vision</h3>
                    </div>
                    <div class="space-y-6">
                        <blockquote class="text-gray-700 text-lg italic leading-relaxed pl-4 border-l-4 border-adsco-accent">
                            "A higher quality of life for the people of Bayugan, Agusan del Sur through quality education."
                        </blockquote>
                        <div class="p-6 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl border border-yellow-200 shadow-sm">
                            <p class="text-gray-800 leading-relaxed">
                                <span class="font-bold text-adsco-accent block mb-2">ADSCO Learn</span>
                                A self-paced advanced Learning Management System ensuring compliance with the Data Privacy Act of 2012, providing secure and efficient education delivery.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Timeline -->
            <div class="bg-white p-10 rounded-2xl shadow-xl fade-in-up">
                <h3 class="text-3xl font-bold text-gray-900 mb-12 text-center">Our Journey Through Time</h3>
                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-4 md:left-1/2 transform md:-translate-x-1/2 h-full w-1 timeline-line"></div>
                    
                    <!-- Timeline items -->
                    <div class="space-y-16">
                        <!-- 1966 -->
                        <div class="relative flex flex-col md:flex-row items-center timeline-item">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-10 h-10 timeline-dot-brown rounded-full border-4 border-white shadow-lg z-10 flex items-center justify-center">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <div class="md:w-1/2 md:pr-16 md:text-right ml-16 md:ml-0">
                                <div class="inline-block px-4 py-2 bg-adsco-primary text-white font-bold rounded-lg mb-3 shadow-md">
                                    1966
                                </div>
                                <p class="text-gray-700 leading-relaxed">Founded as <span class="font-bold text-adsco-primary">Southern Agusan Institute (SAI)</span> by Dr. Inocencio P. Angeles and his wife Clarita J. Angeles</p>
                            </div>
                            <div class="md:w-1/2 md:pl-16 mt-4 md:mt-0 ml-16 md:ml-0">
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <p class="text-gray-700 leading-relaxed">Humble beginnings with just a two-storey building offering high school education</p>
                                </div>
                            </div>
                        </div>

                        <!-- 1968 -->
                        <div class="relative flex flex-col md:flex-row items-center timeline-item">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-10 h-10 timeline-dot-brown rounded-full border-4 border-white shadow-lg z-10 flex items-center justify-center">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <div class="md:w-1/2 md:pr-16 md:text-right ml-16 md:ml-0">
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <p class="text-gray-700 leading-relaxed">Government recognition for Collegiate and Vocational courses, renamed to <span class="font-bold text-adsco-primary">Southern Agusan College</span></p>
                                </div>
                            </div>
                            <div class="md:w-1/2 md:pl-16 mt-4 md:mt-0 ml-16 md:ml-0">
                                <div class="inline-block px-4 py-2 bg-adsco-primary text-white font-bold rounded-lg mb-3 shadow-md">
                                    1968
                                </div>
                                <p class="text-gray-700 leading-relaxed">Expansion to 14+12 rooms to accommodate growing student population</p>
                            </div>
                        </div>

                        <!-- 1975 -->
                        <div class="relative flex flex-col md:flex-row items-center timeline-item">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-10 h-10 timeline-dot-brown rounded-full border-4 border-white shadow-lg z-10 flex items-center justify-center">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <div class="md:w-1/2 md:pr-16 md:text-right ml-16 md:ml-0">
                                <div class="inline-block px-4 py-2 bg-adsco-primary text-white font-bold rounded-lg mb-3 shadow-md">
                                    1975
                                </div>
                                <p class="text-gray-700 leading-relaxed">Renamed to <span class="font-bold text-adsco-primary">Agusan Del Sur College (ADSCO)</span></p>
                            </div>
                            <div class="md:w-1/2 md:pl-16 mt-4 md:mt-0 ml-16 md:ml-0">
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <p class="text-gray-700 leading-relaxed">Became a leading educational institution in Agusan del Sur</p>
                                </div>
                            </div>
                        </div>

                        <!-- Present -->
                        <div class="relative flex flex-col md:flex-row items-center timeline-item">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-10 h-10 timeline-dot-gold rounded-full border-4 border-white shadow-lg z-10 flex items-center justify-center animate-pulse-slow">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <div class="md:w-1/2 md:pr-16 md:text-right ml-16 md:ml-0">
                                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-6 rounded-xl border border-yellow-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <p class="text-gray-700 leading-relaxed">Now offering diverse programs from Kindergarten to College including TESDA courses</p>
                                </div>
                            </div>
                            <div class="md:w-1/2 md:pl-16 mt-4 md:mt-0 ml-16 md:ml-0">
                                <div class="inline-block px-4 py-2 bg-gradient-to-r from-adsco-accent to-adsco-secondary text-black font-bold rounded-lg mb-3 shadow-md">
                                    Present
                                </div>
                                <p class="text-gray-700 leading-relaxed">Modern facilities including gymnasium, conference rooms, and advanced educational technology</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Founders Tribute -->
            <div class="mt-16 founder-gradient text-white p-10 rounded-2xl shadow-2xl fade-in-up">
                <div class="max-w-4xl mx-auto text-center">
                    <h3 class="text-3xl font-bold mb-8">A Legacy of Service</h3>
                    <p class="text-lg mb-8 leading-relaxed opacity-95">
                        Founded with the insurance money from their daughter's passing, Dr. Inocencio P. Angeles and Clarita J. Angeles turned personal tragedy into a lasting legacy for the people of Agusan.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-12 mt-10">
                        <div class="text-center group">
                            <div class="w-20 h-20 founder-icon-bg rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <p class="font-bold text-lg">Dr. Inocencio P. Angeles</p>
                            <p class="text-gray-300 text-sm mt-1">Founder & Pioneer</p>
                        </div>
                        <div class="text-center group">
                            <div class="w-20 h-20 founder-icon-bg rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <p class="font-bold text-lg">Clarita J. Angeles</p>
                            <p class="text-gray-300 text-sm mt-1">Co-Founder & Visionary</p>
                        </div>
                    </div>
                    <blockquote class="mt-10 text-xl italic font-semibold border-t border-white/30 pt-8">
                        "A DREAM COME TRUE. A VISION REALIZED. A GOAL ATTAINED."
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 sm:py-20 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in-up">
                <span class="inline-block px-4 py-2 bg-adsco-accent/10 text-adsco-primary font-semibold rounded-full mb-4">
                    Get In Touch
                </span>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl lg:text-5xl mb-4">Contact Us</h2>
                <p class="max-w-2xl mx-auto text-xl text-gray-600">Have questions? We're here to help!</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Contact Info -->
                <div class="bg-gradient-to-br from-gray-50 to-white p-10 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">Get in Touch</h3>
                    <div class="space-y-6">
                        <div class="flex items-start group">
                            <div class="flex-shrink-0 h-12 w-12 contact-icon bg-adsco-primary/10 rounded-lg flex items-center justify-center group-hover:bg-adsco-primary/20 transition-colors duration-300">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-900 mb-1">Email</p>
                                <a href="mailto:adsco1966@gmail.com" class="text-gray-600 hover:text-adsco-primary transition-colors duration-300">adsco1966@gmail.com</a>
                            </div>
                        </div>
                        
                        <div class="flex items-start group">
                            <div class="flex-shrink-0 h-12 w-12 contact-icon bg-adsco-primary/10 rounded-lg flex items-center justify-center group-hover:bg-adsco-primary/20 transition-colors duration-300">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-900 mb-1">Phone</p>
                                <a href="tel:+6385231-2150" class="text-gray-600 hover:text-adsco-primary transition-colors duration-300">(085) 231-2150</a>
                            </div>
                        </div>
                        
                        <div class="flex items-start group">
                            <div class="flex-shrink-0 h-12 w-12 contact-icon bg-adsco-primary/10 rounded-lg flex items-center justify-center group-hover:bg-adsco-primary/20 transition-colors duration-300">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-900 mb-1">Address</p>
                                <p class="text-gray-600 leading-relaxed">
                                    Purok 24, Pan-Philippine Hwy,<br>
                                    Bayugan City, Agusan Del Sur, PH
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Map placeholder -->
                    <div class="mt-8 h-48 bg-gray-200 rounded-xl overflow-hidden shadow-inner">
                        <div class="w-full h-full flex items-center justify-center text-gray-500">
                            <i class="fas fa-map-marked-alt text-4xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="bg-gradient-to-br from-gray-50 to-white p-10 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">Send Us a Message</h3>
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg animate-fade-in">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg animate-fade-in">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autocomplete="name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-adsco-primary focus:border-transparent transition-all duration-300"
                                   placeholder="John Doe">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-adsco-primary focus:border-transparent transition-all duration-300"
                                   placeholder="john@example.com">
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="5" 
                                      required 
                                      autocomplete="off"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-adsco-primary focus:border-transparent transition-all duration-300 resize-none"
                                      placeholder="Tell us how we can help you...">{{ old('message') }}</textarea>
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-lg shadow-lg text-base font-semibold text-white btn-submit hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                <span>Send Message</span>
                                <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-adsco-primary text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-6 group">
                        <img class="h-24 w-auto transition-transform duration-300 group-hover:scale-110" 
                             src="{{ asset('assets/img/adsco-logo.png') }}" 
                             alt="ADSCO Logo">
                        <span class="ml-4 text-3xl font-bold text-adsco-accent">ADSCO</span>
                    </div>
                    <p class="text-gray-300 mb-6 max-w-md leading-relaxed">
                        Agusan Del Sur College - Providing quality education in Bayugan City since 1966. 
                        Empowering students with knowledge, skills, and values for a better future.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-6 text-adsco-accent">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-gray-300 hover:text-white hover:pl-2 transition-all duration-300 inline-block">About Us</a></li>
                        <li><a href="#features" class="text-gray-300 hover:text-white hover:pl-2 transition-all duration-300 inline-block">Features</a></li>
                        <li><a href="#contact" class="text-gray-300 hover:text-white hover:pl-2 transition-all duration-300 inline-block">Contact</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-300 hover:text-white hover:pl-2 transition-all duration-300 inline-block">Login</a></li>
                    </ul>
                </div>
                
                <!-- Legal -->
                <div>
                    <h3 class="text-lg font-bold mb-6 text-adsco-accent">Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-white hover:pl-2 transition-all duration-300 inline-block">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white hover:pl-2 transition-all duration-300 inline-block">Terms & Conditions</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white hover:pl-2 transition-all duration-300 inline-block">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-white/10">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        &copy; {{ date('Y') }} Agusan Del Sur College. All rights reserved.
                    </p>
                    <p class="text-gray-400 text-sm">
                        Crafted with <span class="text-red-400">❤</span> for better education
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 bg-adsco-primary text-white rounded-full shadow-lg opacity-0 invisible hover:bg-adsco-accent transition-all duration-300 hover:scale-110 z-40">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        });

        // Counter animation
        const counters = document.querySelectorAll('.counter');
        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current) + (counter.textContent.includes('%') ? '%' : '+');
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target + (target < 100 && target > 10 ? '%' : '+');
                }
            };
            
            updateCounter();
        };

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.2,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    
                    // Animate counters when visible
                    if (entry.target.classList.contains('counter')) {
                        animateCounter(entry.target);
                    }
                    
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('.fade-in-up, .feature-card, .timeline-item, .counter').forEach(el => {
            observer.observe(el);
        });

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        const offsetTop = target.offsetTop - 80;
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                        
                        // Close mobile menu if open
                        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                            mobileMenu.classList.add('hidden');
                        }
                    }
                }
            });
        });

        // Back to top button
        const backToTopButton = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.add('opacity-0', 'invisible');
                backToTopButton.classList.remove('opacity-100', 'visible');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Add active state to nav links
        const navLinks = document.querySelectorAll('.nav-link');
        window.addEventListener('scroll', () => {
            let current = '';
            const sections = document.querySelectorAll('section[id]');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>