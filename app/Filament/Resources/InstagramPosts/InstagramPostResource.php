<?php

namespace App\Filament\Resources\InstagramPosts;

use App\Filament\Resources\InstagramPosts\Pages\CreateInstagramPost;
use App\Filament\Resources\InstagramPosts\Pages\EditInstagramPost;
use App\Filament\Resources\InstagramPosts\Pages\ListInstagramPosts;
use App\Models\InstagramPost;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class InstagramPostResource extends Resource
{
    protected static ?string $model = InstagramPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static ?string $navigationLabel = 'Instagram';

    protected static ?string $modelLabel = 'postingan Instagram';

    protected static ?string $pluralModelLabel = 'Postingan Instagram';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('url')
                ->label('Link postingan')
                ->placeholder('https://www.instagram.com/p/XXXXXXXXXXX/')
                ->helperText('Buka postingan di Instagram → ⋯ → Salin tautan, lalu tempel di sini.')
                ->required()
                ->url()
                ->regex(InstagramPost::URL_PATTERN)
                ->validationMessages(['regex' => 'Harus link postingan Instagram (instagram.com/p/… atau /reel/…).'])
                ->columnSpanFull(),

            FileUpload::make('image')
                ->label('Gambar postingan')
                ->helperText('Simpan/screenshot gambar postingan lalu unggah di sini. Rasio persegi (1:1) paling rapi.')
                ->required()
                ->image()
                ->imageCropAspectRatio('1:1')
                ->disk('s3')
                ->directory('instagram')
                ->visibility('public')
                ->maxSize(5120)
                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                    $img = @imagecreatefromstring(file_get_contents($file->getRealPath()));

                    if (! $img) {
                        return $file->store('instagram', 's3');
                    }

                    // Square-crop from the centre and shrink to 600px.
                    $w = imagesx($img);
                    $h = imagesy($img);
                    $side = min($w, $h);
                    $square = imagecreatetruecolor(600, 600);
                    imagecopyresampled($square, $img, 0, 0, (int) (($w - $side) / 2), (int) (($h - $side) / 2), 600, 600, $side, $side);

                    ob_start();
                    imagejpeg($square, null, 75);
                    $binary = ob_get_clean();
                    imagedestroy($img);
                    imagedestroy($square);

                    $filename = 'instagram/'.Str::uuid().'.jpg';

                    if (Storage::disk('s3')->put($filename, $binary) === false) {
                        throw new \RuntimeException('Gagal mengunggah gambar ke storage. Periksa konfigurasi Object Storage (S3).');
                    }

                    return $filename;
                }),

            Textarea::make('caption')
                ->label('Caption singkat')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Opsional. Tampil saat kursor diarahkan ke gambar.'),

            DatePicker::make('posted_at')
                ->label('Tanggal posting')
                ->native(false),

            Toggle::make('is_active')
                ->label('Tampilkan di situs')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image_url')->label('Gambar')->square(),
                TextColumn::make('caption')->label('Caption')->limit(60)->placeholder('—')->wrap(),
                TextColumn::make('posted_at')->label('Tanggal')->date('d M Y')->sortable()->placeholder('—'),
                TextColumn::make('url')->label('Link')->limit(40)->url(fn (InstagramPost $r): string => $r->url, shouldOpenInNewTab: true)->color('info'),
                ToggleColumn::make('is_active')->label('Tampil'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstagramPosts::route('/'),
            'create' => CreateInstagramPost::route('/create'),
            'edit' => EditInstagramPost::route('/{record}/edit'),
        ];
    }
}
