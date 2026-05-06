@extends('layout')

@section('title', 'Login - AnonymousChat')

@section('content')
<div class="container min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="w-100" style="max-width: 450px;">
        <div class="glass-card p-4 p-md-5" style="border-radius: 3rem;">
            <div class="text-center mb-5">
                <div class="glass-card d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; border-radius: 2rem; background: rgba(255, 255, 255, 0.05);">
                    <img src="{{ asset('favicon.png') }}" alt="Logo" style="width: 50px; height: 50px;">
                </div>
                <h2 class="h2 fw-bold text-white mb-2">Welcome Back</h2>
                <p class="text-secondary fw-medium">Log in to your secure account</p>
            </div>

            @if ($errors->any())
                <div class="alert bg-danger bg-opacity-10 border-danger border-opacity-20 text-danger p-3 rounded-4 small mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-widest ms-2 mb-2">Email Address</label>
                    <input type="email" name="email" required class="form-control glass-input" placeholder="name@example.com">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-widest ms-2 mb-2">Password</label>
                    <input type="password" name="password" required class="form-control glass-input" placeholder="••••••••">
                </div>
                <button type="submit" class="btn glass-button w-100 py-3 fs-5 mb-4">
                    Sign In
                </button>
            </form>

            <div class="text-center pt-4 border-top border-white border-opacity-5">
                <p class="small text-secondary mb-3">
                    Don't have an account? <a href="{{ route('register') }}" class="text-lavender fw-bold text-decoration-none">Create one now</a>
                </p>
                <a href="{{ route('home') }}" class="small text-secondary text-uppercase tracking-widest fw-bold text-decoration-none d-flex align-items-center justify-content-center gap-2">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
