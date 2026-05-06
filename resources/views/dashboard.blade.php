@extends('layout')

@section('title', 'Dashboard - AnonymousChat')

@section('styles')
<style>
    .btn-action {
        background: linear-gradient(135deg, #7C3AED 0%, #A78BFA 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.4);
    }
    .radar-ping {
        animation: radar-ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
    @keyframes radar-ping {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.5); opacity: 0; }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex flex-col p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
    <!-- Navbar-style Header -->
    <header class="flex items-center justify-between mb-8 sm:mb-12">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="relative group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-lavender-500 to-indigo-600 flex items-center justify-center text-xl font-bold shadow-xl">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-4 border-darkbg rounded-full"></div>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white leading-tight">Welcome, {{ $user->name ?: 'Anonymous' }}</h1>
                <p class="text-xs sm:text-sm text-gray-500 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    {{ $user->location ?: 'Detecting location...' }}
                </p>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="glass p-3 rounded-2xl text-gray-400 hover:text-red-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </header>

    <div class="grid lg:grid-cols-12 gap-8 flex-1">
        <!-- Sidebar Info -->
        <aside class="lg:col-span-4 space-y-6 order-2 lg:order-1">
            <div class="glass rounded-[2.5rem] p-6 sm:p-8 space-y-6">
                <h2 class="text-lg font-bold text-lavender-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profile Details
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 rounded-2xl p-4">
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Status</p>
                        <p class="text-sm font-semibold text-white">{{ $user->is_guest ? 'Guest' : 'Member' }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-4">
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Age</p>
                        <p class="text-sm font-semibold text-white">{{ $user->age ?: 'N/A' }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-4">
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Gender</p>
                        <p class="text-sm font-semibold text-white capitalize">{{ $user->gender }}</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-4">
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Target</p>
                        <p class="text-sm font-semibold text-white capitalize">{{ $user->target_gender }}</p>
                    </div>
                </div>
            </div>

            <div class="glass rounded-[2.5rem] p-6 border-lavender-500/5">
                <p class="text-sm text-gray-500 text-center italic">"Stay anonymous. Stay safe. Be respectful to everyone you meet."</p>
            </div>
        </aside>

        <!-- Main Action Section -->
        <main class="lg:col-span-8 order-1 lg:order-2">
            @if (session('message'))
                <div class="mb-6 glass border-lavender-500/20 bg-lavender-500/5 p-4 rounded-2xl flex items-center gap-3 text-lavender-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('message') }}</span>
                </div>
            @endif

            <div class="h-full flex flex-col items-center justify-center py-12 lg:py-0">
                @if ($activeSession)
                    <div class="glass w-full rounded-[3rem] p-8 sm:p-12 text-center border-green-500/10 relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-500 to-emerald-400"></div>
                        <div class="w-24 h-24 mx-auto bg-green-500/10 rounded-full flex items-center justify-center mb-8">
                            <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <h2 class="text-3xl font-black text-white mb-4">Active Chat Found!</h2>
                        <p class="text-gray-400 mb-10 max-w-sm mx-auto">You're currently connected. Don't keep them waiting!</p>
                        <a href="{{ route('chat', $activeSession->id) }}" class="inline-flex items-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold py-5 px-12 rounded-2xl transition-all shadow-xl shadow-green-500/20 text-lg w-full sm:w-auto justify-center">
                            <span>Join Conversation</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                @else
                    <div class="glass w-full rounded-[3rem] p-8 sm:p-12 lg:p-20 text-center flex flex-col items-center relative overflow-hidden">
                        <div class="relative mb-12">
                            <div class="w-32 h-32 rounded-full bg-lavender-500/5 flex items-center justify-center border border-lavender-500/10 z-10 relative overflow-hidden group">
                                <img src="{{ asset('favicon.png') }}" alt="App Icon" class="w-full h-full object-cover p-4 group-hover:scale-110 transition-transform">
                            </div>
                            <div class="absolute inset-0 rounded-full border-2 border-lavender-500/20 radar-ping"></div>
                            <div class="absolute inset-[-20px] rounded-full border-2 border-lavender-500/10 radar-ping" style="animation-delay: 0.6s"></div>
                        </div>

                        <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Ready to Explore?</h2>
                        <p class="text-gray-400 mb-12 max-w-md leading-relaxed font-medium">Click below to start searching for a chat partner. Our matching is fast and secure.</p>
                        
                        <form action="{{ route('find-match') }}" method="POST" class="w-full max-w-sm">
                            @csrf
                            <button type="submit" class="w-full btn-action text-white font-black py-5 rounded-2xl text-xl flex items-center justify-center gap-3 group">
                                <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span>Find Someone</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection