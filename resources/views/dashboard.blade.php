@extends('layout')

@section('title', 'Dashboard - AnonChat')

@section('styles')
<style>
    @media (max-width: 480px) {
        .form-container {
            padding: 1.5rem !important;
        }
        .btn-large {
            padding: 1rem !important;
            font-size: 1.1rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen p-4 sm:p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-700 mb-6">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">Welcome{{ $user->name ? ', ' . $user->name : '' }}!</h1>
                    <p class="text-gray-400 text-sm sm:block">📍 {{ $user->location }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white text-sm">
                        Logout
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 text-sm text-gray-400">
                @if($user->age)
                <div>
                    <p>Age: <span class="text-white">{{ $user->age }}</span></div>
                @endif
                <div>
                    <p>Gender: <span class="text-white capitalize">{{ $user->gender }}</span></div>
                <div>
                    <p>Looking for: <span class="text-white capitalize">{{ $user->target_gender }}</span></div>
                <div>
                    <p>Account: <span class="text-white">{{ $user->is_guest ? 'Guest' : 'Registered' }}</span></div>
            </div>

            @if (session('message'))
                <div class="bg-primary/20 border border-primary text-primary px-4 py-3 rounded-lg mt-4">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        @if ($activeSession)
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-4 sm:py-6 rounded-lg mb-6 text-center">
                <p class="font-bold text-lg mb-2">You have an active chat!</p>
                <a href="{{ route('chat', $activeSession->id) }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 sm:py-3 px-6 sm:px-8 rounded-lg transition">
                    Join Chat
                </a>
            </div>
        @else
            <div class="bg-gray-800 rounded-2xl p-6 sm:p-8 border border-gray-700">
                <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 text-center">Find a Chat Partner</h2>
                <p class="text-gray-400 text-center mb-4 sm:mb-6">Connect with strangers anonymously based on your preferences.</p>
                
                <form action="{{ route('find-match') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-600 text-white font-bold py-4 rounded-xl transition text-lg btn-large">
                        Find Match
                    </button>
                </form>
            </div>
        @endif

        <div class="mt-6 text-center">
            <p class="text-gray-500 text-xs sm:text-sm">Stay anonymous. Be respectful.</p>
        </div>
    </div>
</div>
@endsection