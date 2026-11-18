<?php
// Shared helpers: paths, escaping, settings, formatting, and view rendering.

function app_root()
{
    return dirname(__DIR__);
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function str_cut($value, $max)
{
    $value = (string) $value;
    $max = (int) $max;
    if ($max < 1) {
        return '';
    }
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $max, 'UTF-8');
    }
    if (strlen($value) <= $max) {
        return $value;
    }
    $cut = substr($value, 0, $max);
    $trimmed = preg_replace('/[\x80-\xBF]+$/', '', $cut);
    return is_string($trimmed) ? $trimmed : $cut;
}

function clean_text($value, $max)
{
    $value = str_replace(["\r\n", "\r"], "\n", (string) $value);
    $value = preg_replace("/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/", '', $value);
    if (!is_string($value)) {
        $value = '';
    }
    return trim(str_cut($value, $max));
}

function clean_line($value, $max)
{
    $value = clean_text($value, $max);
    $value = str_replace(["\n", "\t"], ' ', $value);
    $value = preg_replace('/ {2,}/', ' ', $value);
    return trim(is_string($value) ? $value : '');
}

function slugify($text)
{
    $text = strtolower(trim((string) $text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim(is_string($text) ? $text : '', '-');
    if ($text === '') {
        $text = 'item';
    }
    return str_cut($text, 180);
}

function unique_slug($table, $wanted, $ignoreId = 0)
{
    $allowed = ['services' => true, 'posts' => true, 'pages' => true];
    if (!isset($allowed[$table])) {
        throw new InvalidArgumentException('Unknown slug table');
    }
    $base = slugify($wanted);
    $slug = $base;
    $n = 2;
    $ignoreId = (int) $ignoreId;
    while (db()->one('SELECT id FROM ' . $table . ' WHERE slug = ? AND id <> ?', [$slug, $ignoreId])) {
        $slug = $base . '-' . $n;
        $n++;
        if ($n > 60) {
            break;
        }
    }
    return $slug;
}

function reserved_slug($slug)
{
    $reserved = [
        'admin', 'install', 'install.php', 'index.php', 'services', 'branches', 'gallery',
        'blog', 'contact', 'quote', 'faq', 'sitemap.xml', 'robots.txt', 'assets', 'uploads',
        'about', 'privacy', 'terms',
    ];
    return in_array($slug, $reserved, true);
}

function is_core_page($slug)
{
    return in_array($slug, ['about', 'privacy', 'terms'], true);
}

function is_installed()
{
    return is_file(app_root() . '/config/config.php') && is_file(app_root() . '/config/installed.lock');
}

function base_url()
{
    if (isset($GLOBALS['ftee_config']) && is_array($GLOBALS['ftee_config']) && array_key_exists('base_url', $GLOBALS['ftee_config'])) {
        return rtrim((string) $GLOBALS['ftee_config']['base_url'], '/');
    }
    return '';
}

function url($path = '/')
{
    $base = base_url();
    $path = '/' . ltrim((string) $path, '/');
    if ($path !== '/') {
        $path = rtrim($path, '/');
    }
    if ($base === '') {
        return $path;
    }
    if ($path === '/') {
        return $base . '/';
    }
    return $base . $path;
}

function asset($path, $bust = true)
{
    $rel = ltrim((string) $path, '/');
    $href = url('assets/' . $rel);
    if (!$bust) {
        return $href;
    }
    $full = app_root() . '/assets/' . $rel;
    $version = is_file($full) ? (string) filemtime($full) : '1';
    return $href . '?v=' . $version;
}

function media_url($path)
{
    $path = ltrim((string) $path, '/');
    if (strpos($path, 'assets/') === 0) {
        return url($path);
    }
    return url('uploads/' . $path);
}

function is_https()
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    if (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443') {
        return true;
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }
    return false;
}

function valid_origin($value)
{
    return (bool) preg_match('#^https?://[A-Za-z0-9.-]+(?::\d{1,5})?$#', (string) $value);
}

function valid_optional_url($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return true;
    }
    return (bool) filter_var($value, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $value);
}

function site_origin()
{
    $set = '';
    if (isset($GLOBALS['ftee_db'])) {
        $set = trim((string) setting('site_url', ''));
    }
    if ($set !== '' && valid_origin($set)) {
        return rtrim($set, '/');
    }
    $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : 'localhost';
    $host = preg_replace('/[^A-Za-z0-9.:_-]/', '', $host);
    if (!is_string($host) || $host === '') {
        $host = 'localhost';
    }
    return (is_https() ? 'https' : 'http') . '://' . $host;
}

