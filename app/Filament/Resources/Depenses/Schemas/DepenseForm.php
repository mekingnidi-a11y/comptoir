<?php

namespace App\Filament\Resources\Depenses\Schemas;

use App\Models\Journee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DepenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dépense')
                    ->icon(Heroicon::OutlinedArrowTrendingDown)->iconColor('rose')
                    ->description('Choisissez une journée encore ouverte, puis le motif et le montant.')
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
                            ->prefixIcon(Heroicon::OutlinedClipboardDocumentList)->prefixIconColor('indigo'),
                        TextInput::make('montant')
                            ->label('Montant')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix('F CFA')
                            ->prefixIcon(Heroicon::OutlinedBanknotes)->prefixIconColor('orange'),
                    ])
                    ->columns(2),
            ]);
    }
}
