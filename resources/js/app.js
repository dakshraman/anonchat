import Pusher from 'pusher-js';
import Echo from 'laravel-echo';

// Make both available globally
window.Pusher = Pusher;
window.Echo = Echo;

// Initialize Echo with Reverb configuration
function initEcho() {
    if (typeof window.Echo !== 'undefined') {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: 'reverb-app-key', // Required by Echo even for Reverb
            wsHost: window.location.hostname,
            wsPort: 8080,
            wssPort: 8080,
            forceTLS: window.location.protocol === 'https:',
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }
        });
        console.log('Echo initialized via Vite bundle');
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEcho);
} else {
    initEcho();
}
