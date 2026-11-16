<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Article;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article vendu')
                    ->description('Ces éléments apparaissent ensuite dans la saisie journalière, sans tout retaper.')
                    ->schema([
                        TextInput::make('nom')->label('Nom')->required()->maxLength(255),
                        Select::make('categorie')
                            ->label('Catégorie')
                            ->options(Article::CATEGORIES)
                            ->required()
                            ->native(false),
                        TextInput::make('prix_unitaire')
                            ->label('Prix unitaire')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix('F CFA'),
                        TextInput::make('unite')
                            ->label('Unité')
                            ->default('pièce')
                            ->required()
                            ->maxLength(30),
                        Toggle::make('actif')->label('Actif')->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
