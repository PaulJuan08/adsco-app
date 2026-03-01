@extends('layouts.student')

@section('title', 'Colleges - Student Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Colleges & Departments</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-university"></i> Browse all academic colleges and their programs
                    </p>
                </div>
            </div>
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-university"></i>
                    <span class="badge-count">{{ $totalColleges }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Active Colleges</div>
                    <div class="alert-subtitle">{{ $totalPrograms }} programs available</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Colleges</div>
                    <div class="stat-number">{{ number_format($totalColleges) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-university"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Programs</div>
                    <div class="stat-number">{{ number_format($totalPrograms) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-section" style="margin-bottom: 2rem;">
        <div class="search-container" style="max-width: 500px; margin: 0 auto;">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search colleges..." id="search-colleges">
        </div>
    </div>

    <!-- Colleges Grid -->
    @if($colleges->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-university"></i>
            </div>
            <h3 class="empty-title">No colleges available</h3>
            <p class="empty-text">There are no active colleges at the moment.</p>
        </div>
    @else
        <div class="colleges-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
            @foreach($colleges as $college)
            @php
                $encryptedId = Crypt::encrypt($college->id);
            @endphp
            <div class="college-card" style="background: white; border-radius: 12px; box-shadow: var(--shadow-md); overflow: hidden; transition: all 0.3s ease; cursor: pointer;" onclick="window.location.href='{{ route('student.colleges.show', $encryptedId) }}'">
                <div class="college-header" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 1.5rem; color: white; position: relative;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold;">
                            {{ strtoupper(substr($college->college_name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">{{ $college->college_name }}</h3>
                            <p style="margin: 0.25rem 0 0; opacity: 0.9; font-size: 0.85rem;">
                                <i class="fas fa-calendar-alt"></i> Est. {{ $college->created_at->format('Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="college-body" style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: #4f46e5;">{{ $college->programs_count }}</div>
                            <div style="font-size: 0.85rem; color: #6b7280;">Programs</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ $college->students_count }}</div>
                            <div style="font-size: 0.85rem; color: #6b7280;">Students</div>
                        </div>
                    </div>
                    
                    @if($college->description)
                    <p style="color: #4b5563; font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem;">
                        {{ Str::limit($college->description, 100) }}
                    </p>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-success" style="background: #d1fae5; color: #059669; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem;">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                        <span style="color: #4f46e5; font-size: 0.9rem;">
                            View Programs <i class="fas fa-arrow-right"></i>
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($colleges->hasPages())
        <div style="margin-top: 2rem;">
            {{ $colleges->links() }}
        </div>
        @endif
    @endif

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search-colleges');
        const collegeCards = document.querySelectorAll('.college-card');
        
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                collegeCards.forEach(card => {
                    const collegeName = card.querySelector('h3').textContent.toLowerCase();
                    
                    if (searchTerm === '' || collegeName.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush