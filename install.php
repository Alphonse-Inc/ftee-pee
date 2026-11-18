<?php
// Browser installer. Creates tables, seeds content, writes config, then locks itself.

define('FTEE_INSTALLER', true);
require __DIR__ . '/app/bootstrap.php';

$script = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME']) : '/install.php';
$dir = rtrim(dirname($script), '/');
$prefix = ($dir === '' || $dir === '/' || $dir === '.') ? '' : $dir;
$guessOrigin = (is_https() ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^A-Za-z0-9.:_-]/', '', (string) $_SERVER['HTTP_HOST']) : 'localhost');

if (is_installed()) {
    http_response_code(403);
    header('X-Robots-Tag: noindex, nofollow');
    render_installer([
        'mode' => 'locked',
        'prefix' => $prefix,
        'home' => url('/'),
        'admin' => url('/admin'),
    ]);
    exit;
}

$errors = [];
$old = [
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_name' => '',
    'db_user' => '',
    'db_pass' => '',
    'base_url' => $prefix,
    'site_url' => $guessOrigin,
    'admin_name' => '',
    'admin_email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
        $errors[] = 'The form expired. Refresh the page and try again.';
    }
    $old['db_host'] = clean_line(isset($_POST['db_host']) ? $_POST['db_host'] : '', 120);
    $old['db_port'] = clean_line(isset($_POST['db_port']) ? $_POST['db_port'] : '3306', 6);
    $old['db_name'] = clean_line(isset($_POST['db_name']) ? $_POST['db_name'] : '', 64);
    $old['db_user'] = clean_line(isset($_POST['db_user']) ? $_POST['db_user'] : '', 64);
    $old['db_pass'] = isset($_POST['db_pass']) ? (string) $_POST['db_pass'] : '';
    $old['site_url'] = clean_line(isset($_POST['site_url']) ? $_POST['site_url'] : '', 190);
    $old['admin_name'] = clean_line(isset($_POST['admin_name']) ? $_POST['admin_name'] : '', 120);
    $old['admin_email'] = strtolower(clean_line(isset($_POST['admin_email']) ? $_POST['admin_email'] : '', 190));
    $adminPassword = isset($_POST['admin_password']) ? (string) $_POST['admin_password'] : '';
    $adminConfirm = isset($_POST['admin_password_confirm']) ? (string) $_POST['admin_password_confirm'] : '';
    $base = normalize_base_url(isset($_POST['base_url']) ? $_POST['base_url'] : '');
    $old['base_url'] = isset($_POST['base_url']) ? (string) $_POST['base_url'] : '';

    if (!extension_loaded('pdo_mysql')) {
        $errors[] = 'Enable the pdo_mysql extension in cPanel Select PHP Version, then reload this page.';
    }
    if (!preg_match('/^[A-Za-z0-9._-]+$/', $old['db_host'])) {
        $errors[] = 'Database host looks invalid. localhost is the usual value.';
    }
    if (!preg_match('/^\d{2,5}$/', $old['db_port'])) {
        $errors[] = 'Database port should be a number, usually 3306.';
    }
    if (!preg_match('/^[A-Za-z0-9_]+$/', $old['db_name']) || !preg_match('/^[A-Za-z0-9_]+$/', $old['db_user'])) {
        $errors[] = 'Database name and user should use letters, numbers, and underscores.';
    }
    if ($base === false) {
        $errors[] = 'Folder path must be empty for the domain root, or a path like /fteepee.';
    } else {
        $old['base_url'] = $base;
    }
    if ($old['site_url'] !== '' && !valid_origin($old['site_url'])) {
        $errors[] = 'Site URL must be only the origin, such as https://example.com.';
    }
    if ($old['admin_name'] === '') {
        $errors[] = 'Enter the first admin’s name.';
    }
    if (!filter_var($old['admin_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid admin email.';
    }
    if (strlen($adminPassword) < 8 || strlen($adminPassword) > 200) {
        $errors[] = 'Use an admin password of at least 8 characters.';
    }
    if ($adminPassword !== $adminConfirm) {
        $errors[] = 'The two admin passwords do not match.';
    }
    if (!is_writable(app_root() . '/config')) {
        $errors[] = 'The config directory must be writable during install.';
    }

    if (!$errors) {
        try {
            $config = [
                'db_host' => $old['db_host'],
                'db_port' => $old['db_port'],
                'db_name' => $old['db_name'],
                'db_user' => $old['db_user'],
                'db_pass' => $old['db_pass'],
                'base_url' => $base,
            ];
            $database = Database::connect($config);
            $pdo = $database->pdo();
            $exists = $pdo->query("SHOW TABLES LIKE 'settings'");
            if ($exists && $exists->fetch()) {
                throw new RuntimeException('This database already has FTEE-PEE tables. Use an empty database, or drop those tables, then run the installer again.');
            }
            run_sql_file($pdo, app_root() . '/database/schema.sql');
            require app_root() . '/database/seed.php';
            $pdo->beginTransaction();
            try {
                seed_site($database, $old['site_url']);
                $database->insert(
                    'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)',
                    [$old['admin_name'], $old['admin_email'], password_hash($adminPassword, PASSWORD_DEFAULT), 'admin']
                );
                $pdo->commit();
            } catch (Exception $seedError) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $seedError;
            }
            write_config_file($config);
            $htaccessWarning = '';
            try {
                write_rewrite_base($base);
            } catch (Exception $htError) {
                $htaccessWarning = $htError->getMessage();
            }
            $lock = app_root() . '/config/installed.lock';
            file_put_contents($lock, 'Installed ' . date('c') . "\n");
            @chmod($lock, 0640);
            $GLOBALS['ftee_config'] = $config;
            header('X-Robots-Tag: noindex, nofollow');
            render_installer([
                'mode' => 'done',
                'prefix' => $base,
                'home' => ($base === '' ? '' : $base) . '/',
                'admin' => ($base === '' ? '' : $base) . '/admin',
                'email' => $old['admin_email'],
                'warning' => $htaccessWarning,
                'gd' => function_exists('imagecreatetruecolor'),
                'rewrite' => ($base === '' ? '/' : $base . '/'),
            ]);
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            if (strpos($e->getMessage(), 'already has FTEE-PEE tables') === false) {
                $errors[] = 'If this attempt created tables before it stopped, drop them or use a fresh database, then run the installer again.';
            }
        }
    }
}

header('X-Robots-Tag: noindex, nofollow');
render_installer([
    'mode' => 'form',
    'prefix' => $prefix,
    'errors' => $errors,
    'old' => $old,
]);

function render_installer(array $data)
{
    extract($data, EXTR_SKIP);
    require app_root() . '/templates/install.php';
}
