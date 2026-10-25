<?php

namespace App\Services;

use RuntimeException;

class NativeMailService
{
    public function send(
        string $recipientEmail,
        string $subject,
        string $body,
        ?string $recipientName = null,
    ): string {
        $fromAddress = (string) config('mail.from.address', 'hello@rovinyawolff.com');
        $fromName = (string) config('mail.from.name', 'Rovinya Wolff Studio');
        $messageId = sprintf('<%s@rovinyawolff.com>', bin2hex(random_bytes(16)));
        $date = now()->toRfc2822String();
        $to = $this->formatAddress($recipientEmail, $recipientName);
        $normalizedBody = preg_replace("/\r\n|\r|\n/", "\r\n", trim($body))."\r\n";

        $headers = [
            "Date: {$date}",
            'From: '.$this->formatAddress($fromAddress, $fromName),
            "To: {$to}",
            'Reply-To: '.$this->formatAddress($fromAddress, $fromName),
            "Message-ID: {$messageId}",
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];

        if (! mail(
            $recipientEmail,
            $subject,
            $normalizedBody,
            implode("\r\n", $headers),
            '-f'.$fromAddress,
        )) {
            throw new RuntimeException('The mail server rejected the outgoing message.');
        }

        $this->archiveToSentFolder(
            implode("\r\n", [...$headers, 'Subject: '.$this->encodeHeader($subject)])."\r\n\r\n".$normalizedBody,
        );

        return trim($messageId, '<>');
    }

    private function archiveToSentFolder(string $message): void
    {
        $maildir = rtrim((string) config('mail.native_sent_maildir'), '/\\');

        if ($maildir === '' || ! is_dir($maildir) || ! is_writable($maildir)) {
            throw new RuntimeException('The Roundcube Sent folder is unavailable.');
        }

        $filename = sprintf(
            '%d.M%dP%d.%s,S=%d:2,S',
            time(),
            (int) ((microtime(true) * 1_000_000) % 1_000_000),
            getmypid(),
            gethostname() ?: 'rovinyawolff',
            strlen($message),
        );
        $path = $maildir.DIRECTORY_SEPARATOR.$filename;

        if (file_put_contents($path, $message, LOCK_EX) === false) {
            throw new RuntimeException('The sent message could not be archived in Roundcube.');
        }

        chmod($path, 0600);
    }

    private function formatAddress(string $email, ?string $name = null): string
    {
        $safeEmail = str_replace(["\r", "\n"], '', trim($email));
        $safeName = str_replace(["\r", "\n", '"'], ['', '', '\\"'], trim((string) $name));

        return $safeName === '' ? $safeEmail : sprintf('"%s" <%s>', $safeName, $safeEmail);
    }

    private function encodeHeader(string $value): string
    {
        $safeValue = str_replace(["\r", "\n"], '', trim($value));

        return mb_encode_mimeheader($safeValue, 'UTF-8', 'B', "\r\n");
    }
}
