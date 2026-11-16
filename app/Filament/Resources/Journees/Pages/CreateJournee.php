<?php

namespace App\Filament\Resources\Journees\Pages;

use App\Filament\Resources\Journees\JourneeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJournee extends CreateRecord
{
    protected static string $resource = JourneeResource::class;

    protected function afterCreate(): void
    {
        $this->record->recalculer();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
