<?php

namespace App\Filament\Resources\Gerants\Pages;

use App\Filament\Resources\Gerants\GerantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGerant extends EditRecord
{
    protected static string $resource = GerantResource::class;

    /** @var array<string, mixed> */
    protected array $compteData = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->compteData = [
            'compte_email' => $data['compte_email'] ?? null,
            'compte_password' => $data['compte_password'] ?? null,
        ];
        unset($data['compte_email'], $data['compte_password']);

        return $data;
    }

    protected function afterSave(): void
    {
        GerantResource::syncCompte($this->record, $this->compteData);
    }

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
