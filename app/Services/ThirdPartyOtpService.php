<?php

namespace App\Services;

class ThirdPartyOtpService
{

    public function send(string $to, string $message): bool
    {

        \Log::info("Sending OTP to {$to}: {$message}");
        return true;
    }
}
