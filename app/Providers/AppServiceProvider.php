<?php

namespace App\Providers;

use App\Models\InstagramPost;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Every public page needs the site settings and resolved contact
        // details (DB value first, config/company.php as fallback). Resolve
        // them once per request and share with the public views only.
        View::composer(['layout.app', 'frontend.*', 'partials.*'], function ($view): void {
            $view->with('setting', $this->siteSetting())
                ->with('site', $this->siteContact());
        });

        View::composer('layout.app', function ($view): void {
            $view->with('instagramPosts', $this->instagramPosts());
        });
    }

    /**
     * @return Collection<int, InstagramPost>
     */
    private function instagramPosts(): Collection
    {
        return once(function (): Collection {
            try {
                return InstagramPost::query()->visible()->take((int) config('company.instagram.limit', 9))->get();
            } catch (\Throwable) {
                return collect();
            }
        });
    }

    private function siteSetting(): ?SiteSetting
    {
        return once(function (): ?SiteSetting {
            try {
                return SiteSetting::query()->first();
            } catch (\Throwable) {
                // Table missing (fresh install before migrate) — fall back to defaults.
                return null;
            }
        });
    }

    /**
     * @return array<string, string|null>
     */
    private function siteContact(): array
    {
        return once(function (): array {
            $setting = $this->siteSetting();
            $whatsapp = $setting?->whatsapp ?: config('company.whatsapp');

            $digits = preg_replace('/\D+/', '', (string) $whatsapp);
            if (str_starts_with($digits, '0')) {
                $digits = '62'.substr($digits, 1);
            }

            return [
                'phone' => $setting?->phone ?: config('company.phone'),
                'whatsapp' => $whatsapp,
                'whatsapp_number' => $digits,
                'whatsapp_link' => $digits ? 'https://wa.me/'.$digits : null,
                'email' => config('company.email'),
                'website' => $setting?->website ?: config('company.website'),
                'address' => $setting?->address ?: config('company.address'),
            ];
        });
    }
}
