<?php

namespace App\Models;

use App\Enums\ConexaSaudeMagicLinkType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConexaSaudeMagicLink extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => ConexaSaudeMagicLinkType::class,
            'expires_at' => 'datetime',
        ];
    }

    //is expired attribure
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
