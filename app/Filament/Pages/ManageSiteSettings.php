<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\SocialFeeds;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                            ->helperText('Ditampilkan di bagian kontak & footer. Kosongkan untuk menyembunyikan.'),

                        TextInput::make('marketplace_whatsapp')
                            ->label('WhatsApp Marketplace')
                            ->tel()
                            ->placeholder('0812-5203-2058')
                            ->helperText('Tujuan pesanan keranjang & tombol chat di halaman Marketplace saja. Kosongkan untuk memakai nomor bawaan (0812-5203-2058).'),

                        TextInput::make('website')
                            ->label('Website')
                            ->placeholder('interg.co.id'),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Instagram otomatis')
                    ->description('Isi token agar panel Instagram di website menampilkan postingan terbaru secara otomatis (diperbarui tiap jam). Tanpa token, yang tampil adalah postingan yang diinput manual di menu Instagram.')
                    ->collapsible()
                    ->schema([
                        Placeholder::make('instagram_status')
                            ->label('Status')
                            ->content(function (): string {
                                $setting = SiteSetting::instance();

                                if (blank($setting->instagram_access_token)) {
                                    return 'Belum terhubung — memakai postingan manual.';
                                }

                                return 'Terhubung. Token diperpanjang otomatis'.($setting->instagram_token_refreshed_at ? ' (terakhir '.$setting->instagram_token_refreshed_at->locale('id')->diffForHumans().').' : '.');
                            }),

                        TextInput::make('instagram_access_token')
                            ->label('Access token Instagram')
                            ->password()
                            ->revealable()
                            ->autocomplete('off')
                            ->helperText('Akun Instagram harus Business/Creator. Buat token di Meta for Developers → aplikasi → Instagram API with Instagram Login → Generate token. Kosongkan untuk tidak mengubah token yang tersimpan.'),

                        Toggle::make('instagram_disconnect')
                            ->label('Putuskan Instagram (hapus token)')
                            ->default(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $setting = SiteSetting::instance();

        $disconnect = (bool) ($state['instagram_disconnect'] ?? false);
        $token = trim((string) ($state['instagram_access_token'] ?? ''));
        unset($state['instagram_disconnect'], $state['instagram_access_token']);

        if ($disconnect) {
            SocialFeeds::forget();
            $setting->forceFill(['instagram_access_token' => null, 'instagram_token_refreshed_at' => null]);
        } elseif ($token !== '') {
            SocialFeeds::forget();
            $setting->forceFill(['instagram_access_token' => $token, 'instagram_token_refreshed_at' => null]);
        }

        $setting->fill($state)->save();
        SocialFeeds::forget();

        $this->data['instagram_access_token'] = null;
        $this->data['instagram_disconnect'] = false;

        Notification::make()
            ->success()
            ->title('Pengaturan tersimpan')
            ->send();
    }
}
