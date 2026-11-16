<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'date',
    'gerant_id',
    'statut',
    'fond_caisse',
    'pourcentage_applique',
    'recette',
    'total_depenses',
    'commission',
    'benefice',
    'commission_versee',
    'cloturee_at',
    'saisie_par_id',
    'notes',
])]
class Journee extends Model
{
    public const STATUT_OUVERTE = 'ouverte';

    public const STATUT_CLOTUREE = 'cloturee';

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'fond_caisse' => 'integer',
            'pourcentage_applique' => 'decimal:2',
            'recette' => 'integer',
            'total_depenses' => 'integer',
            'commission' => 'integer',
            'benefice' => 'integer',
            'commission_versee' => 'boolean',
            'cloturee_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Journee $journee): void {
            if ($journee->pourcentage_applique === null && $journee->gerant_id) {
                $gerant = $journee->relationLoaded('gerant') ? $journee->gerant : Gerant::query()->find($journee->gerant_id);
                $journee->pourcentage_applique = $gerant?->pourcentage ?? 0;
            }
            if (! $journee->saisie_par_id && auth()->id()) {
                $journee->saisie_par_id = auth()->id();
            }
        });
    }

    public function gerant(): BelongsTo
    {
        return $this->belongsTo(Gerant::class);
    }

    public function saisiePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saisie_par_id');
    }

    public function encaissements(): HasMany
    {
        return $this->hasMany(Encaissement::class);
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function estOuverte(): bool
    {
        return $this->statut === self::STATUT_OUVERTE;
    }

    public function libelle(): string
    {
        $date = $this->date?->format('d/m/Y') ?? '—';
        $gerant = $this->gerant?->nom ?? 'Gérant';

        return $date.' — '.$gerant;
    }

    public static function queryOuvertes(?User $user = null): Builder
    {
        $query = static::query()
            ->where('statut', self::STATUT_OUVERTE)
            ->with('gerant')
            ->orderByDesc('date');

        $user ??= auth()->user();
        if ($user instanceof User && $user->isGerant()) {
            $query->where('gerant_id', $user->gerant_id);
        }

        return $query;
    }

    public function recalculer(): void
    {
        $recette = (int) $this->encaissements()->sum('montant');
        $depenses = (int) $this->depenses()->sum('montant');
        $pct = (float) ($this->pourcentage_applique ?: $this->gerant?->pourcentage ?: 0);
        $commission = (int) round($recette * $pct / 100);
        $benefice = $recette - $depenses - $commission;

        $this->forceFill([
            'recette' => $recette,
            'total_depenses' => $depenses,
            'commission' => $commission,
            'benefice' => $benefice,
        ])->saveQuietly();
    }

    public function cloturer(): void
    {
        $this->recalculer();
        $this->forceFill([
            'statut' => self::STATUT_CLOTUREE,
            'cloturee_at' => now(),
        ])->saveQuietly();
    }

    public function rouvrir(): void
    {
        $this->forceFill([
            'statut' => self::STATUT_OUVERTE,
            'cloturee_at' => null,
        ])->saveQuietly();
    }
}
