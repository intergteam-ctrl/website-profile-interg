<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Support\ImageUploader;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('category')
                    ->placeholder('IoT & Smart City / Display Solution'),

                DateTimePicker::make('published_at')
                    ->label('Tanggal Publish')
                    ->native(false)
                    ->helperText('Kosongkan untuk menyimpan sebagai draft.'),

                Textarea::make('excerpt')
                    ->label('Ringkasan')
                    ->rows(3)
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('Konten')
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->image()
                    ->disk('s3')
                    ->directory('posts')
                    ->visibility('public')
                    ->imagePreviewHeight('100')
                    ->maxSize(5120)
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file, BaseFileUpload $component): string => ImageUploader::store($file, 'posts', $component, maxWidth: 1200, quality: 75)),
            ]);
    }
}
