<?php

namespace App\Livewire;

use App\DTO\ConexaSaudeMagicLinkDTO;
use App\Enums\ConexaSaudeMagicLinkType;
use App\Services\ConexaSaude\ConexaSaudeApiService;
use App\Services\ConexaSaudeMagicLinkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class MyAppointments extends Component
{
    public function mount(
        ConexaSaudeApiService $conexaSaudeApiService,
        ConexaSaudeMagicLinkService $conexaSaudeMagicLinkService
    ): void {


        if (
            Auth::user()->myAppointmentsMagicLink != null
            && !Auth::user()->myAppointmentsMagicLink->isExpired()
        ) {
            return;
        }

        $magicLink = $conexaSaudeApiService->getMagicLinkByPacientId(
            Auth::user()->conexa_saude_id,
            ConexaSaudeMagicLinkType::MyAppointments
        );

        if (empty($magicLink)) {

            Auth::guard('web')->logout();

            Session::invalidate();
            Session::regenerateToken();

            redirect(route('login'));

            return;
        }

        $conexaSaudeMagicLinkService->updateOrCreateConexaSaudeMagicLink(
            new ConexaSaudeMagicLinkDTO(
                userId: Auth::user()->id,
                type: ConexaSaudeMagicLinkType::MyAppointments,
                magicLink: $magicLink['object']['linkMagicoWeb']
            )
        );
    }
    public function render()
    {
        return view('livewire.my-appointments');
    }
}
