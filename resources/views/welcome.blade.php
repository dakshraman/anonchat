@extends('layout')

@section('title', 'AnonymousChat - Connect with Strangers')

@section('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #A78BFA 0%, #C4B5FD 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .btn-primary {
        background: linear-gradient(135deg, #7C3AED 0%, #A78BFA 100%);
        box-shadow: 0 4px 14px 0 rgba(124, 58, 237, 0.3);
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px 0 rgba(124, 58, 237, 0.4);
    }
    .input-glass {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(167, 139, 250, 0.2);
        color: white;
    }
    .input-glass:focus {
        border-color: #A78BFA;
        background: rgba(255, 255, 255, 0.06);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8 relative">
    <!-- Ambient Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-lavender-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-lavender-800/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-5xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <!-- Hero Section -->
        <div class="text-center lg:text-left space-y-6">
            <div class="inline-block p-4 rounded-3xl glass mb-4">
                <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
            </div>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-none">
                <span class="hero-gradient">AnonymousChat</span>
            </h1>
            <p class="text-lg sm:text-xl text-gray-400 max-w-md mx-auto lg:mx-0 leading-relaxed font-medium">
                The most secure way to connect with strangers worldwide. Fully anonymous, completely real-time.
            </p>
            <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500">
                <div class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <span>1,240 Online</span>
                </div>
                <span>•</span>
                <span>Real-time Matching</span>
            </div>
        </div>

        <!-- Forms Container -->
        <div class="space-y-6 w-full max-w-md mx-auto">
            <!-- Guest Form -->
            <div class="glass p-6 sm:p-8 rounded-[2.5rem] shadow-2xl">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Quick Start
                </h2>
                <form action="{{ route('guest') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="text" name="name" 
                        class="w-full input-glass rounded-2xl px-5 py-4 focus:outline-none transition-all" 
                        placeholder="Choose a nickname (Optional)">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="age" 
                            class="w-full input-glass rounded-2xl px-5 py-4 focus:outline-none transition-all" 
                            placeholder="Age">
                        <select name="gender" required class="w-full input-glass rounded-2xl px-5 py-4 focus:outline-none transition-all appearance-none bg-panel">
                            <option value="" disabled selected>I am...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <select name="target_gender" required class="w-full input-glass rounded-2xl px-5 py-4 focus:outline-none transition-all appearance-none bg-panel">
                        <option value="any">Chat with anyone</option>
                        <option value="male">Men</option>
                        <option value="female">Women</option>
                        <option value="both">Both</option>
                    </select>

                    <button type="submit" class="w-full btn-primary text-white font-bold py-4 rounded-2xl text-lg flex items-center justify-center gap-2 group">
                        <span>Find Partner</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>

            <!-- Auth Buttons -->
            <div class="flex gap-4">
                <a href="{{ route('register') }}" class="flex-1 glass text-center py-4 rounded-2xl font-semibold hover:bg-white/5 transition-all">Register</a>
                <a href="{{ route('login') }}" class="flex-1 glass text-center py-4 rounded-2xl font-semibold hover:bg-white/5 transition-all">Login</a>
            </div>
        </div>
    </div>

    <!-- Footer Mobile Info -->
    <div class="mt-12 lg:hidden text-center text-sm text-gray-500 flex flex-col items-center gap-2">
        <div class="flex items-center gap-1.5">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <span>Location: <span id="location" class="text-gray-300">Detecting...</span></span>
        </div>
    </div>
</div>

<script>
    fetch('https://ipapi.co/json/')
        .then(response => response.json())
        .then(data => {
            document.getElementById('location').textContent = data.city + ', ' + data.country;
        })
        .catch(() => {
            document.getElementById('location').textContent = 'Unknown';
        });
</script>
@endsection