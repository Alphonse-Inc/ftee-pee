<?php
// Admin sign-in, session identity, and database login throttling.

class Auth
{
    const MAX_IP = 5;
    const MAX_EMAIL = 8;

    public static function check()
    {
        return !empty($_SESSION['user_id']);
    }

    public static function id()
    {
        return self::check() ? (int) $_SESSION['user_id'] : 0;
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        if (!empty($GLOBALS['ftee_user']) && is_array($GLOBALS['ftee_user'])) {
            return $GLOBALS['ftee_user'];
        }
        $user = db()->one(
            'SELECT id, name, email, role FROM users WHERE id = ?',
            [self::id()]
        );
        if (!$user) {
            unset($_SESSION['user_id']);
            return null;
        }
        $GLOBALS['ftee_user'] = $user;
        return $user;
    }

    public static function attempt($email, $password)
    {
        $email = strtolower(trim((string) $email));
        $password = (string) $password;
        $ip = client_ip();
        self::prune();
        if (self::throttled($ip, $email)) {
            return 'throttled';
        }
        if ($password === '' || strlen($password) > 200) {
            self::record($ip, $email);
            self::burnTime($password);
            return 'invalid';
        }
        $user = db()->one('SELECT id, password_hash FROM users WHERE email = ?', [$email]);
        if (!$user) {
            self::burnTime($password);
            self::record($ip, $email);
            return 'invalid';
        }
        if (!password_verify($password, (string) $user['password_hash'])) {
            self::record($ip, $email);
            return 'invalid';
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        unset($_SESSION['csrf_token'], $GLOBALS['ftee_user']);
        db()->run('DELETE FROM login_attempts WHERE ip_address = ? OR email = ?', [$ip, $email]);
        return 'ok';
    }

    public static function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    private static function throttled($ip, $email)
    {
        $ipCount = (int) db()->value(
            'SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)',
            [$ip]
        );
        $emailCount = (int) db()->value(
            'SELECT COUNT(*) FROM login_attempts WHERE email = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)',
            [$email]
        );
        return $ipCount >= self::MAX_IP || ($email !== '' && $emailCount >= self::MAX_EMAIL);
    }

    private static function record($ip, $email)
    {
        db()->insert(
            'INSERT INTO login_attempts (ip_address, email, attempted_at) VALUES (?, ?, NOW())',
            [$ip, $email]
        );
    }

    private static function prune()
    {
        db()->run('DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 2 DAY)');
    }

    private static function burnTime($password)
    {
        password_verify($password, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
    }
}
