<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Encaissement;
use App\Models\Journee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BoutiqueMetrics
{
    public static function scopeJournees(?User $user = null): Builder
    {
        $query = Journee::query();
        $user ??= auth()->user();

        if ($user && $user->isGerant()) {
            $query->where('gerant_id', $user->gerant_id);
        }

        return $query;
    }

    public static function totaux(?Carbon $du = null, ?Carbon $au = null, ?int $gerantId = null, bool $clotureesSeulement = false): array
    {
        $q = static::scopeJournees();

        if ($du) {
            $q->whereDate('date', '>=', $du);
        }
        if ($au) {
            $q->whereDate('date', '<=', $au);
        }
        if ($gerantId) {
            $q->where('gerant_id', $gerantId);
        }
        if ($clotureesSeulement) {
            $q->where('statut', Journee::STATUT_CLOTUREE);
        }

        $row = $q->selectRaw('COUNT(*) as n, COALESCE(SUM(recette),0) as recette, COALESCE(SUM(total_depenses),0) as depenses, COALESCE(SUM(commission),0) as commission, COALESCE(SUM(benefice),0) as benefice')->first();

        return [
            'n' => (int) ($row->n ?? 0),
            'recette' => (int) ($row->recette ?? 0),
            'depenses' => (int) ($row->depenses ?? 0),
            'commission' => (int) ($row->commission ?? 0),
            'benefice' => (int) ($row->benefice ?? 0),
        ];
    }

    public static function sparklineRecette(int $jours = 14): array
    {
        $from = now()->subDays($jours - 1)->startOfDay();
        $rows = static::scopeJournees()
            ->whereDate('date', '>=', $from)
            ->selectRaw('date, SUM(recette) as recette')
            ->groupBy('date')
            ->pluck('recette', 'date');

        $data = [];
        for ($i = 0; $i < $jours; $i++) {
            $d = $from->copy()->addDays($i)->toDateString();
            $data[] = (int) ($rows[$d] ?? 0);
        }

        return $data;
    }

    public static function recetteParJour(int $jours = 14): array
    {
        $from = now()->subDays($jours - 1)->startOfDay();
        $rows = static::scopeJournees()
            ->whereDate('date', '>=', $from)
            ->selectRaw('date, SUM(recette) as recette, SUM(total_depenses) as depenses, SUM(benefice) as benefice')
            ->groupBy('date')
            ->get()
            ->keyBy(fn ($r) => Carbon::parse($r->date)->toDateString());

        $labels = [];
        $recette = [];
        $depenses = [];
        $benefice = [];

        for ($i = 0; $i < $jours; $i++) {
            $day = $from->copy()->addDays($i);
            $key = $day->toDateString();
            $labels[] = $day->translatedFormat('d/m');
            $row = $rows[$key] ?? null;
            $recette[] = (int) ($row->recette ?? 0);
            $depenses[] = (int) ($row->depenses ?? 0);
            $benefice[] = (int) ($row->benefice ?? 0);
        }

        return compact('labels', 'recette', 'depenses', 'benefice');
    }

    public static function topArticles(int $limit = 8, ?Carbon $du = null, ?Carbon $au = null): array
    {
        $q = Encaissement::query()
            ->join('journees', 'journees.id', '=', 'encaissements.journee_id')
            ->join('articles', 'articles.id', '=', 'encaissements.article_id');

        $user = auth()->user();
        if ($user && $user->isGerant()) {
            $q->where('journees.gerant_id', $user->gerant_id);
        }
        if ($du) {
            $q->whereDate('journees.date', '>=', $du);
        }
        if ($au) {
            $q->whereDate('journees.date', '<=', $au);
        }

        return $q->select('articles.nom', DB::raw('SUM(encaissements.quantite) as qte'), DB::raw('SUM(encaissements.montant) as ca'))
            ->groupBy('articles.id', 'articles.nom')
            ->orderByDesc('ca')
            ->limit($limit)
            ->get()
            ->all();
    }

    public static function parGerant(?Carbon $du = null, ?Carbon $au = null): array
    {
        $q = Journee::query()
            ->join('gerants', 'gerants.id', '=', 'journees.gerant_id')
            ->select(
                'gerants.nom',
                DB::raw('SUM(journees.recette) as recette'),
                DB::raw('SUM(journees.total_depenses) as depenses'),
                DB::raw('SUM(journees.commission) as commission'),
                DB::raw('SUM(journees.benefice) as benefice'),
            )
            ->groupBy('gerants.id', 'gerants.nom')
            ->orderByDesc('recette');

        $user = auth()->user();
        if ($user && $user->isGerant()) {
            $q->where('journees.gerant_id', $user->gerant_id);
        }
        if ($du) {
            $q->whereDate('journees.date', '>=', $du);
        }
        if ($au) {
            $q->whereDate('journees.date', '<=', $au);
        }

        return $q->get()->all();
    }

    public static function articlesActifs(): int
    {
        return Article::query()->where('actif', true)->count();
    }
}
