<?php

namespace App\Filament\Resources\Journees\Tables;

use App\Filament\Resources\Journees\JourneeResource;
use App\Models\Journee;
use App\Support\FilamentBrand;
use App\Support\FilamentCards;
use App\Support\Money;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JourneesTable
{
    public static function configure(Table $table): Table
    {
        return FilamentCards::apply($table)
            ->columns(FilamentCards::columns([
                FilamentBrand::line(
                    TextColumn::make('date')->date('d/m/Y')->weight(FontWeight::Bold)->sortable(),
                    'Date',
                    Heroicon::OutlinedCalendarDays,
                ),
                FilamentBrand::line(
                    TextColumn::make('gerant.nom')->searchable()->sortable(),
                    'Gérant',
                    Heroicon::OutlinedUser,
                ),
                FilamentBrand::line(
                    TextColumn::make('statut')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state === Journee::STATUT_CLOTUREE ? 'Clôturée' : 'Ouverte')
                        ->color(fn ($state) => $state === Journee::STATUT_CLOTUREE ? 'success' : 'warning'),
                    'État',
                    Heroicon::OutlinedShieldCheck,
                ),
            ], [
                FilamentBrand::line(
                    TextColumn::make('recette')->formatStateUsing(fn ($state) => Money::format($state))->sortable(),
                    'Recette',
                    Heroicon::OutlinedBanknotes,
                ),
                FilamentBrand::line(
                    TextColumn::make('total_depenses')->formatStateUsing(fn ($state) => Money::format($state)),
                    'Dépenses',
                    Heroicon::OutlinedArrowTrendingDown,
                ),
                FilamentBrand::line(
                    TextColumn::make('commission')->formatStateUsing(fn ($state) => Money::format($state)),
                    'Salaire',
                    Heroicon::OutlinedUser,
                ),
                FilamentBrand::line(
                    TextColumn::make('benefice')
                        ->formatStateUsing(fn ($state) => Money::format($state))
                        ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                        ->sortable(),
                    'Bénéfice',
                    Heroicon::OutlinedChartBar,
                ),
                FilamentBrand::booleanLine('commission_versee', 'Payé', Heroicon::OutlinedShieldCheck),
            ]))
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('statut')
                    ->options([
                        Journee::STATUT_OUVERTE => 'Ouverte',
                        Journee::STATUT_CLOTUREE => 'Clôturée',
                    ]),
                SelectFilter::make('gerant_id')
                    ->label('Gérant')
                    ->relationship('gerant', 'nom')
                    ->visible(fn () => auth()->user()?->isProprietaire()),
            ])
            ->recordActions([
                FilamentCards::viewAction(JourneeResource::class)
                    ->modalHeading(fn (Journee $record): string => 'Journée '.$record->date?->format('d/m/Y').' — '.($record->gerant?->nom ?? '')),
                EditAction::make()->iconButton()
                    ->visible(fn (Journee $record) => $record->estOuverte() || auth()->user()?->isProprietaire()),
                Action::make('cloturer')
                    ->iconButton()
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Clôturer la journée ?')
                    ->modalDescription('Les totaux (recette, salaire gérant, bénéfice) sont figés. La saisie ne pourra plus être modifiée sans réouverture.')
                    ->visible(fn (Journee $record) => $record->estOuverte())
                    ->action(function (Journee $record): void {
                        $record->cloturer();
                        Notification::make()->title('Journée clôturée')->success()->send();
                    }),
                DeleteAction::make()->iconButton()
                    ->visible(fn (Journee $record) => $record->estOuverte() || auth()->user()?->isProprietaire()),
            ]);
    }
}
