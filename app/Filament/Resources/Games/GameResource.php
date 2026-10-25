<?php

namespace App\Filament\Resources\Games;

use App\Filament\Resources\Games\Pages\CreateGame;
use App\Filament\Resources\Games\Pages\EditGame;
use App\Filament\Resources\Games\Pages\ListGames;
use App\Filament\Resources\Games\RelationManagers\FilesRelationManager;
use App\Models\Game;
use App\Models\GameFile;
use App\Support\AppStore;
use App\Support\PlayStore;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?string $navigationLabel = 'InterG Studio';

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
                ->prefix('/studio/')
                ->required()
                ->alphaDash()
                ->maxLength(120)
                ->unique(ignoreRecord: true),

            TextInput::make('play_package')
                ->label('Link Google Play')
                ->placeholder('https://play.google.com/store/apps/details?id=com.interg.namaapp')
                ->helperText('Tempel link aplikasi di Google Play. Ikon, screenshot, deskripsi dan rating diambil otomatis dari Google Play (diperbarui tiap 12 jam). Kosongkan untuk game HTML tanpa Google Play.')
                ->formatStateUsing(fn (?string $state): ?string => $state ? PlayStore::url($state) : null)
                ->dehydrateStateUsing(fn (?string $state): ?string => PlayStore::packageFrom($state))
                ->rule(fn () => function (string $attribute, $value, \Closure $fail): void {
                    if (filled($value) && ! PlayStore::packageFrom($value)) {
                        $fail('Harus link aplikasi Google Play (…/store/apps/details?id=…).');
                    }
                })
                ->columnSpanFull(),

            TextInput::make('app_store_id')
                ->label('Link App Store (Apple)')
                ->placeholder('https://apps.apple.com/id/app/nama-app/id1234567890')
                ->helperText('Opsional. Tempel link aplikasi di App Store. Jika diisi, tombol App Store muncul di website; ikon/screenshot/deskripsi diambil dari App Store bila Google Play tidak tersedia.')
                ->formatStateUsing(fn (?string $state): ?string => $state ? AppStore::url($state) : null)
                ->dehydrateStateUsing(fn (?string $state): ?string => AppStore::idFrom($state))
                ->rule(fn () => function (string $attribute, $value, \Closure $fail): void {
                    if (filled($value) && ! AppStore::idFrom($value)) {
                        $fail('Harus link aplikasi App Store (apps.apple.com/…/id1234567890).');
                    }
                })
                ->columnSpanFull(),

            FileUpload::make('html_files')
                ->label('File HTML')
                ->helperText('Bisa pilih beberapa file sekaligus. Nama file menjadi alamatnya: privacy.html → /studio/{folder}/privacy. File dengan nama yang sama akan menimpa file lama. Maks. 8 MB per file.')
                ->multiple()
                ->acceptedFileTypes(['text/html', '.html', '.htm'])
                ->maxSize(Game::MAX_KB)
                ->storeFiles(false)
                ->rule(fn () => function (string $attribute, $value, \Closure $fail): void {
                    foreach ((array) $value as $file) {
                        if ($file instanceof TemporaryUploadedFile && ! in_array(strtolower($file->getClientOriginalExtension()), ['html', 'htm'], true)) {
                            $fail('Semua file harus berformat .html atau .htm.');

                            return;
                        }
                    }
                })
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3)
                ->maxLength(1000)
                ->columnSpanFull(),

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
                TextColumn::make('play_package')->label('Google Play')->placeholder('—')->color('gray')->limit(32),
                TextColumn::make('app_store_id')->label('App Store')->placeholder('—')->color('gray'),
                TextColumn::make('slug')->label('Folder')->searchable()->weight('bold')->prefix('/studio/'),
                TextColumn::make('title')->label('Judul')->searchable(),
                TextColumn::make('files_count')->label('File')->counts('files')->suffix(' file'),
                ToggleColumn::make('is_active')->label('Tampil'),
                TextColumn::make('updated_at')->label('Diubah')->since()->sortable(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Buka folder')
                    ->icon(Heroicon::OutlinedFolderOpen)
                    ->url(fn (Game $record): string => route('games.show', $record), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    /**
     * Saves uploaded HTML files into the folder (kept in the database:
     * Laravel Cloud Object Storage deletes .html files). Same name = replace.
     *
     * @param  array<int|string, mixed>|null  $files
     */
    public static function storeUploadedFiles(Game $game, ?array $files): void
    {
        foreach ((array) $files as $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                continue;
            }

            GameFile::query()->updateOrCreate(
                ['game_id' => $game->id, 'name' => GameFile::nameFromFilename($file->getClientOriginalName())],
                ['html' => (string) $file->get()],
            );
        }

        $game->touch();
    }

    public static function getRelations(): array
    {
        return [FilesRelationManager::class];
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
