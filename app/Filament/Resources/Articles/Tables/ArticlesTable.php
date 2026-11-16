<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
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

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return FilamentCards::apply($table)
            ->columns(FilamentCards::columns([
                FilamentBrand::line(
                    TextColumn::make('nom')->searchable()->sortable()->weight(FontWeight::Bold),
                    'Nom',
                    Heroicon::OutlinedCake,
                ),
                FilamentBrand::line(
                    TextColumn::make('categorie')
                        ->formatStateUsing(fn ($state) => Article::CATEGORIES[$state] ?? $state)
                        ->badge(),
                    'Catégorie',
                    Heroicon::OutlinedTag,
                ),
            ], [
                FilamentBrand::line(
                    TextColumn::make('prix_unitaire')->formatStateUsing(fn ($state) => Money::format($state))->sortable(),
                    'Prix',
                    Heroicon::OutlinedBanknotes,
                ),
                FilamentBrand::line(
                    TextColumn::make('unite'),
                    'Unité',
                    Heroicon::OutlinedHashtag,
                ),
                FilamentBrand::booleanLine('actif', 'Actif', Heroicon::OutlinedShieldCheck),
            ]))
            ->filters([
                SelectFilter::make('categorie')->options(Article::CATEGORIES),
            ])
            ->defaultSort('nom')
            ->recordActions([
                FilamentCards::viewAction(ArticleResource::class),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ]);
    }
}
