@extends('layout')

@section('title', 'Dashboard - AnonymousChat')

@section('styles')
<style>
    .radar-ping {
        position: absolute;
        inset: -20px;
        border: 2px solid rgba(139, 92, 246, 0.3);
        border-radius: 50%;
        animation: radar-ping 3s infinite;
    }
    .radar-ping-2 {
        animation-delay: 1s;
    }
    @keyframes radar-ping {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.8); opacity: 0; }
    }
</style>
@endsection

@section('content')
<div class="container py-4 py-md-5">
    <!-- Header -->
    <header class="d-flex align-items-center justify-content-between mb-5">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <div class="glass-card d-flex align-items-center justify-content-center fw-bold fs-4 text-white" style="width: 60px; height: 60px; border-radius: 1.2rem;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="position-absolute bottom-0 end-0 bg-success border border-4 border-dark rounded-circle" style="width: 18px; height: 18px;"></div>
            </div>
            <div>
                <h1 class="h3 fw-bold text-white mb-0">Welcome, <span style="color: #a78bfa;">{{ auth()->user()->name ?: 'Anonymous' }}</span></h1>
                <p class="small text-secondary mb-0 d-flex align-items-center gap-1">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    {{ auth()->user()->location ?: 'Detecting location...' }}
                </p>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn glass-button-secondary p-3" style="border-radius: 1rem;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </header>

    <livewire:dashboard />
</div>
@endsection
