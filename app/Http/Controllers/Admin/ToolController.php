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
        return $this->tool('kuitansi.html');
    }

    /** Receipt in the FCom Nusantara format (blank by default). */
    public function kuitansiFcom(): Response
    {
        return $this->tool('kuitansi-fcom.html');
    }

    private function tool(string $file): Response
    {
        return response(file_get_contents(resource_path('tools/'.$file)))
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'private, no-store')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