function absolute_url($path)
{
    $path = (string) $path;
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return rtrim(site_origin(), '/') . $path;
}

function canonical_url($path = null)
{
    if ($path === null) {
        $path = request_path();
    }
    return absolute_url(url($path));
}

function request_path()
{
    $uri = parse_url(isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    if (!is_string($uri) || $uri === '') {
        $uri = '/';
    }
    $uri = rawurldecode($uri);
    $base = base_url();
    if ($base !== '' && ($uri === $base || strpos($uri, $base . '/') === 0)) {
        $uri = substr($uri, strlen($base));
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }
    }
    $uri = '/' . trim($uri, '/');
    if ($uri === '/') {
        return '/';
    }
    return rtrim($uri, '/');
}

function redirect($path, $code = 302)
{
    header('Location: ' . url($path), true, (int) $code);
    exit;
}

function client_ip()
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
    if (strlen($ip) > 45) {
        return '';
    }
    return $ip;
}

function flash($key, $message = null)
{
    if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
        $_SESSION['_flash'] = [];
    }
    if ($message === null) {
        if (!isset($_SESSION['_flash'][$key])) {
            return null;
        }
        $stored = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $stored;
    }
    $_SESSION['_flash'][$key] = $message;
    return null;
}

function db()
{
    if (empty($GLOBALS['ftee_db']) || !($GLOBALS['ftee_db'] instanceof Database)) {
        throw new RuntimeException('Database is not connected');
    }
    return $GLOBALS['ftee_db'];
}

function settings_all()
{
    if (isset($GLOBALS['ftee_settings']) && is_array($GLOBALS['ftee_settings'])) {
        return $GLOBALS['ftee_settings'];
    }
    $rows = db()->all('SELECT setting_key, setting_value FROM settings');
    $all = [];
    foreach ($rows as $row) {
        $all[$row['setting_key']] = (string) $row['setting_value'];
    }
    $GLOBALS['ftee_settings'] = $all;
    return $all;
}

function setting($key, $default = '')
{
    $all = settings_all();
    if (!array_key_exists($key, $all) || $all[$key] === '') {
        return $default;
    }
    return $all[$key];
}

function forget_settings()
{
    unset($GLOBALS['ftee_settings']);
}

function head_office()
{
    if (array_key_exists('ftee_head_office', $GLOBALS)) {
        return $GLOBALS['ftee_head_office'];
    }
    $row = db()->one('SELECT * FROM branches WHERE is_head_office = 1 ORDER BY id ASC LIMIT 1');
    if (!$row) {
        $row = db()->one('SELECT * FROM branches WHERE is_published = 1 ORDER BY sort_order ASC, id ASC LIMIT 1');
    }
    $GLOBALS['ftee_head_office'] = $row;
    return $row;
}

function forget_head_office()
{
    unset($GLOBALS['ftee_head_office']);
}

function published_services()
{
    if (isset($GLOBALS['ftee_services']) && is_array($GLOBALS['ftee_services'])) {
        return $GLOBALS['ftee_services'];
    }
    $GLOBALS['ftee_services'] = db()->all(
        'SELECT id, name, slug, summary, icon_key, sort_order FROM services WHERE is_published = 1 ORDER BY sort_order ASC, id ASC'
    );
    return $GLOBALS['ftee_services'];
}

function branch_lines(array $branch)
{
    $lines = [];
    if (!empty($branch['plus_code'])) {
        $lines[] = $branch['plus_code'];
    }
    $place = [];
    if (!empty($branch['area'])) {
        $place[] = $branch['area'];
    }
    if (!empty($branch['address_line']) && strcasecmp($branch['address_line'], isset($branch['area']) ? $branch['area'] : '') !== 0) {
        $place[] = $branch['address_line'];
    }
    if ($place) {
        $lines[] = implode(', ', $place);
    }
    $city = trim((isset($branch['city']) ? $branch['city'] : '') . (!empty($branch['postal_code']) ? ' ' . $branch['postal_code'] : ''));
    if ($city !== '') {
        $lines[] = $city;
    }
    $region = [];
    if (!empty($branch['state']) && strcasecmp($branch['state'], isset($branch['city']) ? $branch['city'] : '') !== 0) {
        $region[] = $branch['state'];
    }
    if (!empty($branch['country'])) {
        $region[] = $branch['country'];
    }
    if ($region) {
        $lines[] = implode(', ', $region);
    }
    return $lines;
}

