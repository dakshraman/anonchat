@extends('layout')

@section('title', 'Login - AnonChat')

@section('styles')
<style>
    @media (max-width: 480px) {
        .form-container {
            padding: 1.5rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl p-6 sm:p-8 border border-gray-700 max-w-md w-full form-container">
        <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center">Welcome Back</h2>
        
        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-2">Email</label>
                <input type="email" name="email" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                    placeholder="your@email.com">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                    placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-indigo-600 text-white font-bold py-3 rounded-lg transition">
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-400">
                New here? <a href="{{ route('register') }}" class="text-primary hover:underline">Create account</a>
            </p>
        </div>
        
        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="text-gray-500 hover:underline">← Back to home</a>
        </div>
    </div>
</div>
@endsection