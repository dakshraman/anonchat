@extends('layout')

@section('title', 'Chat - AnonChat')

@section('styles')
<style>
    .chat-messages {
        max-height: 60vh;
        overflow-y: auto;
    }
    .message-sent {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
    }
    .message-received {
        background: #374151;
    }
    .typing-indicator span {
        animation: typing 1.4s infinite;
        display: inline-block;
    }
    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }
    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-5px); }
    }
    .profile-badge {
        background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.2));
        border: 1px solid rgba(139,92,246,0.3);
    }
    @media (max-width: 640px) {
        .chat-container {
            padding: 0;
        }
        .message-bubble {
            max-width: 85vw;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-gray-800 border-b border-gray-700 p-4 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-xl font-bold">
                    {{ strtoupper(substr($otherUser->getDisplayName(), 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-lg font-bold">{{ $otherUser->getDisplayName() }}</h1>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400">
                        @if($otherUser->age)
                        <span class="bg-gray-700 px-2 py-0.5 rounded">{{ $otherUser->age }}</span>
                        @endif
                        <span class="bg-gray-700 px-2 py-0.5 rounded capitalize">{{ $otherUser->gender }}</span>
                        <span>• {{ $otherUser->location }}</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('chat.end', $session->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                    End
                </button>
            </form>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 max-w-4xl mx-auto w-full p-4 chat-container">
        <div id="chat-messages" class="chat-messages space-y-4">
            <div class="text-center text-gray-500 py-8">
                <p class="text-lg">Start your conversation!</p>
                <p class="text-sm mt-2">You are chatting with {{ $otherUser->getDisplayName() }}</p>
                @if($otherUser->age)
                <p class="text-sm">{{ $otherUser->age }} years old</p>
                @endif
                <p class="text-sm capitalize">{{ $otherUser->gender }}</p>
                <p class="text-sm text-gray-400 mt-1">📍 {{ $otherUser->location }}</p>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="hidden text-gray-400 text-sm ml-4 mt-2">
            <span>•</span><span>•</span><span>•</span>
            <span class="ml-2">is typing...</span>
        </div>

        <!-- Message Input -->
        <form id="message-form" class="mt-4 flex gap-2">
            @csrf
            <input type="text" id="message-input" name="message" 
                class="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"
                placeholder="Type a message..." autocomplete="off">
            <button type="submit" class="bg-primary hover:bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg transition">
                Send
            </button>
        </form>
    </div>
</div>

<script>
    const sessionId = {{ $session->id }};
    const userId = {{ auth()->id() }};
    const otherUserName = '{{ $otherUser->getDisplayName() }}';
    const otherUserAge = {{ $otherUser->age ?? 'null' }};
    const otherUserGender = '{{ $otherUser->gender }}';
    const otherUserLocation = '{{ $otherUser->location }}';
    let typingTimeout = null;

    // Load existing messages
    loadMessages();

    // Listen for new messages
    Echo.private('chat.session.' + sessionId)
        .listen('.chat.message', (data) => {
            if (data.sender_id !== userId) {
                addMessage(data.sender_name, data.sender_location, data.message, false);
                markTyping(data.sender_id, false);
            }
        })
        .listen('.user.typing', (data) => {
            if (data.user_id !== userId) {
                markTyping(data.user_id, data.is_typing);
            }
        });

    // Send message
    document.getElementById('message-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        
        if (!message) return;

        try {
            await fetch(`/chat/${sessionId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });
            
            addMessage('You', '{{ auth()->user()->location }}', message, true);
            input.value = '';
            stopTyping();
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });

    // Typing indicator
    document.getElementById('message-input').addEventListener('input', () => {
        sendTyping(true);
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => stopTyping(), 1000);
    });

    async function sendTyping(isTyping) {
        try {
            await fetch(`/chat/${sessionId}/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ typing: isTyping })
            });
        } catch (error) {}
    }

    function stopTyping() {
        sendTyping(false);
    }

    async function loadMessages() {
        try {
            const response = await fetch(`/chat/${sessionId}/messages`);
            const data = await response.json();
            
            const container = document.getElementById('chat-messages');
            container.innerHTML = '';
            
            // Add welcome message
            const welcomeDiv = document.createElement('div');
            welcomeDiv.className = 'text-center text-gray-500 py-4';
            let profileInfo = `<p class="text-lg">You are chatting with ${otherUserName}</p>`;
            if (otherUserAge) profileInfo += `<p class="text-sm">${otherUserAge} years old</p>`;
            profileInfo += `<p class="text-sm capitalize">${otherUserGender}</p>`;
            profileInfo += `<p class="text-sm text-gray-400 mt-1">📍 ${otherUserLocation}</p>`;
            welcomeDiv.innerHTML = profileInfo;
            container.appendChild(welcomeDiv);
            
            data.messages.forEach(msg => {
                const isSent = msg.sender_id === userId;
                addMessage(msg.sender_name, msg.sender_location, msg.message, isSent);
            });
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    function addMessage(name, location, message, isSent) {
        const container = document.getElementById('chat-messages');
        const div = document.createElement('div');
        div.className = isSent ? 'flex justify-end' : 'flex justify-start';
        
        div.innerHTML = `
            <div class="max-w-xs md:max-w-md message-bubble ${isSent ? 'message-sent' : 'message-received'} rounded-2xl px-4 py-3">
                <p class="text-xs font-bold mb-1 opacity-75">${name}</p>
                <p>${message}</p>
                <p class="text-xs opacity-50 mt-1">${location}</p>
            </div>
        `;
        
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function markTyping(senderId, isTyping) {
        const indicator = document.getElementById('typing-indicator');
        if (isTyping) {
            indicator.classList.remove('hidden');
        } else {
            indicator.classList.add('hidden');
        }
    }
</script>
@endsection