<?php

namespace App\Filament\Resources\Games\RelationManagers;

use App\Models\GameFile;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Files inside a game folder, listed under the Edit page. */
class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    protected static ?string $title = 'Isi folder';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->select(['id', 'game_id', 'name', 'updated_at'])->selectRaw('LENGTH(html) as bytes'))
            ->recordTitleAttribute('name')
            ->emptyStateHeading('Folder masih kosong')
            ->emptyStateDescription('Upload file HTML lewat kolom "File HTML" di atas, lalu Simpan.')
            ->columns([
                TextColumn::make('name')->label('Nama file')->formatStateUsing(fn (string $state): string => $state.'.html')->weight('bold')->searchable(),
                TextColumn::make('link')->label('Alamat')
                    ->state(fn (GameFile $record): string => route('games.file', [$this->getOwnerRecord(), $record->name]))
                    ->url(fn (GameFile $record): string => route('games.file', [$this->getOwnerRecord(), $record->name]), shouldOpenInNewTab: true)
                    ->color('info')->copyable(),
                TextColumn::make('bytes')->label('Ukuran')->formatStateUsing(fn ($state): string => GameFile::formatBytes((int) $state)),
                TextColumn::make('updated_at')->label('Diubah')->since(),
            ])
            ->recordActions([
                Action::make('open')->label('Buka')->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (GameFile $record): string => route('games.file', [$this->getOwnerRecord(), $record->name]), shouldOpenInNewTab: true),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
