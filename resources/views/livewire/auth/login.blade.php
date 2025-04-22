<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Access your account patient')" :description="__('Enter your email and cpf below to log in')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email Address')"
            type="email"
            required
            autocomplete="email"
            :placeholder="__('email@example.com')"
            autofocus
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="cpf"
                :label="__('CPF')"
                id="cpf"
                type="text"
                required
                autocomplete="cpf"
            />
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember Me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>


    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Don\'t have an account?') }}
        <flux:link :href="route('register')" wire:navigate>{{ __('Find out more') }}</flux:link>
    </div>
</div>
<script>

    const cpfInput = document.getElementById('cpf');

    // Configuração da máscara para CPF
    const maskOptions = {
        mask: '000.000.000-00', // Formato do CPF
        lazy: false // Para que a máscara apareça imediatamente
    };

    IMask(cpfInput, maskOptions);

</script>
