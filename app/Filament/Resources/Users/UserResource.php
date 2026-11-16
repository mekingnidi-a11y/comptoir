<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use App\Support\FilamentBrand;
use App\Support\FilamentCards;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $modelLabel = 'utilisateur';

    protected static ?string $pluralModelLabel = 'utilisateurs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 21;

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
        return $schema->components([
            Section::make('Utilisateur')
                ->icon(Heroicon::OutlinedUsers)->iconColor('pink')
                ->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon(Heroicon::OutlinedUser)->prefixIconColor('violet'),
                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->prefixIcon(Heroicon::OutlinedEnvelope)->prefixIconColor('sky'),
                    TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->prefixIcon(Heroicon::OutlinedLockClosed)->prefixIconColor('gray'),
                    Select::make('role')
                        ->label('Rôle')
                        ->options([
                            User::ROLE_PROPRIETAIRE => 'Propriétaire',
                            User::ROLE_GERANT => 'Gérant',
                        ])
                        ->required()
                        ->native(false)
                        ->prefixIcon(Heroicon::OutlinedShieldCheck)->prefixIconColor('purple'),
                    Select::make('gerant_id')
                        ->label('Gérant lié')
                        ->relationship('gerant', 'nom')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->prefixIcon(Heroicon::OutlinedUserGroup)->prefixIconColor('pink'),
                    Toggle::make('actif')->label('Actif')->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return FilamentCards::apply($table)
            ->columns(FilamentCards::columns([
                FilamentBrand::line(
                    TextColumn::make('name')->searchable()->weight(FontWeight::Bold),
                    'Nom',
                    Heroicon::OutlinedUser,
                ),
                FilamentBrand::line(
                    TextColumn::make('email')->searchable(),
                    'E-mail',
                    Heroicon::OutlinedEnvelope,
                ),
            ], [
                FilamentBrand::line(
                    TextColumn::make('role')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state === User::ROLE_PROPRIETAIRE ? 'Propriétaire' : 'Gérant'),
                    'Rôle',
                    Heroicon::OutlinedShieldCheck,
                ),
                FilamentBrand::line(
                    TextColumn::make('gerant.nom')->placeholder('—'),
                    'Gérant',
                    Heroicon::OutlinedUserGroup,
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
