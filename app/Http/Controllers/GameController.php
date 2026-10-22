<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(): View
    {
        return view('frontend.games', [
            // Folder-style listing; the HTML itself is not loaded here, only its size.
            'games' => Game::query()->visible()
                ->select(['id', 'title', 'slug', 'description', 'file_name', 'updated_at'])
                ->selectRaw('LENGTH(html) as html_bytes')
                ->get(),
        ]);
    }

    public function show(Game $game): View
    {
        abort_unless($game->is_active, 404);

        return view('frontend.game-show', [
            'game' => $game,
            'others' => Game::query()->visible()->whereKeyNot($game->id)->take(6)->get(['id', 'title', 'slug', 'image']),
        ]);
    }

    /**
     * Raw game HTML. Served with a CSP sandbox so it runs in an opaque origin
     * even when opened directly: scripts work, but cookies, storage of
     * interg.co.id, the admin session and top-level navigation do not.
     */
    public function play(Game $game): Response
    {
        abort_unless($game->is_active, 404);

        return response(self::withStorageShim((string) $game->html))
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Security-Policy', 'sandbox allow-scripts allow-pointer-lock allow-popups allow-modals')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('Referrer-Policy', 'no-referrer')
            ->header('X-Robots-Tag', 'noindex')
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
