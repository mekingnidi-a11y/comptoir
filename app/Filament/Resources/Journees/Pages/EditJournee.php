<?php

namespace App\Filament\Resources\Journees\Pages;

use App\Filament\Resources\Journees\JourneeResource;
use App\Models\Journee;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJournee extends EditRecord
{
    protected static string $resource = JourneeResource::class;

    protected function afterSave(): void
    {
        $this->record->recalculer();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cloturer')
                ->label('Clôturer')
                ->icon('heroicon-o-lock-closed')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->estOuverte())
                ->action(function (): void {
                    $this->record->cloturer();
                    Notification::make()->title('Journée clôturée')->success()->send();
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),
            Action::make('rouvrir')
                ->label('Réouvrir')
                ->icon('heroicon-o-lock-open')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => ! $this->record->estOuverte() && auth()->user()?->isProprietaire())
                ->action(function (): void {
                    $this->record->rouvrir();
                    Notification::make()->title('Journée réouverte')->success()->send();
                    $this->refreshFormData(['statut']);
                }),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    public static function canEdit($record): bool
    {
        if (! $record instanceof Journee) {
            return parent::canEdit($record);
        }

        return $record->estOuverte() || (auth()->user()?->isProprietaire() ?? false);
    }
}
