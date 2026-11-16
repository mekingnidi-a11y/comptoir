<?php

namespace App\Filament\Widgets;

use App\Support\BoutiqueMetrics;
use Filament\Widgets\ChartWidget;

class ActiviteChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = '14 derniers jours';

    protected ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = BoutiqueMetrics::recetteParJour();

        return [
            'datasets' => [
                [
                    'label' => 'Recette',
                    'data' => $data['recette'],
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Dépenses',
                    'data' => $data['depenses'],
                    'borderColor' => 'rgb(244, 63, 94)',
                    'backgroundColor' => 'rgba(244, 63, 94, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Bénéfice',
                    'data' => $data['benefice'],
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }
}
