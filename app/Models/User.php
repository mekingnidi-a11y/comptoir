<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'gerant_id', 'actif'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_PROPRIETAIRE = 'proprietaire';

    public const ROLE_GERANT = 'gerant';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function gerant(): BelongsTo
    {
        return $this->belongsTo(Gerant::class);
    }

    public function isProprietaire(): bool
    {
        return $this->role === self::ROLE_PROPRIETAIRE;
    }

    public function isGerant(): bool
    {
        return $this->role === self::ROLE_GERANT;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->actif;
    }
}