function branch_map_url(array $branch)
{
    if (!empty($branch['map_url']) && valid_optional_url($branch['map_url']) && trim($branch['map_url']) !== '') {
        return $branch['map_url'];
    }
    $parts = [];
    foreach (['plus_code', 'area', 'address_line', 'city', 'state', 'country'] as $key) {
        if (!empty($branch[$key])) {
            $parts[] = $branch[$key];
        }
    }
    $query = trim(implode(' ', $parts));
    if ($query === '') {
        return '';
    }
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
}

function format_text($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    $blocks = preg_split("/\n\s*\n/", $value);
    if (!is_array($blocks)) {
        return '';
    }
    $html = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        $html .= '<p>' . nl2br(e($block), false) . '</p>';
    }
    return $html;
}

function line_items($value)
{
    $lines = preg_split("/\n/", (string) $value);
    $items = [];
    if (!is_array($lines)) {
        return $items;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $items[] = $line;
        }
    }
    return $items;
}

function is_nigerian_phone($value)
{
    $digits = preg_replace('/\D+/', '', (string) $value);
    if (!is_string($digits)) {
        return false;
    }
    if (strpos($digits, '234') === 0 && strlen($digits) === 13) {
        $digits = '0' . substr($digits, 3);
    }
    if (strlen($digits) === 10 && preg_match('/^[1-9]/', $digits)) {
        $digits = '0' . $digits;
    }
    return (bool) preg_match('/^0\d{10}$/', $digits);
}

function format_nigerian_phone($value)
{
    $digits = preg_replace('/\D+/', '', (string) $value);
    if (!is_string($digits)) {
        return '';
    }
    if (strpos($digits, '234') === 0 && strlen($digits) === 13) {
        $digits = '0' . substr($digits, 3);
    }
    if (strlen($digits) === 10) {
        $digits = '0' . $digits;
    }
    if (preg_match('/^(0\d{3})(\d{3})(\d{4})$/', $digits, $match)) {
        return $match[1] . ' ' . $match[2] . ' ' . $match[3];
    }
    return $digits;
}

function phone_intl($stored)
{
    $digits = preg_replace('/\D+/', '', (string) $stored);
    if (!is_string($digits) || $digits === '') {
        return '';
    }
    if (strpos($digits, '0') === 0) {
        return '+234' . substr($digits, 1);
    }
    if (strpos($digits, '234') === 0) {
        return '+' . $digits;
    }
    return '+' . $digits;
}

function whatsapp_href($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $value)) {
        return $value;
    }
    $digits = preg_replace('/\D+/', '', $value);
    if (!is_string($digits) || $digits === '') {
        return '';
    }
    if (strpos($digits, '0') === 0) {
        $digits = '234' . substr($digits, 1);
    }
    return 'https://wa.me/' . $digits;
}

function package_price(array $package)
{
    if (trim((string) $package['price_label']) !== '') {
        return (string) $package['price_label'];
    }
    if ($package['price_amount'] !== null && $package['price_amount'] !== '') {
        return '₦' . number_format((int) $package['price_amount']);
    }
    return 'Price on request';
}

function format_stamp($value)
{
    if (!$value) {
        return '';
    }
    $ts = strtotime((string) $value);
    if ($ts === false) {
        return '';
    }
    return date('j M Y, H:i', $ts);
}

function format_day($value)
{
    if (!$value) {
        return '';
    }
    $ts = strtotime((string) $value);
    if ($ts === false) {
        return '';
    }
    return date('j M Y', $ts);
}

function nav_current($path)
{
    $req = request_path();
    $path = rtrim((string) $path, '/');
    if ($path === '') {
        $path = '/';
    }
    if ($path === '/') {
        return $req === '/' ? ' aria-current="page"' : '';
    }
    if ($req === $path || strpos($req, $path . '/') === 0) {
        return ' aria-current="page"';
    }
    return '';
}

function field_value($posted, $row, $key, $default = '')
{
    if (is_array($posted) && array_key_exists($key, $posted)) {
        return $posted[$key];
    }
    if (is_array($row) && array_key_exists($key, $row) && $row[$key] !== null) {
        return $row[$key];
    }
    return $default;
}

function field_checked($posted, $row, $key, $default = false)
{
    if (is_array($posted)) {
        return isset($posted[$key]);
    }
    if (is_array($row) && array_key_exists($key, $row)) {
        return (int) $row[$key] === 1;
    }
    return (bool) $default;
}

function mail_status_label($status)
{
    if ($status === 'sent') {
        return 'Email sent';
    }
    if ($status === 'failed') {
        return 'Email failed. The enquiry is still saved here.';
    }
    return 'Email not sent. Delivery is off, or no recipient is set.';
}

