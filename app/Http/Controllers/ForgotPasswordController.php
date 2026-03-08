<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Http;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Verify Turnstile
        $turnstileToken = $request->input('cf-turnstile-response');
        if (empty($turnstileToken)) {
            return back()->withErrors(['email' => 'Security verification failed. Please try again.']);
        }
        $turnstileResult = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => config('services.turnstile.secret_key'),
            'response' => $turnstileToken,
            'remoteip' => $request->ip(),
        ]);
        if (!$turnstileResult->json('success', false)) {
            return back()->withErrors(['email' => 'Security verification failed. Please try again.']);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'A password reset link has been sent to your email address.');
        }

        return back()->withErrors(['email' => 'We could not find a user with that email address.']);
    }
}
