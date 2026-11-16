<?php

namespace App\Filament\Resources\Encaissements\Pages;

use App\Filament\Resources\Encaissements\EncaissementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEncaissement extends CreateRecord
{
    protected static string $resource = EncaissementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