function json_ld_script($data)
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if (!is_string($json)) {
        return '';
    }
    return '<script type="application/ld+json">' . $json . '</script>';
}

function local_business_node()
{
    $name = setting('business_name', 'FTEE-PEE NIG LTD');
    $node = [
        '@type' => 'LocalBusiness',
        '@id' => canonical_url('/') . '#business',
        'name' => $name,
        'url' => canonical_url('/'),
        'description' => setting('seo_description'),
        'areaServed' => [
            '@type' => 'City',
            'name' => 'Lagos',
        ],
    ];
    $office = head_office();
    if ($office) {
        $street = [];
        foreach (['plus_code', 'area', 'address_line'] as $key) {
            if (!empty($office[$key])) {
                $street[] = $office[$key];
            }
        }
        $country = (strcasecmp((string) $office['country'], 'Nigeria') === 0) ? 'NG' : $office['country'];
        $node['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => implode(', ', $street),
            'addressLocality' => $office['city'],
            'addressRegion' => $office['state'],
            'postalCode' => $office['postal_code'],
            'addressCountry' => $country,
        ];
        $map = branch_map_url($office);
        if ($map !== '') {
            $node['hasMap'] = $map;
        }
        if (!empty($office['phone'])) {
            $node['telephone'] = phone_intl($office['phone']);
        }
    }
    if (empty($node['telephone']) && setting('phone') !== '') {
        $node['telephone'] = phone_intl(setting('phone'));
    }
    if (setting('email') !== '') {
        $node['email'] = setting('email');
    }
    if (setting('logo_path') !== '') {
        $image = absolute_url(media_url(setting('logo_path')));
        $node['image'] = $image;
        $node['logo'] = $image;
    }
    $same = [];
    foreach (['facebook', 'instagram', 'x_url', 'linkedin'] as $key) {
        $value = trim(setting($key));
        if ($value !== '' && valid_optional_url($value)) {
            $same[] = $value;
        }
    }
    if ($same) {
        $node['sameAs'] = $same;
    }
    $items = [];
    foreach (published_services() as $service) {
        $items[] = [
            '@type' => 'Offer',
            'itemOffered' => [
                '@type' => 'Service',
                'name' => $service['name'],
                'url' => canonical_url('/services/' . $service['slug']),
            ],
        ];
    }
    if ($items) {
        $node['hasOfferCatalog'] = [
            '@type' => 'OfferCatalog',
            'name' => 'Services',
            'itemListElement' => $items,
        ];
    }
    return $node;
}

function faq_node(array $faqs)
{
    $main = [];
    foreach ($faqs as $faq) {
        $main[] = [
            '@type' => 'Question',
            'name' => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => trim(preg_replace('/\s+/', ' ', (string) $faq['answer'])),
            ],
        ];
    }
    return [
        '@type' => 'FAQPage',
        'mainEntity' => $main,
    ];
}

function breadcrumb_node(array $items)
{
    $list = [];
    $position = 1;
    foreach ($items as $item) {
        $list[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $item['name'],
            'item' => canonical_url($item['path']),
        ];
        $position++;
    }
    return [
        '@type' => 'BreadcrumbList',
        'itemListElement' => $list,
    ];
}

function require_admin()
{
    if (!Auth::check()) {
        flash('error', 'Sign in to continue.');
        redirect('/admin/login');
    }
}

function require_csrf()
{
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null;
    if (Csrf::verify($token)) {
        return;
    }
    http_response_code(419);
    $title = 'Form expired';
    $message = 'That form expired or did not include a valid security token. Go back, refresh the page, and submit it again.';
    if (Auth::check()) {
        render('admin/message', [
            'title' => $title,
            'nav' => '',
            'message' => $message,
        ], 'admin/layout');
    } else {
        render('message', [
            'seoTitle' => $title,
            'heading' => $title,
            'message' => $message,
            'robots' => 'noindex, nofollow',
            'bodyClass' => 'page-simple',
        ]);
    }
    exit;
}

function render($template, array $data = [], $layout = 'layout')
{
    extract($data, EXTR_SKIP);
    $contentTemplate = app_root() . '/templates/' . $template . '.php';
    if (!is_file($contentTemplate)) {
        throw new RuntimeException('Missing template ' . $template);
    }
    if ($layout === null || $layout === '') {
        require $contentTemplate;
        return;
    }
    $layoutFile = app_root() . '/templates/' . $layout . '.php';
    if (!is_file($layoutFile)) {
        throw new RuntimeException('Missing layout ' . $layout);
    }
    require $layoutFile;
}

