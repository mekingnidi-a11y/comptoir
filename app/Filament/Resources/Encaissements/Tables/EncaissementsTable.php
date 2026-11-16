<?php

namespace App\Filament\Resources\Encaissements\Tables;

use App\Filament\Resources\Encaissements\EncaissementResource;
use App\Models\Encaissement;
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

class EncaissementsTable
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
                    TextColumn::make('article.nom')->searchable()->weight(FontWeight::Bold),
                    'Article',
                    Heroicon::OutlinedCake,
                ),
            ], [
                FilamentBrand::line(
                    TextColumn::make('quantite'),
                    'Qté',
                    Heroicon::OutlinedHashtag,
                ),
                FilamentBrand::line(
                    TextColumn::make('prix_unitaire')->formatStateUsing(fn ($state) => Money::format($state)),
                    'Prix',
                    Heroicon::OutlinedBanknotes,
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
                FilamentCards::viewAction(EncaissementResource::class),
                EditAction::make()->iconButton()
                    ->visible(fn (Encaissement $record) => $record->journee?->estOuverte() ?? false),
                DeleteAction::make()->iconButton()
                    ->visible(fn (Encaissement $record) => $record->journee?->estOuverte() ?? false),
            ]);
    }
}
