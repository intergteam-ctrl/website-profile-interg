<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $navigationLabel = 'Pesan Masuk';

    protected static ?string $modelLabel = 'pesan';

    protected static ?string $pluralModelLabel = 'Pesan Masuk';

    protected static ?int $navigationSort = -1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::query()->unread()->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pengirim')
                ->columns(2)
                ->components([
                    TextEntry::make('name')->label('Nama'),
                    TextEntry::make('email')
                        ->label('Email')
                        ->copyable()
                        ->url(fn (ContactMessage $record): string => 'mailto:'.$record->email),
                    TextEntry::make('company')->label('Perusahaan / Instansi')->placeholder('—'),
                    TextEntry::make('service')->label('Layanan')->badge()->placeholder('—'),
                    TextEntry::make('created_at')->label('Diterima')->dateTime('d M Y, H:i'),
                    TextEntry::make('ip_address')->label('IP')->placeholder('—'),
                ]),
            Section::make('Pesan')
                ->components([
                    TextEntry::make('message')
                        ->hiddenLabel()
                        ->prose()
                        ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                        ->html(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (ContactMessage $record): ?string => $record->read_at ? null : 'font-semibold')
            ->columns([
                IconColumn::make('read_at')
                    ->label('')
                    ->state(fn (ContactMessage $record): bool => $record->read_at === null)
                    ->boolean()
                    ->trueIcon(Heroicon::Envelope)
                    ->falseIcon(Heroicon::OutlinedEnvelopeOpen)
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->tooltip(fn (ContactMessage $record): string => $record->read_at ? 'Sudah dibaca' : 'Belum dibaca'),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('company')->label('Perusahaan')->searchable()->toggleable()->placeholder('—'),
                TextColumn::make('service')->label('Layanan')->badge()->toggleable()->placeholder('—'),
                TextColumn::make('message')->label('Pesan')->limit(60)->wrap()->toggleable(),
                TextColumn::make('created_at')->label('Diterima')->since()->sortable()
                    ->tooltip(fn (ContactMessage $record): string => $record->created_at->format('d M Y H:i')),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Status')
                    ->nullable()
                    ->trueLabel('Sudah dibaca')
                    ->falseLabel('Belum dibaca'),
                SelectFilter::make('service')
                    ->label('Layanan')
                    ->options(array_combine(ContactMessage::SERVICES, ContactMessage::SERVICES)),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('toggleRead')
                    ->label(fn (ContactMessage $record): string => $record->read_at ? 'Tandai belum dibaca' : 'Tandai dibaca')
                    ->icon(fn (ContactMessage $record) => $record->read_at ? Heroicon::OutlinedEnvelope : Heroicon::OutlinedEnvelopeOpen)
                    ->color('gray')
                    ->action(fn (ContactMessage $record) => $record->update(['read_at' => $record->read_at ? null : now()])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