function run_sql_file(PDO $pdo, $path)
{
    $sql = file_get_contents($path);
    if (!is_string($sql) || trim($sql) === '') {
        throw new RuntimeException('SQL file is empty: ' . $path);
    }
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    if (!is_string($sql)) {
        throw new RuntimeException('Could not read SQL file.');
    }
    $parts = preg_split('/;\s*(?:\r?\n|$)/', $sql);
    if (!is_array($parts)) {
        throw new RuntimeException('Could not split SQL file.');
    }
    foreach ($parts as $statement) {
        $statement = trim($statement);
        if ($statement === '') {
            continue;
        }
        $pdo->exec($statement);
    }
}

function normalize_base_url($value)
{
    $value = trim((string) $value);
    if ($value === '' || $value === '/') {
        return '';
    }
    if ($value[0] !== '/') {
        $value = '/' . $value;
    }
    $value = rtrim($value, '/');
    if (strpos($value, '..') !== false || !preg_match('#^/[A-Za-z0-9._/-]+$#', $value)) {
        return false;
    }
    return $value;
}

function write_config_file(array $config)
{
    $body = "<?php\n"
        . "// Site configuration written by the installer.\n"
        . "// If the site moves, edit base_url here and set the same folder as RewriteBase in .htaccess.\n"
        . "// Use '' at the domain root, or '/folder' in a subdirectory. Do not put the domain in base_url.\n"
        . "// The public domain is Admin, then Settings, then Site URL.\n\n"
        . 'return ' . var_export($config, true) . ";\n";
    $path = app_root() . '/config/config.php';
    $tmp = $path . '.tmp';
    if (file_put_contents($tmp, $body) === false) {
        throw new RuntimeException('Could not write config/config.php. Make the config directory writable during install.');
    }
    @chmod($tmp, 0640);
    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        throw new RuntimeException('Could not move config into place.');
    }
}

function write_rewrite_base($baseUrl)
{
    $path = app_root() . '/.htaccess';
    $ht = file_get_contents($path);
    if (!is_string($ht)) {
        throw new RuntimeException('Could not read .htaccess.');
    }
    $rewrite = ($baseUrl === '' ? '/' : $baseUrl . '/');
    $updated = preg_replace('/^[ \t]*RewriteBase[ \t]+\S+[ \t]*$/m', '    RewriteBase ' . $rewrite, $ht, 1, $count);
    if (!is_string($updated) || (int) $count < 1) {
        throw new RuntimeException('Could not find RewriteBase in .htaccess.');
    }
    if (file_put_contents($path, $updated) === false) {
        throw new RuntimeException('Could not update RewriteBase. Make .htaccess writable during install, or set it by hand.');
    }
}

function icon_keys()
{
    return [
        'print' => 'Design and printing',
        'clean' => 'Cleaning',
        'wheel' => 'Wheel balancing and alignment',
        'wash' => 'Car wash and detailing',
    ];
}

function service_icon($key)
{
    $open = '<svg viewBox="0 0 48 48" width="36" height="36" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.6">';
    $close = '</svg>';
    if ($key === 'clean') {
        echo $open . '<path d="M16 30c0-6 4-10 8-14 4 4 8 8 8 14a8 8 0 0 1-16 0z"/><path d="M24 8v6M18 14h12"/>' . $close;
        return;
    }
    if ($key === 'wheel') {
        echo $open . '<circle cx="24" cy="24" r="14"/><circle cx="24" cy="24" r="4"/><path d="M24 10v6M24 32v6M10 24h6M32 24h6"/>' . $close;
        return;
    }
    if ($key === 'wash') {
        echo $open . '<path d="M24 8c6 8 10 12 10 18a10 10 0 0 1-20 0c0-6 4-10 10-18z"/>' . $close;
        return;
    }
    echo $open . '<rect x="10" y="12" width="22" height="26"/><path d="M16 12V8h10v4M14 22h14M14 28h10"/><circle cx="34" cy="34" r="6"/><path d="M34 30v8M30 34h8"/>' . $close;
}

function office_field($posted, $office, $postKey, $rowKey, $default = '')
{
    if (is_array($posted) && array_key_exists($postKey, $posted)) {
        return $posted[$postKey];
    }
    if (is_array($office) && array_key_exists($rowKey, $office) && $office[$rowKey] !== null) {
        return $office[$rowKey];
    }
    return $default;
}
