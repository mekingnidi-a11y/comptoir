<?php

namespace App\Filament\Resources\Journees;

use App\Filament\Resources\Journees\Pages\CreateJournee;
use App\Filament\Resources\Journees\Pages\EditJournee;
use App\Filament\Resources\Journees\Pages\ListJournees;
use App\Filament\Resources\Journees\Pages\ViewJournee;
use App\Filament\Resources\Journees\Schemas\JourneeForm;
use App\Filament\Resources\Journees\Tables\JourneesTable;
use App\Models\Journee;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Money;

class JourneeResource extends Resource
{
    protected static ?string $model = Journee::class;

    protected static ?string $recordTitleAttribute = 'date';

    protected static ?string $navigationLabel = 'Journées de caisse';

    protected static ?string $modelLabel = 'journée';

    protected static ?string $pluralModelLabel = 'journées';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return 'Caisse';
    }

    public static function form(Schema $schema): Schema
    {
        return JourneeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('État de la journée')
                ->icon(Heroicon::OutlinedClipboardDocumentList)->iconColor('indigo')
                ->columns(3)
                ->schema([
                    TextEntry::make('date')->label('Date')->date('d/m/Y'),
                    TextEntry::make('gerant.nom')->label('Gérant'),
                    TextEntry::make('statut')->label('Statut')->badge()
                        ->color(fn ($state) => $state === Journee::STATUT_CLOTUREE ? 'success' : 'warning'),
                    TextEntry::make('recette')->label('Recette')->formatStateUsing(fn ($state) => Money::format($state)),
                    TextEntry::make('total_depenses')->label('Dépenses')->formatStateUsing(fn ($state) => Money::format($state)),
                    TextEntry::make('commission')->label('Salaire gérant')->formatStateUsing(fn ($state) => Money::format($state))
                        ->helperText(fn ($record) => $record?->pourcentage_applique.' % de la recette'),
                    TextEntry::make('benefice')->label('Bénéfice')->formatStateUsing(fn ($state) => Money::format($state)),
                    TextEntry::make('fond_caisse')->label('Fond de caisse')->formatStateUsing(fn ($state) => Money::format($state)),
                    TextEntry::make('commission_versee')->label('Salaire versé')->boolean(),
                    TextEntry::make('notes')->label('Notes')->columnSpanFull(),
                ]),
            Section::make('Encaissements')->icon(Heroicon::OutlinedBanknotes)->iconColor('orange')->schema([
                RepeatableEntry::make('encaissements')->label('')->schema([
                    TextEntry::make('article.nom')->label('Article'),
                    TextEntry::make('quantite')->label('Qté'),
                    TextEntry::make('prix_unitaire')->label('Prix')->formatStateUsing(fn ($state) => Money::format($state)),
                    TextEntry::make('montant')->label('Montant')->formatStateUsing(fn ($state) => Money::format($state)),
                ])->columns(4),
            ]),
            Section::make('Dépenses')->icon(Heroicon::OutlinedArrowTrendingDown)->iconColor('rose')->schema([
                RepeatableEntry::make('depenses')->label('')->schema([
                    TextEntry::make('categorie.nom')->label('Motif')->placeholder('—'),
                    TextEntry::make('libelle')->label('Libellé'),
                    TextEntry::make('montant')->label('Montant')->formatStateUsing(fn ($state) => Money::format($state)),
                ])->columns(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return JourneesTable::configure($table);
    }

    public static function canEdit($record): bool
    {
        if (! $record instanceof Journee) {
            return parent::canEdit($record);
        }

        return $record->estOuverte() || (auth()->user()?->isProprietaire() ?? false);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['gerant']);
        $user = auth()->user();
        if ($user instanceof User && $user->isGerant()) {
            $query->where('gerant_id', $user->gerant_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournees::route('/'),
            'create' => CreateJournee::route('/create'),
            'view' => ViewJournee::route('/{record}'),
            'edit' => EditJournee::route('/{record}/edit'),
        ];
    }
}
