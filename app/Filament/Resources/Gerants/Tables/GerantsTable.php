<?php

namespace App\Filament\Resources\Gerants\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GerantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')->label('Nom')->searchable()->sortable()->weight('bold'),
                TextColumn::make('telephone')->label('Téléphone')->searchable(),
                TextColumn::make('pourcentage')->label('%')->suffix(' %')->sortable(),
                TextColumn::make('user.email')->label('Compte')->placeholder('—'),
                IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->defaultSort('nom')
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ]);
    }
}
