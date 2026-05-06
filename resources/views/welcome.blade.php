@extends('layout')

@section('title', 'AnonymousChat - Real-Time Chat')

@section('styles')
<style>
    .glass-panel {
        background: rgba(30, 27, 46, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(196, 181, 253, 0.1);
    }
    .input-field {
        background: rgba(19, 17, 28, 0.8);
        border: 1px solid rgba(196, 181, 253, 0.2);
        transition: all 0.3s ease;
    }
    .input-field:focus {
        border-color: #a78bfa;
        box-shadow: 0 0 0 2px rgba(167, 139, 250, 0.2);
    }
    .hero-gradient {
        background: linear-gradient(135deg, #a78bfa, #c4b5fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .btn-primary {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    .btn-secondary {
        background: rgba(42, 38, 64, 0.8);
        border: 1px solid rgba(196, 181, 253, 0.2);
        transition: all 0.2s;
    }
    .btn-secondary:hover {
        background: rgba(76, 29, 149, 0.4);
        border-color: rgba(167, 139, 250, 0.4);
    }
</style>
@endsection

@section('content')
<div class="flex-1 flex flex-col items-center justify-center p-4 sm:p-8 relative overflow-hidden">
    <!-- Background Accents -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-lavender-900/20 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-lavender-800/20 blur-[100px] pointer-events-none"></div>

    <div class="text-center mb-10 z-10 w-full max-w-lg">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-panel mb-6 shadow-lg border border-lavender-500/20">
            <svg class="w-10 h-10 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </div>
        <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight hero-gradient mb-4">
            AnonymousChat
        </h1>
        <p class="text-lg sm:text-xl text-lavender-200/70 font-medium">Connect instantly. Stay anonymous.</p>
    </div>

    <div class="w-full max-w-4xl z-10 grid md:grid-cols-2 gap-6 relative">
        <!-- Guest Form -->
        <div class="glass-panel rounded-3xl p-6 sm:p-8 shadow-2xl">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 rounded-full bg-lavender-500/20 flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Quick Start</h2>
            </div>
            <form action="{{ route('guest') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <input type="text" name="name"
                        class="w-full input-field rounded-xl px-4 py-3.5 text-white placeholder-lavender-200/30 focus:outline-none"
                        placeholder="Choose a nickname (Optional)">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <input type="number" name="age"
                            class="w-full input-field rounded-xl px-4 py-3.5 text-white placeholder-lavender-200/30 focus:outline-none"
                            placeholder="Your age">
                    </div>
                    <div>
                        <select name="gender" required class="w-full input-field rounded-xl px-4 py-3.5 text-white appearance-none focus:outline-none bg-darkbg cursor-pointer">
                            <option value="" disabled selected class="text-gray-500">I am a...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div>
                    <select name="target_gender" required class="w-full input-field rounded-xl px-4 py-3.5 text-white appearance-none focus:outline-none bg-darkbg cursor-pointer">
                        <option value="any">Chat with anyone</option>
                        <option value="male">Chat with Men</option>
                        <option value="female">Chat with Women</option>
                        <option value="both">Chat with Both</option>
                    </select>
                </div>
                <button type="submit" class="w-full btn-primary text-white font-bold py-4 rounded-xl text-lg mt-2 flex items-center justify-center group">
                    <span>Find a Partner</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </form>
        </div>

        <!-- Auth Section -->
        <div class="glass-panel rounded-3xl p-6 sm:p-8 shadow-2xl flex flex-col justify-center">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-full bg-lavender-500/10 flex items-center justify-center mb-4 border border-lavender-500/20">
                    <svg class="w-8 h-8 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Save Your Chats</h2>
                <p class="text-lavender-200/60 text-sm">Create an account to keep your identity and view past conversations.</p>
            </div>

            <div class="space-y-4 w-full">
                <a href="{{ route('register') }}" class="flex items-center justify-center w-full btn-secondary text-white font-semibold py-3.5 rounded-xl">
                    Create Account
                </a>
                <a href="{{ route('login') }}" class="flex items-center justify-center w-full bg-transparent hover:bg-white/5 border border-transparent hover:border-white/10 text-lavender-300 font-semibold py-3.5 rounded-xl transition-all">
                    Sign In
                </a>
            </div>
        </div>
    </div>

    <div class="absolute bottom-6 z-10 flex items-center gap-2 text-lavender-200/40 text-sm font-medium bg-darkbg/50 px-4 py-2 rounded-full backdrop-blur-sm border border-lavender-500/10">
        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
        <span>Location: <span id="location" class="text-lavender-200/80">Detecting...</span></span>
    </div>
</div>

<script>
    fetch('https://ipapi.co/json/')
        .then(response => response.json())
        .then(data => {
            document.getElementById('location').textContent = data.city + ', ' + data.country;
        })
        .catch(() => {
            document.getElementById('location').textContent = 'Hidden';
        });
</script>
@endsection
