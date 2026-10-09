<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class ToolController extends Controller
{
    /**
     * Receipt (kuitansi) generator. The HTML lives in resources/ — not
     * public/ — so it can only be reached through this authenticated route.
     */
    public function kuitansi(): Response
    {
        return response(file_get_contents(resource_path('tools/kuitansi.html')))
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'private, no-store')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
