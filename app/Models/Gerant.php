<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['nom', 'telephone', 'pourcentage', 'actif', 'notes'])]
class Gerant extends Model
{
    protected function casts(): array
    {
        return [
            'pourcentage' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function journees(): HasMany
    {
        return $this->hasMany(Journee::class);
    }
}
