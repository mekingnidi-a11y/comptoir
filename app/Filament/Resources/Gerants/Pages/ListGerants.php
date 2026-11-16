<?php

namespace App\Filament\Resources\Gerants\Pages;

use App\Filament\Resources\Gerants\GerantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGerants extends ListRecords
{
    protected static string $resource = GerantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
