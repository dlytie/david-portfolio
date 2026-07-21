<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function store(ContactRequest $request): RedirectResponse
    {
        $recipient = config('portfolio.contact.email');

        if (! config('portfolio.contact.enabled') || blank($recipient)) {
            return back()
                ->withInput()
                ->withErrors(['contact' => 'Form kontak belum diaktifkan. Silakan gunakan tautan kontak yang tersedia.']);
        }

        $messageData = collect($request->validated())
            ->except('website')
            ->all();

        try {
            Mail::to($recipient)->send(new ContactMessageMail($messageData));
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors(['contact' => 'Pesan belum berhasil dikirim. Silakan coba kembali beberapa saat lagi.']);
        }

        return back()->with('contact_success', 'Pesan berhasil dikirim. Terima kasih sudah menghubungi saya.');
    }
}
