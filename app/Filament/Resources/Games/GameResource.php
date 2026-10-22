<?php

namespace App\Filament\Resources\Games;

use App\Filament\Resources\Games\Pages\CreateGame;
use App\Filament\Resources\Games\Pages\EditGame;
use App\Filament\Resources\Games\Pages\ListGames;
use App\Models\Game;
use App\Support\ImageUploader;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?string $navigationLabel = 'Games';

    protected static ?string $modelLabel = 'game';

    protected static ?string $pluralModelLabel = 'Games';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Judul game')
                ->required()
                ->maxLength(120)
                ->live(onBlur: true)
                ->afterStateUpdated(function (?string $state, Set $set, string $operation): void {
                    if ($operation === 'create') {
                        $set('slug', Str::slug((string) $state));
                    }
                }),

            TextInput::make('slug')
                ->label('Alamat (slug)')
                ->prefix('/games/')
                ->required()
                ->alphaDash()
                ->maxLength(120)
                ->unique(ignoreRecord: true),

            FileUpload::make('html_file')
                ->label('File game (.html)')
                ->helperText('Satu file HTML yang berisi semua kode game (CSS/JS/gambar di dalam file atau dari CDN). Maks. 8 MB. Saat edit, kosongkan untuk tetap memakai file lama.')
                ->acceptedFileTypes(['text/html', '.html', '.htm'])
                ->maxSize(Game::MAX_KB)
                ->storeFiles(false)
                ->required(fn (string $operation): bool => $operation === 'create')
                ->rule(fn () => function (string $attribute, $value, \Closure $fail): void {
                    if ($value instanceof TemporaryUploadedFile && ! in_array(strtolower($value->getClientOriginalExtension()), ['html', 'htm'], true)) {
                        $fail('File harus berformat .html atau .htm.');
                    }
                })
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('Deskripsi / cara bermain')
                ->rows(3)
                ->maxLength(1000)
                ->columnSpanFull(),

            FileUpload::make('image')
                ->label('Gambar sampul (opsional)')
                ->helperText('Rasio 16:9 paling rapi. Butuh Object Storage terpasang.')
                ->image()
                ->disk(ImageUploader::disk())
                ->directory('games')
                // Preview via signed URL: works for public and private buckets alike.
                ->visibility('private')
                ->maxSize(5120)
                ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file, BaseFileUpload $component): string => ImageUploader::store($file, 'games', $component, maxWidth: 800, quality: 75)),

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
                ImageColumn::make('image_url')->label('Sampul'),
                TextColumn::make('title')->label('Judul')->searchable()->weight('bold'),
                TextColumn::make('file_name')->label('File')->placeholder('—')->color('gray'),
                TextColumn::make('size_label')->label('Ukuran'),
                ToggleColumn::make('is_active')->label('Tampil'),
                TextColumn::make('updated_at')->label('Diubah')->since()->sortable(),
            ])
            ->recordActions([
                Action::make('play')
                    ->label('Main')
                    ->icon(Heroicon::OutlinedPlay)
                    ->url(fn (Game $record): string => route('games.show', $record), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    /**
     * Turns the uploaded file (kept in memory, not on disk) into the stored
     * HTML. Laravel Cloud Object Storage refuses .html files, so the game
     * lives in the database.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function absorbHtmlUpload(array $data): array
    {
        $file = $data['html_file'] ?? null;
        $file = is_array($file) ? reset($file) : $file;
        unset($data['html_file']);

        if ($file instanceof TemporaryUploadedFile) {
            $data['html'] = (string) $file->get();
            $data['file_name'] = Str::limit($file->getClientOriginalName(), 180, '');
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGames::route('/'),
            'create' => CreateGame::route('/create'),
            'edit' => EditGame::route('/{record}/edit'),
        ];
    }
}
