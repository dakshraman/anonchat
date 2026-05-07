import Pusher from 'pusher-js';
import Echo from 'laravel-echo';

// Make both available globally
window.Pusher = Pusher;
window.Echo = Echo;

// Initialize Echo with Reverb configuration
function initEcho() {
    try {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: 'reverb-app-key',
            wsHost: window.location.hostname,
            wsPort: 8081,
            wssPort: 443,
            forceTLS: window.location.protocol === 'https:',
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            }
        });
        console.log('Echo initialized - realtime enabled');
    } catch (e) {
        console.log('Echo init failed:', e.message);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEcho);
} else {
    initEcho();
}