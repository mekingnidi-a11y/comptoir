<?php

namespace App\Filament\Pages;

use App\Models\Gerant;
use App\Support\BoutiqueMetrics;
use App\Support\Money;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reporting extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Reporting';

    protected static ?string $title = 'Reporting';

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament.pages.reporting';

    public ?string $du = null;

    public ?string $au = null;

    public ?string $gerant_id = null;

    public static function getNavigationGroup(): ?string
    {
        return 'Caisse';
    }

    public function mount(): void
    {
        $this->du = now()->startOfMonth()->toDateString();
        $this->au = now()->toDateString();
        if (auth()->user()?->isGerant()) {
            $this->gerant_id = (string) auth()->user()->gerant_id;
        }
    }

    public function gerants(): Collection
    {
        return Gerant::query()->orderBy('nom')->get(['id', 'nom']);
    }

    /**
     * @return array{n:int, recette:int, depenses:int, commission:int, benefice:int}
     */
    public function totaux(): array
    {
        return BoutiqueMetrics::totaux(
            $this->du ? now()->parse($this->du) : null,
            $this->au ? now()->parse($this->au) : null,
            $this->gerantId(),
        );
    }

    /**
     * @return list<object>
     */
    public function parGerant(): array
    {
        return BoutiqueMetrics::parGerant(
            $this->du ? now()->parse($this->du) : null,
            $this->au ? now()->parse($this->au) : null,
        );
    }

    /**
     * @return list<object>
     */
    public function topArticles(): array
    {
        return BoutiqueMetrics::topArticles(
            12,
            $this->du ? now()->parse($this->du) : null,
            $this->au ? now()->parse($this->au) : null,
        );
    }

    public function journees(): Collection
    {
        $q = BoutiqueMetrics::scopeJournees()->with('gerant')->orderByDesc('date');
        if ($this->du) {
            $q->whereDate('date', '>=', $this->du);
        }
        if ($this->au) {
            $q->whereDate('date', '<=', $this->au);
        }
        if ($this->gerantId()) {
            $q->where('gerant_id', $this->gerantId());
        }

        return $q->limit(60)->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exporter CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(fn () => $this->exportCsv()),
        ];
    }

    public function exportCsv(): StreamedResponse
    {
        $totaux = $this->totaux();
        $filename = 'reporting-'.$this->du.'-'.$this->au.'.csv';
        $lignes = $this->journees();

        return response()->streamDownload(function () use ($totaux, $lignes): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Date', 'Gérant', 'État', 'Recette', 'Dépenses', 'Salaire gérant', 'Bénéfice', '%'], ';');
            foreach ($lignes as $j) {
                fputcsv($out, [
                    $j->date?->format('d/m/Y'),
                    $j->gerant?->nom,
                    $j->statut,
                    $j->recette,
                    $j->total_depenses,
                    $j->commission,
                    $j->benefice,
                    $j->pourcentage_applique,
                ], ';');
            }
            fputcsv($out, [], ';');
            fputcsv($out, ['TOTAL', '', $totaux['n'].' jours', $totaux['recette'], $totaux['depenses'], $totaux['commission'], $totaux['benefice'], ''], ';');
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function money(int|float|string|null $n): string
    {
        return Money::format($n);
    }

    public function updatedDu(): void
    {
        $this->validateDates();
    }

    public function updatedAu(): void
    {
        $this->validateDates();
    }

    protected function gerantId(): ?int
    {
        return filled($this->gerant_id) ? (int) $this->gerant_id : null;
    }

    protected function validateDates(): void
    {
        if ($this->du && $this->au && $this->du > $this->au) {
            Notification::make()->title('La date de début doit précéder la date de fin.')->warning()->send();
        }
    }
}
