<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

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
            $journee = $ligne->journee ?? Journee::query()->find($ligne->journee_id);
            if ($journee && ! $journee->estOuverte()) {
                throw ValidationException::withMessages([
                    'journee_id' => 'Cette journée est clôturée. Rouvrez-la pour enregistrer un encaissement.',
                ]);
            }
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
