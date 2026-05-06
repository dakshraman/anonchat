@extends('layout')

@section('title', 'Chat - AnonymousChat')

@section('styles')
<style>
    body {
        /* Prevent bounce on mobile */
        overscroll-behavior-y: none;
        background-color: #13111C;
    }

    .chat-layout {
        height: 100dvh; /* Dynamic viewport height for mobile browsers */
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        background: rgba(30, 27, 46, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(196, 181, 253, 0.08);
        padding-top: env(safe-area-inset-top, 0);
    }

    .chat-container {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        scroll-behavior: smooth;
    }

    .message-wrapper {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
        max-width: 85%;
        animation: fadeIn 0.3s ease-out forwards;
    }

    .message-wrapper.sent {
        align-self: flex-end;
        align-items: flex-end;
    }

    .message-wrapper.received {
        align-self: flex-start;
        align-items: flex-start;
    }

    .message-bubble {
        padding: 12px 16px;
        position: relative;
        font-size: 0.95rem;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .message-wrapper.sent .message-bubble {
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        color: white;
        border-radius: 20px 20px 4px 20px;
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.2);
    }

    .message-wrapper.received .message-bubble {
        background: #2A2640;
        color: #e2e8f0;
        border-radius: 20px 20px 20px 4px;
        border: 1px solid rgba(196, 181, 253, 0.1);
    }

    .message-meta {
        font-size: 0.7rem;
        color: rgba(196, 181, 253, 0.5);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .input-area {
        background: rgba(30, 27, 46, 0.95);
        border-top: 1px solid rgba(196, 181, 253, 0.08);
        padding: 12px 16px;
        padding-bottom: calc(12px + env(safe-area-inset-bottom, 0));
    }

    .chat-input {
        background: #13111C;
        border: 1px solid rgba(196, 181, 253, 0.2);
        border-radius: 24px;
        padding: 12px 20px;
        padding-right: 50px; /* Space for send button */
        color: white;
        transition: border-color 0.2s;
    }

    .chat-input:focus {
        outline: none;
        border-color: #a78bfa;
    }

    .send-btn {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #8b5cf6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.2s;
        border: none;
    }

    .send-btn:hover:not(:disabled) {
        background: #a78bfa;
        transform: translateY(-50%) scale(1.05);
    }

    .send-btn:disabled {
        background: #4c1d95;
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Typing Animation */
    .typing-bubble {
        background: #2A2640;
        border-radius: 20px 20px 20px 4px;
        padding: 12px 16px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid rgba(196, 181, 253, 0.1);
        align-self: flex-start;
        margin-bottom: 12px;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        background: #a78bfa;
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes typing {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="chat-layout w-full max-w-4xl mx-auto bg-darkbg shadow-2xl md:border-x border-lavender-500/10">

    <!-- Header -->
    <header class="chat-header z-20 sticky top-0 flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-lavender-400 to-lavender-600 flex items-center justify-center text-white font-bold shadow-md">
                    {{ strtoupper(substr($otherUser->getDisplayName(), 0, 1)) }}
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#1E1B2E] rounded-full"></div>
            </div>
            <div class="flex flex-col">
                <h1 class="text-white font-semibold leading-tight">{{ $otherUser->getDisplayName() }}</h1>
                <div class="text-[0.7rem] text-lavender-300/80 flex items-center gap-1.5 mt-0.5">
                    @if($otherUser->age)<span>{{ $otherUser->age }}</span>&bull;@endif
                    <span class="capitalize">{{ $otherUser->gender }}</span>
                    &bull;
                    <span class="flex items-center"><svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>{{ $otherUser->location }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('chat.end', $session->id) }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span class="hidden sm:inline">End Chat</span>
            </button>
        </form>
    </header>

    <!-- Chat Messages -->
    <main id="chat-messages" class="chat-container">
        <!-- System Message / Welcome -->
        <div class="flex justify-center my-6">
            <div class="bg-panel border border-lavender-500/10 rounded-2xl px-6 py-4 text-center max-w-sm shadow-sm">
                <div class="w-12 h-12 bg-lavender-500/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <p class="text-white font-medium text-sm mb-1">Chat connected securely</p>
                <p class="text-xs text-lavender-300/60">Say hi to {{ $otherUser->getDisplayName() }}! Remember to stay anonymous and be respectful.</p>
            </div>
        </div>

        <!-- Messages will be injected here via JS -->
    </main>

    <!-- Typing Indicator (Hidden by default) -->
    <div id="typing-indicator" class="px-4 pb-2 hidden">
        <div class="typing-bubble">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>
    </div>

    <!-- Input Area -->
    <footer class="input-area z-20">
        <form id="message-form" class="relative max-w-4xl mx-auto w-full">
            @csrf
            <input type="text" id="message-input" name="message"
                class="w-full chat-input text-base"
                placeholder="Message..." autocomplete="off">
            <button type="submit" id="send-btn" class="send-btn" disabled>
                <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
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

    // Load existing messages
    loadMessages();

    // Scroll to bottom helper
    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Input change handler for send button state
    messageInput.addEventListener('input', (e) => {
        sendBtn.disabled = e.target.value.trim() === '';

        sendTyping(true);
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => stopTyping(), 1000);
    });

    // Listen for new messages
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

    // Send message
    document.getElementById('message-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();

        if (!message) return;

        // Optimistically add message
        addMessage('You', message, true);

        messageInput.value = '';
        sendBtn.disabled = true;
        messageInput.focus();
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
        } catch (error) {
            console.error('Error sending message:', error);
            // Optionally handle failed send
        }
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

            data.messages.forEach(msg => {
                const isSent = msg.sender_id === userId;
                addMessage(msg.sender_name, msg.message, isSent, false); // false = don't animate initial load
            });
            scrollToBottom();
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    function addMessage(name, message, isSent, animate = true) {
        const div = document.createElement('div');
        div.className = `message-wrapper ${isSent ? 'sent' : 'received'}`;
        if (!animate) {
            div.style.animation = 'none';
        }

        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        div.innerHTML = `
            <div class="message-bubble">
                ${message}
            </div>
            <div class="message-meta">
                ${isSent ? `<span>${time}</span><svg class="w-3 h-3 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>` : `<span>${time}</span>`}
            </div>
        `;

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
