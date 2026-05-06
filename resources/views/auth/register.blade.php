@extends('layout')

@section('title', 'Register - AnonymousChat')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 sm:p-6 relative py-12">
    <!-- Ambient Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-lavender-600/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-xl">
        <div class="glass rounded-[2.5rem] p-8 sm:p-10 shadow-2xl relative overflow-hidden">
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-lavender-500/10 flex items-center justify-center mb-6 border border-lavender-500/20 overflow-hidden">
                    <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-full h-full object-cover p-3">
                </div>
                <h2 class="text-3xl font-black text-white mb-2 tracking-tight">Create Account</h2>
                <p class="text-gray-500 font-medium">Join the world's most secure anonymous chat</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl text-sm font-medium">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Nickname</label>
                        <input type="text" name="name" required 
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-lavender-500/50 transition-all placeholder-gray-700"
                            placeholder="Alex">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" name="email" required 
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-lavender-500/50 transition-all placeholder-gray-700"
                            placeholder="name@example.com">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Password</label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-lavender-500/50 transition-all placeholder-gray-700"
                            placeholder="Min 8 characters">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Confirm</label>
                        <input type="password" name="password_confirmation" required 
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-lavender-500/50 transition-all placeholder-gray-700"
                            placeholder="Repeat password">
                    </div>
                </div>

                <div class="pt-2 border-t border-white/5">
                    <p class="text-xs font-bold text-lavender-400 uppercase tracking-widest mb-5">Personal Preferences</p>
                    <div class="grid sm:grid-cols-3 gap-5">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Your Age</label>
                            <input type="number" name="age" 
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-lavender-500/50 transition-all placeholder-gray-700"
                                placeholder="21">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">You are</label>
                            <select name="gender" required class="w-full bg-panel border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-lavender-500/50 transition-all appearance-none">
                                <option value="" disabled selected>...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Target</label>
                            <select name="target_gender" required class="w-full bg-panel border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-lavender-500/50 transition-all appearance-none">
                                <option value="any">Anyone</option>
                                <option value="male">Men</option>
                                <option value="female">Women</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-lavender-600 to-indigo-600 text-white font-black py-5 rounded-2xl text-lg shadow-xl shadow-lavender-600/20 hover:scale-[1.01] active:scale-[0.99] transition-all">
                    Complete Registration
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-white/5 text-center space-y-4">
                <p class="text-sm text-gray-500 font-medium">
                    Already have an account? <a href="{{ route('login') }}" class="text-lavender-400 hover:text-lavender-300 transition-colors">Sign in here</a>
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs text-gray-600 hover:text-gray-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection