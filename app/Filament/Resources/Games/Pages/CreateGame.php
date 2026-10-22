<?php

namespace App\Filament\Resources\Games\Pages;

use App\Filament\Resources\Games\GameResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGame extends CreateRecord
{
    protected static string $resource = GameResource::class;

    /** @var array<int|string, mixed>|null */
    protected ?array $uploadedHtml = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->uploadedHtml = (array) ($data['html_files'] ?? []);
        unset($data['html_files']);

        return $data;
    }

    protected function afterCreate(): void
    {
        GameResource::storeUploadedFiles($this->record, $this->uploadedHtml);
    }
}
