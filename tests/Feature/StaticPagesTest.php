<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    public function test_static_html_pages_in_public_folders_have_an_html_extension(): void
    {
        // A file without ".html" is sent as a download instead of a web page.
        foreach (glob(public_path('*/*')) as $path) {
            if (is_file($path) && preg_match('/<html|<!doctype html/i', (string) file_get_contents($path, length: 512))) {
                $this->assertMatchesRegularExpression('/\.html?$/i', $path, basename(dirname($path)).'/'.basename($path).' must end in .html');
            }
        }
    }

    public function test_privacy_pages_open_with_and_without_extension(): void
    {
        $this->get('/fruits-bomb/privacy')->assertOk()->assertHeader('Content-Type', 'text/html; charset=UTF-8')->assertSee('Fruits Bomb');
        $this->get('/juragan-dapur-gizi/privacy')->assertOk();
        $this->get('/fruits-bomb/nope')->assertNotFound();
        $this->get('/fruits-bomb/..%2F..%2F.env')->assertNotFound();
        $this->get('/admin/login')->assertOk();
    }
}
