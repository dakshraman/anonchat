@extends('layout')

@section('title', 'Register - AnonChat')

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
        <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center">Create Account</h2>
        
        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-2">Name</label>
                <input type="text" name="name" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                    placeholder="Your name">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Email</label>
                <input type="email" name="email" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                    placeholder="your@email.com">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Password</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                    placeholder="Min 8 characters">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                    placeholder="Confirm password">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Age (Optional)</label>
                <input type="number" name="age"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                    placeholder="Any age">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">I am</label>
                <select name="gender" required class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Want to chat with</label>
                <select name="target_gender" required class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary">
                    <option value="any">Anyone</option>
                    <option value="male">Men</option>
                    <option value="female">Women</option>
                    <option value="both">Both</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-indigo-600 text-white font-bold py-3 rounded-lg transition">
                Create Account
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-400">
                Already have account? <a href="{{ route('login') }}" class="text-primary hover:underline">Login</a>
            </p>
        </div>
        
        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="text-gray-500 hover:underline">← Back to home</a>
        </div>
    </div>
</div>
@endsection