<?php

namespace App\Filament\Resources\CategorieDepenses;

use App\Filament\Resources\CategorieDepenses\Pages\CreateCategorieDepense;
use App\Filament\Resources\CategorieDepenses\Pages\EditCategorieDepense;
use App\Filament\Resources\CategorieDepenses\Pages\ListCategorieDepenses;
use App\Models\CategorieDepense;
use App\Support\FilamentBrand;
use App\Support\FilamentCards;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategorieDepenseResource extends Resource
{
    protected static ?string $model = CategorieDepense::class;

    protected static ?string $recordTitleAttribute = 'nom';

    protected static ?string $navigationLabel = 'Motifs de dépense';

    protected static ?string $modelLabel = 'motif de dépense';

    protected static ?string $pluralModelLabel = 'motifs de dépense';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 31;

    public static function getNavigationGroup(): ?string
    {
        return 'Catalogue';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isProprietaire() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Motif de dépense')
                ->icon(Heroicon::OutlinedTag)->iconColor('slate')
                ->schema([
                    TextInput::make('nom')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon(Heroicon::OutlinedTag)->prefixIconColor('slate'),
                    Toggle::make('actif')->label('Actif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return FilamentCards::apply($table)
            ->columns(FilamentCards::columns([
                FilamentBrand::line(
                    TextColumn::make('nom')->searchable()->sortable()->weight(FontWeight::Bold),
                    'Nom',
                    Heroicon::OutlinedTag,
                ),
                FilamentBrand::booleanLine('actif', 'Actif', Heroicon::OutlinedShieldCheck),
            ]))
            ->recordActions([
                FilamentCards::viewAction(self::class),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategorieDepenses::route('/'),
            'create' => CreateCategorieDepense::route('/create'),
            'edit' => EditCategorieDepense::route('/{record}/edit'),
        ];
    }
}
