<?php

namespace App\Filament\Resources\Portfolios\Tables;

use App\Models\Portfolio;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PortfoliosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Foto'),
                TextColumn::make('title')
                    ->label('Judul')
                    ->description(fn (Portfolio $record): ?string => $record->subtitle)
                    ->searchable(),
                TextColumn::make('group')
                    ->label('Tampil di')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Portfolio::GROUPS[$state] ?? $state)
                    ->color(fn (string $state): string => $state === 'other' ? 'gray' : 'success')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('group')
                    ->label('Tampil di')
                    ->options(Portfolio::GROUPS),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
