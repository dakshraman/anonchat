<?php

use App\Models\ChatSession;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.session.{sessionId}', function ($user, $sessionId) {
    $session = ChatSession::where('id', $sessionId)
        ->where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })
        ->where('status', 'active')
        ->first();

    return $session !== null;
});