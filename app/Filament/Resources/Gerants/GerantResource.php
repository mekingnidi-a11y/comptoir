<?php

namespace App\Filament\Resources\Gerants;

use App\Filament\Resources\Gerants\Pages\CreateGerant;
use App\Filament\Resources\Gerants\Pages\EditGerant;
use App\Filament\Resources\Gerants\Pages\ListGerants;
use App\Filament\Resources\Gerants\Schemas\GerantForm;
use App\Filament\Resources\Gerants\Tables\GerantsTable;
use App\Models\Gerant;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GerantResource extends Resource
{
    protected static ?string $model = Gerant::class;

    protected static ?string $recordTitleAttribute = 'nom';

    protected static ?string $navigationLabel = 'Gérants';

    protected static ?string $modelLabel = 'gérant';

    protected static ?string $pluralModelLabel = 'gérants';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 20;

    public static function getNavigationGroup(): ?string
    {
        return 'Équipe';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isProprietaire() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return GerantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GerantsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGerants::route('/'),
            'create' => CreateGerant::route('/create'),
            'edit' => EditGerant::route('/{record}/edit'),
        ];
    }

    public static function syncCompte(Gerant $gerant, array $data): void
    {
        $email = $data['compte_email'] ?? null;
        $password = $data['compte_password'] ?? null;

        if (! filled($email)) {
            return;
        }

        $user = $gerant->user ?? new User;
        $user->name = $gerant->nom;
        $user->email = $email;
        $user->role = User::ROLE_GERANT;
        $user->gerant_id = $gerant->id;
        $user->actif = $gerant->actif;
        if (filled($password)) {
            $user->password = $password;
        } elseif (! $user->exists) {
            $user->password = str()->password(10);
        }
        $user->save();
    }
}
