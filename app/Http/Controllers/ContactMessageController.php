<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: real visitors never see or fill this field. Bots usually do.
        // Pretend success so the bot gets no signal, but store nothing.
        // The form sends the page language so the reply matches it.
        $locale = in_array($request->input('locale'), Locale::prefixed(), true) ? $request->input('locale') : Locale::DEFAULT;
        app()->setLocale($locale);

        if (filled($request->input('website_url'))) {
            return $this->success($locale);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'company' => ['nullable', 'string', 'max:190'],
            'service' => ['nullable', 'string', Rule::in(ContactMessage::SERVICES)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [], [
            'name' => __('nama'),
            'email' => 'email',
            'company' => __('perusahaan'),
            'service' => __('layanan'),
            'message' => __('pesan'),
        ]);

        ContactMessage::create($data + [
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ]);

        return $this->success($locale);
    }

    private function success(string $locale): RedirectResponse
    {
        $home = $locale === Locale::DEFAULT ? '/' : '/'.$locale;

        return redirect()
            ->to($home.'#contact')
            ->with('contact_success', __('Terima kasih! Pesan Anda sudah kami terima dan tim kami akan segera menghubungi Anda.'));
    }
}
