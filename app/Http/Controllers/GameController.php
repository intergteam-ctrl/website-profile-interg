<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GameController extends Controller
{
    /** /games — every game shown as a folder. */
    public function index(): View
    {
        $games = Game::query()->visible()->withCount('files')->get();

        // Folder sizes without loading the HTML itself.
        $sizes = DB::table('game_files')->whereIn('game_id', $games->pluck('id'))
            ->groupBy('game_id')->selectRaw('game_id, SUM(LENGTH(html)) as bytes, MAX(updated_at) as changed')
            ->get()->keyBy('game_id');

        return view('frontend.games', compact('games', 'sizes'));
    }

    /** /games/{folder} — the files inside one folder. */
    public function show(Game $game): View
    {
        abort_unless($game->is_active, 404);

        $files = $game->files()->select(['id', 'game_id', 'name', 'updated_at'])->selectRaw('LENGTH(html) as bytes')->get();

        return view('frontend.game-folder', compact('game', 'files'));
    }

    /**
     * /games/{folder}/{file} — raw HTML (".html" suffix optional, so relative
     * links like href="terms.html" between files keep working).
     *
     * Served with a CSP sandbox: it runs in an opaque origin, so scripts work
     * but cookies, storage of this site and the admin session are out of reach.
     */
    public function file(Game $game, string $file): Response|RedirectResponse
    {
        abort_unless($game->is_active, 404);

        $name = preg_replace('/\.html?$/i', '', $file);
        $record = $game->files()->where('name', strtolower($name))->first();

        if (! $record) {
            // Old links (/games/{folder}/play) → the folder's first file.
            $first = $file === 'play' ? $game->files()->first(['id', 'game_id', 'name']) : null;
            abort_unless($first, 404);

            return redirect()->route('games.file', [$game, $first->name], 301);
        }

        return response(self::withStorageShim((string) $record->html))
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Security-Policy', 'sandbox allow-scripts allow-pointer-lock allow-popups allow-modals')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('Referrer-Policy', 'no-referrer')
            ->header('Cache-Control', 'public, max-age=300');
    }

    /**
     * In the sandbox, touching localStorage/sessionStorage throws, which makes
     * many games crash on load (high scores, settings). Give them an in-memory
     * replacement instead, inserted as early as possible in the document.
     */
    private static function withStorageShim(string $html): string
    {
        $shim = '<script>(function(){function S(){var d={};return{getItem:function(k){k=String(k);return Object.prototype.hasOwnProperty.call(d,k)?d[k]:null},setItem:function(k,v){d[String(k)]=String(v)},removeItem:function(k){delete d[String(k)]},clear:function(){d={}},key:function(i){return Object.keys(d)[i]||null},get length(){return Object.keys(d).length}}}'
            .'["localStorage","sessionStorage"].forEach(function(n){try{window[n].length}catch(e){try{Object.defineProperty(window,n,{value:S(),configurable:true})}catch(_){}}})})();</script>';

        foreach (['/<head\b[^>]*>/i', '/<html\b[^>]*>/i'] as $pattern) {
            if (preg_match($pattern, $html, $m, PREG_OFFSET_CAPTURE)) {
                $at = $m[0][1] + strlen($m[0][0]);

                return substr($html, 0, $at).$shim.substr($html, $at);
            }
        }

        // No <html>/<head>: keep any doctype first so the page is not in quirks mode.
        if (preg_match('/^\s*<!doctype[^>]*>/i', $html, $m)) {
            return $m[0].$shim.substr($html, strlen($m[0]));
        }

        return $shim.$html;
    }
}
