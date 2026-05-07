@extends('layout')

@section('title', 'Chat - AnonymousChat')

@section('styles')
<style>
    body {
        overscroll-behavior-y: none;
        overflow: hidden;
    }
    .chat-wrapper {
        height: 100vh;
        height: 100dvh;
        display: flex;
        flex-direction: column;
    }
    .messages-container {
        flex: 1;
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    .bubble-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 85%;
        margin-bottom: 4px;
    }
    .bubble-wrapper-sent { align-self: flex-end; align-items: flex-end; }
    .bubble-wrapper-received { align-self: flex-start; align-items: flex-start; }
    
    .bubble {
        padding: 14px 18px;
        font-size: 1rem;
        line-height: 1.4;
        position: relative;
        animation: bubble-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }
    
    .bubble-sent {
        background: linear-gradient(135deg, var(--lavender-dark) 0%, var(--indigo) 100%);
        color: white;
        border-radius: 1.5rem 1.5rem 0.25rem 1.5rem;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3), inset 0 1px 0 rgba(255,255,255,0.2);
    }
    
    .bubble-received {
        background: rgba(255, 255, 255, 0.05);
        color: #f8f9fa;
        border-radius: 1.5rem 1.5rem 1.5rem 0.25rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);
    }
    
    @keyframes bubble-in {
        from { opacity: 0; transform: translateY(20px) scale(0.95) translateZ(0); }
        to { opacity: 1; transform: translateY(0) scale(1) translateZ(0); }
    }
    .typing-dot {
        width: 6px;
        height: 6px;
        background: var(--lavender);
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out both;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
        40% { transform: scale(1.2); opacity: 1; }
    }
    
    #disconnect-overlay {
        position: absolute;
        inset: 0;
        background: rgba(3, 0, 20, 0.8);
        backdrop-filter: blur(8px);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    #disconnect-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="chat-wrapper glass-card rounded-0 border-top-0 border-bottom-0 shadow-none" style="background: rgba(11, 10, 16, 0.6);">
                <!-- Header -->
                <header class="p-3 border-bottom border-white border-opacity-10 d-flex align-items-center justify-content-between sticky-top bg-dark bg-opacity-25 backdrop-blur-xl">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('dashboard') }}" wire:navigate class="btn glass-button-secondary p-2 d-lg-none">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <div class="position-relative">
                            <div class="glass-card d-flex align-items-center justify-content-center text-white fw-bold" style="width: 45px; height: 45px; border-radius: 1rem; background: linear-gradient(135deg, #a78bfa, #6366f1);">
                                {{ strtoupper(substr($otherUser->getDisplayName(), 0, 1)) }}
                            </div>
                            <div class="position-absolute bottom-0 end-0 bg-success border border-2 border-dark rounded-circle" style="width: 12px; height: 12px;"></div>
                        </div>
                        <div>
                            <h1 class="h6 fw-bold text-white mb-0">{{ $otherUser->getDisplayName() }}</h1>
                            <p class="small text-secondary mb-0" style="font-size: 0.7rem;">
                                {{ $otherUser->age ? $otherUser->age . ' • ' : '' }}{{ $otherUser->gender }} • {{ $otherUser->location }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('chat.skip', $session->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning btn-sm px-3 rounded-3 fw-bold d-flex align-items-center gap-2">
                                Skip
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                            </button>
                        </form>
                        <form action="{{ route('chat.end', $session->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-3 fw-bold">
                                End Chat
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Disconnect Overlay -->
                <div id="disconnect-overlay">
                    <div class="glass-card p-5 text-center" style="max-width: 400px; border-radius: 2rem;">
                        <div class="mx-auto bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <svg class="text-danger" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        </div>
                        <h3 class="h4 fw-bold text-white mb-2">Partner Disconnected</h3>
                        <p class="text-secondary mb-4">The other person has ended the chat session.</p>
                        <form action="{{ route('chat.skip', $session->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn glass-button w-100 py-3">Find New Partner</button>
                        </form>
                    </div>
                </div>

                <!-- Messages -->
                <main id="chat-messages" class="messages-container p-4 d-flex flex-column gap-3">
                    <div class="text-center py-5 opacity-50">
                        <div class="glass-card d-inline-flex p-3 rounded-4 mb-3">
                            <svg class="text-lavender" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <p class="small fw-bold text-white text-uppercase tracking-widest mb-1">Secure Connection</p>
                        <p class="small text-secondary mb-0">End-to-end encrypted</p>
                    </div>
                </main>

                <!-- Footer -->
                <footer class="p-3 p-md-4">
                    <div id="typing-indicator" class="d-none mb-3 px-2">
                        <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-pill px-3 py-2">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>

                    <form id="message-form" class="position-relative">
                        @csrf
                        <input type="text" id="message-input" name="message" 
                            class="form-control glass-input w-100 py-3 pe-5 fs-6 shadow-lg"
                            placeholder="Type a message..." autocomplete="off" style="border-radius: 1.5rem;">
                        <button type="submit" id="send-btn" disabled 
                            class="btn glass-button position-absolute end-0 top-50 translate-middle-y me-2 d-flex align-items-center justify-content-center p-2"
                            style="width: 42px; height: 42px; border-radius: 1rem;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                        </button>
                    </form>
                </footer>
            </div>
        </div>
    </div>
</div>

<script>
    // Note: Livewire scripts will be re-run on wire:navigate.
    // If you have persistent JS state, handle it accordingly.
    
    const sessionId = {{ $session->id }};
    const userId = {{ auth()->id() }};
    const otherUserId = {{ $otherUser->id }};
    const chatContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    let typingTimeout = null;
    let sessionActive = true;

    // Poll to check if other user left the chat
    setInterval(async () => {
        if (!sessionActive) return;
        try {
            const response = await fetch(`/chat/${sessionId}/status`);
            const data = await response.json();
            if (!data.active) {
                sessionActive = false;
                showDisconnectOverlay();
            }
        } catch (error) {}
    }, 3000);

    function showDisconnectOverlay() {
        const overlay = document.getElementById('disconnect-overlay');
        if (overlay) {
            overlay.classList.add('active');
        }
        if (messageInput) messageInput.disabled = true;
        if (sendBtn) sendBtn.disabled = true;
    }

    loadMessages();

    function scrollToBottom() {
        if (!chatContainer) return;
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight,
            behavior: 'smooth'
        });
    }

    if (messageInput) {
        messageInput.addEventListener('input', (e) => {
            sendBtn.disabled = e.target.value.trim() === '';
            sendTyping(true);
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => stopTyping(), 1500);
        });
    }

    // Re-initialize Echo if needed or ensure it's global
    if (window.Echo) {
        window.Echo.private('chat.session.' + sessionId)
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
            })
            .listen('ChatEndedEvent', (data) => {
                const overlay = document.getElementById('disconnect-overlay');
                if (overlay) {
                    overlay.classList.add('active');
                }
                if (messageInput) messageInput.disabled = true;
                if (sendBtn) sendBtn.disabled = true;
            });
    }

    const messageForm = document.getElementById('message-form');
    if (messageForm) {
        messageForm.addEventListener('submit', async (e) => {
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
    }

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
        if (!chatContainer) return;
        const wrapper = document.createElement('div');
        wrapper.className = `bubble-wrapper ${isSent ? 'bubble-wrapper-sent' : 'bubble-wrapper-received'}`;
        
        const div = document.createElement('div');
        div.className = `bubble ${isSent ? 'bubble-sent' : 'bubble-received'}`;
        if (!animate) div.style.animation = 'none';
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        div.innerHTML = `<div>${message}</div>`;
        
        const meta = document.createElement('div');
        meta.className = 'mt-1 opacity-50 fw-medium';
        meta.style.fontSize = '0.65rem';
        meta.style.letterSpacing = '0.05em';
        meta.innerHTML = `${isSent ? 'You' : name} • ${time}`;
        
        wrapper.appendChild(div);
        wrapper.appendChild(meta);
        
        chatContainer.appendChild(wrapper);
        scrollToBottom();
    }

    function markTyping(isTyping) {
        const indicator = document.getElementById('typing-indicator');
        if (!indicator) return;
        if (isTyping) {
            indicator.classList.remove('d-none');
            scrollToBottom();
        } else {
            indicator.classList.add('d-none');
        }
    }
</script>
@endsection
