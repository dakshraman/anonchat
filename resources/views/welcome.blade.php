@extends('layout')

@section('title', 'AnonChat - Anonymous Real-Time Chat')

@section('styles')
<style>
    @media (max-width: 768px) {
        .auth-grid {
            grid-template-columns: 1fr;
        }
        .hero-title {
            font-size: 2.5rem !important;
        }
    }
    @media (max-width: 480px) {
        .form-container {
            padding: 1.5rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">
    <div class="text-center mb-8 sm:mb-12">
        <h1 class="text-4xl sm:text-6xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-4 hero-title">
            AnonChat
        </h1>
        <p class="text-lg sm:text-xl text-gray-400">Anonymous real-time chat with strangers</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6 sm:gap-8 max-w-4xl w-full auth-grid">
        <div class="bg-gray-800 rounded-2xl p-6 sm:p-8 border border-gray-700 form-container">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Continue as Guest</h2>
            <form action="{{ route('guest') }}" method="POST" class="space-y-3 sm:space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Your Name (Optional)</label>
                    <input type="text" name="name"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                        placeholder="Anonymous">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Your Age</label>
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
                    Start Chatting
                </button>
            </form>
        </div>

        <div class="bg-gray-800 rounded-2xl p-6 sm:p-8 border border-gray-700 form-container">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Sign Up / Login</h2>
            <div class="space-y-3 sm:space-y-4">
                <a href="{{ route('register') }}" class="block w-full bg-secondary hover:bg-purple-600 text-white font-bold py-3 rounded-lg transition text-center">
                    Create Account
                </a>
                <a href="{{ route('login') }}" class="block w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition text-center">
                    Login
                </a>
            </div>
            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-700">
                <p class="text-gray-400 text-sm">
                    Create an account to save your profile and chat history.
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 sm:mt-12 text-center text-gray-500 text-sm">
        <p>Your IP location: <span id="location">Detecting...</span></p>
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