<?php

namespace App\Filament\Resources\Depenses;

use App\Filament\Resources\Depenses\Pages\CreateDepense;
use App\Filament\Resources\Depenses\Pages\EditDepense;
use App\Filament\Resources\Depenses\Pages\ListDepenses;
use App\Filament\Resources\Depenses\Schemas\DepenseForm;
use App\Filament\Resources\Depenses\Tables\DepensesTable;
use App\Models\Depense;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DepenseResource extends Resource
{
    protected static ?string $model = Depense::class;

    protected static ?string $navigationLabel = 'Dépenses';

    protected static ?string $modelLabel = 'dépense';

    protected static ?string $pluralModelLabel = 'dépenses';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingDown;

    protected static ?int $navigationSort = 13;

    public static function getNavigationGroup(): ?string
    {
        return 'Mouvement';
    }

    public static function form(Schema $schema): Schema
    {
        return DepenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepensesTable::configure($table);
    }

    public static function canEdit($record): bool
    {
        if (! $record instanceof Depense) {
            return parent::canEdit($record);
        }

        return $record->journee?->estOuverte() ?? false;
    }

    public static function canDelete($record): bool
    {
        if (! $record instanceof Depense) {
            return parent::canDelete($record);
        }

        return $record->journee?->estOuverte() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['journee.gerant', 'categorie']);
        $user = auth()->user();
        if ($user instanceof User && $user->isGerant()) {
            $query->whereHas('journee', fn (Builder $q) => $q->where('gerant_id', $user->gerant_id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepenses::route('/'),
            'create' => CreateDepense::route('/create'),
            'edit' => EditDepense::route('/{record}/edit'),
        ];
    }
}
