<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/guest', [AuthController::class, 'guest'])->name('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [ChatController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::post('/find-match', [ChatController::class, 'findMatch'])->middleware('auth')->name('find-match');

Route::get('/chat/{sessionId}', [ChatController::class, 'chat'])->middleware('auth')->name('chat');
Route::post('/chat/{sessionId}/send', [ChatController::class, 'sendMessage'])->middleware('auth')->name('chat.send');
Route::post('/chat/{sessionId}/typing', [ChatController::class, 'typing'])->middleware('auth')->name('chat.typing');
Route::post('/chat/{sessionId}/end', [ChatController::class, 'endChat'])->middleware('auth')->name('chat.end');
Route::post('/chat/{sessionId}/skip', [ChatController::class, 'skipChat'])->middleware('auth')->name('chat.skip');
Route::get('/chat/{sessionId}/messages', [ChatController::class, 'getMessages'])->middleware('auth')->name('chat.messages');