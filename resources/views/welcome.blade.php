@extends('layout')

@section('title', 'AnonymousChat - Connect with Strangers')

@section('content')
<div class="container min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="row w-100 align-items-center gy-5">
        <!-- Hero Section -->
        <div class="col-lg-7 text-center text-lg-start">
            <div class="glass-card d-inline-block p-4 mb-4" style="border-radius: 2.5rem;">
                <img src="{{ asset('favicon.png') }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
            </div>
            <h1 class="display-2 fw-800 text-white mb-3" style="font-weight: 800; line-height: 1.1;">
                <span style="background: linear-gradient(to right, #a78bfa, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Connect</span><br>
                Anonymously.
            </h1>
            <p class="lead text-secondary-emphasis mb-4 fs-4 fw-medium" style="max-width: 500px;">
                The most secure way to connect with strangers worldwide. <span style="color: #c4b5fd;">Fully anonymous, real-time.</span>
            </p>
            <div class="d-none d-lg-flex align-items-center gap-4 text-secondary text-uppercase fw-bold small tracking-widest">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success" style="width: 10px; height: 10px; box-shadow: 0 0 10px rgba(34,197,94,0.5);"></div>
                    <span>1,240 Online</span>
                </div>
                <span>•</span>
                <span>End-to-End Encrypted</span>
            </div>
        </div>

        <!-- Forms Container -->
        <div class="col-lg-5">
            <div class="glass-card p-4 p-md-5" style="border-radius: 3rem; position: relative;">
                <h2 class="h3 fw-bold text-white mb-4 d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-white bg-opacity-10 d-flex align-items-center justify-center p-2" style="width: 40px; height: 40px;">
                        <svg class="text-lavender" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
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

                    <button type="submit" class="btn glass-button w-100 py-3 fs-5 mb-4">
                        Find Partner
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
