@php
    $user = Auth::user();
    $role = $user->role ?? 'guest';
@endphp

{{-- Include the appropriate sidebar based on role --}}
@switch($role)
    @case('admin')
        @include('components.sidebar-admin')
        @break
        
    @case('registrar')
        @include('components.sidebar-registrar')
        @break
        
    @case('teacher')
        @include('components.sidebar-teacher')
        @break
        
    @case('student')
        @include('components.sidebar-student')
        @break
        
    @default
        @include('components.sidebar-guest')
@endswitch