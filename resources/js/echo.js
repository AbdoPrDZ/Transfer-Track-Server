import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: "reverb",
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
  wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
  enabledTransports: ["ws", "wss"],
  auth: {
    headers: {
      Authorization:
        "Bearer 5|axNZ9RxlXxD2pJhrmFtqs94LZh3oitsnDppW5KVnbd11043e",
      Accept: "json/application",
    },
  },
});

window.channel = window.Echo.channel("presence-Chat.1");
window.channel.listenForWhisper("whisper", (e) => {
  console.log(e);
});
