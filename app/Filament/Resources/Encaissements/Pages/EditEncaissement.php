<?php

namespace App\Filament\Resources\Encaissements\Pages;

use App\Filament\Resources\Encaissements\EncaissementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEncaissement extends EditRecord
{
    protected static string $resource = EncaissementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
