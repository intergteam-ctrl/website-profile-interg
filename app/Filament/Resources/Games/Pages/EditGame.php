<?php

namespace App\Filament\Resources\Games\Pages;

use App\Filament\Resources\Games\GameResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGame extends EditRecord
{
    protected static string $resource = GameResource::class;

    /** @var array<int|string, mixed>|null */
    protected ?array $uploadedHtml = null;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->uploadedHtml = (array) ($data['html_files'] ?? []);
        unset($data['html_files']);

        return $data;
    }

    protected function afterSave(): void
    {
        GameResource::storeUploadedFiles($this->record, $this->uploadedHtml);

        // Clear the upload field so the same files are not added again.
        $this->data['html_files'] = [];
    }
}
