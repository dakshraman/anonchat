<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $sessionId;
    public int $senderId;
    public string $message;
    public string $senderName;
    public string $senderLocation;

    public function __construct(int $sessionId, int $senderId, string $message, string $senderName, string $senderLocation)
    {
        $this->sessionId = $sessionId;
        $this->senderId = $senderId;
        $this->message = $message;
        $this->senderName = $senderName;
        $this->senderLocation = $senderLocation;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.session.' . $this->sessionId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->sessionId,
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
            'sender_location' => $this->senderLocation,
            'message' => $this->message,
            'created_at' => now()->toIso8601String(),
        ];
    }
}