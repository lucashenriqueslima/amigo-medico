<?php

namespace App\DTO;

use App\Enums\ConexaSaudeMagicLinkType;
use Carbon\Carbon;

class ConexaSaudeMagicLinkDTO
{
    public function __construct(
        public ?string $userId,
        public ?ConexaSaudeMagicLinkType $type,
        public ?string $magicLink,

    ) {}

    public function toArrayForDB(): array
    {
        return [
            'user_id' => $this->userId,
            'type' => $this->type,
            'magic_link' => $this->magicLink,
            'expires_at' => Carbon::now()->addMinutes(5)->toDateTimeString(),
        ];
    }
}
