<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nom', 'categorie', 'prix_unitaire', 'unite', 'actif'])]
class Article extends Model
{
    public const CATEGORIES = [
        'gateau' => 'Gâteau',
        'glace' => 'Crème glacée',
        'boisson' => 'Boisson',
        'snack' => 'Snack / salé',
        'autre' => 'Autre',
    ];

    protected function casts(): array
    {
        return [
            'prix_unitaire' => 'integer',
            'actif' => 'boolean',
        ];
    }

    public function encaissements(): HasMany
    {
        return $this->hasMany(Encaissement::class);
    }
}
