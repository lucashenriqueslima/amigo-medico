<x-layouts.app :title="__('Dashboard')">
    <iframe
    id="iframe-container"
    src="{{ auth()->user()->dashboard_magic_link }}"
    frameborder="0"
    class="w-full h-full"
    allow="camera; microphone;"
    >
    </iframe>
</x-layouts.app>

