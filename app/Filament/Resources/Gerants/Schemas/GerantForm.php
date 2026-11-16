<?php

namespace App\Filament\Resources\Gerants\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class GerantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gérant')
                    ->icon(Heroicon::OutlinedUser)
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('pourcentage')
                            ->label('Pourcentage sur la recette')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.5)
                            ->suffix('%')
                            ->required()
                            ->helperText('Salaire du jour = recette × ce pourcentage. Chaque gérant a le sien.'),
                        Toggle::make('actif')
                            ->label('Actif')
                            ->default(true),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Compte de connexion (optionnel)')
                    ->icon(Heroicon::OutlinedKey)
                    ->description('Le gérant pourra alors saisir lui-même sa journée.')
                    ->schema([
                        TextInput::make('compte_email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255)
                            ->default(fn ($record) => $record?->user?->email),
                        TextInput::make('compte_password')
                            ->label('Mot de passe')
                            ->password()
                            ->revealable()
                            ->helperText('Laisser vide pour ne pas changer un compte existant.'),
                    ])
                    ->columns(2),
            ]);
    }
}
