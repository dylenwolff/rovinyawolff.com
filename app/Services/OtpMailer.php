<?php

namespace App\Services;

class OtpMailer
{
    public function __construct(private readonly NativeMailService $mailer) {}

    public function send(string $email, string $code): void
    {
        $body = implode("\n", [
            "Your Rovinya Wolff Studio admin sign-in code is {$code}.",
            '',
            'This code expires in 10 minutes.',
            'If you did not request it, you can ignore this email.',
        ]);

        $this->mailer->send($email, 'Your admin sign-in code', $body);
    }
}
