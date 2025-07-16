<?php

namespace App\Services;

use App\DTO\ConexaSaudeMagicLinkDTO;
use App\Models\ConexaSaudeMagicLink;

class ConexaSaudeMagicLinkService
{
    public function updateOrCreateConexaSaudeMagicLink(
        ConexaSaudeMagicLinkDTO $conexaSaudeMagicLinkDTO,
    ): ConexaSaudeMagicLink {
        return ConexaSaudeMagicLink::updateOrCreate(
            [
                'user_id' => $conexaSaudeMagicLinkDTO->userId,
                'type' => $conexaSaudeMagicLinkDTO->type,
            ],
            $conexaSaudeMagicLinkDTO->toArrayForDB()
        );
    }
}
