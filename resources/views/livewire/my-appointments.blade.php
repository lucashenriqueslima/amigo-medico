<iframe
    id="iframe-container"
    src="{{ auth()->user()->myAppointmentsMagicLink->magic_link }}"
    frameborder="0"
    class="w-full h-full"
    allow="camera; microphone;"
>
</iframe>
