<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADSCO</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Force white theme */
        body {
            background-color: white !important;
        }
        /* Custom colors */
        .bg-adsco-blue {
            background-color: #270e00;
        }
        .text-adsco-gold {
            color: #ffc400;
        }
        .bg-adsco-gold {
            background-color: #ffc400;
        }
    </style>
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- logo -->
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}?v=1" type="image/png" sizes="128x128">
</head>
<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-16 w-auto" src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo">
                    <span class="ml-3 text-xl font-bold text-gray-900">ADSCO</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-900 hover:text-blue-600 font-medium">Home</a>
                    <a href="#about" class="text-gray-900 hover:text-blue-600 font-medium">About</a>
                    <a href="#contact" class="text-gray-900 hover:text-blue-600 font-medium">Contact</a>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-900 hover:text-blue-900 font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-900 hover:text-blue-600 font-medium">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 px-4 py-2 bg-[#002147] text-white font-medium rounded-md hover:bg-[#021199]">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-24 pb-12 sm:pt-32 sm:pb-16 lg:pt-40 lg:pb-20 overflow-hidden">
        <!-- All background layers -->
        <div class="absolute inset-0">
            <!-- Navy Blue base -->
            <div class="absolute inset-0 bg-adsco-blue"></div>
            
            <!-- Image overlay -->
            <div 
                class="absolute inset-0 opacity-20 bg-cover bg-center"
                style="background-image: url('/assets/img/adsco-image1.jpg');"
            ></div>
            
            <!-- Dark gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-transparent from-40% via-transparent via-60% to-black/25 to-100%"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center md:text-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">
                        Start your journey with 
                        <span class="text-adsco-gold">
                            ADSCO
                        </span>
                    </h1>
                    <p class="mt-6 text-xl text-gray-200 max-w-2xl">
                        To develop young, dynamic, responsible and successful leaders, professionals, entrepreneurs, and citizens who can contribute to the growth and development
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="{{ route('login') }}" class="px-8 py-3 bg-[#d3541b] text-white font-medium rounded-lg hover:bg-[#d3541b] transition duration-300">
                            Get started
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#about" class="px-8 py-3 bg-white/10 backdrop-blur-sm text-white font-medium rounded-lg border border-white/20 hover:bg-white/20 transition duration-300">
                            Learn more
                            <i class="fas fa-book ml-2"></i>
                        </a>
                    </div>
                    
                    <!-- Stats/Highlights -->
                    <div class="mt-12 flex flex-wrap gap-6 justify-center md:justify-start">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-adsco-gold">100+</div>
                            <div class="text-gray-300">Successful Graduates</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-adsco-gold">98%</div>
                            <div class="text-gray-300">Placement Rate</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-adsco-gold">10+</div>
                            <div class="text-gray-300">Years Experience</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content -->
                <div class="relative">
                    <!-- Main Image Container -->
                    <!-- <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition-transform duration-300">
                        <img src="/assets/img/adsco-image1.jpg" alt="ADSCO Campus" class="w-full h-auto object-cover"> -->
                        
                        <!-- Image Overlay Effect -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="mt-16 text-center">
                <a href="#about" class="inline-block animate-bounce">
                    <div class="w-8 h-12 border-2 border-white/50 rounded-full flex justify-center">
                        <div class="w-1 h-3 bg-white/70 rounded-full mt-2"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-12 sm:py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl lg:text-5xl">
                    Everything you need to succeed
                </h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                    Our platform provides all the tools for your learning journey
                </p>
            </div>
            
            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-md transition">
                    <div class="w-12 h-12 bg-adsco-blue rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb text-white text-xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">New Ideas</h3>
                    <p class="mt-2 text-gray-600">
                        Learn from today's information and get ahead of the curve.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-md transition">
                    <div class="w-12 h-12 bg-adsco-blue rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Comprehensive Curriculum</h3>
                    <p class="mt-2 text-gray-600">
                        Structured learning paths designed to take you from beginner to expert.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-md transition">
                    <div class="w-12 h-12 bg-adsco-blue rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Flexible Scheduling</h3>
                    <p class="mt-2 text-gray-600">
                        Learn at your own pace with 24/7 access to all course materials.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-12 sm:py-16 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl lg:text-5xl">
                    About ADSCO
                </h2>
                <p class="mt-4 max-w-3xl mx-auto text-xl text-gray-600">
                    A legacy of quality education in Agusan del Sur since 1966
                </p>
            </div>
            
            <!-- Mission & Vision Cards -->
            <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Mission Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg border-l-4 border-[#002147] transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#002147] rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Our Mission</h3>
                    </div>
                    <div class="space-y-4">
                        <p class="text-gray-600 flex items-start">
                            <span class="text-[#002147] font-bold mr-2">•</span>
                            To develop young, dynamic, responsible and successful leaders, professionals, entrepreneurs, and citizens who can contribute to the growth and development of our country.
                        </p>
                        <p class="text-gray-600 flex items-start">
                            <span class="text-[#002147] font-bold mr-2">•</span>
                            To mold young individuals intellectually, socially, emotionally, physically, and spiritually by providing a comprehensive curriculum and support activities.
                        </p>
                        <p class="text-gray-600 flex items-start">
                            <span class="text-[#002147] font-bold mr-2">•</span>
                            To enhance the potential of each individual through conceptual, technical, experiential and practical learning.
                        </p>
                        <p class="text-gray-600 flex items-start">
                            <span class="text-[#002147] font-bold mr-2">•</span>
                            To provide competent and professional teaching staff responsive to student needs.
                        </p>
                    </div>
                </div>

                <!-- Vision Card -->
                <div class="bg-white p-8 rounded-xl shadow-lg border-l-4 border-[#D4AF37] transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#D4AF37] rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Our Vision</h3>
                    </div>
                    <p class="text-gray-600 text-lg italic">
                        "A higher quality of life for the people of Bayugan, Agusan del Sur through quality education."
                    </p>
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                        <p class="text-gray-700">
                            <span class="font-semibold text-[#D4AF37]">ADSCO Learn:</span> A self-paced advanced Learning Management System ensuring compliance with the Data Privacy Act of 2012.
                        </p>
                    </div>
                </div>
            </div>

            <!-- History Timeline -->
            <div class="mt-16 bg-white p-8 rounded-xl shadow-lg">
                <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Our Journey</h3>
                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-4 md:left-1/2 transform md:-translate-x-1/2 h-full w-1 bg-gradient-to-b from-[#002147] to-[#D4AF37]"></div>
                    
                    <!-- Timeline items -->
                    <div class="space-y-12">
                        <!-- 1966 -->
                        <div class="relative flex flex-col md:flex-row items-center">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-8 h-8 bg-[#002147] rounded-full border-4 border-white z-10"></div>
                            <div class="md:w-1/2 md:pr-12 md:text-right ml-12 md:ml-0">
                                <h4 class="text-xl font-bold text-gray-900">1966</h4>
                                <p class="text-gray-600">Founded as <span class="font-semibold text-[#002147]">Southern Agusan Institute (SAI)</span> by Dr. Inocencio P. Angeles and his wife Clarita J. Angeles</p>
                            </div>
                            <div class="md:w-1/2 md:pl-12 mt-4 md:mt-0 ml-12 md:ml-0">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700">Humble beginnings with just a two-storey building offering high school education</p>
                                </div>
                            </div>
                        </div>

                        <!-- 1968 -->
                        <div class="relative flex flex-col md:flex-row items-center">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-8 h-8 bg-[#002147] rounded-full border-4 border-white z-10"></div>
                            <div class="md:w-1/2 md:pr-12 md:text-right ml-12 md:ml-0">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700">Government recognition for Collegiate and Vocational courses, renamed to <span class="font-semibold text-[#002147]">Southern Agusan College</span></p>
                                </div>
                            </div>
                            <div class="md:w-1/2 md:pl-12 mt-4 md:mt-0 ml-12 md:ml-0">
                                <h4 class="text-xl font-bold text-gray-900">1968</h4>
                                <p class="text-gray-600">Expansion to 14+12 rooms to accommodate growing student population</p>
                            </div>
                        </div>

                        <!-- 1975 -->
                        <div class="relative flex flex-col md:flex-row items-center">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-8 h-8 bg-[#002147] rounded-full border-4 border-white z-10"></div>
                            <div class="md:w-1/2 md:pr-12 md:text-right ml-12 md:ml-0">
                                <h4 class="text-xl font-bold text-gray-900">1975</h4>
                                <p class="text-gray-600">Renamed to <span class="font-semibold text-[#002147]">Agusan Del Sur College (ADSCO)</span></p>
                            </div>
                            <div class="md:w-1/2 md:pl-12 mt-4 md:mt-0 ml-12 md:ml-0">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700">Became a leading educational institution in Agusan del Sur</p>
                                </div>
                            </div>
                        </div>

                        <!-- Present -->
                        <div class="relative flex flex-col md:flex-row items-center">
                            <div class="absolute left-2 md:left-1/2 md:-translate-x-1/2 w-8 h-8 bg-[#D4AF37] rounded-full border-4 border-white z-10"></div>
                            <div class="md:w-1/2 md:pr-12 md:text-right ml-12 md:ml-0">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700">Now offering diverse programs from Kindergarten to College including TESDA courses</p>
                                </div>
                            </div>
                            <div class="md:w-1/2 md:pl-12 mt-4 md:mt-0 ml-12 md:ml-0">
                                <h4 class="text-xl font-bold text-gray-900">Present</h4>
                                <p class="text-gray-600">Modern facilities including gymnasium, conference rooms, and advanced educational technology</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Founders Tribute -->
            <div class="mt-16 bg-gradient-to-r from-[#002147] to-[#001a3a] text-white p-8 rounded-xl shadow-lg">
                <div class="max-w-3xl mx-auto text-center">
                    <h3 class="text-2xl font-bold mb-6">A Legacy of Service</h3>
                    <p class="text-lg mb-6">
                        Founded with the insurance money from their daughter's passing, Dr. Inocencio P. Angeles and Clarita J. Angeles turned personal tragedy into a lasting legacy for the people of Agusan.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-8 mt-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-[#D4AF37] rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <p class="font-semibold">Dr. Inocencio P. Angeles</p>
                            <p class="text-gray-300 text-sm">Founder & Pioneer</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-[#D4AF37] rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <p class="font-semibold">Clarita J. Angeles</p>
                            <p class="text-gray-300 text-sm">Co-Founder & Visionary</p>
                        </div>
                    </div>
                    <p class="mt-8 italic">
                        "A DREAM COME TRUE. A VISION REALIZED. A GOAL ATTAINED."
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-12 sm:py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl lg:text-5xl">
                    Contact Us
                </h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                    Have questions? We're here to help!
                </p>
            </div>
            
            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-8 rounded-xl">
                    <h3 class="text-2xl font-semibold text-gray-900">Get in Touch</h3>
                    <div class="mt-6 space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-[#002147]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-600">
                                adsco1966@gmail.com
                            </p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-[#002147]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-600">
                                (085) 231-2150 
                            </p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-[#002147]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-600">
                                Purok 24, Pan-Philippine Hwy, Bayugan City, Agusan Del Sur, PH.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-8 rounded-xl">
                    <h3 class="text-2xl font-semibold text-gray-900">Send Us a Message</h3>
                    <form method="POST" action="#">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#002147] focus:border-[#002147]">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#002147] focus:border-[#002147]">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#002147] focus:border-[#002147]"></textarea>
                        </div>
                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#002147] hover:bg-[#001a3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#002147]">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->

    <!-- Footer -->
    <footer class="bg-adsco-blue text-white pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-6">
                        <img class="h-32 w-auto" src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo">
                        <span class="ml-3 text-2xl font-bold text-adsco-gold">ADSCO</span>
                    </div>
                    <p class="text-gray-300 mb-6 max-w-md">
                        Agusan Del Sur College - Providing quality education in Bayugan City since 1966. 
                        Empowering students with knowledge, skills, and values for a better future.
                    </p>
                </div>
                
                <!-- Links -->
                <div>
                    <h3 class="text-lg font-bold mb-6">COMPANY</h3>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-gray-300 hover:text-white">About Us</a></li>
                        <li><a href="#contact" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-6">LEGAL</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-700">
                <p class="text-center text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Agusan Del Sur College. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>