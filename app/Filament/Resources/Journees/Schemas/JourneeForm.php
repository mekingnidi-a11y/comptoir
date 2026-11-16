<?php

namespace App\Filament\Resources\Journees\Schemas;

use App\Models\Gerant;
use App\Models\Journee;
use Filament\Forms\Components\DatePicker;
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
                Section::make('Paramètres de la journée')
                    ->icon(Heroicon::OutlinedCalendarDays)->iconColor('amber')
                    ->description('Créez la journée, choisissez le gérant, puis clôturez-la. Les ventes et dépenses se saisissent dans Mouvement.')
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
                            ->helperText('Salaire du gérant = recette × ce pourcentage, figé pour cette journée.'),
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
            ]);
    }
}
