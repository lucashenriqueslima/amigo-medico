<?php

namespace App\Livewire;

use App\DTO\ConexaSaudeMagicLinkDTO;
use App\Enums\ConexaSaudeMagicLinkType;
use App\Services\ConexaSaude\ConexaSaudeApiService;
use App\Services\ConexaSaudeMagicLinkService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Dashboard extends Component
{

    public function mount(
        ConexaSaudeApiService $conexaSaudeApiService,
        ConexaSaudeMagicLinkService $conexaSaudeMagicLinkService
    ): void {

        if (!Auth::user()->dashboardMagicLink->isExpired()) {
            return;
        }

        $magicLink = $conexaSaudeApiService->getMagicLinkByPacientId(
            Auth::user()->conexa_saude_id,
            ConexaSaudeMagicLinkType::Dashboard
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
                type: ConexaSaudeMagicLinkType::Dashboard,
                magicLink: $magicLink['object']['linkMagicoWeb']
            )
        );
    }
    public function render()
    {
        return view('livewire.dashboard');
    }
}
