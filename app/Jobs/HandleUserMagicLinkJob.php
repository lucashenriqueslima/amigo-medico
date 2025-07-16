<?php

namespace App\Jobs;

use App\DTO\ConexaSaudeMagicLinkDTO;
use App\Enums\ConexaSaudeMagicLinkType;
use App\Models\ConexaSaudeMagicLink;
use App\Models\User;
use App\Services\ConexaSaude\ConexaSaudeApiService;
use App\Services\ConexaSaudeMagicLinkService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleUserMagicLinkJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected User $user,
        protected ConexaSaudeMagicLinkType $conexaSaudeMagicLinkType,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        ConexaSaudeApiService $conexaSaudeApiService,
        ConexaSaudeMagicLinkService $conexaSaudeMagicLinkService
    ): void {
        $response = $conexaSaudeApiService->getMagicLinkByPacientId(
            $this->user->conexa_saude_id,
            $this->conexaSaudeMagicLinkType
        );

        if (empty($response)) {
            return;
        }

        $conexaSaudeMagicLinkService->updateOrCreateConexaSaudeMagicLink(
            new ConexaSaudeMagicLinkDTO(
                userId: $this->user->id,
                type: $this->conexaSaudeMagicLinkType,
                magicLink: $response['object']['linkMagicoWeb']
            )
        );
    }
}
