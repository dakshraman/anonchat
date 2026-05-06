@extends('layout')

@section('title', 'Login - AnonymousChat')

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
<div class="flex-1 flex flex-col items-center justify-center p-4 relative overflow-hidden">
    <!-- Background Accents -->
    <div class="absolute top-[10%] right-[10%] w-[30%] h-[30%] rounded-full bg-lavender-800/20 blur-[100px] pointer-events-none"></div>

    <div class="glass-panel rounded-3xl p-6 sm:p-8 shadow-2xl max-w-md w-full form-container z-10 relative">
        <div class="text-center mb-6">
            <div class="w-12 h-12 mx-auto rounded-full bg-lavender-500/10 flex items-center justify-center mb-4 border border-lavender-500/20">
                <svg class="w-6 h-6 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">Welcome Back</h2>
            <p class="text-lavender-200/60 text-sm">Sign in to continue chatting</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">Email Address</label>
                <input type="email" name="email" required
                    class="w-full input-field rounded-xl px-4 py-3.5 text-white placeholder-lavender-200/30 focus:outline-none"
                    placeholder="name@example.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-lavender-200/80 mb-1.5">Password</label>
                <input type="password" name="password" required
                    class="w-full input-field rounded-xl px-4 py-3.5 text-white placeholder-lavender-200/30 focus:outline-none"
                    placeholder="••••••••">
            </div>
            <button type="submit" class="w-full btn-primary text-white font-bold py-3.5 rounded-xl mt-4 flex justify-center items-center gap-2">
                <span>Sign In</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="mt-8 text-center border-t border-lavender-500/10 pt-6">
            <p class="text-lavender-200/60 text-sm">
                New here? <a href="{{ route('register') }}" class="text-lavender-400 hover:text-lavender-300 font-medium transition-colors">Create an account</a>
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
