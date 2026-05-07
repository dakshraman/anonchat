@extends('layout')

@section('title', 'Dashboard - AnonymousChat')

@section('styles')
<style>
    .radar-ping {
        position: absolute;
        inset: -20px;
        border: 2px solid rgba(167, 139, 250, 0.4);
        border-radius: 50%;
        animation: radar-ping 2.5s cubic-bezier(0.2, 0.8, 0.2, 1) infinite;
        box-shadow: inset 0 0 20px rgba(167, 139, 250, 0.2);
    }
    .radar-ping-2 {
        animation-delay: 0.8s;
        border-color: rgba(99, 102, 241, 0.3);
    }
    .radar-ping-3 {
        animation-delay: 1.6s;
        border-color: rgba(52, 211, 153, 0.2);
    }
    @keyframes radar-ping {
        0% { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(2.2); opacity: 0; }
    }
    .fade-enter {
        animation: fadeEnter 0.5s ease-out forwards;
    }
    @keyframes fadeEnter {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="container py-4 py-md-5 fade-enter">
    <!-- Header -->
    <header class="d-flex align-items-center justify-content-between mb-5 glass-card p-4 mx-auto" style="max-width: 1200px;">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <div class="glass-sm d-flex align-items-center justify-content-center fw-bold fs-4 text-white" style="width: 56px; height: 56px; border-radius: 1.2rem; background: linear-gradient(135deg, rgba(167,139,250,0.2), rgba(99,102,241,0.2));">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="position-absolute bottom-0 end-0 bg-success border border-3 border-dark rounded-circle" style="width: 16px; height: 16px; box-shadow: 0 0 10px rgba(34,197,94,0.5);"></div>
            </div>
            <div>
                <h1 class="h4 fw-bold text-white mb-1" style="letter-spacing: -0.02em;">Welcome, <span style="background: linear-gradient(135deg, #a78bfa, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ auth()->user()->name ?: 'Anonymous' }}</span></h1>
                <p class="small text-secondary mb-0 d-flex align-items-center gap-1 fw-medium">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    {{ auth()->user()->location ?: 'Detecting location...' }}
                </p>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn glass-button-secondary p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 1.2rem;" title="Logout">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </header>

    <div class="mx-auto" style="max-width: 1200px;">
        <livewire:dashboard />
    </div>
</div>
@endsection
