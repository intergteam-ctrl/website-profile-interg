<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('idr', true)
                    ->sortable('Harga')
                    ->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.')),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'baru' => 'Baru',
                        'bekas' => 'Bekas',
                        'digital' => 'Digital License',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'baru' => 'success',
                        'bekas' => 'danger',
                        'digital' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
