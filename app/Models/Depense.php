<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['journee_id', 'categorie_depense_id', 'libelle', 'montant'])]
class Depense extends Model
{
    protected function casts(): array
    {
        return [
            'montant' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (Depense $depense) => $depense->journee?->recalculer());
        static::deleted(fn (Depense $depense) => $depense->journee?->recalculer());
    }

    public function journee(): BelongsTo
    {
        return $this->belongsTo(Journee::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_depense_id');
    }
}
