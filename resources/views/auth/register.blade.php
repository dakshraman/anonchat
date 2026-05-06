@extends('layout')

@section('title', 'Register - AnonymousChat')

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
    .btn-primary {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    @media (max-width: 480px) {
        .form-container {
            padding: 1.5rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="flex-1 flex flex-col items-center justify-center p-4 relative overflow-hidden py-10">
    <!-- Background Accents -->
    <div class="absolute top-[10%] left-[10%] w-[30%] h-[30%] rounded-full bg-lavender-900/20 blur-[100px] pointer-events-none"></div>

    <div class="glass-panel rounded-3xl p-6 sm:p-8 shadow-2xl max-w-md w-full form-container z-10 relative">
        <div class="text-center mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">Create Account</h2>
            <p class="text-lavender-200/60 text-sm">Join the community and save your settings</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">Nickname</label>
                <input type="text" name="name" required
                    class="w-full input-field rounded-xl px-4 py-3 text-white placeholder-lavender-200/30 focus:outline-none"
                    placeholder="How should we call you?">
            </div>
            <div>
                <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">Email Address</label>
                <input type="email" name="email" required
                    class="w-full input-field rounded-xl px-4 py-3 text-white placeholder-lavender-200/30 focus:outline-none"
                    placeholder="name@example.com">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full input-field rounded-xl px-4 py-3 text-white placeholder-lavender-200/30 focus:outline-none"
                        placeholder="Min 8 chars">
                </div>
                <div>
                    <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">Confirm</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full input-field rounded-xl px-4 py-3 text-white placeholder-lavender-200/30 focus:outline-none"
                        placeholder="Repeat password">
                </div>
            </div>

            <div class="pt-2 pb-1 border-t border-lavender-500/10 mt-2">
                <p class="text-xs text-lavender-200/50 uppercase tracking-wider font-semibold mb-3">Chat Preferences</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">Your Age (Optional)</label>
                    <input type="number" name="age"
                        class="w-full input-field rounded-xl px-4 py-3 text-white placeholder-lavender-200/30 focus:outline-none"
                        placeholder="Age">
                </div>
                <div>
                    <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">You are</label>
                    <select name="gender" required class="w-full input-field rounded-xl px-4 py-3 text-white appearance-none focus:outline-none bg-darkbg cursor-pointer">
                        <option value="" disabled selected class="text-gray-500">Select...</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">I want to chat with</label>
                <select name="target_gender" required class="w-full input-field rounded-xl px-4 py-3 text-white appearance-none focus:outline-none bg-darkbg cursor-pointer">
                    <option value="any">Anyone</option>
                    <option value="male">Men</option>
                    <option value="female">Women</option>
                    <option value="both">Both</option>
                </select>
            </div>
            <button type="submit" class="w-full btn-primary text-white font-bold py-3.5 rounded-xl mt-4 flex justify-center items-center gap-2">
                <span>Join AnonymousChat</span>
            </button>
        </form>

        <div class="mt-8 text-center border-t border-lavender-500/10 pt-6">
            <p class="text-lavender-200/60 text-sm">
                Already have an account? <a href="{{ route('login') }}" class="text-lavender-400 hover:text-lavender-300 font-medium transition-colors">Sign in</a>
            </p>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="text-lavender-200/40 hover:text-lavender-200/80 text-sm flex items-center justify-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to home
            </a>
        </div>
    </div>
</div>
@endsection
