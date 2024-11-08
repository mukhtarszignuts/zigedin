
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make sure the window object has the correct types
declare global {
  interface Window {
    Echo: Echo;
    Pusher: typeof Pusher;
  }
}

// Configure Pusher and Echo
window.Pusher = Pusher;
const echo = new Echo({
  broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
    headers: {
        Authorization: `Bearer ${localStorage.getItem('auth-token')}`
    }
}
});

export default echo;