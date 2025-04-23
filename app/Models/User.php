<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\ConexaSaudeMagicLinkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    protected $casts = [
        'magic_links' => 'array'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        //max 2 characters
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn($name) => Str::upper(Str::substr($name, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }


    public function dashboardMagicLink(): HasOne
    {
        return $this->hasOne(ConexaSaudeMagicLink::class)
            ->where('type', ConexaSaudeMagicLinkType::Dashboard);
    }

    public function myAppointmentsMagicLink(): HasOne
    {
        return $this->hasOne(ConexaSaudeMagicLink::class)
            ->where('type', ConexaSaudeMagicLinkType::MyAppointments);
    }
    public function magicLinks(): HasMany
    {
        return $this->hasMany(ConexaSaudeMagicLink::class);
    }
}
