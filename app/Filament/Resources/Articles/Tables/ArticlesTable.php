<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Models\Article;
use App\Support\Money;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')->label('Nom')->searchable()->sortable()->weight('bold'),
                TextColumn::make('categorie')
                    ->label('Catégorie')
                    ->formatStateUsing(fn ($state) => Article::CATEGORIES[$state] ?? $state)
                    ->badge(),
                TextColumn::make('prix_unitaire')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => Money::format($state))
                    ->sortable(),
                TextColumn::make('unite')->label('Unité'),
                IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('categorie')->options(Article::CATEGORIES),
            ])
            ->defaultSort('nom')
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ]);
    }
}
