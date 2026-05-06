<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Events\UserTypingEvent;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        $activeSession = ChatSession::where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('status', 'active')->first();

        return view('dashboard', compact('user', 'activeSession'));
    }

    public function findMatch(Request $request)
    {
        $user = auth()->user();

        $targetGenders = [];
        if ($user->target_gender === 'any' || $user->target_gender === 'both') {
            $targetGenders = ['male', 'female', 'other'];
        } else {
            $targetGenders = [$user->target_gender];
        }

        $potentialMatch = User::where('id', '!=', $user->id)
            ->where('is_guest', '!=', false)
            ->where('is_online', true)
            ->whereIn('gender', $targetGenders)
            ->where(function ($query) use ($user) {
                $query->where('target_gender', $user->gender)
                    ->orWhere('target_gender', 'both')
                    ->orWhere('target_gender', 'any');
            })
            ->whereDoesntHave('chatSessionsAsUser1', function ($query) {
                $query->where('status', 'active');
            })
            ->whereDoesntHave('chatSessionsAsUser2', function ($query) {
                $query->where('status', 'active');
            })
            ->first();

        if (!$potentialMatch) {
            $user->update(['is_online' => true]);
            
            $existingWaiting = ChatSession::where('status', 'waiting')
                ->where('user1_id', '!=', $user->id)
                ->whereHas('user1', function ($query) use ($user, $targetGenders) {
                    $query->whereIn('gender', $targetGenders)
                        ->where(function ($q) use ($user) {
                            $q->where('target_gender', $user->gender)
                                ->orWhere('target_gender', 'both')
                                ->orWhere('target_gender', 'any');
                        });
                })
                ->first();

            if ($existingWaiting) {
                $existingWaiting->update([
                    'user2_id' => $user->id,
                    'status' => 'active',
                    'started_at' => now(),
                ]);
                
                $user->update(['is_online' => false]);
                $existingWaiting->user1->update(['is_online' => false]);
                
                return redirect("/chat/{$existingWaiting->id}");
            }

            ChatSession::create([
                'user1_id' => $user->id,
                'user2_id' => null,
                'status' => 'waiting',
            ]);

            return redirect('/dashboard')->with('message', 'Searching for a chat partner...');
        }

        $chatSession = ChatSession::create([
            'user1_id' => $user->id,
            'user2_id' => $potentialMatch->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        $user->update(['is_online' => false]);
        $potentialMatch->update(['is_online' => false]);

        return redirect("/chat/{$chatSession->id}");
    }

    public function chat($sessionId)
    {
        $user = auth()->user();
        
        $session = ChatSession::where('id', $sessionId)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->with(['user1', 'user2', 'messages'])
            ->firstOrFail();

        if ($session->status === 'ended') {
            return redirect('/dashboard')->with('error', 'Chat session has ended.');
        }

        $otherUser = $session->getOtherUser($user->id);

        return view('chat', compact('session', 'otherUser'));
    }

    public function sendMessage(Request $request, $sessionId)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $user = auth()->user();
        
        $session = ChatSession::where('id', $sessionId)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->where('status', 'active')
            ->firstOrFail();

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        event(new ChatMessageEvent(
            $session->id,
            $user->id,
            $message->message,
            $user->getDisplayName(),
            $user->location
        ));

        return response()->json(['success' => true]);
    }

    public function typing(Request $request, $sessionId)
    {
        $user = auth()->user();
        
        event(new UserTypingEvent($sessionId, $user->id, $request->typing === 'true'));

        return response()->json(['success' => true]);
    }

    public function endChat($sessionId)
    {
        $user = auth()->user();
        
        $session = ChatSession::where('id', $sessionId)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->firstOrFail();

        $session->end();

        $otherUser = $session->getOtherUser($user->id);
        $otherUser->update(['is_online' => true]);

        return redirect('/dashboard')->with('message', 'Chat ended. Find a new partner!');
    }

    public function getMessages($sessionId)
    {
        $user = auth()->user();
        
        $session = ChatSession::where('id', $sessionId)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->with('messages.sender')
            ->firstOrFail();

        $messages = $session->messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->getDisplayName(),
                'message' => $msg->message,
                'location' => $msg->sender->location,
                'created_at' => $msg->created_at->toIso8601String(),
            ];
        });

        return response()->json(['messages' => $messages]);
    }
}