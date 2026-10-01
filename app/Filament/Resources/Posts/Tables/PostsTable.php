<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('s3'),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('category')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('published_at')
                    ->label('Publish')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d M Y') : 'Draft')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->placeholder('Draft'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                //
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
