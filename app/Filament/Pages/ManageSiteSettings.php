<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::instance()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hero')
                    ->description('Bagian paling atas halaman utama.')
                    ->components([
                        TextInput::make('hero_tag')
                            ->label('Tagline Kecil')
                            ->placeholder('Total IT Solution Provider'),

                        Textarea::make('hero_title')
                            ->label('Judul Utama')
                            ->rows(3)
                            ->helperText('Gunakan Enter untuk pindah baris. Kosongkan untuk memakai teks bawaan.'),

                        Textarea::make('hero_subtitle')
                            ->label('Sub-judul')
                            ->rows(3),
                    ]),

                Section::make('Tentang Kami')
                    ->components([
                        Textarea::make('about_text')
                            ->label('Teks About')
                            ->rows(5),
                    ]),

                Section::make('Layanan')
                    ->components([
                        Textarea::make('services_intro')
                            ->label('Intro Layanan')
                            ->rows(3),
                    ]),

                Section::make('Kontak')
                    ->columns(2)
                    ->components([
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->placeholder('+62 341 400 272'),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->placeholder('0812-3356-956')
                            ->helperText('Otomatis dikonversi ke format wa.me di frontend.'),

                        TextInput::make('website')
                            ->label('Website')
                            ->placeholder('interg.co.id'),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        SiteSetting::instance()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Pengaturan tersimpan')
            ->send();
    }
}
