import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Echo and Pusher setup
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
    wsHost: import.meta.env.VITE_PUSHER_HOST
        ? import.meta.env.VITE_PUSHER_HOST
        : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "http") !== "http",
    enabledTransports: ["ws", "wss"],
});


const userIdMeta = document.querySelector('meta[name="user-id"]');
const userId = userIdMeta ? userIdMeta.getAttribute("content") : null;

if (userId) {
window.Echo.private(`chat.${userId}`).listen(
    ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
    (e) => {
        Livewire.dispatch("refresh");
    }
);
} else {
    console.warn("User ID meta tag not found.");
}
import "./echo";
