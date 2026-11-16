<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActiviteChart;
use App\Filament\Widgets\StatsBoutique;
use App\Filament\Widgets\TopArticlesWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static bool $isDiscovered = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Tableau de bord';

    protected static ?string $title = 'Tableau de bord';

    public function getWidgets(): array
    {
        return [
            StatsBoutique::class,
            ActiviteChart::class,
            TopArticlesWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }
}
