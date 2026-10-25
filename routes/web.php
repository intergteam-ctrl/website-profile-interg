<?php

use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\GameController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use Illuminate\Support\Facades\Route;

// Frontend — registered once per language: Indonesian at /, English at
// /en and Mandarin at /zh (route names prefixed "en." / "zh.").
$frontend = function (): void {
    Route::get('/', [FrontendController::class, 'home'])->name('home');
    Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
    Route::get('/blog/{slug}', [FrontendController::class, 'blogShow'])->name('blog.show');
    Route::get('/portfolio', [FrontendController::class, 'portfolio'])->name('portfolio');
    Route::get('/marketplace', [FrontendController::class, 'marketplace'])->name('marketplace');
    Route::get('/download', [FrontendController::class, 'downloads'])->name('downloads');
    Route::get('/studio', [GameController::class, 'index'])->name('games');
    Route::get('/studio/{game}', [GameController::class, 'show'])->name('games.show');
    Route::get('/studio/{game}/{file}', [GameController::class, 'file'])
        ->where('file', '[A-Za-z0-9._-]+')
        ->name('games.file');
};

Route::middleware(SetLocale::class.':'.Locale::DEFAULT)->group($frontend);

// "Games" was renamed to InterG Studio (/studio). Old links — including
// game files and app privacy pages under /games/… — keep working (301).
Route::get('{locale}/games/{path?}', fn (string $locale, string $path = '') => redirect('/'.$locale.'/studio'.($path !== '' ? '/'.$path : '').(request()->getQueryString() ? '?'.request()->getQueryString() : ''), 301))
    ->whereIn('locale', Locale::prefixed())->where('path', '.*');
Route::get('games/{path?}', fn (string $path = '') => redirect('/studio'.($path !== '' ? '/'.$path : '').(request()->getQueryString() ? '?'.request()->getQueryString() : ''), 301))
    ->where('path', '.*');
foreach (Locale::prefixed() as $locale) {
    Route::prefix($locale)->name($locale.'.')->middleware(SetLocale::class.':'.$locale)->group($frontend);
}

// Contact form submission (rate-limited: 5 per minute per IP).
Route::post('/contact', [ContactMessageController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

// About / Services / Contact live as sections on the home page. These routes
// previously pointed at controller methods that did not exist (HTTP 500), so
// they now redirect to the matching section to keep any old links working.
// GET-only on purpose: Route::redirect() registers ANY, which would override POST /contact.
Route::get('/about', fn () => redirect('/#about', 301))->name('about');
Route::get('/services', fn () => redirect('/#services', 301))->name('services');
Route::get('/contact', fn () => redirect('/#contact', 301))->name('contact');

// The old static "editor" dashboard was publicly reachable without login.
// All content management now happens in the authenticated Filament panel.
Route::get('/editor/dashboard', fn () => redirect('/admin', 301))->name('editor.dashboard');
Route::get('/Editor/dashboard', fn () => redirect('/admin', 301));

// Internal tools — admin login required (same accounts as /admin).
Route::middleware(EnsureAdmin::class)->prefix('admin/tools')->name('admin.tools.')->group(function () {
    Route::get('/kuitansi', [ToolController::class, 'kuitansi'])->name('kuitansi');
    Route::get('/kuitansi-fcom', [ToolController::class, 'kuitansiFcom'])->name('kuitansi-fcom');
});

// Static pages uploaded to public/{folder}/{name}.html (e.g. app privacy
// policies) are served by the web server at /{folder}/{name}.html. This
// fallback also makes them reachable without ".html": /{folder}/{name}.
// It only runs when no other route matched, so it cannot shadow anything.
Route::fallback(function (string $fallbackPlaceholder = '') {
    if (preg_match('#^([a-z0-9][a-z0-9_-]*)/([a-z0-9][a-z0-9_-]*)$#i', trim($fallbackPlaceholder, '/'), $m)) {
        $file = public_path($m[1].'/'.$m[2].'.html');

        if (is_file($file)) {
            return response(file_get_contents($file))
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Cache-Control', 'public, max-age=300');
        }
    }

    abort(404);
});
