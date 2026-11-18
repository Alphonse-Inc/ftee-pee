<?php
// Session CSRF token for every state-changing form.

class Csrf
{
    public static function token()
    {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field()
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function verify($token)
    {
        $known = isset($_SESSION['csrf_token']) ? (string) $_SESSION['csrf_token'] : '';
        if ($known === '' || !is_string($token) || $token === '') {
            return false;
        }
        return hash_equals($known, $token);
    }
}
