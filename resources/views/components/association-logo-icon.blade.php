<img
    {{ $attributes->merge(['class' => 'max-h-10']) }}
    src="{{ asset('images/' . (auth()->user()?->association?->logo_path ?? 'logo.png')) }}"
    alt="Logo"
    {{ $attributes }}
/>
