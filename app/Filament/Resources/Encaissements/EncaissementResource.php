<?php

namespace App\Filament\Resources\Encaissements;

use App\Filament\Resources\Encaissements\Pages\CreateEncaissement;
use App\Filament\Resources\Encaissements\Pages\EditEncaissement;
use App\Filament\Resources\Encaissements\Pages\ListEncaissements;
use App\Filament\Resources\Encaissements\Schemas\EncaissementForm;
use App\Filament\Resources\Encaissements\Tables\EncaissementsTable;
use App\Models\Encaissement;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EncaissementResource extends Resource
{
    protected static ?string $model = Encaissement::class;

    protected static ?string $navigationLabel = 'Encaissements';

    protected static ?string $modelLabel = 'encaissement';

    protected static ?string $pluralModelLabel = 'encaissements';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 12;

    public static function getNavigationGroup(): ?string
    {
        return 'Mouvement';
    }

    public static function form(Schema $schema): Schema
    {
        return EncaissementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EncaissementsTable::configure($table);
    }

    public static function canEdit($record): bool
    {
        if (! $record instanceof Encaissement) {
            return parent::canEdit($record);
        }

        return $record->journee?->estOuverte() ?? false;
    }

    public static function canDelete($record): bool
    {
        if (! $record instanceof Encaissement) {
            return parent::canDelete($record);
        }

        return $record->journee?->estOuverte() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['journee.gerant', 'article']);
        $user = auth()->user();
        if ($user instanceof User && $user->isGerant()) {
            $query->whereHas('journee', fn (Builder $q) => $q->where('gerant_id', $user->gerant_id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEncaissements::route('/'),
            'create' => CreateEncaissement::route('/create'),
            'edit' => EditEncaissement::route('/{record}/edit'),
        ];
    }
}
