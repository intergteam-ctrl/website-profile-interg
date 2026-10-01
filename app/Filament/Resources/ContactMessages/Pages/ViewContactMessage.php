<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var ContactMessage $message */
        $message = $this->getRecord();

        // Opening a message marks it as read.
        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }
    }

    protected function getHeaderActions(): array
    {
        /** @var ContactMessage $message */
        $message = $this->getRecord();

        return [
            Action::make('reply')
                ->label('Balas via Email')
                ->icon('heroicon-o-paper-airplane')
                ->url('mailto:'.$message->email.'?subject='.rawurlencode('Re: Pertanyaan '.($message->service ?: 'Anda').' — Inter G Queen Bumindo'))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
