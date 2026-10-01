<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama kategori')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique('categories', 'slug'),
                    ]),

                TextInput::make('name')
                    ->label('Nama produk')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation): void {
                        // Auto-fill the slug only when creating, so existing URLs never change silently.
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Terisi otomatis dari nama produk.'),

                TextInput::make('brand')
                    ->label('Merek')
                    ->required(),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),

                Textarea::make('description')
                    ->label('Deskripsi & spesifikasi')
                    ->required()
                    ->rows(8)
                    ->placeholder("Laptop bisnis tangguh, kondisi mulus, baterai sehat.\n\nProcessor: Intel Core i5-1245U\nRAM: 16GB DDR4\nStorage: 512GB SSD")
                    ->helperText('Paragraf pertama = ringkasan. Kosongkan satu baris, lalu tulis spesifikasi per baris dengan format "Nama: Nilai" — otomatis tampil sebagai tabel spesifikasi.')
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Foto produk')
                    ->helperText('Maks. 5 MB. Otomatis diperkecil ke lebar 800px.')
                    ->image()
                    ->disk('s3')
                    ->directory('products')
                    ->visibility('public')
                    ->imagePreviewHeight('100')
                    ->maxSize(5120)
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                        $maxWidth = 800;
                        $quality = 70;

                        $img = @imagecreatefromstring(file_get_contents($file->getRealPath()));

                        if (! $img) {
                            return $file->store('products', 's3');
                        }

                        $w = imagesx($img);
                        $h = imagesy($img);
                        if ($w > $maxWidth) {
                            $img = imagescale($img, $maxWidth, (int) round($h * $maxWidth / $w));
                        }

                        $filename = 'products/'.Str::uuid().'.jpg';

                        ob_start();
                        imagejpeg($img, null, $quality);
                        $binary = ob_get_clean();
                        imagedestroy($img);

                        $saved = Storage::disk('s3')->put($filename, $binary);

                        if ($saved === false) {
                            throw new \RuntimeException('Gagal mengunggah gambar ke storage. Periksa konfigurasi Object Storage (S3).');
                        }

                        return $filename;
                    }),

                Select::make('status')
                    ->options([
                        'baru' => 'Baru',
                        'bekas' => 'Bekas',
                        'digital' => 'Digital License',
                    ])
                    ->native(false)
                    ->required(),
            ]);
    }
}
