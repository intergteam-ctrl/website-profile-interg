<?php

namespace App\Filament\Resources\Downloads;

use App\Filament\Resources\Downloads\Pages\CreateDownload;
use App\Filament\Resources\Downloads\Pages\EditDownload;
use App\Filament\Resources\Downloads\Pages\ListDownloads;
use App\Models\Download;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static ?string $navigationLabel = 'Download';

    protected static ?string $modelLabel = 'file download';

    protected static ?string $pluralModelLabel = 'Download';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Judul')
                ->placeholder('Company Profile Inter G 2025')
                ->required()
                ->maxLength(150),

            TextInput::make('category')
                ->label('Kategori')
                ->placeholder('Brosur, Software, Panduan …')
                ->helperText('Opsional. Item dengan kategori sama dikelompokkan di halaman Download.')
                ->maxLength(60)
                ->datalist(fn () => Download::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all()),

            TextInput::make('url')
                ->label('Link / URL download')
                ->placeholder('https://drive.google.com/file/d/…')
                ->helperText('Tempel link Google Drive, OneDrive, Dropbox, atau link file langsung. Pastikan aksesnya "Siapa saja yang memiliki link".')
                ->required()
                ->url()
                ->maxLength(2048)
                ->regex(Download::URL_PATTERN)
                ->validationMessages(['regex' => 'Link harus diawali http:// atau https://'])
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(4)
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
                TextColumn::make('title')->label('Judul')->searchable()->weight('bold')->wrap(),
                TextColumn::make('category')->label('Kategori')->badge()->placeholder('—')->sortable(),
                TextColumn::make('url')->label('Link')->limit(40)->url(fn (Download $r): string => $r->safe_url, shouldOpenInNewTab: true)->color('info'),
                ToggleColumn::make('is_active')->label('Tampil'),
                TextColumn::make('updated_at')->label('Diubah')->since()->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloads::route('/'),
            'create' => CreateDownload::route('/create'),
            'edit' => EditDownload::route('/{record}/edit'),
        ];
    }
}
