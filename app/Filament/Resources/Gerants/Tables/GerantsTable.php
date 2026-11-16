<?php

namespace App\Filament\Resources\Gerants\Tables;

use App\Filament\Resources\Gerants\GerantResource;
use App\Support\FilamentBrand;
use App\Support\FilamentCards;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GerantsTable
{
    public static function configure(Table $table): Table
    {
        return FilamentCards::apply($table)
            ->columns(FilamentCards::columns([
                FilamentBrand::line(
                    TextColumn::make('nom')->searchable()->sortable()->weight(FontWeight::Bold),
                    'Nom',
                    Heroicon::OutlinedUser,
                ),
                FilamentBrand::line(
                    TextColumn::make('telephone')->searchable(),
                    'Téléphone',
                    Heroicon::OutlinedPhone,
                ),
            ], [
                FilamentBrand::line(
                    TextColumn::make('pourcentage')->suffix(' %')->sortable(),
                    '%',
                    Heroicon::OutlinedChartBar,
                ),
                FilamentBrand::line(
                    TextColumn::make('user.email')->placeholder('—'),
                    'Compte',
                    Heroicon::OutlinedEnvelope,
                ),
                FilamentBrand::booleanLine('actif', 'Actif', Heroicon::OutlinedShieldCheck),
            ]))
            ->defaultSort('nom')
            ->recordActions([
                FilamentCards::viewAction(GerantResource::class),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ]);
    }
}
