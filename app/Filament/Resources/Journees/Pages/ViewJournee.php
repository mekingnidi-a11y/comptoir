<?php

namespace App\Filament\Resources\Journees\Pages;

use App\Filament\Resources\Journees\JourneeResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewJournee extends ViewRecord
{
    protected static string $resource = JourneeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->estOuverte() || auth()->user()?->isProprietaire()),
            Action::make('cloturer')
                ->label('Clôturer')
                ->icon('heroicon-o-lock-closed')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->estOuverte())
                ->action(function (): void {
                    $this->record->cloturer();
                    Notification::make()->title('Journée clôturée')->success()->send();
                    $this->refreshFormData(['statut']);
                }),
        ];
    }
}
