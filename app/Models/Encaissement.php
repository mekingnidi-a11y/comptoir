<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['journee_id', 'article_id', 'quantite', 'prix_unitaire', 'montant', 'note'])]
class Encaissement extends Model
{
    protected function casts(): array
    {
        return [
            'quantite' => 'decimal:2',
            'prix_unitaire' => 'integer',
            'montant' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Encaissement $ligne): void {
            $ligne->montant = (int) round(((float) $ligne->quantite) * ((int) $ligne->prix_unitaire));
        });

        static::saved(fn (Encaissement $ligne) => $ligne->journee?->recalculer());
        static::deleted(fn (Encaissement $ligne) => $ligne->journee?->recalculer());
    }

    public function journee(): BelongsTo
    {
        return $this->belongsTo(Journee::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
