<?php

namespace App\Filament\Resources\Journees\Schemas;

use App\Models\Article;
use App\Models\Gerant;
use App\Models\Journee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rules\Unique;

class JourneeForm
{
    public static function configure(Schema $schema): Schema
    {
        $estGerant = fn () => auth()->user()?->isGerant() ?? false;

        return $schema
            ->components([
                Section::make('Journée')
                    ->icon(Heroicon::OutlinedCalendarDays)->iconColor('amber')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->required()
                            ->prefixIcon(Heroicon::OutlinedCalendarDays)->prefixIconColor('amber')
                            ->unique(
                                table: Journee::class,
                                column: 'date',
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('gerant_id', $get('gerant_id')),
                            ),
                        Select::make('gerant_id')
                            ->label('Gérant')
                            ->relationship(
                                'gerant',
                                'nom',
                                fn ($query) => $query->where('actif', true)->orderBy('nom'),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->prefixIcon(Heroicon::OutlinedUser)->prefixIconColor('violet')
                            ->default(fn () => auth()->user()?->gerant_id)
                            ->disabled($estGerant)
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                $gerant = Gerant::query()->find($state);
                                $set('pourcentage_applique', $gerant?->pourcentage);
                            }),
                        TextInput::make('pourcentage_applique')
                            ->label('% appliqué')
                            ->numeric()
                            ->suffix('%')
                            ->required()
                            ->prefixIcon(Heroicon::OutlinedChartBar)->prefixIconColor('lime')
                            ->helperText('Figé pour cette journée, même si le % du gérant change plus tard.'),
                        TextInput::make('fond_caisse')
                            ->label('Fond de caisse')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('F CFA')
                            ->prefixIcon(Heroicon::OutlinedBanknotes)->prefixIconColor('orange'),
                        Toggle::make('commission_versee')
                            ->label('Salaire du gérant déjà versé')
                            ->default(false),
                        Textarea::make('notes')
                            ->label('Notes / observations')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Encaissements')
                    ->icon(Heroicon::OutlinedBanknotes)->iconColor('orange')
                    ->description('Choisissez un article du catalogue, indiquez la quantité vendue.')
                    ->schema([
                        Repeater::make('encaissements')
                            ->relationship()
                            ->label('')
                            ->schema([
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
                                    })
                                    ->columnSpan(2),
                                TextInput::make('quantite')
                                    ->label('Quantité')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0)
                                    ->step(0.5)
                                    ->prefixIcon(Heroicon::OutlinedHashtag)->prefixIconColor('slate'),
                                TextInput::make('prix_unitaire')
                                    ->label('Prix unit.')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffix('F')
                                    ->prefixIcon(Heroicon::OutlinedBanknotes)->prefixIconColor('orange'),
                                TextInput::make('note')
                                    ->label('Note')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Ajouter un encaissement')
                            ->cloneable()
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
                Section::make('Dépenses du jour')
                    ->icon(Heroicon::OutlinedArrowTrendingDown)->iconColor('rose')
                    ->schema([
                        Repeater::make('depenses')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Select::make('categorie_depense_id')
                                    ->label('Motif')
                                    ->relationship(
                                        'categorie',
                                        'nom',
                                        fn ($query) => $query->where('actif', true)->orderBy('nom'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->prefixIcon(Heroicon::OutlinedTag)->prefixIconColor('slate'),
                                TextInput::make('libelle')
                                    ->label('Libellé')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2)
                                    ->prefixIcon(Heroicon::OutlinedClipboardDocumentList)->prefixIconColor('indigo'),
                                TextInput::make('montant')
                                    ->label('Montant')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffix('F CFA')
                                    ->prefixIcon(Heroicon::OutlinedBanknotes)->prefixIconColor('orange'),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une dépense')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
