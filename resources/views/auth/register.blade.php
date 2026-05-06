@extends('layout')

@section('title', 'Register - AnonymousChat')

@section('content')
<div class="container min-vh-100 d-flex align-items-center justify-content-center p-4 py-5">
    <div class="w-100" style="max-width: 600px;">
        <div class="glass-card p-4 p-md-5" style="border-radius: 3.5rem;">
            <div class="text-center mb-5">
                <div class="glass-card d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; border-radius: 2rem; background: rgba(255, 255, 255, 0.05);">
                    <img src="{{ asset('favicon.png') }}" alt="Logo" style="width: 50px; height: 50px;">
                </div>
                <h2 class="h2 fw-bold text-white mb-2">Create Account</h2>
                <p class="text-secondary fw-medium">Join the most secure anonymous chat</p>
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

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-widest ms-2 mb-2">Nickname</label>
                        <input type="text" name="name" required class="form-control glass-input" placeholder="Alex">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-widest ms-2 mb-2">Email</label>
                        <input type="email" name="email" required class="form-control glass-input" placeholder="name@example.com">
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-widest ms-2 mb-2">Password</label>
                        <input type="password" name="password" required minlength="8" class="form-control glass-input" placeholder="Min 8 chars">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase tracking-widest ms-2 mb-2">Confirm</label>
                        <input type="password" name="password_confirmation" required class="form-control glass-input" placeholder="Repeat password">
                    </div>
                </div>

                <div class="pt-4 mt-4 border-top border-white border-opacity-5">
                    <p class="small fw-bold text-lavender text-uppercase tracking-widest mb-4 ms-2">Personal Identity</p>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label text-secondary text-uppercase fw-bold" style="font-size: 0.65rem; tracking-widest: 0.1em; margin-left: 0.5rem;">Age</label>
                            <input type="number" name="age" class="form-control glass-input" placeholder="21">
                        </div>
                        <div class="col-4">
                            <label class="form-label text-secondary text-uppercase fw-bold" style="font-size: 0.65rem; tracking-widest: 0.1em; margin-left: 0.5rem;">I am</label>
                            <select name="gender" required class="form-select glass-input">
                                <option value="" disabled selected>...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label text-secondary text-uppercase fw-bold" style="font-size: 0.65rem; tracking-widest: 0.1em; margin-left: 0.5rem;">Seeking</label>
                            <select name="target_gender" required class="form-select glass-input">
                                <option value="any">Anyone</option>
                                <option value="male">Men</option>
                                <option value="female">Women</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn glass-button w-100 py-3 fs-5 mt-5 mb-4">
                    Complete Registration
                </button>
            </form>

            <div class="text-center pt-4 border-top border-white border-opacity-5">
                <p class="small text-secondary mb-3">
                    Already have an account? <a href="{{ route('login') }}" class="text-lavender fw-bold text-decoration-none">Sign in here</a>
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
