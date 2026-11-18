<?php
// Best-effort enquiry email. The enquiry is already stored before this runs.

class Mailer
{
    public static function sendEnquiry(array $enquiry)
    {
        if (setting('mail_enabled') !== '1') {
            return 'skipped';
        }
        $to = trim(setting('mail_recipient'));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return 'skipped';
        }
        if (!function_exists('mail')) {
            return 'failed';
        }
        $subject = self::headerSafe('New enquiry from ' . $enquiry['name'] . ' — ' . setting('business_name'));
        $lines = [
            'A new enquiry was saved on the website.',
            '',
            'Name: ' . $enquiry['name'],
            'Phone: ' . $enquiry['phone'],
            'Email: ' . $enquiry['email'],
            'Service: ' . $enquiry['service_name'],
            'Branch: ' . $enquiry['branch_name'],
            '',
            $enquiry['message'],
            '',
            'This copy is also in the admin, including when email does not arrive.',
        ];
        $from = trim(setting('mail_from'));
        if ($from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $from = $to;
        }
        $reply = filter_var($enquiry['email'], FILTER_VALIDATE_EMAIL) ? $enquiry['email'] : $from;
        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . self::headerSafe($from),
            'Reply-To: ' . self::headerSafe($reply),
        ]);
        $ok = @mail($to, $subject, implode("\n", $lines), $headers);
        return $ok ? 'sent' : 'failed';
    }

    private static function headerSafe($value)
    {
        return trim(str_replace(["\r", "\n", "%0a", "%0d", "%0A", "%0D"], '', (string) $value));
    }
}
