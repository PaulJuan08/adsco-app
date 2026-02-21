<?php
// app/Http/Controllers/Auth/VerificationController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show()
    {
        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request, $encryptedId, $hash)
    {
        try {
            // Decrypt the user ID
            $id = Crypt::decrypt($encryptedId);
            
            // Find the user
            $user = User::findOrFail($id);
            
            // Check if the hash matches
            if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
                return redirect()->route('login')
                    ->with('error', 'Invalid verification link.');
            }
            
            // Check if already verified
            if ($user->hasVerifiedEmail()) {
                return redirect()->route('dashboard')
                    ->with('info', 'Email already verified.');
            }
            
            // Mark as verified
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
            
            return redirect()->route('dashboard')
                ->with('success', 'Email verified successfully! Your account is now pending admin approval.');
                
        } catch (\Exception $e) {
            \Log::error('Verification failed: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Invalid or expired verification link.');
        }
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('success', 'Verification link sent! Please check your email.');
        } catch (\Exception $e) {
            \Log::error('Resend verification failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send verification email. Please try again.');
        }
    }
}