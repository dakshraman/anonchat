@extends('layout')

@section('title', 'Chat - AnonymousChat')

@section('styles')
<style>
    body {
        overscroll-behavior-y: none;
        overflow: hidden;
    }
    .chat-grid {
        height: 100dvh;
        display: grid;
        grid-template-rows: auto 1fr auto;
    }
    .messages-container::-webkit-scrollbar { width: 4px; }
    .messages-container::-webkit-scrollbar-track { background: transparent; }
    .messages-container::-webkit-scrollbar-thumb { background: rgba(167, 139, 250, 0.1); border-radius: 10px; }
    
    .bubble {
        max-width: 80%;
        padding: 12px 16px;
        border-radius: 20px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
        animation: bubble-in 0.2s ease-out forwards;
    }
    .bubble-sent {
        background: linear-gradient(135deg, #7C3AED 0%, #A78BFA 100%);
        color: white;
        border-bottom-right-radius: 4px;
        align-self: flex-end;
    }
    .bubble-received {
        background: #242133;
        color: #E2E8F0;
        border-bottom-left-radius: 4px;
        align-self: flex-start;
        border: 1px solid rgba(167, 139, 250, 0.1);
    }
    @keyframes bubble-in {
        from { opacity: 0; transform: translateY(10px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .typing-dot {
        width: 6px;
        height: 6px;
        background: #A78BFA;
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out both;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="chat-grid w-full max-w-4xl mx-auto bg-darkbg md:border-x border-white/5">
    <!-- Header -->
    <header class="glass sticky top-0 z-30 px-4 py-3 sm:px-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="p-2 hover:bg-white/5 rounded-xl transition-colors md:hidden">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="relative">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-lavender-400 to-indigo-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    {{ strtoupper(substr($otherUser->getDisplayName(), 0, 1)) }}
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-darkbg rounded-full"></div>
            </div>
            <div>
                <h1 class="text-sm sm:text-base font-bold text-white leading-tight">{{ $otherUser->getDisplayName() }}</h1>
                <p class="text-[10px] sm:text-xs text-gray-500 font-medium flex items-center gap-1 uppercase tracking-wider">
                    {{ $otherUser->age ? $otherUser->age . ' • ' : '' }}{{ $otherUser->gender }} • {{ $otherUser->location }}
                </p>
            </div>
        </div>
        
        <form action="{{ route('chat.end', $session->id) }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-bold transition-all">
                End Chat
            </button>
        </form>
    </header>

    <!-- Messages -->
    <main id="chat-messages" class="messages-container overflow-y-auto px-4 py-6 flex flex-col gap-4">
        <div class="flex flex-col items-center gap-4 my-8 opacity-50">
            <div class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center">
                <svg class="w-8 h-8 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <div class="text-center">
                <p class="text-xs font-bold text-white uppercase tracking-[0.2em]">Secure Connection</p>
                <p class="text-[10px] text-gray-500 mt-1">Chat is anonymous and end-to-end encrypted</p>
            </div>
        </div>
    </main>

    <!-- Typing & Input -->
    <footer class="z-30 p-4 sm:p-6 bg-darkbg/80 backdrop-blur-xl">
        <div id="typing-indicator" class="hidden px-4 mb-3">
            <div class="flex items-center gap-1.5 bg-panel border border-white/5 rounded-full px-4 py-2 w-fit">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>

        <form id="message-form" class="relative group">
            @csrf
            <input type="text" id="message-input" name="message" 
                class="w-full bg-panel border border-white/10 rounded-2xl sm:rounded-[2rem] px-5 py-4 sm:py-5 pr-14 focus:outline-none focus:border-lavender-500/50 transition-all text-sm sm:text-base placeholder-gray-600"
                placeholder="Type a message..." autocomplete="off">
            <button type="submit" id="send-btn" disabled 
                class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 bg-lavender-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-white disabled:opacity-30 disabled:grayscale transition-all hover:scale-105 active:scale-95">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
            </button>
        </form>
    </footer>
</div>

<script>
    const sessionId = {{ $session->id }};
    const userId = {{ auth()->id() }};
    const chatContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    let typingTimeout = null;

    loadMessages();

    function scrollToBottom() {
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight,
            behavior: 'smooth'
        });
    }

    messageInput.addEventListener('input', (e) => {
        sendBtn.disabled = e.target.value.trim() === '';
        sendTyping(true);
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => stopTyping(), 1500);
    });

    Echo.private('chat.session.' + sessionId)
        .listen('.chat.message', (data) => {
            if (data.sender_id !== userId) {
                addMessage(data.sender_name, data.message, false);
                markTyping(false);
            }
        })
        .listen('.user.typing', (data) => {
            if (data.user_id !== userId) {
                markTyping(data.is_typing);
            }
        });

    document.getElementById('message-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        addMessage('You', message, true);
        messageInput.value = '';
        sendBtn.disabled = true;
        stopTyping();
        
        try {
            await fetch(`/chat/${sessionId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });
        } catch (error) { console.error(error); }
    });

    async function sendTyping(isTyping) {
        try {
            await fetch(`/chat/${sessionId}/typing`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ typing: isTyping })
            });
        } catch (error) {}
    }

    function stopTyping() { sendTyping(false); }

    async function loadMessages() {
        try {
            const response = await fetch(`/chat/${sessionId}/messages`);
            const data = await response.json();
            data.messages.forEach(msg => addMessage(msg.sender_name, msg.message, msg.sender_id === userId, false));
            scrollToBottom();
        } catch (error) { console.error(error); }
    }

    function addMessage(name, message, isSent, animate = true) {
        const div = document.createElement('div');
        div.className = `bubble ${isSent ? 'bubble-sent' : 'bubble-received'}`;
        if (!animate) div.style.animation = 'none';
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        div.innerHTML = `<div class="font-bold text-[10px] mb-1 opacity-50 uppercase tracking-tighter">${isSent ? 'You' : name} • ${time}</div><div>${message}</div>`;
        
        chatContainer.appendChild(div);
        scrollToBottom();
    }

    function markTyping(isTyping) {
        const indicator = document.getElementById('typing-indicator');
        if (isTyping) {
            indicator.classList.remove('hidden');
            scrollToBottom();
        } else {
            indicator.classList.add('hidden');
        }
    }
</script>
@endsection