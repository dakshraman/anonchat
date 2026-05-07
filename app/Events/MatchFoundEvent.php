<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchFoundEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatSession $chatSession,
        public int $currentUserId
    ) {}

    public function broadcastOn()
    {
        // Get the other user's ID
        $otherUserId = $this->chatSession->user1_id === $this->currentUserId 
            ? $this->chatSession->user2_id 
            : $this->chatSession->user1_id;

        return new PrivateChannel('user.' . $otherUserId);
    }

    public function broadcastWith()
    {
        return [
            'session_id' => $this->chatSession->id,
            'message' => 'A new chat partner has been found!',
        ];
    }
}