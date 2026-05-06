@extends('layout')

@section('title', 'Dashboard - AnonymousChat')

@section('styles')
<style>
    .glass-panel {
        background: rgba(30, 27, 46, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(196, 181, 253, 0.1);
    }
    .btn-find {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
        transition: all 0.3s ease;
    }
    .btn-find:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    }
    .stat-card {
        background: rgba(42, 38, 64, 0.5);
        border: 1px solid rgba(196, 181, 253, 0.05);
    }
    /* Radar Animation */
    .radar-ping {
        animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
</style>
@endsection

@section('content')
<div class="flex-1 flex flex-col p-4 sm:p-6 max-w-3xl mx-auto w-full relative">

    <!-- Top Header -->
    <div class="flex items-center justify-between mb-6 sm:mb-8 mt-2">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-lavender-500 to-lavender-700 flex items-center justify-center text-xl font-bold shadow-lg shadow-lavender-500/20">
                {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-white">Hello, {{ $user->name ?: 'Anonymous' }}</h1>
                <div class="flex items-center gap-1.5 text-lavender-300 text-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ $user->location ?: 'Unknown' }}</span>
                </div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-panel hover:bg-panelhover text-lavender-300 hover:text-white transition-colors border border-lavender-500/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-4 gap-3 sm:gap-4 mb-8">
        <div class="stat-card rounded-2xl p-3 sm:p-4 text-center">
            <p class="text-xs text-lavender-300/70 mb-1 uppercase tracking-wider font-semibold">Status</p>
            <p class="text-sm sm:text-base font-medium text-white">{{ $user->is_guest ? 'Guest' : 'Member' }}</p>
        </div>
        <div class="stat-card rounded-2xl p-3 sm:p-4 text-center">
            <p class="text-xs text-lavender-300/70 mb-1 uppercase tracking-wider font-semibold">Age</p>
            <p class="text-sm sm:text-base font-medium text-white">{{ $user->age ?: 'N/A' }}</p>
        </div>
        <div class="stat-card rounded-2xl p-3 sm:p-4 text-center">
            <p class="text-xs text-lavender-300/70 mb-1 uppercase tracking-wider font-semibold">Gender</p>
            <p class="text-sm sm:text-base font-medium text-white capitalize">{{ $user->gender }}</p>
        </div>
        <div class="stat-card rounded-2xl p-3 sm:p-4 text-center">
            <p class="text-xs text-lavender-300/70 mb-1 uppercase tracking-wider font-semibold">Target</p>
            <p class="text-sm sm:text-base font-medium text-white capitalize">{{ $user->target_gender }}</p>
        </div>
    </div>

    @if (session('message'))
        <div class="bg-lavender-500/10 border border-lavender-500/30 text-lavender-300 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-medium">{{ session('message') }}</p>
        </div>
    @endif

    <!-- Main Action Area -->
    <div class="flex-1 flex flex-col justify-center items-center">
        @if ($activeSession)
            <div class="glass-panel w-full rounded-3xl p-8 text-center border-green-500/20 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-400 to-emerald-500"></div>
                <div class="w-20 h-20 mx-auto bg-green-500/10 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Active Chat Found!</h2>
                <p class="text-lavender-200/70 mb-8">You are currently connected with a stranger.</p>
                <a href="{{ route('chat', $activeSession->id) }}" class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-10 rounded-xl transition-all shadow-lg shadow-green-500/20 w-full sm:w-auto text-lg">
                    <span>Return to Chat</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        @else
            <div class="glass-panel w-full rounded-3xl p-8 sm:p-12 text-center flex flex-col items-center">
                <div class="relative mb-8">
                    <div class="w-24 h-24 rounded-full bg-lavender-500/10 flex items-center justify-center border border-lavender-500/20 z-10 relative">
                        <svg class="w-10 h-10 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <!-- Radar rings -->
                    <div class="absolute inset-0 rounded-full border-2 border-lavender-500/30 radar-ping"></div>
                    <div class="absolute inset-[-20px] rounded-full border-2 border-lavender-500/10 radar-ping" style="animation-delay: 0.5s"></div>
                </div>

                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Ready to Connect?</h2>
                <p class="text-lavender-200/70 mb-10 max-w-md">Our algorithm will match you with a stranger based on your preferences. Conversations are entirely anonymous.</p>

                <form action="{{ route('find-match') }}" method="POST" class="w-full max-w-xs">
                    @csrf
                    <button type="submit" class="w-full btn-find text-white font-bold py-4 rounded-xl text-lg flex items-center justify-center gap-2 group">
                        <svg class="w-6 h-6 group-hover:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Start Searching</span>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
