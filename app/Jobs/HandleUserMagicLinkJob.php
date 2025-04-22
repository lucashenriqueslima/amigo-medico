<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ConexaSaude\ConexaSaudeApiService;
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
        protected string $magicLinkColumnName,
        protected string $migicLinkType,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ConexaSaudeApiService $conexaSaudeApiService): void
    {
        $response = $conexaSaudeApiService->getMagicLinkByPacientId(
            $this->user->conexa_saude_id,
            $this->migicLinkType
        );

        if (empty($response)) {
            return;
        }

        $this->user->update([
            $this->magicLinkColumnName => $response['object']['linkMagicoWeb'],
        ]);
    }
}
