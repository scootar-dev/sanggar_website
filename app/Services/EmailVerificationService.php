<?php

namespace App\Services;

use App\Mail\OtpRegistrationMail;
use App\Models\User;
use Illuminate\Support\Facades\{Cache, Mail, Log};

class EmailVerificationService
{
    public function changed(User $user): bool
    {
        $user->forceFill(['email_verified_at' => null, 'google_id' => null])->save();
        $user->tokens()->delete();
        $otp = random_int(100000, 999999);
        Cache::put('otp_register_'.$user->id, $otp, now()->addMinutes(10));
        try {
            Mail::to($user->email)->send(new OtpRegistrationMail($otp));
            return true;
        } catch (\Throwable $e) {
            Log::warning('security.email_verification_delivery_failed', ['user_id' => $user->id]);
            return false;
        }
    }
}
