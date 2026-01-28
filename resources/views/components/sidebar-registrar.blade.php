<div class="sidebar bg-school-navy text-white p-3">
    <div class="sidebar-header text-center mb-4">
        <h5 class="text-heritage-gold">ADSCO LMS</h5>
        <small>Registrar Portal</small>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('registrar.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('registrar.dashboard') ? 'active bg-heritage-gold text-dark' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a href="{{ route('registrar.users.index') }}" 
               class="nav-link text-white {{ request()->routeIs('registrar.users.*') ? 'active bg-heritage-gold text-dark' : '' }}">
                <i class="fas fa-users me-2"></i> Manage Users
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a href="{{ route('registrar.approvals') }}" 
               class="nav-link text-white {{ request()->routeIs('registrar.approvals') ? 'active bg-heritage-gold text-dark' : '' }}">
                <i class="fas fa-user-check me-2"></i> Pending Approvals
                @if($pendingCount = \App\Models\User::where('is_approved', 0)->whereIn('role', [3,4])->count())
                    <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a href="#" class="nav-link text-white">
                <i class="fas fa-file-alt me-2"></i> Reports
            </a>
        </li>
        
        <hr class="text-light">
        
        <li class="nav-item">
            <a href="{{ route('profile.edit') }}" class="nav-link text-white">
                <i class="fas fa-user me-2"></i> My Profile
            </a>
        </li>
    </ul>
</div>