@extends('layouts.guest')

@section('title', 'Login - ADSCO LMS')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-school-navy text-white text-center">
                    <h4 class="mb-0">ADSCO LMS Login</h4>
                    <small>Learning Management System</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="login" class="form-label">Email or ID</label>
                            <input type="text" 
                                class="form-control @error('login') is-invalid @enderror" 
                                id="login" 
                                name="login" 
                                value="{{ old('login') }}" 
                                required 
                                autofocus
                                placeholder="Enter email or ID">
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>
                        
                        <div class="text-center">
                            <a href="#" class="text-decoration-none">Forgot Password?</a>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-2">Don't have an account?</p>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            Register New Account
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>© {{ date('Y') }} ADSCO LMS. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection