<?php

namespace App\Filament\Resources\Depenses\Tables;

use App\Filament\Resources\Depenses\DepenseResource;
use App\Models\Depense;
use App\Models\Journee;
use App\Support\FilamentBrand;
use App\Support\FilamentCards;
use App\Support\Money;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DepensesTable
{
    public static function configure(Table $table): Table
    {
        return FilamentCards::apply($table)
            ->columns(FilamentCards::columns([
                FilamentBrand::line(
                    TextColumn::make('journee.date')->date('d/m/Y')->weight(FontWeight::Bold)->sortable(),
                    'Journée',
                    Heroicon::OutlinedCalendarDays,
                ),
                FilamentBrand::line(
                    TextColumn::make('journee.gerant.nom')->searchable(),
                    'Gérant',
                    Heroicon::OutlinedUser,
                ),
                FilamentBrand::line(
                    TextColumn::make('libelle')->searchable()->weight(FontWeight::Bold),
                    'Libellé',
                    Heroicon::OutlinedClipboardDocumentList,
                ),
            ], [
                FilamentBrand::line(
                    TextColumn::make('categorie.nom')->placeholder('—'),
                    'Motif',
                    Heroicon::OutlinedTag,
                ),
                FilamentBrand::line(
                    TextColumn::make('montant')->formatStateUsing(fn ($state) => Money::format($state))->sortable(),
                    'Montant',
                    Heroicon::OutlinedBanknotes,
                ),
            ]))
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('journee_id')
                    ->label('Journée')
                    ->options(fn () => Journee::query()->with('gerant')->orderByDesc('date')->get()->mapWithKeys(
                        fn (Journee $journee) => [$journee->id => $journee->libelle()]
                    )),
            ])
            ->recordActions([
                FilamentCards::viewAction(DepenseResource::class),
                EditAction::make()->iconButton()
                    ->visible(fn (Depense $record) => $record->journee?->estOuverte() ?? false),
                DeleteAction::make()->iconButton()
                    ->visible(fn (Depense $record) => $record->journee?->estOuverte() ?? false),
            ]);
    }
}
