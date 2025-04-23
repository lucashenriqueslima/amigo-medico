<?php

namespace App\Services\ConexaSaude;

use App\Enums\ConexaSaudeMagicLinkType;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConexaSaudeApiService
{
    private PendingRequest $httpClient;
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('conexa_saude.api_url');
        $this->apiKey = config('conexa_saude.api_key');

        $this->httpClient = Http::withHeaders([
            'token' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->baseUrl($this->apiUrl);
    }

    public function getPatientByCpf(string $cpf): array
    {
        try {
            $response = $this->httpClient->get("/patients/cpf/{$cpf}");

            $response = $response->json();

            if ($response['status'] == 422) {
                return [];
            }

            return $response['object']['patient'] ?? [];
        } catch (Exception $e) {
            Log::error('ConexaSaudeApiService - getPatientByCpf', [
                'cpf' => $cpf,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function getMagicLinkByPacientId(string $pacientId, ConexaSaudeMagicLinkType $conexaSaudeMagicLinkType)
    {

        if ($conexaSaudeMagicLinkType === ConexaSaudeMagicLinkType::Dashboard) {
            $conexaSaudeMagicLinkType = null;
        }

        $response = $this->httpClient->get("/patients/generate-magiclink-access-app/{$pacientId}", [
            'embed' => true,
            'route' => $conexaSaudeMagicLinkType?->value,
        ]);

        return $response->json();
    }
}
