<div
    x-data="{ show: false, message: '' }"
    x-on:toast.window="show = true; message = $event.detail.message; $refs.ding.play(); setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    style="display: none;"
    class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg"
>
    <audio x-ref="ding" src="/sounds/notification.mp3" preload="auto"></audio>
    <p x-text="message"></p>
</div> 