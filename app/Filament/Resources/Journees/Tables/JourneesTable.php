<?php

namespace App\Filament\Resources\Journees\Tables;

use App\Models\Journee;
use App\Support\Money;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JourneesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('gerant.nom')->label('Gérant')->searchable()->sortable(),
                TextColumn::make('statut')
                    ->label('État')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === Journee::STATUT_CLOTUREE ? 'Clôturée' : 'Ouverte')
                    ->color(fn ($state) => $state === Journee::STATUT_CLOTUREE ? 'success' : 'warning'),
                TextColumn::make('recette')->label('Recette')->formatStateUsing(fn ($state) => Money::format($state))->sortable(),
                TextColumn::make('total_depenses')->label('Dépenses')->formatStateUsing(fn ($state) => Money::format($state)),
                TextColumn::make('commission')->label('Salaire gérant')->formatStateUsing(fn ($state) => Money::format($state)),
                TextColumn::make('benefice')
                    ->label('Bénéfice')
                    ->formatStateUsing(fn ($state) => Money::format($state))
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->sortable(),
                IconColumn::make('commission_versee')->label('Payé')->boolean(),
            ])
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
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton()
                    ->visible(fn (Journee $record) => $record->estOuverte() || auth()->user()?->isProprietaire()),
                Action::make('cloturer')
                    ->iconButton()
                    ->icon('heroicon-o-lock-closed')
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
