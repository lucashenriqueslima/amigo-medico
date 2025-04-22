<x-layouts.app :title="__('Consultations')">
    <iframe
    id="iframe-container"
    src="{{ auth()->user()->my_consultations_magic_link }}"
    frameborder="0"
    class="w-full h-full"
    allow="camera; microphone;"
    >
    </iframe>
</x-layouts.app>
