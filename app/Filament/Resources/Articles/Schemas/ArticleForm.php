<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Article;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article vendu')
                    ->icon(Heroicon::OutlinedCake)->iconColor('pink')
                    ->description('Ces éléments apparaissent ensuite dans la saisie journalière, sans tout retaper.')
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon(Heroicon::OutlinedCake)->prefixIconColor('pink'),
                        Select::make('categorie')
                            ->label('Catégorie')
                            ->options(Article::CATEGORIES)
                            ->required()
                            ->native(false)
                            ->prefixIcon(Heroicon::OutlinedTag)->prefixIconColor('slate'),
                        TextInput::make('prix_unitaire')
                            ->label('Prix unitaire')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix('F CFA')
                            ->prefixIcon(Heroicon::OutlinedBanknotes)->prefixIconColor('orange'),
                        TextInput::make('unite')
                            ->label('Unité')
                            ->default('pièce')
                            ->required()
                            ->maxLength(30)
                            ->prefixIcon(Heroicon::OutlinedHashtag)->prefixIconColor('slate'),
                        Toggle::make('actif')->label('Actif')->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
