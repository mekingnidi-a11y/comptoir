<?php

namespace App\Filament\Widgets;

use App\Support\BoutiqueMetrics;
use App\Support\Money;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsBoutique extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'Vue d’ensemble';

    protected function getColumns(): int|array|null
    {
        return ['default' => 1, 'md' => 2, 'xl' => 4];
    }

    protected function getStats(): array
    {
        $aujourdhui = BoutiqueMetrics::totaux(now(), now());
        $mois = BoutiqueMetrics::totaux(now()->startOfMonth(), now());
        $spark = BoutiqueMetrics::sparklineRecette();
        $ouvertes = BoutiqueMetrics::scopeJournees()->where('statut', 'ouverte')->count();

        return [
            Stat::make('Recette du jour', Money::format($aujourdhui['recette']))
                ->icon(Heroicon::OutlinedBanknotes)
                ->description($aujourdhui['n'].' journée(s) saisie(s) aujourd’hui')
                ->color('success')
                ->chart($spark),
            Stat::make('Bénéfice du jour', Money::format($aujourdhui['benefice']))
                ->icon(Heroicon::OutlinedChartBar)
                ->description('Après dépenses et % gérant')
                ->color($aujourdhui['benefice'] >= 0 ? 'success' : 'danger'),
            Stat::make('Recette du mois', Money::format($mois['recette']))
                ->icon(Heroicon::OutlinedCalendarDays)
                ->description('Bénéfice mois : '.Money::format($mois['benefice']))
                ->color('primary')
                ->chart($spark),
            Stat::make('Commissions du mois', Money::format($mois['commission']))
                ->icon(Heroicon::OutlinedUser)
                ->description($ouvertes.' journée(s) encore ouverte(s)')
                ->color($ouvertes > 0 ? 'warning' : 'info'),
        ];
    }
}
