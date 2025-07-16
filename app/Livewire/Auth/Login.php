<?php

namespace App\Livewire\Auth;

use App\DTO\ConexaSaudeMagicLinkDTO;
use App\Enums\ConexaSaudeMagicLinkType;
use App\Helpers\StringHelper;
use App\Jobs\HandleUserMagicLinkJob;
use App\Models\Association;
use App\Models\ConexaSaudeMagicLink;
use App\Models\User;
use App\Services\ConexaSaude\ConexaSaudeApiService;
use App\Services\ConexaSaudeMagicLinkService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string|max:14')]
    public string $cpf = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(
        ConexaSaudeApiService $conexaSaudeApiService,
        ConexaSaudeMagicLinkService $conexaSaudeMagicLinkService
    ): void {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $cpfOnlyNumbers = StringHelper::onlyNumbers($this->cpf);

        $patient = $conexaSaudeApiService->getPatientByCpf($cpfOnlyNumbers);

        if (empty($patient)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $magicLink = $conexaSaudeApiService->getMagicLinkByPacientId(
            $patient['id'],
            ConexaSaudeMagicLinkType::Dashboard
        );

        if ($this->email != $patient['mail']) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = User::updateOrCreate([
            'cpf' => $patient['cpf']
        ], [
            'cpf' => $patient['cpf'],
            'association_id' => Association::where('slug', $patient['enterprise'])->first()?->id,
            'name' => $patient['name'],
            'email' => $patient['mail'],
            'conexa_saude_id' => $patient['id'],
            'password' => bcrypt($patient['cpf']),
        ]);

        $conexaSaudeMagicLinkService->updateOrCreateConexaSaudeMagicLink(
            new ConexaSaudeMagicLinkDTO(
                userId: $user->id,
                type: ConexaSaudeMagicLinkType::Dashboard,
                magicLink: $magicLink['object']['linkMagicoWeb'],
            )
        );

        Auth::loginUsingId($user->id, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        dispatch(
            new HandleUserMagicLinkJob(
                $user,
                ConexaSaudeMagicLinkType::MyAppointments,
            )
        );

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}
