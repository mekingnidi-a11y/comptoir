<?php

namespace App\Support;

use Closure;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;

class FilamentCards
{
    /**
     * @param  array<int, mixed>  $summary
     * @param  array<int, mixed>  $details
     * @return array<int, mixed>
     */
    public static function columns(array $summary, array $details = []): array
    {
        $stacks = [
            Stack::make($summary)->space(1),
        ];

        if ($details !== []) {
            $stacks[] = Stack::make($details)->space(1);
        }

        return $stacks;
    }

    /**
     * @param  class-string|null  $resource
     */
    public static function viewAction(?string $resource = null, ?Closure $editRecord = null): ViewAction
    {
        $action = ViewAction::make()
            ->label('Détail')
            ->modal()
            ->modalWidth('5xl')
            ->modalHeading(fn ($record): string => (string) ($record?->nom
                ?? $record?->libelle
                ?? $record?->name
                ?? ($record?->date?->format('d/m/Y') ?? null)
                ?? 'Détail'))
            ->modalCancelActionLabel('Retour')
            ->icon(Heroicon::OutlinedEye)
            ->iconButton()
            ->extraModalFooterActions(fn (): array => [
                EditAction::make()
                    ->label('Modifier')
                    ->icon(Heroicon::OutlinedPencil)
                    ->color('primary')
                    ->modal(false)
                    ->url(function ($record, $livewire) use ($resource, $editRecord): ?string {
                        $target = $editRecord ? $editRecord($record) : $record;
                        $class = $resource;

                        if (! is_string($class) && is_object($livewire) && method_exists($livewire, 'getResource')) {
                            $class = $livewire::getResource();
                        }

                        if (! is_string($class) || ! $target || ! $class::hasPage('edit')) {
                            return null;
                        }

                        return $class::getUrl('edit', ['record' => $target]);
                    }),
                ...($editRecord ? [] : [
                    DeleteAction::make()->label('Supprimer'),
                ]),
            ]);

        if ($resource) {
            $action->schema(fn ($schema) => $resource::form($schema));
        }

        return $action;
    }

    public static function apply(Table $table, bool $clickToDetail = true): Table
    {
        $table->contentGrid([
            'default' => 2,
            'lg' => 3,
        ]);

        if ($clickToDetail) {
            $table
                ->recordUrl(null)
                ->recordAction('view');
        }

        return $table;
    }
}
