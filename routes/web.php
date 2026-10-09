<?php

use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\FrontendController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

// Frontend
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [FrontendController::class, 'blogShow'])->name('blog.show');
Route::get('/portfolio', [FrontendController::class, 'portfolio'])->name('portfolio');
Route::get('/marketplace', [FrontendController::class, 'marketplace'])->name('marketplace');

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
});
