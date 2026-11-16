<?php

namespace App\Filament\Resources\Encaissements\Pages;

use App\Filament\Resources\Encaissements\EncaissementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEncaissements extends ListRecords
{
    protected static string $resource = EncaissementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvel encaissement'),
        ];
    }
}
