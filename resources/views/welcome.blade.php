@extends('layout')

@section('title', 'AnonymousChat - Connect with Strangers')

@section('styles')
<style>
    .fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('content')
<div class="container min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="row w-100 align-items-center gy-5">
        <!-- Hero Section -->
        <div class="col-lg-7 text-center text-lg-start pe-lg-5">
            <div class="glass-sm d-inline-block p-3 mb-4 fade-in-up" style="border-radius: 2rem;">
                <img src="{{ asset('favicon.png') }}" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <h1 class="display-2 fw-800 text-white mb-3 fade-in-up delay-1" style="letter-spacing: -0.02em; line-height: 1.1;">
                <span style="background: linear-gradient(135deg, #a78bfa, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Connect</span><br>
                Anonymously.
            </h1>
            <p class="lead text-secondary-emphasis mb-5 fs-4 fw-light fade-in-up delay-2" style="max-width: 500px; color: #cbd5e1 !important;">
                The most secure way to connect with strangers worldwide. <span class="fw-medium text-white">Fully anonymous, real-time, and beautifully designed.</span>
            </p>
            <div class="d-none d-lg-flex align-items-center gap-4 text-secondary text-uppercase fw-bold small tracking-widest fade-in-up delay-3">
                <div class="d-flex align-items-center gap-2 glass-sm px-4 py-2 rounded-pill">
                    <div class="rounded-circle bg-success" style="width: 8px; height: 8px; box-shadow: 0 0 12px rgba(52,211,153,0.8);"></div>
                    <span class="text-white">Active</span>
                </div>
                <span>•</span>
                <span class="text-white" style="opacity: 0.8;">End-to-End Encrypted</span>
            </div>
        </div>

        <!-- Forms Container -->
        <div class="col-lg-5 fade-in-up delay-2">
            <div class="glass-card p-4 p-md-5 position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(to right, #a78bfa, #6366f1);"></div>
                
                <h2 class="h4 fw-bold text-white mb-4 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-white bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border: 1px solid rgba(255,255,255,0.1);">
                        <svg class="text-lavender" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    Quick Start
                </h2>
                
                <form action="{{ route('guest') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control glass-input w-100" placeholder="Choose a nickname (Optional)">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <input type="number" name="age" class="form-control glass-input w-100" placeholder="Age">
                        </div>
                        <div class="col-6 position-relative">
                            <select name="gender" required class="form-select glass-input w-100 appearance-none">
                                <option value="" disabled selected>I am...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <select name="target_gender" required class="form-select glass-input w-100 appearance-none">
                            <option value="any">Chat with anyone</option>
                            <option value="male">Men</option>
                            <option value="female">Women</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    <button type="submit" class="btn glass-button w-100 py-3 fs-5 mb-4 d-flex justify-content-center align-items-center gap-2">
                        Find Partner
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                </form>

                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('register') }}" wire:navigate class="btn glass-button-secondary w-100 py-3">Register</a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('login') }}" wire:navigate class="btn glass-button-secondary w-100 py-3">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    fetch('https://ipapi.co/json/')
        .then(response => response.json())
        .then(data => {
            const locEl = document.getElementById('location-mobile');
            if(locEl) locEl.textContent = data.city + ', ' + data.country;
        })
        .catch(() => {});
</script>
@endsection
