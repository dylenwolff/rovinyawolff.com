<?php

namespace App\Services;

use RuntimeException;

class OtpMailer
{
    public function send(string $email, string $code): void
    {
        $body = implode("\n", [
            "Your Rovinya Wolff Studio admin sign-in code is {$code}.",
            '',
            'This code expires in 10 minutes.',
            'If you did not request it, you can ignore this email.',
        ]);

        $headers = implode("\r\n", [
            'From: Rovinya Wolff Studio <hello@rovinyawolff.com>',
            'Reply-To: hello@rovinyawolff.com',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ]);

        if (! mail($email, 'Your admin sign-in code', $body, $headers, '-fhello@rovinyawolff.com')) {
            throw new RuntimeException('The native mail transport rejected the OTP message.');
        }
    }
}
