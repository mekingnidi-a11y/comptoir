<?php

namespace App\Filament\Resources\Journees\Pages;

use App\Filament\Resources\Journees\JourneeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJournees extends ListRecords
{
    protected static string $resource = JourneeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle journée'),
        ];
    }
}
