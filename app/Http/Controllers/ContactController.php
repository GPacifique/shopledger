<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Place at app/Http/Controllers/ContactController.php
 */
class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request): RedirectResponse
    {
        // Honeypot: real visitors never see or fill this field. Bots do.
        // Pretend it worked so the bot learns nothing.
        if (filled($request->input('website'))) {
            return back()->with('status', __('Thanks, your message has been sent.'));
        }

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email:rfc', 'max:190'],
            'phone'   => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,30}$/'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        // Set CONTACT_EMAIL in .env (see config/mail.php note). Falls back to the "from" address.
        $to = config('mail.contact_address') ?: config('mail.from.address');

        $body = "New contact message\n\n"
            . "Name: {$data['name']}\n"
            . "Email: {$data['email']}\n"
            . 'Phone: ' . ($data['phone'] ?? 'not given') . "\n\n"
            . $data['message'] . "\n";

        try {
            Mail::raw($body, function ($mail) use ($to, $data) {
                $mail->to($to)
                    ->replyTo($data['email'], $data['name'])
                    ->subject('Contact form: ' . $data['name']);
            });
        } catch (\Throwable $e) {
            Log::error('Contact form email failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['message' => __('We could not send your message right now. Please try again in a few minutes.')]);
        }

        return back()->with('status', __('Thanks, your message has been sent. We will reply by email.'));
    }
}