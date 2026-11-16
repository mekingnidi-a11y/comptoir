<?php

namespace App\Filament\Widgets;

use App\Support\BoutiqueMetrics;
use Filament\Widgets\Widget;

class TopArticlesWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.top-articles';

    protected int|string|array $columnSpan = 1;

    /**
     * @return array<int, object>
     */
    public function getArticles(): array
    {
        return BoutiqueMetrics::topArticles(8, now()->startOfMonth(), now());
    }
}
