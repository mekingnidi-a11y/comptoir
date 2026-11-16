<?php

namespace App\Filament\Resources\CategorieDepenses\Pages;

use App\Filament\Resources\CategorieDepenses\CategorieDepenseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategorieDepense extends EditRecord
{
    protected static string $resource = CategorieDepenseResource::class;

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
