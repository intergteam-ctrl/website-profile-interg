<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\SocialFeeds;
use App\Support\Socials;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Admin → Media Sosial: every social media / app store link of the company.
 * Empty field = hidden on the website.
 */
class ManageSocials extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static ?string $title = 'Media Sosial';

    protected static ?string $navigationLabel = 'Media Sosial';

    protected string $view = 'filament.pages.manage-site-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $setting = SiteSetting::instance();
        $data = is_array($setting->socials) ? $setting->socials : (array) config('company.socials', []);

        $this->form->fill([
            ...array_intersect_key($data, Socials::platforms()),
            'others' => $data['others'] ?? [],
            'youtube_channel_id' => is_array($setting->socials) ? $setting->youtube_channel_id : config('company.youtube.channel_id'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $fields = [];
        foreach (Socials::platforms() as $key => $p) {
            $fields[] = TextInput::make($key)
                ->label($p['label'])
                ->placeholder($p['placeholder'])
                ->url()
                ->maxLength(500)
                ->regex('#^https?://#i')
                ->validationMessages(['regex' => 'Link harus diawali https://']);
        }

        return $schema
            ->components([
                Section::make('Akun media sosial & toko aplikasi')
                    ->description('Tempel link lengkap profil. Kosongkan untuk menyembunyikan. Instagram, YouTube dan TikTok tampil sebagai tab di sisi kanan website; semua yang terisi tampil sebagai ikon di footer.')
                    ->columns(2)
                    ->schema($fields),

                Section::make('Video YouTube')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('youtube_channel_id')
                            ->label('YouTube Channel ID')
                            ->placeholder('UCxxxxxxxxxxxxxxxxxxxxxx')
                            ->regex('/^UC[\w-]{22}$/')
                            ->validationMessages(['regex' => 'Channel ID diawali "UC" dan panjangnya 24 karakter.'])
                            ->helperText('Dipakai untuk menampilkan video terbaru. Terisi otomatis saat link YouTube disimpan; isi manual hanya jika otomatis gagal (YouTube Studio → Pengaturan → Channel → Setelan lanjutan).'),
                    ]),

                Section::make('Link lainnya')
                    ->description('Platform yang tidak ada di daftar di atas (mis. marketplace, portal mitra). Tampil sebagai teks di footer.')
                    ->collapsible()
                    ->schema([
                        Repeater::make('others')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('label')->label('Nama')->required()->maxLength(40)->placeholder('Tokopedia'),
                                TextInput::make('url')->label('Link')->required()->url()->maxLength(500)->regex('#^https?://#i')->placeholder('https://…'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah link')
                            ->reorderable(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $setting = SiteSetting::instance();

        $socials = ['others' => array_values($state['others'] ?? [])];
        foreach (array_keys(Socials::platforms()) as $key) {
            $socials[$key] = filled($state[$key] ?? null) ? trim($state[$key]) : null;
        }

        $old = is_array($setting->socials) ? ($setting->socials['youtube'] ?? null) : config('company.socials.youtube');
        $channelId = $state['youtube_channel_id'] ?? null;

        // New YouTube link (or no ID yet): look up the channel ID automatically.
        if ($socials['youtube'] && ($socials['youtube'] !== $old || blank($channelId))) {
            $channelId = Socials::resolveYoutubeChannelId($socials['youtube']) ?? ($socials['youtube'] === $old ? $channelId : null);
        }
        if (! $socials['youtube']) {
            $channelId = null;
        }

        SocialFeeds::forget();
        $setting->forceFill(['socials' => $socials, 'youtube_channel_id' => $channelId])->save();
        Socials::flush();
        SocialFeeds::forget();

        $this->data['youtube_channel_id'] = $channelId;

        $note = $socials['youtube'] && ! $channelId ? ' Channel ID YouTube tidak ditemukan otomatis — isi manual agar video tampil.' : '';

        Notification::make()
            ->success()
            ->title('Media sosial tersimpan')
            ->body('Perubahan langsung tampil di website.'.$note)
            ->send();
    }
}
