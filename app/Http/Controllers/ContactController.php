<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show()
    {
        return view('welcome');
    }

    public function send(Request $request)
    {
        // Verify Turnstile
        $turnstileToken = $request->input('cf-turnstile-response');
        $turnstileResult = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => config('services.turnstile.secret_key'),
            'response' => $turnstileToken ?? '',
            'remoteip' => $request->ip(),
        ]);
        if (!$turnstileResult->json('success', false)) {
            return back()->withErrors(['message' => 'Security verification failed. Please try again.'])->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ]);

        Mail::to('elishaphatpauljuan@gmail.com')
            ->send(new ContactMessageMail($validated));

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
