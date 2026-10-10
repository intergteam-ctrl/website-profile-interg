<?php

namespace App\Filament\Resources\Portfolios\Schemas;

use App\Models\Portfolio;
use App\Support\ImageUploader;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PortfolioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('group')
                    ->label('Tampilkan di')
                    ->options(Portfolio::GROUPS)
                    ->default('other')
                    ->required()
                    ->native(false)
                    ->live()
                    ->helperText('Menentukan bagian beranda tempat proyek ini muncul. Semua proyek tetap tampil di halaman /portfolio.'),

                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->helperText('Angka kecil tampil lebih dulu.'),

                TextInput::make('title')
                    ->label(fn (Get $get): string => match ($get('group')) {
                        'display' => 'Nama klien / instansi',
                        'software' => 'Nama aplikasi',
                        default => 'Judul proyek',
                    })
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('category')
                    ->label(fn (Get $get): string => $get('group') === 'display' ? 'Jenis ruang' : 'Kategori')
                    ->placeholder(fn (Get $get): string => $get('group') === 'display' ? 'Command Center / Monitoring Room / NOC Room' : 'Display Solution / IoT Solution / Software Development'),

                TextInput::make('subtitle')
                    ->label(fn (Get $get): string => match ($get('group')) {
                        'display' => 'Perangkat / teknologi',
                        'software' => 'Klien / pengguna',
                        default => 'Subjudul',
                    })
                    ->placeholder(fn (Get $get): string => match ($get('group')) {
                        'display' => 'ImagePath Infinite VW-5501',
                        'software' => 'Dishub Jatim',
                        default => '',
                    }),

                TextInput::make('url')
                    ->label('Link aplikasi / proyek')
                    ->url()
                    ->maxLength(255)
                    ->placeholder('https://…')
                    ->helperText('Opsional. Menampilkan tombol "Kunjungi aplikasi" di kartu. Pastikan klien setuju sebelum menautkan sistem mereka.'),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Foto / screenshot')
                    ->helperText('Disarankan rasio 16:10, maks. 5 MB. Otomatis diperkecil.')
                    ->image()
                    ->disk('s3')
                    // Initial Company Profile images are bundled in /public/images,
                    // not on S3. Without this, Filament would treat them as missing
                    // and silently drop them when an admin saves the record.
                    ->fetchFileInformation(fn (?Portfolio $record): bool => ! str_starts_with((string) $record?->image, 'images/'))
                    ->getUploadedFileUsing(fn (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array => str_starts_with($file, 'images/')
                        ? ['name' => basename($file), 'size' => 0, 'type' => null, 'url' => asset($file)]
                        : $component->getUploadedFile($file, $storedFileNames))
                    ->directory('portfolios')
                    ->visibility('public')
                    ->imagePreviewHeight('100')
                    ->maxSize(5120)
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file, BaseFileUpload $component): string => ImageUploader::store($file, 'portfolios', $component, maxWidth: 1000, quality: 75)),
            ]);
    }
}
