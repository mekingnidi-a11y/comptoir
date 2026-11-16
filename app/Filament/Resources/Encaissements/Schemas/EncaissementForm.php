<?php

namespace App\Filament\Resources\Encaissements\Schemas;

use App\Models\Article;
use App\Models\Journee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EncaissementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Encaissement')
                    ->icon(Heroicon::OutlinedBanknotes)->iconColor('orange')
                    ->description('Choisissez une journée encore ouverte, puis l’article vendu.')
                    ->schema([
                        Select::make('journee_id')
                            ->label('Journée')
                            ->options(fn () => Journee::queryOuvertes()->get()->mapWithKeys(
                                fn (Journee $journee) => [$journee->id => $journee->libelle()]
                            ))
                            ->searchable()
                            ->required()
                            ->default(fn () => Journee::queryOuvertes()->whereDate('date', now())->value('id')
                                ?? Journee::queryOuvertes()->value('id'))
                            ->prefixIcon(Heroicon::OutlinedCalendarDays)->prefixIconColor('amber')
                            ->helperText('Seules les journées non clôturées apparaissent.'),
                        Select::make('article_id')
                            ->label('Article')
                            ->relationship(
                                'article',
                                'nom',
                                fn ($query) => $query->where('actif', true)->orderBy('nom'),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->prefixIcon(Heroicon::OutlinedCake)->prefixIconColor('pink')
                            ->afterStateUpdated(function ($state, Set $set): void {
                                $article = Article::query()->find($state);
                                if ($article) {
                                    $set('prix_unitaire', $article->prix_unitaire);
                                }
                            }),
                        TextInput::make('quantite')
                            ->label('Quantité')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(0)
                            ->step(0.5)
                            ->prefixIcon(Heroicon::OutlinedHashtag)->prefixIconColor('slate'),
                        TextInput::make('prix_unitaire')
                            ->label('Prix unitaire')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix('F CFA')
                            ->prefixIcon(Heroicon::OutlinedBanknotes)->prefixIconColor('orange'),
                        TextInput::make('note')
                            ->label('Note')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
